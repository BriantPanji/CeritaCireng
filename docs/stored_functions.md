# Stored Functions - CeritaCireng Database

Stored functions yang dirancang untuk kalkulasi dan query data dengan menjaga ACID properties.

---

## 1. ccf_get_available_stock

### Query

```sql
DELIMITER $$

CREATE FUNCTION ccf_get_available_stock(
    p_id_item INT
)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_stock INT;

    SELECT COALESCE(stock, 0) INTO v_stock
    FROM inventory
    WHERE id_item = p_id_item;

    RETURN v_stock;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Function untuk mengambil stok tersedia dari sebuah item dengan null handling.

**Kenapa (ACID):**

-   **Consistency:** Selalu mengembalikan nilai valid (0 jika NULL) untuk mencegah kalkulasi error.
-   **Isolation:** READS SQL DATA memastikan function membaca data yang consistent.
-   **Durability:** Function bersifat DETERMINISTIC untuk hasil yang predictable.

---

## 2. ccf_calculate_delivery_total_items

### Query

```sql
DELIMITER $$

CREATE FUNCTION ccf_calculate_delivery_total_items(
    p_id_delivery INT
)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total INT;

    SELECT COALESCE(SUM(quantity), 0) INTO v_total
    FROM delivery_items
    WHERE id_delivery = p_id_delivery;

    RETURN v_total;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Function untuk menghitung total quantity items dalam satu delivery.

**Kenapa (ACID):**

-   **Atomicity:** Kalkulasi dilakukan dalam single query untuk menghindari partial results.
-   **Consistency:** Menggunakan COALESCE untuk mengembalikan 0 jika tidak ada items.
-   **Isolation:** READS SQL DATA memastikan data yang dibaca consistent pada saat query execution.

---

## 3. ccf_check_sufficient_stock

### Query

```sql
DELIMITER $$

CREATE FUNCTION ccf_check_sufficient_stock(
    p_id_item INT,
    p_required_quantity INT
)
RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_current_stock INT;

    SELECT COALESCE(stock, 0) INTO v_current_stock
    FROM inventory
    WHERE id_item = p_id_item;

    RETURN v_current_stock >= p_required_quantity;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Function untuk mengecek apakah stok mencukupi untuk quantity tertentu.

**Kenapa (ACID):**

-   **Consistency:** Validasi stok sebelum operasi untuk mencegah negative stock.
-   **Isolation:** Read consistent snapshot dari inventory.
-   **Durability:** Function deterministic memberikan hasil yang sama untuk input yang sama.

---

## 4. ccf_get_outlet_daily_delivered

### Query

```sql
DELIMITER $$

CREATE FUNCTION ccf_get_outlet_daily_delivered(
    p_id_outlet INT,
    p_id_item INT,
    p_date DATE
)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total INT;

    SELECT COALESCE(SUM(di.quantity), 0) INTO v_total
    FROM delivery_items di
    INNER JOIN deliveries d ON d.id = di.id_delivery
    WHERE d.id_outlet = p_id_outlet
      AND di.id_item = p_id_item
      AND DATE(d.assigned_at) = p_date
      AND d.status IN ('DIKIRIM', 'SELESAI');

    RETURN v_total;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Function untuk menghitung total quantity item yang dikirim ke outlet pada tanggal tertentu.

**Kenapa (ACID):**

-   **Atomicity:** Agregasi data delivery items dan deliveries dalam single query atomic.
-   **Consistency:** Filter status memastikan hanya delivery valid yang dihitung.
-   **Isolation:** READS SQL DATA menjamin consistent read pada saat execution.

---

## 5. ccf_get_outlet_daily_returned

### Query

