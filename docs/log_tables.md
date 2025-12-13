# Log Tables - CeritaCireng Database

Tabel-tabel log yang dirancang untuk audit trail dan tracking perubahan data di database. Semua log diisi secara otomatis melalui trigger tanpa perlu intervensi dari aplikasi.

---

## 1. inventory_change_log

### Query

```sql
CREATE TABLE inventory_change_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_item BIGINT UNSIGNED NOT NULL,
    old_stock INT NOT NULL,
    new_stock INT NOT NULL,
    change_amount INT NOT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_inventory_log_item
        FOREIGN KEY (id_item) REFERENCES items(id) ON DELETE CASCADE
);
```

### Penjelasan

**Deskripsi:**
Tabel `inventory_change_log` berfungsi untuk menyimpan catatan historis setiap perubahan stok pada tabel `inventory`. Setiap kali nilai `stock` pada tabel `inventory` berubah (baik bertambah maupun berkurang), trigger akan secara otomatis mencatat perubahan tersebut ke dalam tabel ini.

**Kolom:**

| Kolom           | Tipe Data       | Deskripsi                                                                                                                   |
| --------------- | --------------- | --------------------------------------------------------------------------------------------------------------------------- |
| `id`            | BIGINT UNSIGNED | Primary key dengan auto increment untuk identifikasi unik setiap record log                                                 |
| `id_item`       | BIGINT UNSIGNED | Foreign key yang mereferensi ke tabel `items`, menunjukkan item mana yang stoknya berubah                                   |
| `old_stock`     | INT             | Nilai stok sebelum perubahan terjadi                                                                                        |
| `new_stock`     | INT             | Nilai stok setelah perubahan terjadi                                                                                        |
| `change_amount` | INT             | Selisih antara stok baru dan stok lama. Nilai positif menandakan penambahan stok, nilai negatif menandakan pengurangan stok |
| `timestamp`     | DATETIME        | Waktu dan tanggal ketika perubahan stok terjadi                                                                             |

**Kegunaan:**

1. **Audit Trail:** Menyediakan jejak audit lengkap untuk setiap perubahan inventory, memudahkan investigasi jika terjadi perbedaan stok
2. **Analisis Pergerakan Stok:** Dapat digunakan untuk menganalisis pola pergerakan stok berdasarkan `change_amount` (positif = masuk, negatif = keluar)
3. **Rekonsiliasi:** Membantu proses rekonsiliasi stok fisik dengan stok sistem dengan melihat riwayat perubahan
4. **Compliance:** Memenuhi kebutuhan compliance untuk pencatatan perubahan data inventory

---

## 2. delivery_status_audit

### Query

```sql
CREATE TABLE delivery_status_audit (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_delivery BIGINT UNSIGNED NOT NULL,
    old_status ENUM('DITUGASKAN', 'DIKIRIM', 'SELESAI', 'DIBATALKAN') NOT NULL,
    new_status ENUM('DITUGASKAN', 'DIKIRIM', 'SELESAI', 'DIBATALKAN') NOT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_delivery_audit_delivery
        FOREIGN KEY (id_delivery) REFERENCES deliveries(id) ON DELETE CASCADE
);
```

### Penjelasan

**Deskripsi:**
Tabel `delivery_status_audit` berfungsi untuk mencatat setiap perubahan status pada tabel `deliveries`. Pengiriman (delivery) memiliki lifecycle status dari DITUGASKAN → DIKIRIM → SELESAI, dan setiap transisi status akan dicatat dalam tabel ini untuk keperluan audit dan tracking.

**Kolom:**

