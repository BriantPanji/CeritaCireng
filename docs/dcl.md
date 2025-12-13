# Database Control Language (DCL) - CeritaCireng Database

Dokumentasi DCL untuk pembuatan user database berdasarkan role dengan privilege yang sesuai.

---

## Daftar Tabel Database

| No  | Nama Tabel                       | Deskripsi                        |
| --- | -------------------------------- | -------------------------------- |
| 1   | `roles`                          | Master data role user            |
| 2   | `outlets`                        | Master data outlet/cabang        |
| 3   | `users`                          | Data user sistem                 |
| 4   | `user_details`                   | Detail tambahan user (view)      |
| 5   | `sessions`                       | Session user aktif               |
| 6   | `items`                          | Master data barang               |
| 7   | `products`                       | Master data produk               |
| 8   | `product_items`                  | Relasi produk dan barang         |
| 9   | `outlet_item_settings`           | Pengaturan item per outlet       |
| 10  | `inventory`                      | Stok barang di gudang            |
| 11  | `deliveries`                     | Data pengantaran                 |
| 12  | `delivery_items`                 | Item yang diantar                |
| 13  | `delivery_confirmations`         | Konfirmasi pengantaran           |
| 14  | `delivery_mistakes`              | Kesalahan pengantaran            |
| 15  | `delivery_mistake_items`         | Item kesalahan pengantaran       |
| 16  | `delivery_mistake_confirmations` | Konfirmasi kesalahan pengantaran |
| 17  | `returns`                        | Data pengembalian barang         |
| 18  | `return_items`                   | Item yang dikembalikan           |
| 19  | `return_evidences`               | Bukti pengembalian               |
| 20  | `return_confirmations`           | Konfirmasi pengembalian          |
| 21  | `return_errors`                  | Error pada pengembalian          |
| 22  | `other_expenses`                 | Pengeluaran lainnya              |
| 23  | `attendances`                    | Data absensi                     |
| 24  | `days`                           | Master hari                      |
| 25  | `outlet_closed_days`             | Hari tutup outlet                |
| 26  | `daily_outlet_reports`           | Laporan harian outlet            |
| 27  | `daily_outlet_report_items`      | Item laporan harian outlet       |
| 28  | `cache`                          | Cache aplikasi (Laravel)         |
| 29  | `cache_locks`                    | Lock cache (Laravel)             |
| 30  | `jobs`                           | Queue jobs (Laravel)             |
| 31  | `job_batches`                    | Batch jobs (Laravel)             |
| 32  | `failed_jobs`                    | Failed jobs (Laravel)            |

---

## Daftar Role

| ID  | Nama       | Display Name  | DB Connection    |
| --- | ---------- | ------------- | ---------------- |
| 1   | dev        | Developer     | mysql_dev        |
| 2   | admin      | Administrator | mysql_admin      |
| 3   | inventaris | Gudang        | mysql_inventaris |
| 4   | kurir      | Pengantar     | mysql_kurir      |
| 5   | staff      | Staff         | mysql_staff      |
| 6   | guest      | Tamu          | mysql_guest      |

---

## 1. DEV User (Developer / Owner)

### Deskripsi

User dengan akses penuh ke seluruh database. Digunakan untuk development dan maintenance.

### Query

```sql
-- Hapus user jika sudah ada
DROP USER IF EXISTS 'dev_user'@'localhost';

-- Buat user baru
CREATE USER 'dev_user'@'localhost' IDENTIFIED BY 'DevSecret@2024!';

-- Berikan semua privilege dengan GRANT OPTION
GRANT ALL PRIVILEGES ON cerita_cireng.* TO 'dev_user'@'localhost' WITH GRANT OPTION;

-- Terapkan perubahan
FLUSH PRIVILEGES;
```

### Privilege

-   **ALL PRIVILEGES**: Akses penuh ke semua operasi database
-   **WITH GRANT OPTION**: Dapat memberikan privilege ke user lain

---

## 2. ADMIN User (Administrator)

### Deskripsi

User untuk administrator sistem dengan akses hampir penuh, namun tidak dapat memberikan privilege ke user lain.

### Query

