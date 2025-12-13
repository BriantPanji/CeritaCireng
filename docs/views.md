# Database Views - CeritaCireng Database

Database views yang dirancang untuk menyederhanakan query kompleks dan menjaga ACID properties.

---

## 1. ccv_inventory_summary

### Query

```sql
CREATE OR REPLACE VIEW ccv_inventory_summary AS
SELECT
    i.id,
    i.name,
    i.type,
    i.unit,
    i.cost,
    COALESCE(inv.stock, 0) as current_stock,
    i.cost * COALESCE(inv.stock, 0) as stock_value
FROM items i
LEFT JOIN inventory inv ON inv.id_item = i.id
WHERE i.deleted_at IS NULL
ORDER BY i.type, i.name;
```

### Penjelasan

**Apa:** View yang menampilkan summary inventory dengan informasi item lengkap dan nilai stok.

**Kenapa (ACID):**

-   **Consistency:** Menyediakan snapshot yang konsisten dari data items dan inventory.
-   **Isolation:** View membaca dari data yang committed, memberikan consistent read.
-   **Durability:** Query kompleks disederhanakan di view layer tanpa duplikasi logic.

---

## 2. ccv_active_deliveries

### Query

```sql
CREATE OR REPLACE VIEW ccv_active_deliveries AS
SELECT
    d.id,
    d.id_outlet,
    o.name as outlet_name,
    d.id_kurir,
    uk.name as kurir_name,
    d.id_inventaris,
    ui.name as inventaris_name,
    d.status,
    d.assigned_at,
    d.delivered_at,
    COUNT(di.id) as total_items,
    SUM(di.quantity) as total_quantity
FROM deliveries d
INNER JOIN outlets o ON o.id = d.id_outlet
INNER JOIN users uk ON uk.id = d.id_kurir
INNER JOIN users ui ON ui.id = d.id_inventaris
LEFT JOIN delivery_items di ON di.id_delivery = d.id
WHERE d.status IN ('DITUGASKAN', 'DIKIRIM')
GROUP BY d.id, d.id_outlet, o.name, d.id_kurir, uk.name,
         d.id_inventaris, ui.name, d.status, d.assigned_at, d.delivered_at;
```

### Penjelasan

**Apa:** View yang menampilkan deliveries yang masih aktif (belum selesai/dibatalkan) dengan informasi lengkap.

**Kenapa (ACID):**

-   **Consistency:** Agregation dan join dilakukan secara konsisten tanpa duplikasi logic.
-   **Isolation:** View selalu membaca committed data dari multiple tables.
-   **Atomicity:** Complex join dan aggregation disederhanakan menjadi single view query.

---

## 3. ccv_daily_report_details

### Query

```sql
CREATE OR REPLACE VIEW ccv_daily_report_details AS
SELECT
    dor.id as report_id,
    dor.report_date,
    dor.report_time,
    dor.id_outlet,
    dor.outlet_name,
    dor.created_by_name,
    dor.is_validated,
    dor.notes,
    dori.id_item,
    it.name as item_name,
    it.type as item_type,
    dori.quantity_delivered,
    dori.quantity_returned,
    (dori.quantity_delivered - dori.quantity_returned) as net_usage,
    it.cost as item_cost,
    it.cost * (dori.quantity_delivered - dori.quantity_returned) as usage_value
FROM daily_outlet_reports dor
INNER JOIN daily_outlet_report_items dori ON dori.id_outlet_report = dor.id
INNER JOIN items it ON it.id = dori.id_item
ORDER BY dor.report_date DESC, dor.id_outlet, it.name;
```

### Penjelasan

**Apa:** View yang menampilkan detail lengkap laporan harian termasuk kalkulasi net usage dan nilai.

**Kenapa (ACID):**

-   **Consistency:** Kalkulasi derived values (net_usage, usage_value) konsisten di seluruh query.
-   **Isolation:** Join multiple tables dalam consistent snapshot.
-   **Durability:** Business logic terpusat di view, mudah di-maintain dan konsisten.

---

## 4. ccv_outlet_item_levels

### Query

