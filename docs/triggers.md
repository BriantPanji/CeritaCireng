# Database Triggers - CeritaCireng Database

Triggers yang dirancang untuk menjaga data integrity dan ACID properties secara otomatis.

---

## 1. cct_update_inventory_on_delivery

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_update_inventory_on_delivery
AFTER UPDATE ON deliveries
FOR EACH ROW
BEGIN
    -- Only process when status changes from DITUGASKAN to DIKIRIM
    IF OLD.status = 'DITUGASKAN' AND NEW.status = 'DIKIRIM' THEN
        -- Decrease inventory for each delivered item
        UPDATE inventory i
        INNER JOIN delivery_items di ON i.id_item = di.id_item
        SET i.stock = i.stock - di.quantity
        WHERE di.id_delivery = NEW.id;
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk otomatis mengurangi inventory saat delivery status berubah menjadi DIKIRIM.

**Kenapa (ACID):**

-   **Atomicity:** Update status delivery dan pengurangan inventory terjadi dalam satu transaction.
-   **Consistency:** Inventory selalu ter-update otomatis saat delivery di-confirm.
-   **Isolation:** Trigger execution adalah bagian dari transaction yang sama.
-   **Durability:** Perubahan stok dipastikan tersimpan bersamaan dengan status update.

---

## 2. cct_restore_inventory_on_return

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_restore_inventory_on_return
AFTER INSERT ON return_items
FOR EACH ROW
BEGIN
    -- Increase inventory when return item is created
    UPDATE inventory
    SET stock = stock + NEW.quantity
    WHERE id_item = NEW.id_item;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk otomatis menambah inventory saat return item dicatat.

**Kenapa (ACID):**

-   **Atomicity:** Insert return_item dan update inventory dalam satu transaction.
-   **Consistency:** Stok otomatis ter-restore saat barang dikembalikan.
-   **Isolation:** Concurrent returns tidak menyebabkan incorrect stock count.
-   **Durability:** Stock restoration permanent setelah return recorded.

---

## 3. cct_prevent_negative_stock

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_prevent_negative_stock
BEFORE UPDATE ON inventory
FOR EACH ROW
BEGIN
    IF NEW.stock < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock cannot be negative';
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk mencegah stok menjadi negatif.

**Kenapa (ACID):**

-   **Consistency:** Memastikan business rule (stok >= 0) selalu terpenuhi.
-   **Atomicity:** Validasi terjadi sebelum commit, entire transaction rollback jika validation fails.
-   **Isolation:** Check dilakukan pada row-level dalam transaction context.
-   **Durability:** Invalid state tidak pernah ter-commit ke database.

---

## 4. cct_invalidate_report_on_delivery_change

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_invalidate_report_on_delivery_change
AFTER UPDATE ON delivery_items
FOR EACH ROW
BEGIN
    -- Only invalidate if quantity changed
    IF OLD.quantity != NEW.quantity THEN
        UPDATE daily_outlet_reports dor
        INNER JOIN deliveries d ON d.id_outlet = dor.id_outlet
        INNER JOIN daily_outlet_report_items dori ON dor.id = dori.id_outlet_report
        SET dor.is_validated = FALSE
        WHERE dori.id_item = NEW.id_item
          AND DATE(dor.report_date) = DATE(d.assigned_at)
          AND d.id = NEW.id_delivery;
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk invalidate laporan harian saat delivery items berubah setelah laporan dibuat.

**Kenapa (ACID):**

-   **Consistency:** Memastikan laporan selalu reflect current state atau ditandai sebagai invalid.
-   **Atomicity:** Update delivery_items dan invalidation report dalam satu transaction.
-   **Isolation:** Concurrent changes tidak menyebabkan missed invalidation.
-   **Durability:** Validation status tersimpan permanent untuk audit trail.

---

## 5. cct_validate_delivery_items_stock

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_validate_delivery_items_stock
BEFORE INSERT ON delivery_items
FOR EACH ROW
BEGIN
    DECLARE v_current_stock INT;

    -- Get current stock
    SELECT COALESCE(stock, 0) INTO v_current_stock
    FROM inventory
    WHERE id_item = NEW.id_item;

    -- Check if sufficient stock
    IF v_current_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Insufficient stock for delivery item';
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk validasi stok sebelum menambah item ke delivery.