```sql
-- Hapus user jika sudah ada
DROP USER IF EXISTS 'admin_user'@'localhost';

-- Buat user baru
CREATE USER 'admin_user'@'localhost' IDENTIFIED BY 'AdminSecret@2024!';

-- Privilege penuh untuk data management
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.roles TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.outlets TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.users TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.user_details TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.items TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.products TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.product_items TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.outlet_item_settings TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.inventory TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.deliveries TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_items TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_confirmations TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistakes TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistake_items TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistake_confirmations TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.returns TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.return_items TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.return_evidences TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.return_confirmations TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.return_errors TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.other_expenses TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.attendances TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.days TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.outlet_closed_days TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.daily_outlet_reports TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.daily_outlet_report_items TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.jobs TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.job_batches TO 'admin_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.failed_jobs TO 'admin_user'@'localhost';

-- Terapkan perubahan
FLUSH PRIVILEGES;
```

### Privilege

| Tabel       | SELECT | INSERT | UPDATE | DELETE |
| ----------- | :----: | :----: | :----: | :----: |
| Semua tabel |   ✓    |   ✓    |   ✓    |   ✓    |

---

## 3. INVENTARIS User (Gudang)

### Deskripsi

User untuk staff gudang. Fokus pada pengelolaan inventory, barang, dan pengantaran.

### Query

```sql
-- Hapus user jika sudah ada
DROP USER IF EXISTS 'inventaris_user'@'localhost';

-- Buat user baru
CREATE USER 'inventaris_user'@'localhost' IDENTIFIED BY 'GudangSecret@2024!';

-- Master data (READ ONLY)
GRANT SELECT ON cerita_cireng.roles TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlets TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.users TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.days TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlet_closed_days TO 'inventaris_user'@'localhost';

-- Barang dan Produk (FULL ACCESS)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.items TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.products TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.product_items TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.outlet_item_settings TO 'inventaris_user'@'localhost';

-- Inventory (FULL ACCESS)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.inventory TO 'inventaris_user'@'localhost';

-- Pengantaran (FULL ACCESS)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.deliveries TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_items TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_confirmations TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistakes TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistake_items TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistake_confirmations TO 'inventaris_user'@'localhost';

-- Return (READ ONLY - untuk rekonsiliasi stok)
GRANT SELECT ON cerita_cireng.returns TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_items TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_evidences TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_confirmations TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_errors TO 'inventaris_user'@'localhost';

-- Laporan (READ ONLY)
GRANT SELECT ON cerita_cireng.daily_outlet_reports TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.daily_outlet_report_items TO 'inventaris_user'@'localhost';

-- Absensi (untuk absen sendiri)
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.attendances TO 'inventaris_user'@'localhost';

-- Session (untuk login)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'inventaris_user'@'localhost';

-- Laravel Cache/Jobs
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'inventaris_user'@'localhost';

-- Terapkan perubahan
FLUSH PRIVILEGES;
```

### Privilege

| Tabel                          | SELECT | INSERT | UPDATE | DELETE |
| ------------------------------ | :----: | :----: | :----: | :----: |
| roles, outlets, users, days    |   ✓    |   -    |   -    |   -    |
| items, products, product_items |   ✓    |   ✓    |   ✓    |   ✓    |
| inventory                      |   ✓    |   ✓    |   ✓    |   ✓    |
| deliveries, delivery\_\*       |   ✓    |   ✓    |   ✓    |   ✓    |
| returns, return\_\*            |   ✓    |   -    |   -    |   -    |
| daily_outlet_reports           |   ✓    |   -    |   -    |   -    |
| attendances                    |   ✓    |   ✓    |   ✓    |   -    |

---

## 4. KURIR User (Pengantar)

### Deskripsi

User untuk kurir/pengantar. Akses terbatas pada data pengantaran yang ditugaskan.

### Query

```sql
-- Hapus user jika sudah ada
DROP USER IF EXISTS 'kurir_user'@'localhost';

-- Buat user baru
CREATE USER 'kurir_user'@'localhost' IDENTIFIED BY 'KurirSecret@2024!';

-- Master data (READ ONLY)
GRANT SELECT ON cerita_cireng.roles TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlets TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.users TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.items TO 'kurir_user'@'localhost';

-- Pengantaran (SELECT dan UPDATE status)
GRANT SELECT ON cerita_cireng.deliveries TO 'kurir_user'@'localhost';
GRANT UPDATE (status, delivered_at) ON cerita_cireng.deliveries TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.delivery_items TO 'kurir_user'@'localhost';

-- Konfirmasi pengantaran (INSERT untuk upload bukti)
GRANT SELECT, INSERT ON cerita_cireng.delivery_confirmations TO 'kurir_user'@'localhost';

-- Kesalahan pengantaran (INSERT jika ada masalah)
GRANT SELECT, INSERT ON cerita_cireng.delivery_mistakes TO 'kurir_user'@'localhost';
GRANT SELECT, INSERT ON cerita_cireng.delivery_mistake_items TO 'kurir_user'@'localhost';

-- Return (INSERT untuk mengambil return dari outlet)
GRANT SELECT ON cerita_cireng.returns TO 'kurir_user'@'localhost';
GRANT UPDATE (id_deliverer) ON cerita_cireng.returns TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_items TO 'kurir_user'@'localhost';

-- Absensi (untuk absen sendiri)
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.attendances TO 'kurir_user'@'localhost';

-- Session (untuk login)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'kurir_user'@'localhost';

-- Laravel Cache
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'kurir_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'kurir_user'@'localhost';

-- Terapkan perubahan
FLUSH PRIVILEGES;
```

