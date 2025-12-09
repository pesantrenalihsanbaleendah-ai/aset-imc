# 🗄️ ASET IMC - Database Documentation

## Database Information

- **Database Name:** `aset_imc`
- **Database User:** `root` (default Laragon)
- **Database Password:** (empty)
- **Database Host:** `127.0.0.1` atau `localhost`
- **Database Port:** `3306` (default MySQL)

---

## 📊 Table Specifications

### 1. users
**Tujuan:** Menyimpan data user/pengguna sistem
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    
    -- Extended fields
    role_id BIGINT UNSIGNED,
    employee_id VARCHAR(100),
    phone VARCHAR(20),
    department VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

**Indeks:** `email` (UNIQUE), `role_id`
**Relasi:** 
- Belongs to: `roles` (via role_id)
- Has many: `assets`, `loans`, `maintenances`, `transfers`

---

### 2. roles
**Tujuan:** Menyimpan definisi role/peran pengguna
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    level INT DEFAULT 0,  -- Priority level untuk hierarchy
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Default Data:**
```
id | name         | description                    | level
1  | super_admin  | Super Administrator            | 1000
2  | admin_aset   | Asset Administrator            | 900
3  | approver     | Approver/Manager               | 800
4  | staff        | Staff/Regular User             | 100
5  | auditor      | Auditor                        | 50
```

**Indeks:** `name` (UNIQUE)
**Relasi:**
- Has many: `users`, `permissions` (via role_permissions)

---

### 3. permissions
**Tujuan:** Menyimpan definisi permission/izin sistem
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    module VARCHAR(100),  -- Modul terkait (asset, loan, etc)
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Default Data (20 Permissions):**
```
Asset Module:
- asset.view      | View assets
- asset.create    | Create new assets
- asset.edit      | Edit asset data
- asset.delete    | Delete assets
- asset.import    | Bulk import assets
- asset.export    | Export asset list

Loan Module:
- loan.view       | View loan records
- loan.request    | Request loan
- loan.approve    | Approve loans
- loan.reject     | Reject loans

Maintenance Module:
- maintenance.view      | View maintenance records
- maintenance.create    | Create maintenance request
- maintenance.approve   | Approve maintenance

Transfer Module:
- transfer.view         | View transfers
- transfer.request      | Request transfer
- transfer.approve      | Approve transfer

Disposal Module:
- disposal.view         | View disposals
- disposal.request      | Request disposal
- disposal.approve      | Approve disposal

Report & Admin:
- report.view           | View reports
- report.export         | Export reports
- audit.view            | View audit logs
```

**Indeks:** `name` (UNIQUE), `module`
**Relasi:**
- Has many: `roles` (via role_permissions)

---

### 4. role_permissions
**Tujuan:** Pivot table untuk relasi many-to-many Role dan Permission
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);
```

**Indeks:** `(role_id, permission_id)` UNIQUE

**Contoh Data:**
```
role_id | permission_id
1       | 1-20          (Super Admin has all permissions)
2       | 1-5           (Admin Aset has asset permissions)
3       | 8,9,12        (Approver has approval permissions)
4       | 1,7,11        (Staff has view & request permissions)
5       | 1,7,11,20     (Auditor has view & audit permissions)
```

---

### 5. asset_categories
**Tujuan:** Menyimpan kategori/klasifikasi aset
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE asset_categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    
    -- Depreciation settings
    depreciation_method ENUM('straight_line', 'double_declining') DEFAULT 'straight_line',
    useful_life_years INT DEFAULT 5,  -- Tahun masa manfaat
    residual_value_percent INT DEFAULT 20,  -- % nilai sisa
    
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Default Data:**
```
id | name           | description        | useful_life_years | residual_value_percent
1  | IT Equipment   | Laptop, komputer   | 4                 | 20
2  | Furniture      | Meja, kursi        | 8                 | 10
3  | Kendaraan      | Motor, mobil       | 10                | 15
4  | Mesin          | Mesin produksi     | 10                | 20
5  | Bangunan       | Gedung, ruang      | 20                | 30
```

**Indeks:** `name`, `is_active`
**Relasi:**
- Has many: `assets`

---

### 6. locations
**Tujuan:** Menyimpan lokasi/tempat aset dengan hierarki
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE locations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    parent_id BIGINT UNSIGNED NULLABLE,  -- Parent location untuk hierarki
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50),
    level INT DEFAULT 0,  -- Kedalaman hierarki
    description TEXT,
    
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES locations(id) ON DELETE CASCADE
);
```