| Kolom         | Tipe Data       | Deskripsi                                                                                            |
| ------------- | --------------- | ---------------------------------------------------------------------------------------------------- |
| `id`          | BIGINT UNSIGNED | Primary key dengan auto increment untuk identifikasi unik setiap record audit                        |
| `id_delivery` | BIGINT UNSIGNED | Foreign key yang mereferensi ke tabel `deliveries`, menunjukkan delivery mana yang statusnya berubah |
| `old_status`  | ENUM            | Status delivery sebelum perubahan (DITUGASKAN, DIKIRIM, SELESAI, DIBATALKAN)                         |
| `new_status`  | ENUM            | Status delivery setelah perubahan (DITUGASKAN, DIKIRIM, SELESAI, DIBATALKAN)                         |
| `timestamp`   | DATETIME        | Waktu dan tanggal ketika perubahan status terjadi                                                    |

**Kegunaan:**

1. **Tracking Lifecycle:** Memungkinkan pelacakan lengkap perjalanan status setiap pengiriman dari awal hingga selesai
2. **Analisis Waktu:** Dapat menghitung berapa lama waktu yang dibutuhkan untuk transisi antar status dengan membandingkan timestamp antar record
3. **Investigasi Masalah:** Jika ada pengiriman bermasalah, dapat ditelusuri kapan status terakhir diubah
4. **Laporan Kinerja:** Data dapat digunakan untuk menganalisis kinerja kurir berdasarkan waktu transisi status

---

## 3. item_change_log

### Query

```sql
CREATE TABLE item_change_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_item BIGINT UNSIGNED NOT NULL,
    action ENUM('CREATE', 'UPDATE', 'DELETE', 'RESTORE') NOT NULL,
    field_changed VARCHAR(64) NULL,
    old_value VARCHAR(512) NULL,
    new_value VARCHAR(512) NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_item_log_item
        FOREIGN KEY (id_item) REFERENCES items(id) ON DELETE CASCADE
);
```

### Penjelasan

**Deskripsi:**
Tabel `item_change_log` berfungsi untuk mencatat setiap perubahan pada data master item di tabel `items`. Ini mencakup pembuatan item baru, update field tertentu (nama, harga, unit, tipe), soft delete, dan restore item. Setiap perubahan field dicatat secara terpisah untuk detail tracking yang lebih granular.

**Kolom:**

| Kolom           | Tipe Data       | Deskripsi                                                                                                                                                      |
| --------------- | --------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `id`            | BIGINT UNSIGNED | Primary key dengan auto increment untuk identifikasi unik setiap record log                                                                                    |
| `id_item`       | BIGINT UNSIGNED | Foreign key yang mereferensi ke tabel `items`, menunjukkan item mana yang datanya berubah                                                                      |
| `action`        | ENUM            | Jenis aksi yang dilakukan: CREATE (item baru dibuat), UPDATE (field tertentu diubah), DELETE (item di-soft delete), RESTORE (item di-restore dari soft delete) |
| `field_changed` | VARCHAR(64)     | Nama field yang berubah (untuk action UPDATE). NULL untuk action CREATE. Contoh: "name", "cost", "unit", "type"                                                |
| `old_value`     | VARCHAR(512)    | Nilai field sebelum perubahan. NULL untuk action CREATE                                                                                                        |
| `new_value`     | VARCHAR(512)    | Nilai field setelah perubahan. Untuk CREATE, berisi gabungan semua field awal                                                                                  |
| `timestamp`     | DATETIME        | Waktu dan tanggal ketika perubahan terjadi                                                                                                                     |

**Kegunaan:**

1. **Audit Data Master:** Mencatat semua perubahan pada data master item yang merupakan data penting untuk operasional
2. **Tracking Harga:** Secara khusus berguna untuk melacak riwayat perubahan harga (cost) item dari waktu ke waktu
3. **Rollback Reference:** Jika perlu mengetahui nilai sebelumnya untuk keperluan investigasi atau analisis
4. **Deteksi Perubahan:** Dapat mendeteksi perubahan yang tidak wajar pada data master

---

## 4. daily_report_log

### Query

```sql
CREATE TABLE daily_report_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_report BIGINT UNSIGNED NOT NULL,
    action ENUM('UPDATED', 'VALIDATED', 'INVALIDATED') NOT NULL,
    old_is_validated BOOLEAN NULL,
    new_is_validated BOOLEAN NOT NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_daily_report_log_report
        FOREIGN KEY (id_report) REFERENCES daily_outlet_reports(id) ON DELETE CASCADE
);
```

