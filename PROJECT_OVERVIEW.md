# 🏛️ ASET IMC - Project Overview & Documentation Hub

## Project Summary

**Aset IMC** adalah sistem manajemen aset terintegrasi berbasis web yang dibangun menggunakan **Laravel 12**, **MySQL**, dan **Bootstrap 5**. Sistem ini dirancang untuk mengelola inventarisasi aset, peminjaman, perawatan, dan disposisi aset dengan fitur RBAC (Role-Based Access Control), audit logging, dan reporting yang komprehensif.

---

## 📚 Documentation Files

Proyek ini dilengkapi dengan dokumentasi lengkap. Baca file berikut sesuai kebutuhan:

### 1. **README.md** - Deskripsi Proyek
   - Fitur utama
   - Teknologi yang digunakan
   - Arsitektur sistem
   - Roles dan permissions

### 2. **SETUP.md** - Panduan Instalasi
   - Requirement sistem
   - Langkah instalasi
   - Konfigurasi database
   - Inisialisasi aplikasi
   - Tips development

### 3. **QUICK_START.md** - Mulai dengan Cepat
   - Kredensial login
   - Daftar fitur
   - Struktur database
   - Command penting
   - Troubleshooting

### 4. **IMPLEMENTATION.md** - Checklist Development
   - Status setiap fitur (✅/[ ])
   - Prioritas implementasi
   - Estimasi effort
   - Testing checklist

### 5. **API_TESTING.md** - Routes & Testing Guide
   - Semua API endpoints
   - Test credentials
   - Testing scenarios
   - cURL examples
   - Troubleshooting

### 6. **DATABASE.md** (Optional - Bisa dibuat kemudian)
   - ER Diagram
   - Penjelasan setiap tabel
   - Relationship details
   - Query examples

---

## 🚀 Quick Start

### Akses Aplikasi
```
URL: http://127.0.0.1:8000
Super Admin: superadmin@aset-imc.local / password123
```

### Jalankan Server
```bash
cd C:\laragon\www\aset-imc
php artisan serve
```

### Buat User Baru (Development)
```bash
php artisan tinker

>>> $user = User::create([
>>>   'name' => 'John Doe',
>>>   'email' => 'john@example.com',
>>>   'password' => bcrypt('password123'),
>>>   'role_id' => 2  // 1=Super Admin, 2=Admin Aset, 3=Approver, 4=Staff, 5=Auditor
>>> ]);
```

---

## 📁 Project Structure

```
aset-imc/
├── app/
│   ├── Http/
│   │   ├── Controllers/      ← Business logic
│   │   ├── Middleware/       ← Authorization
│   │   └── Requests/         ← Form validation (to be added)
│   ├── Models/               ← Database models (11 models)
│   ├── Traits/               ← Auditable trait
│   └── Providers/            ← Service providers
│
├── database/
│   ├── migrations/           ← Database schema (12 migrations)
│   ├── seeders/              ← Initial data
│   └── factories/            ← Test factories
│
├── resources/
│   ├── css/                  ← Stylesheets
│   ├── js/                   ← JavaScript files
│   └── views/
│       ├── layouts/          ← Master layout
│       ├── dashboard/        ← Dashboard views
│       └── [modules]/        ← Feature views (to be added)
│
├── routes/
│   ├── web.php               ← Web routes (56 routes)
│   └── auth.php              ← Auth routes (59 routes)
│
├── bootstrap/                ← Framework bootstrap
├── config/                   ← Configuration files
├── storage/                  ← Uploaded files, logs
├── vendor/                   ← Composer dependencies
├── tests/                    ← Unit & Feature tests
│
├── .env                      ← Environment configuration
├── artisan                   ← Laravel CLI
├── composer.json             ← PHP dependencies
├── package.json              ← Node dependencies
├── phpunit.xml               ← Testing configuration
├── vite.config.js            ← Asset bundling
│
└── [DOCS]/
    ├── README.md             ← Project overview
    ├── SETUP.md              ← Installation guide
    ├── QUICK_START.md        ← Quick reference
    ├── IMPLEMENTATION.md     ← Development checklist
    ├── API_TESTING.md        ← Routes & testing
    ├── DATABASE.md           ← (To be created)
    └── PROJECT_OVERVIEW.md   ← This file
```

---

## 🗄️ Database Overview

### 12 Tables Created