**Default Data:**
```
Hierarki:
- Building A (level 0)
  - Floor 1 (level 1)
  - Floor 2 (level 1)
  - Floor 3 (level 1)
- Building B (level 0)
  - Floor 1 (level 1)
  - Floor 2 (level 1)
```

**Indeks:** `parent_id`, `level`, `is_active`
**Relasi:**
- Has many: `children`, `assets`
- Belongs to: `parent` (self-referencing)

**Attribute:**
```php
// Model automatically provides:
$location->full_name  // "Building A > Floor 2"
$location->children   // All child locations
$location->parent     // Parent location
```

---

### 7. assets
**Tujuan:** Menyimpan data master aset dengan informasi lengkap
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE assets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- Identitas
    name VARCHAR(255) NOT NULL,
    code VARCHAR(100) UNIQUE NOT NULL,  -- Kode aset unik
    description TEXT,
    
    -- Klasifikasi
    category_id BIGINT UNSIGNED NOT NULL,
    location_id BIGINT UNSIGNED NOT NULL,
    
    -- Nilai
    purchase_price DECIMAL(15,2) NOT NULL,  -- Harga perolehan
    current_value DECIMAL(15,2),  -- Nilai buku saat ini
    residual_value DECIMAL(15,2),  -- Nilai sisa
    
    -- Informasi Fisik
    serial_number VARCHAR(255),
    specifications JSON,  -- {"brand": "Dell", "model": "XPS", "color": "Black"}
    
    -- Foto
    photo_path VARCHAR(255),  -- Path ke foto aset
    qr_code VARCHAR(255),  -- QR code string atau path
    
    -- Penanggungjawab
    responsible_user_id BIGINT UNSIGNED,
    
    -- Status
    status ENUM('active', 'in_maintenance', 'in_loan', 'damaged', 'archived') DEFAULT 'active',
    condition ENUM('excellent', 'good', 'fair', 'poor') DEFAULT 'good',
    
    -- Metadata
    purchase_date DATE,
    warranty_until DATE,
    last_maintenance_date DATE,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES asset_categories(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (responsible_user_id) REFERENCES users(id) SET NULL
);
```

**Indeks:** 
- `code` (UNIQUE)
- `category_id`
- `location_id`
- `status`
- `responsible_user_id`

**Status Values:**
- `active` - Aset dalam kondisi siap pakai
- `in_maintenance` - Sedang dalam perawatan
- `in_loan` - Sedang dipinjam
- `damaged` - Rusak dan perlu perbaikan
- `archived` - Sudah tidak digunakan

**Condition Values:**
- `excellent` - Sangat baik
- `good` - Baik
- `fair` - Sedang
- `poor` - Buruk

**Relasi:**
- Belongs to: `category`, `location`, `user` (responsible_user)
- Has many: `loans`, `maintenances`, `transfers`, `disposals`

---

### 8. loans
**Tujuan:** Menyimpan data peminjaman aset
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE loans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- Identitas Peminjaman
    asset_id BIGINT UNSIGNED NOT NULL,
    requested_by BIGINT UNSIGNED NOT NULL,  -- User yang meminjam
    approved_by BIGINT UNSIGNED,  -- User yang approve
    
    -- Tanggal
    request_date DATE NOT NULL,
    approved_date DATE,
    checkout_date DATE,  -- Tanggal pickup aset
    expected_return_date DATE NOT NULL,
    actual_return_date DATE,
    
    -- Informasi
    purpose TEXT NOT NULL,  -- Tujuan peminjaman
    notes TEXT,
    
    -- Berkas
    checkout_photo VARCHAR(255),  -- Foto saat checkout
    return_photo VARCHAR(255),  -- Foto saat return
    letter_path VARCHAR(255),  -- Path surat keterangan PDF
    
    -- Status
    status ENUM('pending', 'approved', 'rejected', 'active', 'returned', 'overdue') DEFAULT 'pending',
    condition_checkout ENUM('excellent', 'good', 'fair', 'poor'),  -- Kondisi saat diambil
    condition_return ENUM('excellent', 'good', 'fair', 'poor'),  -- Kondisi saat dikembalikan
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) SET NULL
);
```

**Indeks:**
- `asset_id`
- `requested_by`
- `status`
- `expected_return_date`

**Status Flow:**
```
pending → approved → active → returned
  ↓
rejected
```

**Relasi:**
- Belongs to: `asset`, `user` (requester), `user` (approver)
- Has method: `isOverdue()` → Cek apakah sudah lewat waktu

---

