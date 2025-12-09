# ASET IMC - Quick Start Guide

## 🚀 Mulai Menggunakan Aplikasi

### 1. Akses Aplikasi
```
URL: http://127.0.0.1:8000
```

### 2. Login dengan Super Admin
```
Email: superadmin@aset-imc.local
Password: password123
```

### 3. Menu yang Tersedia

#### Dashboard
- Lihat statistik aset
- Charts kondisi aset
- Notifikasi pending tasks
- Role-specific information

#### Data Aset
- CRUD Aset (Create, Read, Update, Delete)
- Kategorisasi aset
- Manajemen lokasi

#### Peminjaman Aset
- Request peminjaman (Staff)
- Approval (Manager)
- Tracking return
- Print letter

#### Perawatan
- Schedule maintenance
- Report kerusakan
- Tracking biaya

#### Laporan
- Asset inventory
- Loan history
- Maintenance report
- Export PDF/Excel

---

## 🔑 User Roles & Credentials

### Super Admin
- Email: `superadmin@aset-imc.local`
- Password: `password123`
- Akses: ALL ✅

### Membuat User Baru
Gunakan Super Admin untuk create user di future implementation

---

## 📊 Features Yang Sudah Siap

✅ Authentication & Login
✅ Role-Based Access Control
✅ Dashboard dengan Multiple Views
✅ Database dengan Complete Schema
✅ Models & Relationships
✅ Routes untuk Semua Module
✅ Middleware untuk Security
✅ Audit Logging Infrastructure

---

## 📁 Struktur Database

### Users & Access
- `users` - Data pengguna
- `roles` - Role (Super Admin, Admin, Approver, Staff, Auditor)
- `permissions` - Permissions
- `role_permissions` - Role-Permission mapping

### Asset Management
- `assets` - Data aset lengkap
- `asset_categories` - Kategori (IT, Furniture, Kendaraan, dll)
- `locations` - Lokasi (Building → Floor → Room)

### Transactions
- `loans` - Peminjaman aset
- `maintenances` - Perawatan aset
- `transfers` - Mutasi aset
- `disposals` - Penghapusan aset

### Audit
- `audit_logs` - History semua perubahan

---

## 🛠️ Development Commands

### Database
```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Reset database (CAUTION!)
php artisan migrate:fresh --seed
```

### Tinker (Interactive Shell)
```bash
php artisan tinker

# Create user
User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => bcrypt('password')]);

# View roles
Role::all();
```

### Generate Models/Controllers
```bash
# Model dengan migration
php artisan make:model ModelName --migration

# Controller resource
php artisan make:controller ControllerName --resource
```

---

## 🎯 Next Steps untuk Development

### 1. Asset Management Views
- List aset dengan pagination
- Create/Edit aset form
- Photo upload
- QR code display

### 2. Loan Workflow
- Buat loan request form
- Approval interface untuk manager
- Return checklist
- PDF letter generation

### 3. Report Generation
- Asset inventory report
- Depreciation calculation
- Maintenance cost analysis
- Export ke PDF/Excel

### 4. Additional Features
- Email notifications
- SMS/WhatsApp notifications
- Mobile app
- Dark mode
- Advanced search & filtering

---

## 📚 Key Files to Modify

### Controllers
- `app/Http/Controllers/AssetController.php` - Asset CRUD logic
- `app/Http/Controllers/LoanController.php` - Loan workflow
- `app/Http/Controllers/ReportController.php` - Report generation

### Views
- `resources/views/layouts/app.blade.php` - Main layout
- `resources/views/dashboard/index.blade.php` - Dashboard
- Create views untuk aset, loans, maintenance, reports

### Models
- Models sudah lengkap dengan relationships
- Add Auditable trait ke model untuk auto-logging

---

## 🐛 Troubleshooting

### "Route [name] not found"
- Check route definitions di `routes/web.php`
- Pastikan controller method exists

### "Class not found"
- Run `composer dump-autoload`
- Check namespace di model/controller

### Database error
- Pastikan MySQL running
- Check `.env` database configuration
- Run migrations: `php artisan migrate`

### Permission denied
- Check user role di database
- Verify role_permissions mapping
- Check middleware di route

---

## 📞 Support

Untuk dokumentasi lebih lengkap, lihat:
- `README.md` - Features & overview
- `SETUP.md` - Installation & configuration
- `routes/web.php` - Available routes

---

**Status:** Ready for Development ✅
**Version:** 1.0.0
**Last Updated:** December 9, 2025