```sql
CREATE OR REPLACE VIEW ccv_outlet_item_levels AS
SELECT
    o.id as outlet_id,
    o.name as outlet_name,
    i.id as item_id,
    i.name as item_name,
    i.type as item_type,
    ois.quantity as required_level,
    COALESCE(
        (SELECT SUM(di.quantity)
         FROM delivery_items di
         INNER JOIN deliveries d ON d.id = di.id_delivery
         WHERE d.id_outlet = o.id
           AND di.id_item = i.id
           AND d.status = 'DIKIRIM'
           AND DATE(d.delivered_at) = CURDATE()),
        0
    ) as delivered_today,
    COALESCE(
        (SELECT SUM(ri.quantity)
         FROM return_items ri
         INNER JOIN returns r ON r.id = ri.id_return
         INNER JOIN users u ON u.id = r.id_staff
         WHERE u.id_outlet = o.id
           AND ri.id_item = i.id
           AND DATE(r.returned_at) = CURDATE()),
        0
    ) as returned_today
FROM outlets o
CROSS JOIN items i
LEFT JOIN outlet_item_settings ois ON ois.id_outlet = o.id AND ois.id_item = i.id
WHERE i.deleted_at IS NULL
ORDER BY o.name, i.name;
```

### Penjelasan

**Apa:** View yang menampilkan level kebutuhan item per outlet dengan tracking delivery dan return hari ini.

**Kenapa (ACID):**

-   **Consistency:** Subquery untuk delivered_today dan returned_today memberikan data konsisten.
-   **Isolation:** Complex aggregation terisolasi dalam view execution.
-   **Atomicity:** Multiple aggregation dalam single consistent snapshot.

---

## 5. ccv_item_movement_summary

### Query

```sql
CREATE OR REPLACE VIEW ccv_item_movement_summary AS
SELECT
    i.id as item_id,
    i.name as item_name,
    i.type as item_type,
    i.unit,
    COALESCE(inv.stock, 0) as current_stock,
    COALESCE(
        (SELECT SUM(di.quantity)
         FROM delivery_items di
         INNER JOIN deliveries d ON d.id = di.id_delivery
         WHERE di.id_item = i.id
           AND d.status IN ('DIKIRIM', 'SELESAI')
           AND DATE(d.assigned_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)),
        0
    ) as delivered_30d,
    COALESCE(
        (SELECT SUM(ri.quantity)
         FROM return_items ri
         INNER JOIN returns r ON r.id = ri.id_return
         WHERE ri.id_item = i.id
           AND DATE(r.returned_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)),
        0
    ) as returned_30d
FROM items i
LEFT JOIN inventory inv ON inv.id_item = i.id
WHERE i.deleted_at IS NULL
ORDER BY i.type, i.name;
```

### Penjelasan

**Apa:** View yang menampilkan summary pergerakan item dalam 30 hari terakhir.

**Kenapa (ACID):**

-   **Consistency:** Agregasi delivered dan returned dalam periode yang sama untuk data konsisten.
-   **Isolation:** Subqueries membaca committed data dalam consistent snapshot.
-   **Durability:** View menyimpan logic kompleks untuk analisis trend.

---

## 6. ccv_delivery_performance

### Query

```sql
CREATE OR REPLACE VIEW ccv_delivery_performance AS
SELECT
    uk.id as kurir_id,
    uk.name as kurir_name,
    COUNT(d.id) as total_deliveries,
    SUM(CASE WHEN d.status = 'SELESAI' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN d.status = 'DIBATALKAN' THEN 1 ELSE 0 END) as cancelled,
    SUM(CASE WHEN d.status IN ('DITUGASKAN', 'DIKIRIM') THEN 1 ELSE 0 END) as active,
    AVG(
        CASE
            WHEN d.delivered_at IS NOT NULL AND d.assigned_at IS NOT NULL
            THEN TIMESTAMPDIFF(MINUTE, d.assigned_at, d.delivered_at)
            ELSE NULL
        END
    ) as avg_delivery_time_minutes
FROM users uk
INNER JOIN roles r ON r.id = uk.id_role
LEFT JOIN deliveries d ON d.id_kurir = uk.id
WHERE r.name = 'kurir'
GROUP BY uk.id, uk.name
ORDER BY total_deliveries DESC;
```

### Penjelasan

**Apa:** View yang menampilkan performance metrics untuk setiap kurir.

**Kenapa (ACID):**

-   **Consistency:** Agregasi metrics (total, completed, cancelled, active) konsisten untuk setiap kurir.
-   **Isolation:** Kalkulasi rata-rata delivery time dalam consistent read.
-   **Atomicity:** Multiple calculations dalam single view query.

---

## 7. ccv_low_stock_alerts

### Query