| # | Tabel | Fungsi |
|---|-------|--------|
| 1 | `users` | User profiles |
| 2 | `roles` | Role definitions |
| 3 | `permissions` | Permission definitions |
| 4 | `role_permissions` | Role-Permission mapping |
| 5 | `asset_categories` | Asset category classifications |
| 6 | `locations` | Location hierarchy (hierarchical) |
| 7 | `assets` | Master aset dengan photo & QR |
| 8 | `loans` | Peminjaman aset |
| 9 | `maintenances` | Perawatan dan maintenance |
| 10 | `transfers` | Mutasi/perubahan lokasi |
| 11 | `disposals` | Penghapusan aset |
| 12 | `audit_logs` | Audit trail (immutable) |

### Relationship Diagram
```
Users
  ├─ has_one Role
  ├─ has_many Assets (as responsible_user)
  ├─ has_many Loans
  ├─ has_many Maintenances
  └─ has_many Transfers

Assets
  ├─ belongs_to AssetCategory
  ├─ belongs_to Location
  ├─ belongs_to User (responsible_user)
  ├─ has_many Loans
  ├─ has_many Maintenances
  ├─ has_many Transfers
  └─ has_many Disposals

Role ←→ Permission (many_to_many via role_permissions)

Loan ←→ User (requester & approver)
Maintenance ←→ User (created_by & approver)
Transfer ←→ User (requested_by & approver)
Disposal ←→ User (requested_by & approver)
```

---

## 👥 User Roles & Permissions

### 5 Built-in Roles

1. **Super Admin** (1000 - All permissions)
   - Full system control
   - User management
   - Role & permission management
   - Audit log viewing

2. **Admin Aset** (900 - Asset & Inventory)
   - CRUD aset
   - Kategori management
   - Lokasi management
   - Report viewing

3. **Approver/Manager** (800 - Approval Authority)
   - Approve/reject loans
   - Approve maintenance
   - Approve transfers
   - Dashboard dengan pending approvals

4. **Staff** (100 - Basic User)
   - View aset
   - Request peminjaman
   - Request maintenance
   - View personal requests

5. **Auditor** (50 - View & Export)
   - View semua data
   - Export reports
   - View audit logs

### 20 Permissions Defined
```
asset.view, asset.create, asset.edit, asset.delete, asset.import, asset.export
loan.view, loan.request, loan.approve, loan.reject
maintenance.view, maintenance.create, maintenance.approve
transfer.view, transfer.request, transfer.approve
disposal.view, disposal.request, disposal.approve
report.view, report.export
audit.view
```

---

## ✨ Features Completed

### Phase 1: Foundation ✅
- ✅ Database schema & migrations
- ✅ All models dengan relationships
- ✅ Authentication system
- ✅ RBAC (Roles & Permissions)
- ✅ Authorization middleware
- ✅ Dashboard dengan role-based views
- ✅ Audit logging system
- ✅ Responsive layout
- ✅ Test data & seeders

### Phase 2: Core Modules (Next)
- [ ] Asset Management (CRUD + Photo + QR)
- [ ] Loan Management (Request + Approval)
- [ ] Maintenance Module
- [ ] Report Generation
- [ ] QR Code Scanning

### Phase 3: Advanced Features (Future)
- [ ] Email/WhatsApp notifications
- [ ] Dark mode
- [ ] PWA/Offline mode
- [ ] Advanced analytics
- [ ] Mobile app

---

## 🔧 Development Commands

### Database Operations
```bash
# Migrate database
php artisan migrate

# Migrate fresh (reset + migrate)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Fresh + seed
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Create migration
php artisan make:migration create_table_name

# Create seeder
php artisan make:seeder SeederName
```

### Model/Controller/Middleware Creation
```bash
# Create model
php artisan make:model ModelName

# Create migration dengan model
php artisan make:model ModelName -m

# Create controller
php artisan make:controller ControllerName

# Create controller resource (dengan CRUD methods)
php artisan make:controller ControllerName --resource

# Create middleware
php artisan make:middleware MiddlewareName

# Create form request
php artisan make:request FormRequestName

# Create trait
php artisan make:trait TraitName
```

### Development Tools
```bash
# Start development server
php artisan serve

# Tinker (interactive shell)
php artisan tinker

# Show all routes
php artisan route:list

# Cache configuration
php artisan config:cache

# Generate APP_KEY
php artisan key:generate

# Clear cache
php artisan cache:clear

# Tail logs
php artisan logs:tail -f
```

---

## 🛠️ Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Framework | Laravel | 12.41.1 |
| Language | PHP | 8.3.26 |
| Database | MySQL | 8.4.3 |
| Frontend | Bootstrap | 5.3.0 |
| Charts | Chart.js | 3.9.1 |
| Icons | Phosphor/Hero | Latest |
| CSS | Tailwind (optional) | - |
| Build Tool | Vite | - |
| Package Manager | Composer | - |