### Privilege

| Tabel                        | SELECT | INSERT | UPDATE | DELETE |
| ---------------------------- | :----: | :----: | :----: | :----: |
| roles, outlets, users, items |   ✓    |   -    |   -    |   -    |
| deliveries                   |   ✓    |   -    |  ✓\*   |   -    |
| delivery_items               |   ✓    |   -    |   -    |   -    |
| delivery_confirmations       |   ✓    |   ✓    |   -    |   -    |
| delivery_mistakes            |   ✓    |   ✓    |   -    |   -    |
| returns                      |   ✓    |   -    |  ✓\*   |   -    |
| attendances                  |   ✓    |   ✓    |   ✓    |   -    |

> **Note**: ✓\* = UPDATE hanya untuk kolom tertentu

---

## 5. STAFF User (Staff Outlet)

### Deskripsi

User untuk staff outlet. Fokus pada laporan harian, return barang, dan data outlet terkait.

### Query

```sql
-- Hapus user jika sudah ada
DROP USER IF EXISTS 'staff_user'@'localhost';

-- Buat user baru
CREATE USER 'staff_user'@'localhost' IDENTIFIED BY 'StaffSecret@2024!';

-- Master data (READ ONLY)
GRANT SELECT ON cerita_cireng.roles TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlets TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.users TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.items TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.products TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.product_items TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlet_item_settings TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.days TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlet_closed_days TO 'staff_user'@'localhost';

-- Pengantaran (READ ONLY - untuk melihat barang yang diterima)
GRANT SELECT ON cerita_cireng.deliveries TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.delivery_items TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.delivery_confirmations TO 'staff_user'@'localhost';

-- Return (FULL ACCESS - untuk mencatat pengembalian)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.returns TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.return_items TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.return_evidences TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_confirmations TO 'staff_user'@'localhost';
GRANT SELECT, INSERT ON cerita_cireng.return_errors TO 'staff_user'@'localhost';

-- Pengeluaran lain (untuk mencatat gas, galon, dll)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.other_expenses TO 'staff_user'@'localhost';

-- Laporan harian (untuk membuat dan melihat laporan outlet sendiri)
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.daily_outlet_reports TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.daily_outlet_report_items TO 'staff_user'@'localhost';

-- Absensi (untuk absen sendiri)
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.attendances TO 'staff_user'@'localhost';

-- Session (untuk login)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'staff_user'@'localhost';

-- Laravel Cache
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'staff_user'@'localhost';

-- Terapkan perubahan
FLUSH PRIVILEGES;
```

### Privilege

| Tabel                      | SELECT | INSERT | UPDATE | DELETE |
| -------------------------- | :----: | :----: | :----: | :----: |
| roles, outlets, users      |   ✓    |   -    |   -    |   -    |
| items, products            |   ✓    |   -    |   -    |   -    |
| deliveries, delivery_items |   ✓    |   -    |   -    |   -    |
| returns, return_items      |   ✓    |   ✓    |   ✓    |   ✓    |
| return_evidences           |   ✓    |   ✓    |   ✓    |   ✓    |
| other_expenses             |   ✓    |   ✓    |   ✓    |   ✓    |
| daily_outlet_reports       |   ✓    |   ✓    |   ✓    |   -    |
| daily_outlet_report_items  |   ✓    |   ✓    |   ✓    |   -    |
| attendances                |   ✓    |   ✓    |   ✓    |   -    |

---

## 6. GUEST User (Tamu)

### Deskripsi

User dengan akses minimal, hanya dapat melihat data publik. Digunakan untuk user yang belum login atau akses terbatas.

### Query