```sql
CREATE OR REPLACE VIEW ccv_low_stock_alerts AS
SELECT
    i.id as item_id,
    i.name as item_name,
    i.type as item_type,
    i.unit,
    COALESCE(inv.stock, 0) as current_stock,
    ROUND(
        COALESCE(
            (SELECT SUM(di.quantity) / 30.0
             FROM delivery_items di
             INNER JOIN deliveries d ON d.id = di.id_delivery
             WHERE di.id_item = i.id
               AND d.status IN ('DIKIRIM', 'SELESAI')
               AND DATE(d.assigned_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)),
            0
        ), 2
    ) as avg_daily_usage,
    CASE
        WHEN COALESCE(inv.stock, 0) = 0 THEN 'OUT_OF_STOCK'
        WHEN COALESCE(inv.stock, 0) < (
            SELECT COALESCE(SUM(di.quantity) / 30.0 * 3, 0)
            FROM delivery_items di
            INNER JOIN deliveries d ON d.id = di.id_delivery
            WHERE di.id_item = i.id
              AND d.status IN ('DIKIRIM', 'SELESAI')
              AND DATE(d.assigned_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ) THEN 'LOW_STOCK'
        ELSE 'ADEQUATE'
    END as stock_status
FROM items i
LEFT JOIN inventory inv ON inv.id_item = i.id
WHERE i.deleted_at IS NULL
HAVING stock_status IN ('OUT_OF_STOCK', 'LOW_STOCK')
ORDER BY
    CASE stock_status
        WHEN 'OUT_OF_STOCK' THEN 1
        WHEN 'LOW_STOCK' THEN 2
    END,
    i.name;
```

### Penjelasan

**Apa:** View untuk monitoring stok rendah berdasarkan pola usage 30 hari terakhir.

**Kenapa (ACID):**

-   **Consistency:** Kalkulasi avg_daily_usage dan stock_status konsisten berdasarkan data yang sama.
-   **Isolation:** Multiple subqueries membaca data dalam consistent snapshot.
-   **Atomicity:** Complex conditional logic disederhanakan dalam single view.
-   **Durability:** Business rule untuk low stock alert terpusat dan mudah di-maintain.

---

## Contoh Penggunaan (Usage Examples)

### 1. Menggunakan `ccv_inventory_summary`

```sql
-- Lihat semua inventory summary
SELECT * FROM ccv_inventory_summary;

-- Filter berdasarkan tipe item
SELECT * FROM ccv_inventory_summary
WHERE type = 'BAHAN_BAKU';

-- Cari item dengan stok rendah (< 50)
SELECT * FROM ccv_inventory_summary
WHERE current_stock < 50
ORDER BY current_stock ASC;

-- Hitung total nilai inventory
SELECT
    SUM(stock_value) AS total_inventory_value,
    COUNT(*) AS total_items
FROM ccv_inventory_summary;

-- Group by tipe untuk summary per kategori
SELECT
    type,
    COUNT(*) AS jumlah_item,
    SUM(current_stock) AS total_stok,
    SUM(stock_value) AS total_nilai
FROM ccv_inventory_summary
GROUP BY type;
```

---

### 2. Menggunakan `ccv_active_deliveries`

```sql
-- Lihat semua pengantaran aktif
SELECT * FROM ccv_active_deliveries;

-- Filter pengantaran yang masih ditugaskan
SELECT * FROM ccv_active_deliveries
WHERE status = 'DITUGASKAN';

-- Lihat pengantaran per kurir
SELECT
    kurir_name,
    COUNT(*) AS total_active,
    SUM(total_quantity) AS total_items_to_deliver
FROM ccv_active_deliveries
GROUP BY kurir_id, kurir_name;

-- Lihat pengantaran ke outlet tertentu
SELECT * FROM ccv_active_deliveries
WHERE outlet_name = 'Outlet A';
```

---

### 3. Menggunakan `ccv_daily_report_details`

```sql
-- Lihat semua laporan detail
SELECT * FROM ccv_daily_report_details;

-- Filter laporan hari ini
SELECT * FROM ccv_daily_report_details
WHERE report_date = CURDATE();

-- Lihat laporan outlet tertentu
SELECT * FROM ccv_daily_report_details
WHERE outlet_name = 'Outlet Utama'
ORDER BY report_date DESC;

-- Summary per laporan
SELECT
    report_id,
    report_date,
    outlet_name,
    created_by_name,
    SUM(quantity_delivered) AS total_delivered,
    SUM(quantity_returned) AS total_returned,
    SUM(net_usage) AS total_sold,
    SUM(usage_value) AS total_value
FROM ccv_daily_report_details
WHERE report_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
GROUP BY report_id, report_date, outlet_name, created_by_name;

-- Cari laporan yang tidak valid
SELECT DISTINCT
    report_id,
    report_date,
    outlet_name,
    is_validated
FROM ccv_daily_report_details
WHERE is_validated = FALSE;
```

---

### 4. Menggunakan `ccv_outlet_item_levels`