### 9. maintenances
**Tujuan:** Menyimpan data perawatan dan maintenance aset
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE maintenances (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- Identitas
    asset_id BIGINT UNSIGNED NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,  -- User yang membuat request
    approved_by BIGINT UNSIGNED,  -- User yang approve
    
    -- Tipe Maintenance
    type ENUM('preventive', 'corrective', 'emergency') DEFAULT 'preventive',
    -- preventive: Perawatan rutin yang dijadwalkan
    -- corrective: Perbaikan karena ada kerusakan
    -- emergency: Perbaikan darurat
    
    -- Tanggal
    requested_date DATE NOT NULL,
    approved_date DATE,
    maintenance_date DATE,
    completion_date DATE,
    next_maintenance_date DATE,
    
    -- Detail
    description TEXT NOT NULL,  -- Deskripsi maintenance
    findings TEXT,  -- Temuan saat maintenance
    actions_taken TEXT,  -- Aksi yang dilakukan
    
    -- Biaya
    cost DECIMAL(15,2),  -- Biaya maintenance
    vendor VARCHAR(255),  -- Nama vendor/teknisi
    spare_parts JSON,  -- {"part1": "cost1", "part2": "cost2"}
    
    -- Dokumen
    document_path VARCHAR(255),  -- Path ke file bukti maintenance
    
    -- Status
    status ENUM('pending', 'approved', 'in_progress', 'completed', 'rejected') DEFAULT 'pending',
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) SET NULL
);
```

**Indeks:**
- `asset_id`
- `created_by`
- `status`
- `type`
- `maintenance_date`

**Relasi:**
- Belongs to: `asset`, `user` (creator), `user` (approver)

---

### 10. transfers
**Tujuan:** Menyimpan data mutasi/perubahan lokasi aset
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE transfers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- Identitas
    asset_id BIGINT UNSIGNED NOT NULL,
    requested_by BIGINT UNSIGNED NOT NULL,  -- User yang request
    approved_by BIGINT UNSIGNED,  -- User yang approve
    
    -- Lokasi
    from_location_id BIGINT UNSIGNED NOT NULL,  -- Lokasi asal
    to_location_id BIGINT UNSIGNED NOT NULL,  -- Lokasi tujuan
    from_user_id BIGINT UNSIGNED,  -- User penanggungjawab asal
    to_user_id BIGINT UNSIGNED,  -- User penanggungjawab baru
    
    -- Tanggal
    request_date DATE NOT NULL,
    approved_date DATE,
    transfer_date DATE,
    completion_date DATE,
    
    -- Detail
    reason TEXT NOT NULL,  -- Alasan mutasi
    notes TEXT,
    transfer_photo VARCHAR(255),  -- Foto saat transfer
    
    -- Status
    status ENUM('pending', 'approved', 'in_transit', 'completed', 'rejected') DEFAULT 'pending',
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) SET NULL,
    FOREIGN KEY (from_location_id) REFERENCES locations(id),
    FOREIGN KEY (to_location_id) REFERENCES locations(id),
    FOREIGN KEY (from_user_id) REFERENCES users(id) SET NULL,
    FOREIGN KEY (to_user_id) REFERENCES users(id) SET NULL
);
```

**Indeks:**
- `asset_id`
- `status`
- `from_location_id`
- `to_location_id`

**Relasi:**
- Belongs to: `asset`, `user`, `location` (from/to)

---

