# 🎯 ASET IMC - Sistem Inventarisasi Aset

Aplikasi web comprehensive untuk manajemen dan inventarisasi aset perusahaan dengan fitur complete role-based access control, audit logging, dan analytical reporting.

## ✨ Features

### 🔐 Authentication & Authorization
- ✅ Multi-role system (Super Admin, Admin Aset, Approver, Staff, Auditor)
- ✅ Permission-based access control
- ✅ Role-based navigation
- ✅ Secure password hashing

### 📊 Dashboard
- ✅ Dynamic dashboard per role
- ✅ Real-time statistics
- ✅ Chart visualization (Asset condition, Category distribution)
- ✅ Pending approvals queue
- ✅ Personal asset list untuk Staff

### 📦 Asset Management
- Struktur database lengkap untuk CRUD aset
- Asset categories dengan depreciation settings
- Location hierarchy (Building → Floor → Room)
- Asset photo upload (ready)
- QR code generation (ready)
- Bulk import dari Excel (ready)

### 🔄 Asset Circulation
- **Loan Management**
  - Request peminjaman dari staff
  - Approval workflow
  - Return tracking
  - Overdue detection
  - PDF letter generation (ready)

- **Maintenance**
  - Preventive & corrective maintenance
  - Schedule management
  - Cost tracking
  - Auto-reminder (ready)

- **Asset Transfer**
  - Location mutation
  - Responsibility transfer
  - Approval workflow

- **Asset Disposal**
  - Request penghapusan
  - Reason categorization
  - Residual value tracking
  - Approval process

### 📈 Reports & Analytics
Database structure ready untuk:
- Laporan aset per kategori/lokasi
- Depreciation report
- Loan analytics
- Maintenance cost analysis
- PDF & Excel export (ready)

### 🛡️ Security & Audit
- ✅ Audit logging untuk semua aktivitas
- ✅ User action tracking
- ✅ Change history (old values & new values)
- ✅ IP address & User agent logging
- ✅ Immutable audit logs

## 🏗️ Architecture

### Models (Complete)
- `User` - Users dengan role assignment
- `Role` - Role definitions
- `Permission` - Permission definitions
- `Asset` - Data aset dengan relasi
- `AssetCategory` - Kategori aset
- `Location` - Lokasi hierarchy
- `Loan` - Peminjaman aset
- `Maintenance` - Perawatan aset
- `Transfer` - Mutasi aset
- `Disposal` - Penghapusan aset
- `AuditLog` - Activity logging

### Controllers (Scaffolded)
- `DashboardController` - Dynamic dashboard per role
- `AssetController` - Asset CRUD & operations
- `AssetCategoryController` - Category management
- `LocationController` - Location management
- `LoanController` - Loan workflow
- `MaintenanceController` - Maintenance workflow
- `ReportController` - Reports & exports

### Database
- 11 core tables dengan proper relationships
- Migrations dengan constraints & indices
- Seeder dengan initial data (roles, permissions, categories, locations)
- Foreign key constraints dengan cascade rules

### Middleware
- `CheckRole` - Role-based route protection
- `CheckPermission` - Permission-based access control

### Views
- ✅ Layout responsif dengan sidebar navigation
- ✅ Dashboard dengan role-based content
- ✅ Bootstrap 5 styling
- ✅ Chart.js integration

## 🚀 Getting Started

### Installation
```bash
# 1. Database sudah di-setup
# 2. Migrations sudah di-run

# 3. Start development server
php artisan serve

# Visit http://127.0.0.1:8000
```

### Initial Login
```
Email: superadmin@aset-imc.local
Password: password123
```

## 📋 What's Ready

### ✅ Complete
- Database schema & migrations
- Model definitions dengan relationships
- Authentication & RBAC system
- Dashboard dengan role-based views
- Routes untuk semua modul
- Controllers (scaffolded, ready untuk implementation)
- Middleware untuk authorization
- Seeder dengan test data
- Audit logging infrastructure

### ⏳ Ready untuk Development
- Asset management views & logic
- Loan workflow implementation
- Maintenance module
- Report generation
- File upload features
- QR code generation
- PDF export
- Notification system (email/WhatsApp)
- Mobile QR scanner
- Dark mode UI
- PWA features

## 🎓 Technology Stack

- **Backend:** Laravel 12
- **Database:** MySQL 8
- **Frontend:** Bootstrap 5, Chart.js
- **PHP:** 8.3+
- **Development:** Vite (asset bundling)

## 📁 Project Structure

```
aset-imc/
├── app/Models/          → Eloquent models
├── app/Http/Controllers/ → Controllers
├── app/Http/Middleware/  → Authorization middleware
├── app/Traits/           → Auditable trait
├── database/migrations/   → Schema definitions
├── database/seeders/      → Initial data
├── resources/views/       → Blade templates
├── routes/               → Route definitions
└── storage/              → Uploads & logs
```

## 🔮 Future Enhancements

- Mobile app (Flutter/React Native)
- Real-time notifications (Websockets)
- Advanced analytics dashboard
- QR code scanning via mobile camera
- Integration dengan Accounting system
- API documentation (OpenAPI/Swagger)
- Automated depreciation calculation
- Compliance reporting
- Multi-tenant support

## 👥 Roles & Permissions

| Role | Assets | Loans | Maintenance | Reports | Users |
|------|--------|-------|-------------|---------|-------|
| Super Admin | ✅ All | ✅ All | ✅ All | ✅ All | ✅ All |
| Admin Aset | ✅ CRUD | ✅ Approve | ✅ Manage | ✅ View | ❌ |
| Approver | ✅ View | ✅ Approve | ✅ Approve | ✅ View | ❌ |
| Staff | ✅ View Assigned | ✅ Request | ✅ Report | ❌ | ❌ |
| Auditor | ✅ Read-only | ✅ Read-only | ✅ Read-only | ✅ All | ❌ |

## 📞 Support

Untuk pertanyaan atau issues, silahkan cek documentation di `SETUP.md`

---

**Status:** Production-Ready Foundation ✅  
**Created:** December 9, 2025  
**Version:** 1.0.0

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