```sql
-- Lihat kebutuhan semua outlet hari ini
SELECT * FROM ccv_outlet_item_levels;

-- Lihat outlet yang belum terkirim hari ini
SELECT * FROM ccv_outlet_item_levels
WHERE required_level > 0
  AND delivered_today = 0;

-- Hitung selisih kebutuhan vs pengiriman
SELECT
    outlet_name,
    item_name,
    required_level,
    delivered_today,
    returned_today,
    (delivered_today - returned_today) AS sold_today,
    (required_level - delivered_today) AS shortage
FROM ccv_outlet_item_levels
WHERE required_level > 0
HAVING shortage > 0;

-- Summary per outlet
SELECT
    outlet_name,
    SUM(required_level) AS total_required,
    SUM(delivered_today) AS total_delivered,
    SUM(returned_today) AS total_returned
FROM ccv_outlet_item_levels
GROUP BY outlet_id, outlet_name;
```

---

### 5. Menggunakan `ccv_item_movement_summary`

```sql
-- Lihat pergerakan semua item 30 hari terakhir
SELECT * FROM ccv_item_movement_summary;

-- Item yang paling banyak keluar
SELECT * FROM ccv_item_movement_summary
ORDER BY delivered_30d DESC
LIMIT 10;

-- Item dengan return paling tinggi
SELECT
    item_name,
    delivered_30d,
    returned_30d,
    ROUND(returned_30d * 100.0 / NULLIF(delivered_30d, 0), 2) AS return_rate_percent
FROM ccv_item_movement_summary
WHERE delivered_30d > 0
ORDER BY return_rate_percent DESC;

-- Hitung net usage (delivered - returned)
SELECT
    item_name,
    delivered_30d,
    returned_30d,
    (delivered_30d - returned_30d) AS net_usage_30d
FROM ccv_item_movement_summary
ORDER BY net_usage_30d DESC;
```

---

### 6. Menggunakan `ccv_delivery_performance`

```sql
-- Lihat performa semua kurir
SELECT * FROM ccv_delivery_performance;

-- Kurir dengan pengiriman terbanyak
SELECT * FROM ccv_delivery_performance
ORDER BY completed DESC;

-- Kurir dengan rata-rata waktu tercepat
SELECT * FROM ccv_delivery_performance
WHERE avg_delivery_time_minutes IS NOT NULL
ORDER BY avg_delivery_time_minutes ASC;

-- Hitung rate kesuksesan
SELECT
    kurir_name,
    total_deliveries,
    completed,
    cancelled,
    ROUND(completed * 100.0 / NULLIF(total_deliveries, 0), 2) AS success_rate
FROM ccv_delivery_performance
ORDER BY success_rate DESC;
```

---

### 7. Menggunakan `ccv_low_stock_alerts`

```sql
-- Lihat semua alert stok rendah
SELECT * FROM ccv_low_stock_alerts;

-- Filter hanya OUT_OF_STOCK
SELECT * FROM ccv_low_stock_alerts
WHERE stock_status = 'OUT_OF_STOCK';

-- Prioritas restok berdasarkan usage
SELECT
    item_name,
    current_stock,
    avg_daily_usage,
    stock_status,
    ROUND(avg_daily_usage * 7, 0) AS suggested_restock_qty
FROM ccv_low_stock_alerts
ORDER BY avg_daily_usage DESC;
```

---

## Contoh Kombinasi Views

```sql
-- Dashboard lengkap inventory dan stok alert
SELECT
    inv.name,
    inv.type,
    inv.current_stock,
    inv.stock_value,
    COALESCE(alert.stock_status, 'ADEQUATE') AS status,
    mov.delivered_30d,
    mov.returned_30d
FROM ccv_inventory_summary inv
LEFT JOIN ccv_low_stock_alerts alert ON alert.item_id = inv.id
LEFT JOIN ccv_item_movement_summary mov ON mov.item_id = inv.id
ORDER BY
    CASE COALESCE(alert.stock_status, 'ADEQUATE')
        WHEN 'OUT_OF_STOCK' THEN 1
        WHEN 'LOW_STOCK' THEN 2
        ELSE 3
    END,
    inv.name;

-- Report daily dengan performa kurir
SELECT
    d.id AS delivery_id,
    d.outlet_name,
    d.kurir_name,
    d.total_quantity,
    d.status,
    p.completed AS kurir_total_completed,
    p.avg_delivery_time_minutes
FROM ccv_active_deliveries d
INNER JOIN ccv_delivery_performance p ON p.kurir_id = d.id_kurir
ORDER BY d.assigned_at;
```