**Kenapa (ACID):**

-   **Consistency:** Mencegah delivery item creation jika stok tidak cukup.
-   **Atomicity:** Validation happens before insert commits.
-   **Isolation:** Check stock dalam transaction context untuk avoid race conditions.
-   **Durability:** Invalid state tidak pernah tersimpan.

---

## 6. cct_update_delivery_timestamp

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_update_delivery_timestamp
BEFORE UPDATE ON deliveries
FOR EACH ROW
BEGIN
    -- Auto-set delivered_at when status changes to DIKIRIM
    IF OLD.status != 'DIKIRIM' AND NEW.status = 'DIKIRIM' AND NEW.delivered_at IS NULL THEN
        SET NEW.delivered_at = NOW();
    END IF;

    -- Clear delivered_at if status changes back
    IF NEW.status NOT IN ('DIKIRIM', 'SELESAI') THEN
        SET NEW.delivered_at = NULL;
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk otomatis set/clear `delivered_at` timestamp based on status.

**Kenapa (ACID):**

-   **Consistency:** Timestamp selalu konsisten dengan status delivery.
-   **Atomicity:** Status dan timestamp update dalam satu operation.
-   **Isolation:** Concurrent updates tidak menyebabkan timestamp mismatch.
-   **Durability:** Accurate timestamp tersimpan untuk reporting.

---

## 7. cct_prevent_completed_delivery_modification

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_prevent_completed_delivery_modification
BEFORE UPDATE ON deliveries
FOR EACH ROW
BEGIN
    IF OLD.status = 'SELESAI' AND NEW.status != OLD.status THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot modify completed delivery';
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk mencegah modifikasi delivery yang sudah selesai.

**Kenapa (ACID):**

-   **Consistency:** Business rule enforcement - completed deliveries immutable.
-   **Atomicity:** Validation sebelum update commits.
-   **Isolation:** Concurrent attempts to modify blocked consistently.
-   **Durability:** Historical data integrity maintained.

---

## 8. cct_auto_create_inventory_entry

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_auto_create_inventory_entry
AFTER INSERT ON items
FOR EACH ROW
BEGIN
    -- Auto-create inventory entry with 0 stock for new items
    INSERT INTO inventory (id_item, stock)
    VALUES (NEW.id, 0);
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk otomatis membuat inventory entry saat item baru dibuat.

**Kenapa (ACID):**

-   **Consistency:** Setiap item selalu punya inventory entry (avoid NULL checks).
-   **Atomicity:** Item creation dan inventory initialization atomic.
-   **Isolation:** Concurrent item creation tidak menyebabkan missing inventory.
-   **Durability:** Inventory entry guaranteed untuk setiap item.

---

## 9. cct_prevent_item_delete_with_stock

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_prevent_item_delete_with_stock
BEFORE DELETE ON items
FOR EACH ROW
BEGIN
    DECLARE v_stock INT;

    SELECT COALESCE(stock, 0) INTO v_stock
    FROM inventory
    WHERE id_item = OLD.id;

    IF v_stock > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete item with existing stock';
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk mencegah penghapusan item yang masih memiliki stok.

**Kenapa (ACID):**

-   **Consistency:** Business rule - items dengan stok tidak boleh dihapus.
-   **Atomicity:** Validation before delete commits.
-   **Isolation:** Concurrent stock changes checked dalam transaction.
-   **Durability:** Data integrity maintained (no orphaned stock).

---

## 10. cct_validate_return_quantity

### Query

```sql
DELIMITER $$

CREATE TRIGGER cct_validate_return_quantity
BEFORE INSERT ON return_items
FOR EACH ROW
BEGIN
    IF NEW.quantity <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Return quantity must be positive';
    END IF;
END$$

DELIMITER ;
```

### Penjelasan

**Apa:** Trigger untuk validasi quantity return harus positif.

**Kenapa (ACID):**

-   **Consistency:** Business rule validation - return quantity harus > 0.
-   **Atomicity:** Validation sebelum insert commits.
-   **Isolation:** Validation dalam transaction context.
-   **Durability:** Invalid data tidak pernah tersimpan.
