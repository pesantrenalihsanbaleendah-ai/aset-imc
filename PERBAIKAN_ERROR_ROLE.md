# PERBAIKAN ERROR "Trying to get property of non-object"

## Tanggal: 13 Januari 2026

---

## 🐛 Masalah

Error: **"Trying to get property of non-object of type string"**

Terjadi ketika mencoba mengakses `auth()->user()->role->name`

---

## 🔍 Penyebab

1. **User tidak memiliki role_id** - Field `role_id` di tabel `users` adalah NULL
2. **Role tidak ada** - Role dengan ID tertentu tidak ada di tabel `roles`
3. **Relasi tidak di-load** - Eloquent tidak melakukan eager loading

---

## ✅ Perbaikan yang Sudah Dilakukan

### 1. Controller Fixes
✅ **LoanController** - Line 27
```php
// SEBELUM
if ($user->role->name === 'staff') {

// SESUDAH
if ($user && $user->role && $user->role->name === 'staff') {
```

✅ **LoanController** - approve() & return() methods
```php
// Tambahkan eager loading
$loan = Loan::with('asset')->findOrFail($id);
```

✅ **MaintenanceController** - approve() & complete() methods
```php
// Tambahkan eager loading
$maintenance = Maintenance::with('asset')->findOrFail($id);
```

### 2. View Fixes
✅ **loans/index.blade.php** - Line 114
✅ **loans/show.blade.php** - Line 162
✅ **maintenance/index.blade.php** - Line 136
✅ **maintenance/show.blade.php** - Line 188

```blade
{{-- SEBELUM --}}
@if(auth()->user()->role->name != 'staff')

{{-- SESUDAH --}}
@if(auth()->user() && auth()->user()->role && auth()->user()->role->name != 'staff')
```

---

## 🔧 Solusi untuk Database

### Opsi 1: Pastikan Semua User Punya Role

Jalankan query SQL ini untuk memeriksa user tanpa role:

```sql
SELECT id, name, email, role_id FROM users WHERE role_id IS NULL;
```

Jika ada user tanpa role, assign role default:

```sql
-- Cari ID role 'staff' (atau role default lainnya)
SELECT id, name FROM roles;

-- Update user tanpa role
UPDATE users SET role_id = 4 WHERE role_id IS NULL;
-- Ganti 4 dengan ID role yang sesuai
```

### Opsi 2: Buat Seeder untuk Role & User

Buat file seeder untuk memastikan role dan user ada:

```bash
php artisan make:seeder RoleSeeder
php artisan make:seeder UserSeeder
```

**RoleSeeder.php:**
```php
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use App\\Models\\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Admin'],
            ['name' => 'admin_aset', 'display_name' => 'Admin Aset'],
            ['name' => 'approver', 'display_name' => 'Approver'],
            ['name' => 'staff', 'display_name' => 'Staff'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
```

**UserSeeder.php:**
```php
<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use App\\Models\\User;
use App\\Models\\Role;
use Illuminate\\Support\\Facades\\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'super_admin')->first();
        
        if ($adminRole) {
            User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
```

Jalankan seeder:
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

### Opsi 3: Modifikasi Model User

Tambahkan accessor untuk role yang aman:

```php
// Di app/Models/User.php

public function getRoleNameAttribute()
{
    return $this->role ? $this->role->name : 'guest';
}
```

Kemudian di view gunakan:
```blade
@if(auth()->user()->role_name != 'staff')
```

---

## 🎯 Rekomendasi

**Gunakan Opsi 1 + Opsi 2:**

1. ✅ Periksa dan fix user yang tidak punya role (Opsi 1)
2. ✅ Buat seeder untuk memastikan data konsisten (Opsi 2)
3. ✅ Jalankan seeder di environment development

**Untuk Production:**
- Pastikan migration dan seeder dijalankan
- Validasi semua user memiliki role_id
- Backup database sebelum update

---

## 📝 Checklist Perbaikan

### Controller
- [x] LoanController - index() method
- [x] LoanController - approve() method  
- [x] LoanController - return() method
- [x] MaintenanceController - approve() method
- [x] MaintenanceController - complete() method

### Views
- [x] loans/index.blade.php
- [x] loans/show.blade.php
- [x] maintenance/index.blade.php
- [x] maintenance/show.blade.php

### Database
- [ ] Periksa user tanpa role
- [ ] Assign role ke semua user
- [ ] Buat RoleSeeder
- [ ] Buat UserSeeder
- [ ] Jalankan seeder

---

## 🚀 Cara Test

1. **Login ke aplikasi**
2. **Akses halaman:**
   - `/loans` - Harus bisa akses tanpa error
   - `/loans/create` - Harus bisa buat peminjaman
   - `/maintenance` - Harus bisa akses tanpa error
   - `/maintenance/create` - Harus bisa buat pemeliharaan

3. **Test role-based access:**
   - Login sebagai staff → Tidak bisa approve/reject
   - Login sebagai admin → Bisa approve/reject

---

## 💡 Tips Debugging

Jika masih error, cek di `storage/logs/laravel.log`:

```bash
# Windows
type storage\\logs\\laravel.log | findstr "role"

# Atau buka file langsung
notepad storage\\logs\\laravel.log
```

Cari baris yang menyebutkan:
- "Trying to get property"
- "role"
- Line number

---

**Dibuat oleh:** Antigravity AI Assistant  
**Tanggal:** 13 Januari 2026  
**Status:** Controller & View sudah diperbaiki, Database perlu dicek