```sql
-- Hapus user jika sudah ada
DROP USER IF EXISTS 'guest_user'@'localhost';

-- Buat user baru
CREATE USER 'guest_user'@'localhost' IDENTIFIED BY 'GuestSecret@2024!';

-- Hanya bisa melihat data publik minimal
GRANT SELECT ON cerita_cireng.roles TO 'guest_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlets TO 'guest_user'@'localhost';

-- Session (untuk anonymous session)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'guest_user'@'localhost';

-- Laravel Cache
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'guest_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'guest_user'@'localhost';

-- Terapkan perubahan
FLUSH PRIVILEGES;
```

### Privilege

| Tabel          | SELECT | INSERT | UPDATE | DELETE |
| -------------- | :----: | :----: | :----: | :----: |
| roles, outlets |   ✓    |   -    |   -    |   -    |
| sessions       |   ✓    |   ✓    |   ✓    |   ✓    |

---

## Ringkasan Privilege per Role

| Fitur                     | DEV | ADMIN | INVENTARIS | KURIR | STAFF | GUEST |
| ------------------------- | :-: | :---: | :--------: | :---: | :---: | :---: |
| Manajemen User            |  ✓  |   ✓   |     -      |   -   |   -   |   -   |
| Manajemen Outlet          |  ✓  |   ✓   |     -      |   -   |   -   |   -   |
| Manajemen Barang          |  ✓  |   ✓   |     ✓      |   -   |   -   |   -   |
| Manajemen Inventory       |  ✓  |   ✓   |     ✓      |   -   |   -   |   -   |
| Buat Pengantaran          |  ✓  |   ✓   |     ✓      |   -   |   -   |   -   |
| Update Status Pengantaran |  ✓  |   ✓   |     ✓      |   ✓   |   -   |   -   |
| Lihat Pengantaran         |  ✓  |   ✓   |     ✓      |   ✓   |   ✓   |   -   |
| Kelola Return             |  ✓  |   ✓   |     -      |   -   |   ✓   |   -   |
| Buat Laporan Harian       |  ✓  |   ✓   |     -      |   -   |   ✓   |   -   |
| Lihat Laporan Harian      |  ✓  |   ✓   |     ✓      |   -   |   ✓   |   -   |
| Absensi                   |  ✓  |   ✓   |     ✓      |   ✓   |   ✓   |   -   |
| Log Aktivitas             |  ✓  |   ✓   |     -      |   -   |   -   |   -   |

---

## Script Lengkap (All-in-One)

