# Log Triggers - CeritaCireng Database

Trigger-trigger untuk mengisi tabel log yang didefinisikan di `log_tables.md`. Semua trigger ini berjalan secara otomatis di level database tanpa memerlukan intervensi dari aplikasi.

---

## 1. ccl_log_inventory_changes

### Query

```sql
DELIMITER $$

CREATE TRIGGER ccl_log_inventory_changes
AFTER UPDATE ON inventory
FOR EACH ROW
BEGIN
    IF OLD.stock != NEW.stock THEN
        INSERT INTO inventory_change_log (
            id_item,
            old_stock,
            new_stock,
            change_amount,
            timestamp
        ) VALUES (
            NEW.id_item,
            OLD.stock,
            NEW.stock,
            NEW.stock - OLD.stock,
            NOW()
        );
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Deskripsi:**
Trigger `ccl_log_inventory_changes` dijalankan secara otomatis setelah setiap operasi UPDATE pada tabel `inventory`. Trigger ini akan memeriksa apakah nilai `stock` berubah, dan jika ya, akan mencatat perubahan tersebut ke tabel `inventory_change_log`.

**Event:** AFTER UPDATE pada tabel `inventory`

**Kondisi Eksekusi:** Hanya dijalankan jika `OLD.stock != NEW.stock` (nilai stok benar-benar berubah)

**Data yang Dicatat:**

-   `id_item` - ID item yang stoknya berubah (dari `NEW.id_item`)
-   `old_stock` - Nilai stok sebelum perubahan (dari `OLD.stock`)
-   `new_stock` - Nilai stok setelah perubahan (dari `NEW.stock`)
-   `change_amount` - Selisih perubahan, dihitung otomatis sebagai `NEW.stock - OLD.stock`
-   `timestamp` - Waktu saat trigger dijalankan menggunakan `NOW()`

**Cara Kerja:**

1. Eloquent melakukan `UPDATE inventory SET stock = X WHERE id_item = Y`
2. Trigger mendeteksi perubahan pada kolom `stock`
3. Trigger secara otomatis menyimpan log perubahan
4. Tidak perlu ada kode tambahan di aplikasi

**Contoh Skenario:**
| Operasi | old_stock | new_stock | change_amount | Keterangan |
|---------|-----------|-----------|---------------|------------|
| Delivery | 100 | 90 | -10 | Stok berkurang 10 |
| Return | 90 | 95 | +5 | Stok bertambah 5 |
| Adjustment | 95 | 100 | +5 | Penyesuaian manual |

---

## 2. ccl_log_delivery_status_change

### Query

```sql
DELIMITER $$

CREATE TRIGGER ccl_log_delivery_status_change
AFTER UPDATE ON deliveries
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO delivery_status_audit (
            id_delivery,
            old_status,
            new_status,
            timestamp
        ) VALUES (
            NEW.id,
            OLD.status,
            NEW.status,
            NOW()
        );
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Deskripsi:**
Trigger `ccl_log_delivery_status_change` dijalankan secara otomatis setelah setiap operasi UPDATE pada tabel `deliveries`. Trigger ini akan memeriksa apakah nilai `status` berubah, dan jika ya, akan mencatat transisi status tersebut ke tabel `delivery_status_audit`.

**Event:** AFTER UPDATE pada tabel `deliveries`

**Kondisi Eksekusi:** Hanya dijalankan jika `OLD.status != NEW.status` (status benar-benar berubah)

**Data yang Dicatat:**

-   `id_delivery` - ID delivery yang statusnya berubah (dari `NEW.id`)
-   `old_status` - Status sebelum perubahan (dari `OLD.status`)
-   `new_status` - Status setelah perubahan (dari `NEW.status`)
-   `timestamp` - Waktu saat trigger dijalankan menggunakan `NOW()`

**Cara Kerja:**

1. Eloquent melakukan `$delivery->update(['status' => 'DIKIRIM'])`
2. Trigger mendeteksi perubahan pada kolom `status`
3. Trigger secara otomatis menyimpan log transisi status
4. Tidak perlu ada kode tambahan di aplikasi

**Contoh Skenario Lifecycle Delivery:**
| Urutan | old_status | new_status | Keterangan |
|--------|------------|------------|------------|
| 1 | DITUGASKAN | DIKIRIM | Kurir mulai mengirim |
| 2 | DIKIRIM | SELESAI | Pengiriman selesai |

---

## 3. ccl_log_item_insert

### Query

```sql
DELIMITER $$

CREATE TRIGGER ccl_log_item_insert
AFTER INSERT ON items
FOR EACH ROW
BEGIN
    INSERT INTO item_change_log (
        id_item,
        action,
        field_changed,
        old_value,
        new_value,
        timestamp
    ) VALUES (
        NEW.id,
        'CREATE',
        NULL,
        NULL,
        CONCAT('name:', NEW.name, '|cost:', NEW.cost, '|unit:', NEW.unit, '|type:', NEW.type),
        NOW()
    );
END$$

DELIMITER ;
```