### Penjelasan

**Deskripsi:**
Tabel `daily_report_log` berfungsi untuk mencatat setiap perubahan pada laporan harian outlet di tabel `daily_outlet_reports`. Laporan harian adalah dokumen penting yang berisi data penjualan outlet pada tanggal tertentu. Setiap update, validasi, atau invalidasi laporan akan dicatat dalam log ini.

**Kolom:**

| Kolom              | Tipe Data       | Deskripsi                                                                                                           |
| ------------------ | --------------- | ------------------------------------------------------------------------------------------------------------------- |
| `id`               | BIGINT UNSIGNED | Primary key dengan auto increment untuk identifikasi unik setiap record log                                         |
| `id_report`        | BIGINT UNSIGNED | Foreign key yang mereferensi ke tabel `daily_outlet_reports`, menunjukkan laporan mana yang berubah                 |
| `action`           | ENUM            | Jenis aksi: UPDATED (data laporan diubah), VALIDATED (laporan divalidasi/disetujui), INVALIDATED (validasi dicabut) |
| `old_is_validated` | BOOLEAN         | Status validasi sebelum perubahan. NULL jika ini adalah update pertama                                              |
| `new_is_validated` | BOOLEAN         | Status validasi setelah perubahan                                                                                   |
| `timestamp`        | DATETIME        | Waktu dan tanggal ketika perubahan terjadi                                                                          |

**Kegunaan:**

1. **Audit Keuangan:** Laporan harian berkaitan langsung dengan data keuangan, sehingga setiap perubahan harus tercatat untuk keperluan audit
2. **Tracking Validasi:** Mencatat kapan laporan divalidasi dan jika validasi dicabut (invalidated)
3. **Deteksi Anomali:** Jika ada laporan yang sering diubah atau di-invalidate, bisa menjadi indikator masalah
4. **Bukti Perubahan:** Menyimpan status validasi sebelum dan sesudah perubahan untuk keperluan investigasi

---

## 5. return_items_log

### Query

```sql
CREATE TABLE return_items_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_return BIGINT UNSIGNED NOT NULL,
    action ENUM('CREATED', 'CONFIRMED', 'REJECTED', 'UPDATED') NOT NULL,
    staff_id BIGINT UNSIGNED NULL,
    old_status VARCHAR(32) NULL,
    new_status VARCHAR(32) NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_return_log_return
        FOREIGN KEY (id_return) REFERENCES returns(id) ON DELETE CASCADE,
    CONSTRAINT fk_return_log_staff
        FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL
);
```

### Penjelasan

**Deskripsi:**
Tabel `return_items_log` berfungsi untuk mencatat setiap aktivitas terkait pengembalian barang dari outlet ke gudang. Setiap pembuatan return, konfirmasi, penolakan, atau perubahan data akan tercatat dalam log ini untuk keperluan audit dan tracking inventory.

**Kolom:**

| Kolom        | Tipe Data       | Deskripsi                                                                                    |
| ------------ | --------------- | -------------------------------------------------------------------------------------------- |
| `id`         | BIGINT UNSIGNED | Primary key dengan auto increment untuk identifikasi unik setiap record log                  |
| `id_return`  | BIGINT UNSIGNED | Foreign key yang mereferensi ke tabel `returns`, menunjukkan return mana yang dicatat        |
| `action`     | ENUM            | Jenis aksi: CREATED (return baru dibuat), CONFIRMED (dikonfirmasi), REJECTED, UPDATED       |
| `staff_id`   | BIGINT UNSIGNED | Foreign key ke `users`, menunjukkan staff yang membuat/mengkonfirmasi return (nullable)      |
| `old_status` | VARCHAR(32)     | Status sebelum perubahan (untuk action UPDATED)                                             |
| `new_status` | VARCHAR(32)     | Status setelah perubahan (untuk action CONFIRMED/REJECTED/UPDATED)                          |
| `timestamp`  | DATETIME        | Waktu dan tanggal ketika aksi dilakukan                                                     |