```sql
DELIMITER $$

CREATE FUNCTION ccf_get_outlet_daily_returned(
    p_id_outlet INT,
    p_id_item INT,
    p_date DATE
)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total INT;

    SELECT COALESCE(SUM(ri.quantity), 0) INTO v_total
    FROM return_items ri
    INNER JOIN returns r ON r.id = ri.id_return
    INNER JOIN users u ON u.id = r.id_staff
    WHERE u.id_outlet = p_id_outlet
      AND ri.id_item = p_id_item
      AND DATE(r.returned_at) = p_date;

    RETURN v_total;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Function untuk menghitung total quantity item yang dikembalikan dari outlet pada tanggal tertentu.

**Kenapa (ACID):**

-   **Atomicity:** Join multiple tables dan agregasi dalam single atomic query.
-   **Consistency:** Menghitung berdasarkan data relasi yang konsisten (returns -> users -> outlet).
-   **Isolation:** Read operation terisolasi dari concurrent writes.

---

## 6. ccf_calculate_item_usage

### Query

```sql
DELIMITER $$

CREATE FUNCTION ccf_calculate_item_usage(
    p_id_item INT,
    p_start_date DATE,
    p_end_date DATE
)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total_delivered INT;
    DECLARE v_total_returned INT;
    DECLARE v_net_usage INT;

    -- Total delivered
    SELECT COALESCE(SUM(di.quantity), 0) INTO v_total_delivered
    FROM delivery_items di
    INNER JOIN deliveries d ON d.id = di.id_delivery
    WHERE di.id_item = p_id_item
      AND DATE(d.assigned_at) BETWEEN p_start_date AND p_end_date
      AND d.status IN ('DIKIRIM', 'SELESAI');

    -- Total returned
    SELECT COALESCE(SUM(ri.quantity), 0) INTO v_total_returned
    FROM return_items ri
    INNER JOIN returns r ON r.id = ri.id_return
    WHERE ri.id_item = p_id_item
      AND DATE(r.returned_at) BETWEEN p_start_date AND p_end_date;

    SET v_net_usage = v_total_delivered - v_total_returned;

    RETURN v_net_usage;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Function untuk menghitung net usage (delivered - returned) dari item dalam periode waktu.

**Kenapa (ACID):**

-   **Atomicity:** Kalkulasi delivered dan returned dilakukan dalam scope yang sama.
-   **Consistency:** Data agregat konsisten dengan filter status dan date range.
-   **Isolation:** Multiple SELECT statements terisolasi dalam function execution context.
-   **Durability:** Function deterministic menjamin hasil konsisten untuk parameter yang sama.

---

## 7. ccf_validate_delivery_stock

### Query

```sql
DELIMITER $$

CREATE FUNCTION ccf_validate_delivery_stock(
    p_id_delivery INT
)
RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_invalid_count INT;

    SELECT COUNT(*) INTO v_invalid_count
    FROM delivery_items di
    LEFT JOIN inventory i ON i.id_item = di.id_item
    WHERE di.id_delivery = p_id_delivery
      AND (i.stock IS NULL OR i.stock < di.quantity);

    RETURN v_invalid_count = 0;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Function untuk validasi apakah semua items dalam delivery memiliki stok yang cukup.

**Kenapa (ACID):**

-   **Consistency:** Pre-validation sebelum processing delivery untuk menjaga data integrity.
-   **Isolation:** Check stok dalam consistent snapshot.
-   **Atomicity:** Validasi semua items sekaligus dalam single query.

---

## 8. ccf_get_item_cost_total

### Query

```sql
DELIMITER $$

CREATE FUNCTION ccf_get_item_cost_total(
    p_id_delivery INT
)
RETURNS DECIMAL(15,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total_cost DECIMAL(15,2);

    SELECT COALESCE(SUM(i.cost * di.quantity), 0) INTO v_total_cost
    FROM delivery_items di
    INNER JOIN items i ON i.id = di.id_item
    WHERE di.id_delivery = p_id_delivery;

    RETURN v_total_cost;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Function untuk menghitung total biaya items dalam satu delivery.

**Kenapa (ACID):**

-   **Atomicity:** Kalkulasi cost semua items dalam single transaction-safe query.
-   **Consistency:** Join dengan items table memastikan harga yang konsisten.
-   **Isolation:** Membaca data dalam consistent state.
-   **Durability:** Deterministic function untuk hasil yang predictable.
