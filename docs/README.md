# Database Objects Documentation - CeritaCireng

Dokumentasi lengkap untuk database objects (stored procedures, functions, views, indexes, dan triggers) yang dirancang untuk mencapai ACID properties dalam sistem manajemen inventory dan pengiriman Cireng.

## 📋 Daftar Isi

1. [Stored Procedures](./stored_procedures.md) - Operasi kompleks yang melibatkan multiple statements
2. [Stored Functions](./stored_functions.md) - Kalkulasi dan query reusable
3. [Views](./views.md) - Query kompleks yang disederhanakan
4. [Indexes](./indexes.md) - Optimasi performance dan constraint
5. [Triggers](./triggers.md) - Otomasi dan validasi data

## 🎯 Tentang ACID Properties

**ACID** adalah akronim untuk:

-   **Atomicity:** Operasi database harus all-or-nothing (sukses semua atau gagal semua)
-   **Consistency:** Database selalu dalam state yang valid dan konsisten
-   **Isolation:** Transaksi concurrent tidak saling mengganggu
-   **Durability:** Data yang sudah committed permanen tersimpan

## 📝 Konvensi Penamaan

Semua database objects menggunakan prefix `cc-` (CeritaCireng) diikuti dengan tipe object:

| Object Type      | Prefix | Contoh                             |
| ---------------- | ------ | ---------------------------------- |
| Stored Procedure | `ccp_` | `ccp_process_delivery`             |
| Stored Function  | `ccf_` | `ccf_get_available_stock`          |
| View             | `ccv_` | `ccv_inventory_summary`            |
| Index            | `cci_` | `cci_inventory_item`               |
| Trigger          | `cct_` | `cct_update_inventory_on_delivery` |

## 🏗️ Arsitektur Database

Sistem CeritaCireng mengelola:

-   **Items:** Bahan mentah, penunjang, dan kemasan
-   **Inventory:** Stok pusat
-   **Deliveries:** Pengiriman ke outlets
-   **Returns:** Pengembalian dari outlets
-   **Daily Reports:** Laporan harian per outlet
-   **Users & Roles:** Staff, kurir, inventaris, admin

## 📊 Ringkasan Use Cases

### Stored Procedures (6 procedures)

Digunakan untuk operasi kompleks yang melibatkan multiple tables dan memerlukan transaction control:

-   Processing deliveries dengan update inventory
-   Processing returns dengan restore stock
-   Creating daily reports dengan aggregation
-   Canceling deliveries dengan rollback logic
-   Bulk restocking operations
-   Report validation dan locking

### Stored Functions (8 functions)

Digunakan untuk kalkulasi dan query yang reusable:

-   Get stock availability
-   Calculate totals (items, costs)
-   Validate stock sufficiency
-   Get outlet daily delivered/returned
-   Calculate item usage dalam periode
-   Validate delivery stock

### Views (7 views)

Menyederhanakan query kompleks dan reporting:

-   Inventory summary dengan stock values
-   Active deliveries tracking
-   Daily report details dengan calculations
-   Outlet item levels monitoring
-   Item movement summary (30 days)
-   Delivery performance metrics
-   Low stock alerts

### Indexes (15 indexes)

Meningkatkan performance dan enforce constraints:

-   Single column indexes untuk frequent lookups
-   Composite indexes untuk complex queries
-   Unique indexes untuk business constraints
-   Foreign key optimization
-   Date-range query optimization

### Triggers (15 triggers)

Otomasi dan validasi data integrity:

-   Auto-update inventory on delivery/return
-   Prevent negative stock
-   Invalidate reports on source data changes
-   Audit trails untuk changes
-   Validate business rules
-   Auto-set timestamps
-   Cascade operations

## 🚀 Implementasi

**PENTING:** Dokumentasi ini berisi _design specifications_ dan **BELUM DIIMPLEMENTASIKAN** di database.

Untuk implementasi:

1. Review setiap file documentation
2. Adjust sesuai kebutuhan spesifik
3. Test di development environment
4. Run migration untuk production

### Urutan Implementasi yang Disarankan

```
1. Indexes      → Performance foundation
2. Functions    → Reusable calculations
3. Triggers     → Data integrity automation
4. Procedures   → Complex operations
5. Views        → Simplified querying
```

## ⚠️ Catatan Penting

1. **Testing Required:** Semua objects harus di-test secara menyeluruh sebelum production
2. **Performance Impact:** Beberapa triggers dapat mempengaruhi write performance
3. **Transaction Isolation:** Default isolation level MySQL/MariaDB adalah REPEATABLE READ
4. **Backup Strategy:** Pastikan backup strategy mencakup stored routines
5. **Version Control:** Track changes ke database objects di version control

## 🔧 Maintenance

-   **Regular Review:** Review performance dari views dan indexes
-   **Audit Logs:** Monitor audit trail tables (delivery_status_audit, inventory_change_log)
-   **Index Statistics:** Analyze dan optimize indexes secara berkala
-   **Dead Code:** Remove unused procedures/functions/views

## 📚 Referensi

-   [MySQL Stored Procedures Documentation](https://dev.mysql.com/doc/refman/8.0/en/stored-programs.html)
-   [MySQL Triggers Documentation](https://dev.mysql.com/doc/refman/8.0/en/triggers.html)
-   [ACID Properties Explained](https://en.wikipedia.org/wiki/ACID)
-   [Database Indexing Best Practices](https://dev.mysql.com/doc/refman/8.0/en/optimization-indexes.html)

## 📄 Lisensi

Dokumentasi ini adalah bagian dari project CeritaCireng.

---

**Last Updated:** 2025-12-09  
**Author:** Database Design Team  
**Version:** 1.0