**Kegunaan:**

1. **Inventory Accuracy:** Track pengembalian barang untuk menjaga akurasi stok
2. **Quality Control:** Analisis pattern return untuk identifikasi quality issues
3. **Staff Performance:** Monitor staff yang sering melakukan return
4. **Audit Trail:** Jejak lengkap untuk investigasi discrepancy

---

## 6. attendance_change_log

### Query

```sql
CREATE TABLE attendance_change_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_attendance BIGINT UNSIGNED NOT NULL,
    id_user BIGINT UNSIGNED NOT NULL,
    old_status ENUM('HADIR', 'IZIN', 'SAKIT', 'ABSEN') NOT NULL,
    new_status ENUM('HADIR', 'IZIN', 'SAKIT', 'ABSEN') NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_attendance_log_attendance
        FOREIGN KEY (id_attendance) REFERENCES attendances(id) ON DELETE CASCADE,
    CONSTRAINT fk_attendance_log_user
        FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_attendance_log_changer
        FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### Penjelasan

**Deskripsi:**
Tabel `attendance_change_log` berfungsi untuk mencatat setiap perubahan manual pada status kehadiran karyawan. Ini penting untuk audit HR dan mendeteksi manipulasi data absensi.

**Kolom:**

| Kolom           | Tipe Data       | Deskripsi                                                                           |
| --------------- | --------------- | ----------------------------------------------------------------------------------- |
| `id`            | BIGINT UNSIGNED | Primary key dengan auto increment untuk identifikasi unik setiap record log         |
| `id_attendance` | BIGINT UNSIGNED | Foreign key ke `attendances`, menunjukkan record absensi yang diubah               |
| `id_user`       | BIGINT UNSIGNED | Foreign key ke `users`, menunjukkan user yang absensinya diubah                     |
| `old_status`    | ENUM            | Status kehadiran sebelum perubahan (HADIR, IZIN, SAKIT, ABSEN)                      |
| `new_status`    | ENUM            | Status kehadiran setelah perubahan (HADIR, IZIN, SAKIT, ABSEN)                      |
| `changed_by`    | BIGINT UNSIGNED | Foreign key ke `users`, menunjukkan admin/staff yang melakukan perubahan (nullable) |
| `timestamp`     | DATETIME        | Waktu dan tanggal ketika perubahan dilakukan                                        |

**Kegunaan:**

1. **HR Compliance:** Memenuhi requirement audit untuk data kehadiran karyawan
2. **Fraud Detection:** Mendeteksi manipulasi data absensi (misalnya ubah ABSEN jadi HADIR)
3. **Accountability:** Track siapa yang mengubah data dan kapan
4. **Salary Calculation:** Audit trail untuk perhitungan gaji berdasarkan kehadiran

---

## 7. outlet_change_log

### Query

```sql
CREATE TABLE outlet_change_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_outlet BIGINT UNSIGNED NOT NULL,
    action ENUM('CREATE', 'UPDATE', 'DELETE', 'RESTORE') NOT NULL,
    field_changed VARCHAR(64) NULL,
    old_value VARCHAR(512) NULL,
    new_value VARCHAR(512) NULL,
    changed_by BIGINT UNSIGNED NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_outlet_log_outlet
        FOREIGN KEY (id_outlet) REFERENCES outlets(id) ON DELETE CASCADE,
    CONSTRAINT fk_outlet_log_changer
        FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### Penjelasan

**Deskripsi:**
Tabel `outlet_change_log` berfungsi untuk mencatat setiap perubahan pada data master outlet. Ini mencakup pembuatan outlet baru, update field tertentu (nama, lokasi, status), soft delete, dan restore outlet.

**Kolom:**

