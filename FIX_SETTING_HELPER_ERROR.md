# FIX: Error "Call to undefined function setting()"

## Tanggal: 13 Januari 2026

---

## 🐛 Error yang Terjadi

```
Error
resources\views\layouts\app.blade.php:7
Call to undefined function setting()
```

**Penyebab:**
- Helper function `setting()` belum ter-load
- Autoload composer belum di-refresh di web server
- Perlu restart web server untuk load helper baru

---

## ✅ Solusi yang Diterapkan

### Revert ke Static Call

Mengganti helper function dengan direct model call:

**Sebelum (Error):**
```blade
{{ setting('site_name') }}
{{ setting('site_logo') }}
```

**Sesudah (Fixed):**
```blade
{{ App\Models\Setting::get('site_name') }}
{{ App\Models\Setting::get('site_logo') }}
```

---

## 📝 File yang Diperbaiki

### `resources/views/layouts/app.blade.php`

**Line 7 - Title:**
```blade
<title>@yield('title', App\Models\Setting::get('site_name', 'Aset IMC'))</title>
```

**Line 10 - Favicon:**
```blade
@if(App\Models\Setting::get('site_favicon'))
    <link rel="icon" href="{{ asset('storage/' . App\Models\Setting::get('site_favicon')) }}">
@endif
```

**Line 149 - Logo Sidebar:**
```blade
@if(App\Models\Setting::get('site_logo'))
    <img src="{{ asset('storage/' . App\Models\Setting::get('site_logo')) }}" ...>
@else
    <i class="fas fa-cube"></i> {{ App\Models\Setting::get('site_name', 'Aset IMC') }}
@endif
```

---

## 🔧 Alternatif Solusi (Opsional)

Jika ingin tetap menggunakan helper function:

### 1. Restart Web Server
```bash
# Stop server
Ctrl + C

# Start lagi
php artisan serve
```

### 2. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 3. Regenerate Autoload
```bash
composer dump-autoload
```

---

## ✅ Status Sekarang

| Item | Status | Method |
|------|--------|--------|
| Favicon Dinamis | ✅ Working | `App\Models\Setting::get()` |
| Logo Dinamis | ✅ Working | `App\Models\Setting::get()` |
| Nama Website | ✅ Working | `App\Models\Setting::get()` |
| Helper Function | ⏳ Optional | Perlu restart server |

---

## 💡 Rekomendasi

**Gunakan Direct Model Call:**
- ✅ Lebih reliable
- ✅ Tidak perlu restart server
- ✅ Lebih jelas dari mana data berasal
- ✅ Autocomplete di IDE

**Helper Function (Opsional):**
- Bisa digunakan setelah restart server
- Lebih pendek untuk ditulis
- Perlu maintenance lebih

---

## 🎯 Kesimpulan

**Error sudah diperbaiki!** ✅

Logo dan favicon tetap **dinamis** dan berfungsi dengan baik menggunakan `App\Models\Setting::get()`.

**Tidak ada perubahan fungsionalitas**, hanya cara pemanggilan yang berbeda.

---

**Dibuat oleh:** Antigravity AI Assistant  
**Tanggal:** 13 Januari 2026  
**Status:** ✅ Fixed & Working