```sql
-- ============================================
-- DCL Script - CeritaCireng Database
-- ============================================

SET @database = 'cerita_cireng';

-- 1. DEV User
DROP USER IF EXISTS 'dev_user'@'localhost';
CREATE USER 'dev_user'@'localhost' IDENTIFIED BY 'DevSecret@2024!';
GRANT ALL PRIVILEGES ON cerita_cireng.* TO 'dev_user'@'localhost' WITH GRANT OPTION;

-- 2. ADMIN User
DROP USER IF EXISTS 'admin_user'@'localhost';
CREATE USER 'admin_user'@'localhost' IDENTIFIED BY 'AdminSecret@2024!';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.* TO 'admin_user'@'localhost';

-- 3. INVENTARIS User
DROP USER IF EXISTS 'inventaris_user'@'localhost';
CREATE USER 'inventaris_user'@'localhost' IDENTIFIED BY 'GudangSecret@2024!';
-- Master data (READ)
GRANT SELECT ON cerita_cireng.roles TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlets TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.users TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.days TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlet_closed_days TO 'inventaris_user'@'localhost';
-- Items & Inventory (FULL)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.items TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.products TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.product_items TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.outlet_item_settings TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.inventory TO 'inventaris_user'@'localhost';
-- Deliveries (FULL)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.deliveries TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_items TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_confirmations TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistakes TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistake_items TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.delivery_mistake_confirmations TO 'inventaris_user'@'localhost';
-- Returns (READ)
GRANT SELECT ON cerita_cireng.returns TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_items TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_evidences TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_confirmations TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_errors TO 'inventaris_user'@'localhost';
-- Reports (READ)
GRANT SELECT ON cerita_cireng.daily_outlet_reports TO 'inventaris_user'@'localhost';
GRANT SELECT ON cerita_cireng.daily_outlet_report_items TO 'inventaris_user'@'localhost';
-- Attendance
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.attendances TO 'inventaris_user'@'localhost';
-- Session & Cache
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'inventaris_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'inventaris_user'@'localhost';

-- 4. KURIR User
DROP USER IF EXISTS 'kurir_user'@'localhost';
CREATE USER 'kurir_user'@'localhost' IDENTIFIED BY 'KurirSecret@2024!';
-- Master data (READ)
GRANT SELECT ON cerita_cireng.roles TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlets TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.users TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.items TO 'kurir_user'@'localhost';
-- Deliveries (SELECT + limited UPDATE)
GRANT SELECT ON cerita_cireng.deliveries TO 'kurir_user'@'localhost';
GRANT UPDATE (status, delivered_at) ON cerita_cireng.deliveries TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.delivery_items TO 'kurir_user'@'localhost';
GRANT SELECT, INSERT ON cerita_cireng.delivery_confirmations TO 'kurir_user'@'localhost';
GRANT SELECT, INSERT ON cerita_cireng.delivery_mistakes TO 'kurir_user'@'localhost';
GRANT SELECT, INSERT ON cerita_cireng.delivery_mistake_items TO 'kurir_user'@'localhost';
-- Returns (limited access)
GRANT SELECT ON cerita_cireng.returns TO 'kurir_user'@'localhost';
GRANT UPDATE (id_deliverer) ON cerita_cireng.returns TO 'kurir_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_items TO 'kurir_user'@'localhost';
-- Attendance
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.attendances TO 'kurir_user'@'localhost';
-- Session & Cache
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'kurir_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'kurir_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'kurir_user'@'localhost';

-- 5. STAFF User
DROP USER IF EXISTS 'staff_user'@'localhost';
CREATE USER 'staff_user'@'localhost' IDENTIFIED BY 'StaffSecret@2024!';
-- Master data (READ)
GRANT SELECT ON cerita_cireng.roles TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlets TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.users TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.items TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.products TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.product_items TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlet_item_settings TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.days TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlet_closed_days TO 'staff_user'@'localhost';
-- Deliveries (READ)
GRANT SELECT ON cerita_cireng.deliveries TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.delivery_items TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.delivery_confirmations TO 'staff_user'@'localhost';
-- Returns (FULL)
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.returns TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.return_items TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.return_evidences TO 'staff_user'@'localhost';
GRANT SELECT ON cerita_cireng.return_confirmations TO 'staff_user'@'localhost';
GRANT SELECT, INSERT ON cerita_cireng.return_errors TO 'staff_user'@'localhost';
-- Expenses
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.other_expenses TO 'staff_user'@'localhost';
-- Daily Reports
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.daily_outlet_reports TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.daily_outlet_report_items TO 'staff_user'@'localhost';
-- Attendance
GRANT SELECT, INSERT, UPDATE ON cerita_cireng.attendances TO 'staff_user'@'localhost';
-- Session & Cache
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'staff_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'staff_user'@'localhost';

-- 6. GUEST User
DROP USER IF EXISTS 'guest_user'@'localhost';
CREATE USER 'guest_user'@'localhost' IDENTIFIED BY 'GuestSecret@2024!';
GRANT SELECT ON cerita_cireng.roles TO 'guest_user'@'localhost';
GRANT SELECT ON cerita_cireng.outlets TO 'guest_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.sessions TO 'guest_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache TO 'guest_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON cerita_cireng.cache_locks TO 'guest_user'@'localhost';

-- Apply all changes
FLUSH PRIVILEGES;

-- Verify users
SELECT User, Host FROM mysql.user WHERE User LIKE '%_user';
```

---

## Catatan Keamanan

> [!WARNING]
> Password di atas adalah contoh. Pada production, gunakan password yang kuat dan simpan dengan aman.

> [!IMPORTANT]
> Untuk koneksi remote, ganti `'localhost'` dengan IP address atau `'%'` untuk semua host.

### Best Practices

1. **Password Policy**: Gunakan minimal 12 karakter dengan kombinasi huruf besar, kecil, angka, dan simbol
2. **Principle of Least Privilege**: Berikan hanya akses yang diperlukan
3. **Regular Audit**: Periksa privilege secara berkala
4. **Separate Connections**: Gunakan koneksi berbeda untuk setiap role di konfigurasi Laravel (`config/database.php`)

---

## Verifikasi Privilege

Gunakan query berikut untuk memeriksa privilege user:

```sql
-- Lihat semua privilege user tertentu
SHOW GRANTS FOR 'staff_user'@'localhost';

-- Lihat semua user database
SELECT User, Host FROM mysql.user WHERE User LIKE '%_user';

-- Lihat privilege pada tabel tertentu
SELECT * FROM information_schema.TABLE_PRIVILEGES
WHERE GRANTEE LIKE "'staff_user'@'localhost'";
```