| Kolom           | Tipe Data       | Deskripsi                                                                                          |
| --------------- | --------------- | -------------------------------------------------------------------------------------------------- |
| `id`            | BIGINT UNSIGNED | Primary key dengan auto increment untuk identifikasi unik setiap record log                        |
| `id_outlet`     | BIGINT UNSIGNED | Foreign key ke `outlets`, menunjukkan outlet mana yang datanya berubah                            |
| `action`        | ENUM            | Jenis aksi: CREATE (outlet baru), UPDATE (field tertentu diubah), DELETE, RESTORE                  |
| `field_changed` | VARCHAR(64)     | Nama field yang berubah (untuk action UPDATE), contoh: "name", "location", "operation_hours"      |
| `old_value`     | VARCHAR(512)    | Nilai field sebelum perubahan (NULL untuk CREATE)                                                 |
| `new_value`     | VARCHAR(512)    | Nilai field setelah perubahan (untuk CREATE berisi gabungan semua field awal)                     |
| `changed_by`    | BIGINT UNSIGNED | Foreign key ke `users`, menunjukkan admin yang melakukan perubahan (nullable)                      |
| `timestamp`     | DATETIME        | Waktu dan tanggal ketika perubahan dilakukan                                                       |

**Kegunaan:**

1. **Business Structure Audit:** Track perubahan struktur bisnis (outlet baru, ditutup, dll)
2. **Configuration History:** Riwayat perubahan jam operasional atau lokasi outlet
3. **Accountability:** Siapa yang mengubah data outlet dan kapan
4. **Rollback Reference:** Informasi untuk mengembalikan perubahan jika diperlukan

---

## 8. user_management_log

### Query

```sql
CREATE TABLE user_management_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user BIGINT UNSIGNED NOT NULL,
    action ENUM('CREATE', 'UPDATE', 'DELETE', 'RESTORE', 'ROLE_CHANGE') NOT NULL,
    field_changed VARCHAR(64) NULL,
    old_value VARCHAR(512) NULL,
    new_value VARCHAR(512) NULL,
    changed_by BIGINT UNSIGNED NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_user_log_user
        FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_user_log_changer
        FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### Penjelasan

**Deskripsi:**
Tabel `user_management_log` berfungsi untuk mencatat setiap aktivitas terkait manajemen user di sistem. Ini mencakup pembuatan user baru, perubahan data user, perubahan role, deactivation, dan restore user. Log ini sangat penting untuk security audit dan compliance.

**Kolom:**

| Kolom           | Tipe Data       | Deskripsi                                                                                        |
| --------------- | --------------- | ------------------------------------------------------------------------------------------------ |
| `id`            | BIGINT UNSIGNED | Primary key dengan auto increment untuk identifikasi unik setiap record log                      |
| `id_user`       | BIGINT UNSIGNED | Foreign key ke `users`, menunjukkan user mana yang datanya berubah                              |
| `action`        | ENUM            | Jenis aksi: CREATE, UPDATE, DELETE, RESTORE, ROLE_CHANGE                                         |
| `field_changed` | VARCHAR(64)     | Nama field yang berubah, contoh: "email", "display_name", "outlet_id", "role_id"                |
| `old_value`     | VARCHAR(512)    | Nilai field sebelum perubahan (NULL untuk CREATE)                                               |
| `new_value`     | VARCHAR(512)    | Nilai field setelah perubahan                                                                    |
| `changed_by`    | BIGINT UNSIGNED | Foreign key ke `users`, menunjukkan admin yang melakukan perubahan (nullable untuk auto-create) |
| `timestamp`     | DATETIME        | Waktu dan tanggal ketika perubahan dilakukan                                                     |

**Kegunaan:**

1. **Security Audit:** Track siapa yang membuat/mengubah/menghapus user account
2. **Role Changes:** Monitor perubahan role user (staff → inventaris, dll)  
3. **Compliance:** Memenuhi requirement security untuk user management
4. **Investigation:** Jejak lengkap untuk investigasi unauthorized access
5. **Accountability:** Clear audit trail siapa mengubah apa dan kapan
