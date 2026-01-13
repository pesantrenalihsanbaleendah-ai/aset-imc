# LOGO & FAVICON DINAMIS

## Tanggal: 13 Januari 2026

---

## ✅ Yang Sudah Dibuat

### 1. Helper Function
✅ **File**: `app/Helpers/SettingHelper.php`

**Functions:**
```php
setting($key, $default = null)  // Get single setting
settings($group)                 // Get settings by group
```

### 2. Autoload Helper
✅ **File**: `composer.json`
- Ditambahkan ke autoload files
- Helper tersedia di seluruh aplikasi

### 3. Dynamic Logo & Favicon
✅ **File**: `resources/views/layouts/app.blade.php`

**Fitur:**
- ✅ Favicon dinamis dari database
- ✅ Logo sidebar dinamis
- ✅ Nama website dinamis di title & sidebar
- ✅ Fallback jika tidak ada logo/favicon

---

## 🎨 Implementasi

### Favicon (Tab Browser)

**Jika ada favicon di settings:**
```html
<link rel="icon" type="image/png" href="{{ asset('storage/settings/favicon.png') }}">
```

**Jika tidak ada (default):**
```html
<link rel="icon" href="data:image/svg+xml,...">
<!-- Emoji 📦 sebagai favicon -->
```

### Logo Sidebar

**Jika ada logo di settings:**
```html
<img src="{{ asset('storage/settings/logo.png') }}" 
     alt="Nama Website" 
     style="max-height: 40px; max-width: 100%; object-fit: contain;">
```

**Jika tidak ada (default):**
```html
<i class="fas fa-cube"></i> Nama Website
```

### Title Browser

**Dinamis dari settings:**
```html
<title>Halaman - Nama Website dari Settings</title>
```

---

## 📁 File yang Diubah

### 1. Helper Function
```
app/Helpers/SettingHelper.php (NEW)
```

### 2. Composer Configuration
```
composer.json (UPDATED)
- Added: autoload.files
```

### 3. Layout
```
resources/views/layouts/app.blade.php (UPDATED)
- Line 7: Dynamic title
- Line 10-14: Dynamic favicon
- Line 152-157: Dynamic logo sidebar
```

---

## 💻 Cara Menggunakan

### Upload Logo & Favicon

1. **Login sebagai Super Admin**
2. **Buka Pengaturan**: `/admin/settings`
3. **Scroll ke "Pengaturan Tampilan"**
4. **Upload:**
   - **Site Logo**: Untuk sidebar (PNG/JPG, max 2MB)
   - **Site Favicon**: Untuk tab browser (PNG, 16x16 atau 32x32 px)
5. **Klik "Simpan Pengaturan"**

### Hasil

**Setelah upload:**
- ✅ Logo muncul di sidebar (menggantikan icon + text)
- ✅ Favicon muncul di tab browser
- ✅ Nama website berubah di title & sidebar

**Jika dihapus:**
- ✅ Kembali ke default (icon + text)
- ✅ Favicon kembali ke emoji 📦

---

## 🎯 Rekomendasi Ukuran

### Logo Sidebar
- **Format**: PNG (dengan background transparan)
- **Ukuran**: 200x40 px (landscape)
- **Ratio**: 5:1 (lebar : tinggi)
- **Max File**: 2MB
- **Background**: Transparan atau putih

### Favicon
- **Format**: PNG atau ICO
- **Ukuran**: 32x32 px atau 16x16 px
- **Max File**: 100KB
- **Background**: Transparan

---

## 🔧 Helper Function Usage

### Di Blade Template
```blade
{{-- Get single setting --}}
{{ setting('site_name') }}
{{ setting('site_name', 'Default Name') }}

{{-- Check if exists --}}
@if(setting('site_logo'))
    <img src="{{ asset('storage/' . setting('site_logo')) }}">
@endif

{{-- Get by group --}}
@php
    $whatsapp = settings('whatsapp');
@endphp
```

