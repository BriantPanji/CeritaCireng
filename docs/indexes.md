# Database Indexes - CeritaCireng Database

Index yang dirancang untuk meningkatkan performance query dan mendukung ACID properties.

---

## 1. cci_inventory_item

### Query

```sql
CREATE INDEX cci_inventory_item
ON inventory(id_item);
```

### Penjelasan

**Apa:** Index pada kolom `id_item` di tabel `inventory` untuk mempercepat lookup stok.

**Kenapa (ACID):**

-   **Isolation:** Mempercepat row-level locking saat concurrent transactions mengakses inventory berbeda.
-   **Consistency:** Query untuk check stock menjadi lebih cepat, mengurangi lock duration.
-   **Performance:** Lookup inventory by item_id adalah operasi yang sangat frequent.

---

## 2. cci_delivery_outlet_date

### Query

```sql
CREATE INDEX cci_delivery_outlet_date
ON deliveries(id_outlet, assigned_at);
```

### Penjelasan

**Apa:** Composite index pada `id_outlet` dan `assigned_at` untuk query deliveries per outlet per periode.

**Kenapa (ACID):**

-   **Consistency:** Mempercepat query untuk daily reports yang aggregate deliveries by outlet and date.
-   **Isolation:** Range locking yang lebih efisien untuk date-based queries.
-   **Performance:** View `ccv_daily_report_details` dan report generation heavily rely on this pattern.

---

## 3. cci_delivery_status

### Query

```sql
CREATE INDEX cci_delivery_status
ON deliveries(status);
```

### Penjelasan

**Apa:** Index pada kolom `status` untuk filtering deliveries berdasarkan status.

**Kenapa (ACID):**

-   **Consistency:** Mempercepat query untuk active deliveries (status IN ('DITUGASKAN', 'DIKIRIM')).
-   **Isolation:** Mengurangi scan time saat concurrent queries filter by status.
-   **Performance:** View `ccv_active_deliveries` menggunakan status filtering.

---

## 4. cci_delivery_items_delivery_item

### Query

```sql
CREATE INDEX cci_delivery_items_delivery_item
ON delivery_items(id_delivery, id_item);
```

### Penjelasan

**Apa:** Composite index pada `id_delivery` dan `id_item` untuk lookup items dalam delivery.

**Kenapa (ACID):**

-   **Atomicity:** Mempercepat validasi stok untuk semua items dalam delivery sebelum processing.
-   **Consistency:** Join antara deliveries dan delivery_items menjadi lebih efficient.
-   **Isolation:** Row-level locking lebih precise untuk concurrent delivery processing.

---

## 5. cci_return_items_return_item

### Query

```sql
CREATE INDEX cci_return_items_return_item
ON return_items(id_return, id_item);
```

### Penjelasan

**Apa:** Composite index pada `id_return` dan `id_item` untuk lookup items dalam return.

**Kenapa (ACID):**

-   **Atomicity:** Mempercepat aggregation untuk return processing.
-   **Consistency:** Join untuk daily reports menjadi lebih efficient.
-   **Performance:** Subquery dalam views untuk returned quantities menggunakan pattern ini.

---

## 6. cci_return_returned_date

### Query

```sql
CREATE INDEX cci_return_returned_date
ON returns(returned_at);
```

### Penjelasan

**Apa:** Index pada kolom `returned_at` untuk query returns berdasarkan tanggal.

**Kenapa (ACID):**

-   **Consistency:** Mempercepat date-range queries untuk daily reports.
-   **Isolation:** Efficient date filtering mengurangi lock contention.
-   **Performance:** Function `ccf_get_outlet_daily_returned` bergantung pada date filtering.

---

## 7. cci_daily_report_outlet_date

### Query

```sql
CREATE UNIQUE INDEX cci_daily_report_outlet_date
ON daily_outlet_reports(id_outlet, report_date);
```

### Penjelasan

**Apa:** Unique composite index untuk memastikan 1 report per outlet per hari.

**Kenapa (ACID):**

-   **Consistency:** UNIQUE constraint mencegah duplicate reports untuk outlet dan tanggal yang sama.
-   **Atomicity:** Index enforcement terjadi pada transaction level.
-   **Isolation:** Concurrent report creation untuk outlets berbeda tidak conflict.
-   **Durability:** Constraint dijamin oleh database engine.

---

## 8. cci_daily_report_validation