### 11. disposals
**Tujuan:** Menyimpan data penghapusan/disposisi aset
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE disposals (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- Identitas
    asset_id BIGINT UNSIGNED NOT NULL,
    requested_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED,
    
    -- Tanggal
    request_date DATE NOT NULL,
    approved_date DATE,
    disposal_date DATE,
    
    -- Detail Disposisi
    reason ENUM('obsolete', 'damaged', 'sold', 'donated', 'written_off') NOT NULL,
    -- obsolete: Sudah usang/outdated
    -- damaged: Rusak parah tidak bisa diperbaiki
    -- sold: Dijual
    -- donated: Didonasikan
    -- written_off: Dihapus dari catatan
    
    description TEXT,
    
    -- Nilai
    estimated_value DECIMAL(15,2),  -- Estimasi nilai saat disposal
    actual_selling_price DECIMAL(15,2),  -- Harga jual jika dijual
    residual_value DECIMAL(15,2),  -- Nilai residu
    
    -- Dokumen
    disposal_method VARCHAR(255),  -- Cara disposal (scrap, sold, donated, etc)
    documentation_path VARCHAR(255),  -- File bukti disposal
    
    -- Status
    status ENUM('pending', 'approved', 'in_process', 'completed', 'rejected') DEFAULT 'pending',
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (requested_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) SET NULL
);
```

**Indeks:**
- `asset_id`
- `status`
- `reason`
- `disposal_date`

**Relasi:**
- Belongs to: `asset`, `user` (requester), `user` (approver)

---

### 12. audit_logs
**Tujuan:** Menyimpan audit trail untuk semua perubahan data (immutable)
**Status:** Sudah dibuat via migration

```sql
CREATE TABLE audit_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    
    -- Identitas
    user_id BIGINT UNSIGNED,  -- User yang melakukan aksi
    model_type VARCHAR(255) NOT NULL,  -- Model yang diubah (Asset, Loan, etc)
    model_id BIGINT UNSIGNED NOT NULL,  -- ID dari model
    
    -- Aksi
    action ENUM('created', 'updated', 'deleted') NOT NULL,
    
    -- Data Lama dan Baru
    old_values JSON,  -- {"name": "Old Name", "status": "active"}
    new_values JSON,  -- {"name": "New Name", "status": "inactive"}
    
    -- Metadata Akses
    ip_address VARCHAR(45),  -- IPv4 atau IPv6
    user_agent TEXT,  -- Browser info
    
    -- Timestamp
    created_at TIMESTAMP NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) SET NULL
);
```

**Indeks:**
- `user_id`
- `model_type`
- `model_id`
- `action`
- `created_at`

**Karakteristik:**
- **Immutable** - Tidak bisa di-update/delete
- **Automatic** - Diciptakan otomatis via Auditable trait
- Menyimpan old & new values dalam JSON
- Track IP address dan user agent

---

## 🔗 Entity Relationship Diagram

```
┌─────────────┐
│   users     │
├─────────────┤
│ id (PK)     │
│ role_id (FK)├──────┐
│ email       │      │
│ password    │      │
│ ...         │      │
└─────────────┘      │
      ▲              │
      │              │
      │ has_many     │ belongs_to
      │              ▼
      │        ┌──────────────┐
      │        │  roles       │
      │        ├──────────────┤
      │        │ id (PK)      │
      │        │ name         │
      │        │ ...          │
      │        └──────────────┘
      │              │
      │        many_to_many
      │              │
      │        ┌──────────────────────┐
      │        │ role_permissions     │
      │        ├──────────────────────┤
      │        │ role_id (PK/FK)      │
      │        │ permission_id (PK/FK)│
      │        └──────────────────────┘
      │              │
      │              │ belongs_to
      │              ▼
      │        ┌──────────────────┐
      │        │ permissions      │
      │        ├──────────────────┤
      │        │ id (PK)          │
      │        │ name             │
      │        │ module           │
      │        └──────────────────┘
      │
      ├─────────────────────────────────┐
      │                                 │
      │ responsible_user                │
      │                                 │
      ▼                                 ▼
┌──────────────────────┐     ┌────────────────────┐
│   assets             │     │ asset_categories   │
├──────────────────────┤     ├────────────────────┤
│ id (PK)              │     │ id (PK)            │
│ category_id (FK)─────┼─────→ │ name               │
│ location_id (FK)     │     │ ...                │
│ responsible_user...  │     └────────────────────┘
│ ...                  │
└──────────────────────┘
      │
      ├─ location_id ──→ ┌────────────────┐
      │                  │  locations     │
      │                  ├────────────────┤
      │                  │ id (PK)        │
      │                  │ parent_id (FK) │ (self-join)
      │                  │ name           │
      │                  └────────────────┘
      │
      ├─ has_many ──→ ┌──────────────┐
      │               │  loans       │
      │               ├──────────────┤
      │               │ id (PK)      │
      │               │ asset_id (FK)│
      │               │ ...          │
      │               └──────────────┘
      │
      ├─ has_many ──→ ┌──────────────────┐
      │               │ maintenances     │
      │               ├──────────────────┤
      │               │ id (PK)          │
      │               │ asset_id (FK)    │
      │               │ ...              │
      │               └──────────────────┘
      │
      ├─ has_many ──→ ┌──────────────┐
      │               │ transfers    │
      │               ├──────────────┤
      │               │ id (PK)      │
      │               │ asset_id (FK)│
      │               │ ...          │
      │               └──────────────┘
      │
      └─ has_many ──→ ┌──────────────┐
                      │ disposals    │
                      ├──────────────┤
                      │ id (PK)      │
                      │ asset_id (FK)│
                      │ ...          │
                      └──────────────┘

