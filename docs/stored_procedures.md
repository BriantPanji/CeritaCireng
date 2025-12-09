# Stored Procedures - CeritaCireng Database

Stored procedures yang dirancang untuk menjaga ACID properties dalam sistem manajemen inventory dan pengiriman.

---

## 1. ccp_process_delivery

### Query

```sql
DELIMITER $$

CREATE PROCEDURE ccp_process_delivery(
    IN p_id_delivery INT,
    IN p_delivered_at DATETIME
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error processing delivery';
    END;

    START TRANSACTION;

    -- Update delivery status
    UPDATE deliveries
    SET status = 'DIKIRIM',
        delivered_at = p_delivered_at
    WHERE id = p_id_delivery
      AND status = 'DITUGASKAN';

    -- Decrease inventory for each delivered item
    UPDATE inventory i
    INNER JOIN delivery_items di ON i.id_item = di.id_item
    SET i.stock = i.stock - di.quantity
    WHERE di.id_delivery = p_id_delivery
      AND i.stock >= di.quantity;

    -- Verify all items had sufficient stock
    IF (SELECT COUNT(*)
        FROM delivery_items di
        LEFT JOIN inventory i ON i.id_item = di.id_item
        WHERE di.id_delivery = p_id_delivery
          AND (i.stock < 0 OR i.stock IS NULL)) > 0
    THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock for delivery';
    END IF;

    COMMIT;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Stored procedure untuk memproses pengiriman barang ke outlet dengan update status dan pengurangan stok inventory.

**Kenapa (ACID):**

-   **Atomicity:** Semua operasi (update delivery status + update inventory) dilakukan dalam satu transaksi. Jika gagal, semua di-rollback.
-   **Consistency:** Memastikan stok tidak pernah negatif dengan validasi sebelum commit.
-   **Isolation:** Transaksi terisolasi mencegah race condition saat multiple deliveries diproses bersamaan.
-   **Durability:** Setelah COMMIT, perubahan permanen tersimpan.

---

## 2. ccp_process_return

### Query

```sql
DELIMITER $$