### Query

```sql
CREATE INDEX cci_daily_report_validation
ON daily_outlet_reports(is_validated);
```

### Penjelasan

**Apa:** Index pada kolom `is_validated` untuk filter reports yang perlu revalidasi.

**Kenapa (ACID):**

-   **Consistency:** Mempercepat query untuk menemukan invalidated reports.
-   **Performance:** Trigger yang invalidate reports dapat lebih cepat menemukan affected reports.
-   **Isolation:** Filtering validated vs invalidated reports lebih efficient.

---

## 9. cci_daily_report_items_report_item

### Query

```sql
CREATE INDEX cci_daily_report_items_report_item
ON daily_outlet_report_items(id_outlet_report, id_item);
```

### Penjelasan

**Apa:** Composite index untuk join report items dengan reports.

**Kenapa (ACID):**

-   **Atomicity:** Mempercepat batch insert report items saat create report.
-   **Consistency:** Join untuk view `ccv_daily_report_details` menjadi lebih efficient.
-   **Isolation:** Row-level access untuk specific report items lebih precise.

---

## 10. cci_users_outlet

### Query

```sql
CREATE INDEX cci_users_outlet
ON users(id_outlet);
```

### Penjelasan

**Apa:** Index pada `id_outlet` untuk lookup users per outlet (staff).

**Kenapa (ACID):**

-   **Consistency:** Mempercepat query untuk staff assignment and returns processing.
-   **Performance:** Join returns -> users -> outlet menggunakan index ini.
-   **Isolation:** Filtering users by outlet lebih efficient.

---

## 11. cci_users_role

### Query

```sql
CREATE INDEX cci_users_role
ON users(id_role);
```

### Penjelasan

**Apa:** Index pada `id_role` untuk filtering users berdasarkan role.

**Kenapa (ACID):**

-   **Consistency:** Query untuk kurir, staff, inventaris lebih cepat.
-   **Performance:** View `ccv_delivery_performance` filter by role='kurir'.
-   **Isolation:** Role-based queries tidak scan full table.

---

## 12. cci_items_type

### Query

```sql
CREATE INDEX cci_items_type
ON items(type);
```

### Penjelasan

**Apa:** Index pada kolom `type` untuk filtering items berdasarkan kategori.

**Kenapa (ACID):**

-   **Consistency:** Query items by type (BAHAN_MENTAH, BAHAN_PENUNJANG, KEMASAN) lebih efficient.
-   **Performance:** Reports dan views yang group/filter by type menggunakan index ini.
-   **Isolation:** Type-based filtering mengurangi lock contention.

---

## 13. cci_outlet_item_settings_composite

### Query

```sql
CREATE UNIQUE INDEX cci_outlet_item_settings_composite
ON outlet_item_settings(id_outlet, id_item);
```

### Penjelasan

**Apa:** Unique composite index untuk memastikan 1 setting per outlet per item.

**Kenapa (ACID):**

-   **Consistency:** Mencegah duplicate settings untuk kombinasi outlet-item yang sama.
-   **Atomicity:** Constraint enforcement di level transaction.
-   **Isolation:** Concurrent updates untuk outlet berbeda tidak conflict.
-   **Performance:** Lookup required_level per outlet-item combination sangat cepat.

---

## 14. cci_delivery_kurir

### Query

```sql
CREATE INDEX cci_delivery_kurir
ON deliveries(id_kurir);
```

### Penjelasan

**Apa:** Index pada `id_kurir` untuk tracking deliveries per kurir.

**Kenapa (ACID):**

-   **Performance:** View `ccv_delivery_performance` aggregate by kurir_id.
-   **Consistency:** Filtering deliveries by kurir lebih efficient.
-   **Isolation:** Concurrent delivery assignments untuk kurir berbeda tidak interfere.

---

## 15. cci_delivery_inventaris

### Query

```sql
CREATE INDEX cci_delivery_inventaris
ON deliveries(id_inventaris);
```

### Penjelasan

**Apa:** Index pada `id_inventaris` untuk tracking deliveries yang di-assign oleh inventaris staff.

**Kenapa (ACID):**

-   **Consistency:** Query untuk audit trail deliveries per inventaris staff.
-   **Performance:** Filtering dan aggregation by inventaris lebih cepat.
-   **Isolation:** Parallel delivery creation oleh multiple inventaris staff tidak conflict.