### Di Controller
```php
use function setting;

$siteName = setting('site_name');
$logo = setting('site_logo');
$whatsappSettings = settings('whatsapp');
```

---

## 🎨 Customization

### Ubah Ukuran Logo
```html
<img src="..." style="max-height: 50px; max-width: 100%;">
```

### Ubah Default Icon
```html
<i class="fas fa-building"></i> {{ setting('site_name') }}
```

### Ubah Default Favicon
```html
<link rel="icon" href="data:image/svg+xml,<svg>...</svg>">
<!-- Ganti emoji atau SVG -->
```

---

## ✅ Checklist

### Helper
- [x] Helper function created
- [x] Added to composer.json
- [x] Composer dump-autoload run

### Layout
- [x] Dynamic title
- [x] Dynamic favicon
- [x] Dynamic logo sidebar
- [x] Fallback for missing images

### Settings
- [x] site_logo field exists
- [x] site_favicon field exists
- [x] site_name field exists
- [x] Upload form in settings page

---

## 🔄 Cache Behavior

**Settings di-cache selama 1 jam:**
- Perubahan logo/favicon mungkin tidak langsung terlihat
- Refresh browser atau clear cache untuk melihat perubahan
- Cache otomatis clear saat update settings

**Manual clear cache:**
```php
Setting::clearCache();
```

**Clear browser cache:**
- Chrome: Ctrl + Shift + Delete
- Firefox: Ctrl + Shift + Delete
- Edge: Ctrl + Shift + Delete

---

## 📊 Before & After

### Before (Static)
```
Sidebar: [Icon] Aset IMC
Favicon: (default browser icon)
Title: Aset IMC - Sistem Inventarisasi Aset
```

### After (Dynamic)
```
Sidebar: [Logo Image] atau [Icon] Nama dari Settings
Favicon: [Uploaded Favicon] atau 📦
Title: Halaman - Nama dari Settings
```

---

## 🚀 Testing

### Test Logo
1. Upload logo di settings
2. Refresh halaman
3. Logo harus muncul di sidebar
4. Hapus logo → kembali ke icon + text

### Test Favicon
1. Upload favicon di settings
2. Refresh browser (hard refresh: Ctrl+F5)
3. Favicon harus muncul di tab
4. Hapus favicon → kembali ke emoji 📦

### Test Nama Website
1. Ubah site_name di settings
2. Refresh halaman
3. Nama berubah di:
   - Title browser
   - Sidebar (jika tidak ada logo)

---

## 📝 Troubleshooting

### Logo tidak muncul
- ✅ Cek file ada di `storage/app/public/settings/`
- ✅ Jalankan `php artisan storage:link`
- ✅ Cek permission folder storage
- ✅ Clear cache: `Setting::clearCache()`

### Favicon tidak berubah
- ✅ Hard refresh browser (Ctrl+F5)
- ✅ Clear browser cache
- ✅ Cek format file (PNG recommended)
- ✅ Cek ukuran file (max 2MB)

### Helper function error
- ✅ Jalankan `composer dump-autoload`
- ✅ Restart web server
- ✅ Cek file `app/Helpers/SettingHelper.php` exists

---

## 🎯 Status

| Fitur | Status | Keterangan |
|-------|--------|------------|
| Helper Function | ✅ 100% | Tersedia global |
| Dynamic Favicon | ✅ 100% | Dengan fallback |
| Dynamic Logo | ✅ 100% | Dengan fallback |
| Dynamic Title | ✅ 100% | Dari settings |
| Cache | ✅ 100% | 1 jam TTL |

---

**🎉 LOGO & FAVICON DINAMIS SELESAI!**

**Dibuat oleh:** Antigravity AI Assistant  
**Tanggal:** 13 Januari 2026  
**Files Created:** 1 helper  
**Files Updated:** 2 (composer.json, app.blade.php)  
**Status:** ✅ Ready to Use