┌──────────────────┐
│ audit_logs       │
├──────────────────┤
│ id (PK)          │
│ user_id (FK)     │ → links to users
│ model_type       │ → Asset, Loan, etc
│ model_id         │ → ID dari model
│ action           │ → created/updated/deleted
│ old_values (JSON)│
│ new_values (JSON)│
└──────────────────┘
```

---

## 🔍 Useful Queries

### Get Active Assets Count by Category
```sql
SELECT 
    ac.name as category,
    COUNT(a.id) as asset_count,
    SUM(a.purchase_price) as total_value
FROM assets a
JOIN asset_categories ac ON a.category_id = ac.id
WHERE a.status = 'active'
GROUP BY a.category_id, ac.name
ORDER BY asset_count DESC;
```

### Get Overdue Loans
```sql
SELECT 
    l.id,
    a.name as asset_name,
    u.name as borrower_name,
    l.expected_return_date,
    DATEDIFF(CURDATE(), l.expected_return_date) as days_overdue
FROM loans l
JOIN assets a ON l.asset_id = a.id
JOIN users u ON l.requested_by = u.id
WHERE l.status = 'active'
AND l.expected_return_date < CURDATE()
ORDER BY l.expected_return_date;
```

### Get Asset Depreciation
```sql
SELECT 
    a.name,
    ac.name as category,
    a.purchase_price,
    a.purchase_date,
    a.current_value,
    (a.purchase_price - a.current_value) as depreciation
FROM assets a
JOIN asset_categories ac ON a.category_id = ac.id
WHERE a.status = 'active'
ORDER BY depreciation DESC;
```

### Get Location Hierarchy
```sql
SELECT 
    l1.id,
    l1.name as location_name,
    IFNULL(l2.name, 'Root') as parent_location,
    COUNT(a.id) as asset_count
FROM locations l1
LEFT JOIN locations l2 ON l1.parent_id = l2.id
LEFT JOIN assets a ON l1.id = a.location_id
GROUP BY l1.id, l1.name, l2.name
ORDER BY l1.level, l1.name;
```

### Get User Permission Matrix
```sql
SELECT 
    u.name as user_name,
    r.name as role_name,
    GROUP_CONCAT(p.name) as permissions
FROM users u
JOIN roles r ON u.role_id = r.id
JOIN role_permissions rp ON r.id = rp.role_id
JOIN permissions p ON rp.permission_id = p.id
GROUP BY u.id, u.name, r.name
ORDER BY r.level DESC;
```

### Get Maintenance Cost by Asset
```sql
SELECT 
    a.name as asset_name,
    COUNT(m.id) as maintenance_count,
    SUM(m.cost) as total_cost,
    AVG(m.cost) as avg_cost
FROM assets a
LEFT JOIN maintenances m ON a.id = m.asset_id
WHERE m.status = 'completed'
GROUP BY a.id, a.name
ORDER BY total_cost DESC;
```

---

## 📈 Database Performance Tips

1. **Indices Created:**
   - All foreign keys (automatically indexed in MySQL)
   - email pada users (UNIQUE)
   - code pada assets (UNIQUE)
   - name pada roles, permissions (UNIQUE)
   - status, category_id, location_id pada assets
   - expected_return_date pada loans

2. **Query Optimization:**
   - Use eager loading di Eloquent: `with('category', 'location')`
   - Avoid N+1 queries dengan select()
   - Use indexes untuk WHERE dan JOIN conditions

3. **Backup Strategy:**
   ```bash
   # Backup database
   mysqldump -u root aset_imc > aset_imc_backup.sql
   
   # Restore database
   mysql -u root aset_imc < aset_imc_backup.sql
   ```

---

## 🔄 Transaction Handling

```php
// Example: Safe transfer dengan rollback jika error
DB::transaction(function () {
    // Update asset location
    $asset->update(['location_id' => $newLocationId]);
    
    // Create transfer record
    Transfer::create([...]);
    
    // If any error happens, semua perubahan di-rollback
});
```

---

## 🛡️ Data Integrity Constraints

- **ON DELETE CASCADE:** Ketika parent dihapus, child juga dihapus
  - Deletion roles → Delete user role assignments
  - Deletion locations → Delete assets di location itu
  
- **ON DELETE SET NULL:** Ketika parent dihapus, FK menjadi NULL
  - Deletion users → Set responsible_user_id = NULL

- **UNIQUE Constraints:**
  - `email` di users
  - `code` di assets (kode aset harus unik)
  - `name` di roles, permissions

---

**Last Updated:** December 9, 2025
**Database Version:** 1.0
**Total Tables:** 12
**Total Indices:** 25+
**Relationships:** 40+