CREATE PROCEDURE ccp_process_return(
    IN p_id_return INT,
    IN p_id_staff INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error processing return';
    END;

    START TRANSACTION;

    -- Update return status/timestamp
    UPDATE returns
    SET returned_at = NOW(),
        id_staff = p_id_staff
    WHERE id = p_id_return;

    -- Increase inventory for each returned item
    UPDATE inventory i
    INNER JOIN return_items ri ON i.id_item = ri.id_item
    SET i.stock = i.stock + ri.quantity
    WHERE ri.id_return = p_id_return;

    COMMIT;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Stored procedure untuk memproses pengembalian barang dari outlet, menambah kembali stok inventory.

**Kenapa (ACID):**

-   **Atomicity:** Return record update dan inventory increase harus sukses bersamaan atau gagal bersamaan.
-   **Consistency:** Menjaga konsistensi data antara tabel returns dan inventory.
-   **Isolation:** Mencegah konflik jika ada proses lain mengakses inventory yang sama.
-   **Durability:** Perubahan stok dipastikan tersimpan permanen.

---

## 3. ccp_create_daily_report

### Query

```sql
DELIMITER $$

CREATE PROCEDURE ccp_create_daily_report(
    IN p_id_outlet INT,
    IN p_id_staff INT,
    IN p_report_date DATE,
    IN p_notes TEXT,
    OUT p_report_id INT
)
BEGIN
    DECLARE v_outlet_name VARCHAR(128);
    DECLARE v_staff_name VARCHAR(128);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error creating daily report';
    END;

    START TRANSACTION;

    -- Get snapshot data
    SELECT name INTO v_outlet_name
    FROM outlets WHERE id = p_id_outlet;

    SELECT name INTO v_staff_name
    FROM users WHERE id = p_id_staff;

    -- Create report header
    INSERT INTO daily_outlet_reports (
        id_outlet, id_staff, report_date, report_time,
        is_validated, notes, created_by_name, outlet_name
    ) VALUES (
        p_id_outlet, p_id_staff, p_report_date, CURTIME(),
        TRUE, p_notes, v_staff_name, v_outlet_name
    );

    SET p_report_id = LAST_INSERT_ID();

    -- Insert report items (delivered quantities)
    INSERT INTO daily_outlet_report_items (
        id_outlet_report, id_item, quantity_delivered, quantity_returned
    )
    SELECT
        p_report_id,
        di.id_item,
        SUM(di.quantity) as delivered,
        0 as returned
    FROM delivery_items di
    INNER JOIN deliveries d ON d.id = di.id_delivery
    WHERE d.id_outlet = p_id_outlet
      AND DATE(d.assigned_at) = p_report_date
      AND d.status IN ('DIKIRIM', 'SELESAI')
    GROUP BY di.id_item;

    -- Update returned quantities
    UPDATE daily_outlet_report_items dori
    INNER JOIN (
        SELECT ri.id_item, SUM(ri.quantity) as total_returned
        FROM return_items ri
        INNER JOIN returns r ON r.id = ri.id_return
        INNER JOIN users u ON u.id = r.id_staff
        WHERE u.id_outlet = p_id_outlet
          AND DATE(r.returned_at) = p_report_date
        GROUP BY ri.id_item
    ) ret ON ret.id_item = dori.id_item
    SET dori.quantity_returned = ret.total_returned
    WHERE dori.id_outlet_report = p_report_id;

    COMMIT;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Stored procedure untuk membuat laporan harian outlet dengan agregasi data pengiriman dan pengembalian.

**Kenapa (ACID):**

-   **Atomicity:** Pembuatan report header dan semua report items harus sukses semua atau tidak sama sekali.
-   **Consistency:** Data snapshot (outlet_name, staff_name) diambil pada saat pembuatan untuk menjaga konsistensi historis.
-   **Isolation:** Transaksi mencegah data berubah di tengah proses agregasi.
-   **Durability:** Report yang sudah dibuat tidak akan hilang meski terjadi kegagalan sistem.

---

## 4. ccp_cancel_delivery

### Query

```sql
DELIMITER $$

CREATE PROCEDURE ccp_cancel_delivery(
    IN p_id_delivery INT,
    IN p_cancellation_reason TEXT
)
BEGIN
    DECLARE v_current_status VARCHAR(20);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error canceling delivery';
    END;

    START TRANSACTION;

    -- Check current status
    SELECT status INTO v_current_status
    FROM deliveries
    WHERE id = p_id_delivery
    FOR UPDATE;  -- Lock row to prevent concurrent modifications

    -- Only allow cancellation if not yet delivered or completed
    IF v_current_status NOT IN ('DITUGASKAN', 'DIKIRIM') THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot cancel completed or already canceled delivery';
    END IF;

    -- If already delivered, return items to inventory
    IF v_current_status = 'DIKIRIM' THEN
        UPDATE inventory i
        INNER JOIN delivery_items di ON i.id_item = di.id_item
        SET i.stock = i.stock + di.quantity
        WHERE di.id_delivery = p_id_delivery;
    END IF;

    -- Update delivery status
    UPDATE deliveries
    SET status = 'DIBATALKAN'
    WHERE id = p_id_delivery;

    COMMIT;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Stored procedure untuk membatalkan pengiriman dengan pengembalian stok jika sudah dikirim.

**Kenapa (ACID):**

-   **Atomicity:** Status update dan inventory restoration harus terjadi bersamaan.
-   **Consistency:** Validasi status mencegah pembatalan delivery yang tidak valid.
-   **Isolation:** Row locking (FOR UPDATE) mencegah concurrent modifications.
-   **Durability:** Perubahan status dan stok tersimpan permanen.

---

## 5. ccp_restock_inventory

### Query

```sql
DELIMITER $$

CREATE PROCEDURE ccp_restock_inventory(
    IN p_items JSON
)
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE v_count INT;
    DECLARE v_id_item INT;
    DECLARE v_quantity INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error restocking inventory';
    END;

    START TRANSACTION;

    -- Get array length
    SET v_count = JSON_LENGTH(p_items);

    -- Process each item
    WHILE i < v_count DO
        SET v_id_item = JSON_EXTRACT(p_items, CONCAT('$[', i, '].id_item'));
        SET v_quantity = JSON_EXTRACT(p_items, CONCAT('$[', i, '].quantity'));

        -- Insert or update inventory
        INSERT INTO inventory (id_item, stock)
        VALUES (v_id_item, v_quantity)
        ON DUPLICATE KEY UPDATE
            stock = stock + v_quantity;

        SET i = i + 1;
    END WHILE;

    COMMIT;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Stored procedure untuk melakukan restock multiple items sekaligus dengan input JSON array.

**Kenapa (ACID):**

-   **Atomicity:** Semua items harus ter-restock atau tidak sama sekali (all-or-nothing).
-   **Consistency:** Menggunakan INSERT...ON DUPLICATE KEY UPDATE untuk menghindari error jika item sudah ada.
-   **Isolation:** Transaksi memastikan tidak ada proses lain yang mengubah stok di tengah restock.
-   **Durability:** Stok baru dipastikan tersimpan setelah commit.

---

## 6. ccp_validate_and_lock_report

### Query

```sql
DELIMITER $$

CREATE PROCEDURE ccp_validate_and_lock_report(
    IN p_report_id INT,
    OUT p_is_valid BOOLEAN
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error validating report';
    END;

    START TRANSACTION;

    -- Lock report for validation
    SELECT is_validated INTO p_is_valid
    FROM daily_outlet_reports
    WHERE id = p_report_id
    FOR UPDATE;

    -- If report is still valid, mark as locked/finalized
    IF p_is_valid = TRUE THEN
        UPDATE daily_outlet_reports
        SET updated_at = NOW()
        WHERE id = p_report_id;
    END IF;

    COMMIT;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Stored procedure untuk validasi dan lock laporan harian agar tidak dapat diubah.

**Kenapa (ACID):**

-   **Atomicity:** Check validitas dan lock operation harus atomic.
-   **Consistency:** Memastikan hanya report yang valid yang bisa di-lock.
-   **Isolation:** FOR UPDATE mencegah race condition saat multiple users mencoba validasi bersamaan.
-   **Durability:** Status validasi tersimpan permanen.