### Penjelasan

**Deskripsi:**
Trigger `ccl_log_item_insert` dijalankan secara otomatis setelah setiap operasi INSERT pada tabel `items`. Trigger ini mencatat pembuatan item baru dengan menyimpan semua nilai field awal dalam satu record log.

**Event:** AFTER INSERT pada tabel `items`

**Kondisi Eksekusi:** Selalu dijalankan untuk setiap INSERT baru

**Data yang Dicatat:**

-   `id_item` - ID item yang baru dibuat (dari `NEW.id`)
-   `action` - 'CREATE'
-   `field_changed` - NULL (karena ini pembuatan baru)
-   `old_value` - NULL (karena item belum ada sebelumnya)
-   `new_value` - Gabungan semua field awal dalam format "name:xxx|cost:xxx|unit:xxx|type:xxx"
-   `timestamp` - Waktu saat trigger dijalankan menggunakan `NOW()`

**Cara Kerja:**

1. Eloquent melakukan `Item::create([...])`
2. Trigger mendeteksi INSERT baru
3. Trigger secara otomatis menyimpan log dengan action 'CREATE'
4. Tidak perlu ada kode tambahan di aplikasi

---

## 4. ccl_log_item_update

### Query

```sql
DELIMITER $$

CREATE TRIGGER ccl_log_item_update
AFTER UPDATE ON items
FOR EACH ROW
BEGIN
    -- Log name change
    IF OLD.name != NEW.name THEN
        INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
        VALUES (NEW.id, 'UPDATE', 'name', OLD.name, NEW.name, NOW());
    END IF;

    -- Log cost change
    IF OLD.cost != NEW.cost THEN
        INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
        VALUES (NEW.id, 'UPDATE', 'cost', CAST(OLD.cost AS CHAR), CAST(NEW.cost AS CHAR), NOW());
    END IF;

    -- Log unit change
    IF OLD.unit != NEW.unit THEN
        INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
        VALUES (NEW.id, 'UPDATE', 'unit', OLD.unit, NEW.unit, NOW());
    END IF;

    -- Log type change
    IF OLD.type != NEW.type THEN
        INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
        VALUES (NEW.id, 'UPDATE', 'type', OLD.type, NEW.type, NOW());
    END IF;

    -- Log soft delete
    IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
        INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
        VALUES (NEW.id, 'DELETE', 'deleted_at', NULL, CAST(NEW.deleted_at AS CHAR), NOW());
    END IF;

    -- Log restore
    IF OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL THEN
        INSERT INTO item_change_log (id_item, action, field_changed, old_value, new_value, timestamp)
        VALUES (NEW.id, 'RESTORE', 'deleted_at', CAST(OLD.deleted_at AS CHAR), NULL, NOW());
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Deskripsi:**
Trigger `ccl_log_item_update` dijalankan secara otomatis setelah setiap operasi UPDATE pada tabel `items`. Trigger ini mencatat setiap perubahan field secara terpisah (granular logging), sehingga dapat diketahui dengan tepat field mana yang berubah dan kapan.

**Event:** AFTER UPDATE pada tabel `items`

**Kondisi Eksekusi:** Trigger akan memeriksa setiap field berikut dan mencatat jika ada perubahan:

-   `name` - Nama item
-   `cost` - Harga/biaya item
-   `unit` - Satuan item (pcs, gr, ml, unit)
-   `type` - Tipe item (BAHAN_MENTAH, BAHAN_PENUNJANG, KEMASAN)
-   `deleted_at` - Untuk soft delete dan restore

**Data yang Dicatat per Field:**

-   `id_item` - ID item yang diubah (dari `NEW.id`)
-   `action` - 'UPDATE' untuk perubahan biasa, 'DELETE' untuk soft delete, 'RESTORE' untuk restore
-   `field_changed` - Nama field yang berubah
-   `old_value` - Nilai sebelum perubahan
-   `new_value` - Nilai setelah perubahan
-   `timestamp` - Waktu saat trigger dijalankan menggunakan `NOW()`

**Cara Kerja:**

1. Eloquent melakukan `$item->update(['cost' => 6000])`
2. Trigger mendeteksi perubahan pada kolom `cost`
3. Trigger secara otomatis menyimpan log dengan action 'UPDATE' dan field_changed 'cost'
4. Tidak perlu ada kode tambahan di aplikasi

**Catatan Penting:**

-   Jika satu operasi UPDATE mengubah beberapa field sekaligus, akan ada beberapa record log terpisah untuk setiap field yang berubah
-   Soft delete terdeteksi dari perubahan `deleted_at` dari NULL ke NOT NULL
-   Restore terdeteksi dari perubahan `deleted_at` dari NOT NULL ke NULL

---

## 5. ccl_log_daily_report_update

### Query

```sql
DELIMITER $$