---

## 🔐 Security Features

1. **Authentication**
   - Laravel built-in authentication
   - Password hashing dengan bcrypt
   - Session management

2. **Authorization**
   - RBAC system dengan fine-grained permissions
   - Middleware checks per route
   - Model policy untuk resource authorization

3. **Data Protection**
   - CSRF token validation pada setiap form
   - Audit logging untuk semua changes
   - Immutable audit trail

4. **API Security**
   - CORS configuration
   - Rate limiting (to be implemented)
   - API authentication (to be implemented)

---

## 📊 Performance Considerations

- Database indices pada frequently queried columns
- Eager loading untuk relationships (to prevent N+1 queries)
- Query optimization di dashboard
- Cache layer untuk reports (to be implemented)
- Pagination untuk large datasets

---

## 🐛 Troubleshooting

### Aplikasi tidak bisa diakses
```bash
# Pastikan server jalan
php artisan serve

# Buka http://127.0.0.1:8000
```

### Database connection error
```bash
# Check MySQL running di Laragon
# Verify .env settings
php artisan tinker
>>> DB::connection()->getPdo();
```

### Login gagal
```bash
# Reset password
php artisan tinker
>>> $user = User::find(1);
>>> $user->password = bcrypt('password123');
>>> $user->save();
```

### Cache issues
```bash
# Clear all cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

---

## 📞 Support & Contact

Untuk issue atau question:
1. Cek dokumentasi (README.md, SETUP.md, QUICK_START.md)
2. Cek API_TESTING.md untuk endpoints
3. Lihat IMPLEMENTATION.md untuk status fitur
4. Check Laravel logs: `storage/logs/laravel.log`

---

## 📋 Next Steps for Development

1. **Week 1: Asset Module**
   - Implement AssetController CRUD
   - Create asset listing view
   - Add photo upload
   - Generate QR codes

2. **Week 2: Loan Management**
   - Implement loan request form
   - Build approval interface
   - Create surat keterangan PDF
   - Add email notifications

3. **Week 3: Maintenance & Reports**
   - Build maintenance module
   - Create reporting system
   - Add export functionality

4. **Week 4: Polish & Deploy**
   - QR scanning implementation
   - UI/UX refinements
   - Testing & debugging
   - Production deployment

---

## 📄 File Manifest

```
Documentation Files:
- PROJECT_OVERVIEW.md (this file)
- README.md
- SETUP.md
- QUICK_START.md
- IMPLEMENTATION.md
- API_TESTING.md

Core Application Files:
- app/Http/Controllers/
- app/Models/
- app/Traits/
- database/migrations/
- database/seeders/
- routes/web.php
- routes/auth.php
- resources/views/
- .env
```

---

## ✅ Status Summary

| Component | Status | Coverage |
|-----------|--------|----------|
| Database | ✅ Complete | 12/12 tables |
| Models | ✅ Complete | 11/11 models |
| Authentication | ✅ Complete | Register, Login, Logout |
| RBAC | ✅ Complete | 5 roles, 20 permissions |
| Controllers | ✅ Scaffolded | 7 controllers (1 fully done) |
| Routes | ✅ Complete | 115 routes defined |
| Views | 🟨 Partial | Layout + Dashboard |
| CRUD Operations | ⏳ Pending | All modules |
| File Upload | 🟨 Partial | Infrastructure ready |
| Notifications | ⏳ Pending | Email/WhatsApp |
| Reports | ⏳ Pending | Framework ready |
| QR Scanning | ⏳ Pending | Generation ready |

**Overall Progress: ~35% ✅**
- Foundation & Architecture: 100% ✅
- Core Development: ~10% ⏳
- Polish & Deployment: 0% ⏳

---

**Project Start Date:** December 9, 2025
**Last Updated:** December 9, 2025
**Next Review:** After Asset Module Completion

---

## 📖 How to Read Documentation

1. **Baru pertama kali?** → Read `QUICK_START.md`
2. **Mau setup lokal?** → Read `SETUP.md`
3. **Mau understand arsitektur?** → Read `README.md`
4. **Mau develop fitur?** → Read `IMPLEMENTATION.md`
5. **Mau test endpoints?** → Read `API_TESTING.md`
6. **Mau understand database?** → Read `DATABASE.md` (to be created)

---

**Selamat! Aset IMC siap untuk Phase 2 Development! 🎉**
