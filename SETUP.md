# ASET IMC - Sistem Inventarisasi Aset

## Setup & Installation Guide

### Prerequisites
- PHP 8.3+
- MySQL 8.0+
- Composer
- Laragon (recommended for Windows development)

### Installation Steps

#### 1. Database Setup
Database `aset_imc` sudah dibuat otomatis

```bash
# Create database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS aset_imc;"

# Run migrations dan seeders
php artisan migrate:fresh --seed
```

#### 2. Initial Credentials
Setelah seeding, gunakan akun berikut:

**Super Admin:**
- Email: `superadmin@aset-imc.local`
- Password: `password123`

#### 3. Start Development Server
```bash
php artisan serve
```

Server akan berjalan di `http://127.0.0.1:8000`

### Database Structure

#### Roles
- `super_admin` - Super Administrator (akses penuh)
- `admin_aset` - Administrator Aset (CRUD aset, laporan)
- `approver` - Manager/Approver (approval permissions)
- `staff` - Staf/User Umum (request peminjaman, lihat aset assigned)
- `auditor` - Auditor (read-only semua data, lihat audit logs)

#### Tables
- `users` - Data pengguna
- `roles` - Role definitions
- `permissions` - Permission definitions
- `role_permissions` - Role-Permission mapping
- `assets` - Data aset
- `asset_categories` - Kategori aset
- `locations` - Lokasi aset (building → floor → room)
- `loans` - Peminjaman aset
- `maintenances` - Perawatan aset
- `transfers` - Mutasi/perpindahan aset
- `disposals` - Penghapusan aset
- `audit_logs` - Log aktivitas untuk audit trail

### Key Features Implemented

✅ **Authentication & Authorization**
- Role-based access control (RBAC)
- Permission-based authorization
- User role assignment

✅ **Dashboard**
- Dynamic dashboard based on user role
- Statistics cards
- Charts untuk analisis (Admin)
- Approval queue (Manager)
- Personal asset list (Staff)

✅ **Models & Relationships**
- Complete model relationships
- Eloquent relationships untuk semua entity
- Audit trail ready (Auditable trait)

✅ **Routes**
- REST API routes untuk semua modul
- Protected routes dengan middleware auth

✅ **Database**
- Comprehensive migrations dengan constraints
- Foreign keys dengan cascade rules
- Indices untuk performance

### Next Steps untuk Development

#### Asset Management Module
1. Buat view untuk CRUD aset
2. Implement asset photo upload
3. Generate QR code untuk aset
4. Bulk import dari Excel

#### Loan Management
1. Buat form request peminjaman
2. Implement approval workflow
3. PDF letter generation
4. Email notifications

#### Maintenance Module
1. Maintenance schedule
2. Checklist management
3. Cost tracking
4. Preventive maintenance alerts

#### Reports & Analytics
1. Asset inventory report
2. Depreciation calculation
3. Loan analytics
4. Maintenance cost analysis
5. Export ke PDF/Excel

#### Additional Features
1. QR code scanning dengan mobile camera
2. Email/WhatsApp notifications
3. Dark mode UI
4. PDF generation (Maintenance, Loans)
5. Excel export

### File Structure

```
aset-imc/
├── app/
│   ├── Models/          # Eloquent Models
│   ├── Http/
│   │   ├── Controllers/ # Controllers
│   │   └── Middleware/  # RBAC Middleware
│   └── Traits/          # Auditable, etc
├── database/
│   ├── migrations/      # Database migrations
│   └── seeders/         # Database seeders
├── resources/
│   └── views/           # Blade templates
├── routes/              # Route definitions
├── public/              # Public assets
└── storage/             # Upload files
```

### Available Commands

```bash
# Generate new model with migration
php artisan make:model ModelName --migration

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Reset database (DANGEROUS!)
php artisan migrate:fresh --seed

# Create test data
php artisan tinker
```

### Development Tips

1. **Adding New Permission**
   ```php
   // In database seeder
   Permission::create(['name' => 'new.permission']);
   
   // Assign to role
   $role->permissions()->attach($permission_id);
   ```

2. **Check User Permissions**
   ```php
   // In controller or view
   auth()->user()->hasPermission('asset.create')
   auth()->user()->hasRole('admin_aset')
   ```

3. **Audit Logging**
   - Add `use Auditable;` trait ke model untuk auto audit logging
   - Cek `audit_logs` table untuk history

### Troubleshooting

**Database connection error:**
- Pastikan MySQL running
- Check .env DB configuration

**Missing auth routes:**
- File `routes/auth.php` sudah dibuat

**Permission denied:**
- Verify user role di `users` table
- Check `role_permissions` mapping

---

Created: December 9, 2025
Last Updated: December 9, 2025
Status: Foundation Setup Complete ✅
