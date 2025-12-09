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