CREATE TRIGGER ccl_log_daily_report_update
AFTER UPDATE ON daily_outlet_reports
FOR EACH ROW
BEGIN
    DECLARE v_action VARCHAR(32);

    -- Determine action type based on validation status change
    IF OLD.is_validated = FALSE AND NEW.is_validated = TRUE THEN
        SET v_action = 'VALIDATED';
    ELSEIF OLD.is_validated = TRUE AND NEW.is_validated = FALSE THEN
        SET v_action = 'INVALIDATED';
    ELSE
        SET v_action = 'UPDATED';
    END IF;

    INSERT INTO daily_report_log (
        id_report,
        action,
        old_is_validated,
        new_is_validated,
        timestamp
    ) VALUES (
        NEW.id,
        v_action,
        OLD.is_validated,
        NEW.is_validated,
        NOW()
    );
END$$

DELIMITER ;
```

### Penjelasan

**Deskripsi:**
Trigger `ccl_log_daily_report_update` dijalankan secara otomatis setelah setiap operasi UPDATE pada tabel `daily_outlet_reports`. Trigger ini mencatat setiap perubahan pada laporan harian, termasuk perubahan data, validasi, dan invalidasi.

**Event:** AFTER UPDATE pada tabel `daily_outlet_reports`

**Kondisi Eksekusi:** Selalu dijalankan untuk setiap UPDATE

**Penentuan Action (Otomatis dari Data):**
Trigger secara otomatis menentukan jenis action berdasarkan perubahan field `is_validated`:

-   `VALIDATED` - Jika `is_validated` berubah dari FALSE ke TRUE (laporan disetujui)
-   `INVALIDATED` - Jika `is_validated` berubah dari TRUE ke FALSE (persetujuan dicabut)
-   `UPDATED` - Untuk perubahan data lainnya

**Data yang Dicatat:**

-   `id_report` - ID laporan yang diubah (dari `NEW.id`)
-   `action` - Action yang ditentukan otomatis (VALIDATED, INVALIDATED, atau UPDATED)
-   `old_is_validated` - Status validasi sebelum perubahan (dari `OLD.is_validated`)
-   `new_is_validated` - Status validasi setelah perubahan (dari `NEW.is_validated`)
-   `timestamp` - Waktu saat trigger dijalankan menggunakan `NOW()`

**Cara Kerja:**

1. Eloquent melakukan `$report->update(['is_validated' => true])`
2. Trigger mendeteksi UPDATE dan memeriksa perubahan `is_validated`
3. Trigger secara otomatis menentukan action sebagai 'VALIDATED'
4. Trigger menyimpan log dengan status validasi sebelum dan sesudah
5. Tidak perlu ada kode tambahan di aplikasi

---

## Ringkasan Trigger Log

| No  | Nama Trigger                   | Tabel Sumber         | Tabel Log Target      | Event        |
| --- | ------------------------------ | -------------------- | --------------------- | ------------ |
| 1   | ccl_log_inventory_changes      | inventory            | inventory_change_log  | AFTER UPDATE |
| 2   | ccl_log_delivery_status_change | deliveries           | delivery_status_audit | AFTER UPDATE |
| 3   | ccl_log_item_insert            | items                | item_change_log       | AFTER INSERT |
| 4   | ccl_log_item_update            | items                | item_change_log       | AFTER UPDATE |
| 5   | ccl_log_daily_report_update    | daily_outlet_reports | daily_report_log      | AFTER UPDATE |

---

## Catatan Implementasi

### Fully Automatic

Semua trigger di atas berjalan sepenuhnya otomatis tanpa memerlukan kode tambahan di level aplikasi. Cukup gunakan Eloquent seperti biasa dan log akan terisi secara otomatis.

**Contoh penggunaan di Laravel:**

```php
// Inventory akan otomatis ter-log
$inventory->decrement('stock', 10);

// Delivery status akan otomatis ter-log
$delivery->update(['status' => 'DIKIRIM']);

// Item akan otomatis ter-log
Item::create([...]);
$item->update(['cost' => 6000]);
$item->delete(); // soft delete

// Daily report akan otomatis ter-log
$report->update(['is_validated' => true]);
```

### Best Practices

1. **Retention Policy:** Buat scheduled job untuk menghapus log yang sudah terlalu lama (misalnya > 1 tahun) untuk menjaga performa database
2. **Backup:** Pastikan tabel log termasuk dalam backup regular karena berisi data audit penting
3. **Monitoring:** Monitor pertumbuhan ukuran tabel log secara berkala
4. **Query Optimization:** Untuk query log yang kompleks, pertimbangkan untuk menambahkan index sesuai kebutuhan query yang sering dilakukan
