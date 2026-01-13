# PERBAIKAN SQL ERROR - Reserved Keyword 'condition'

## Masalah yang Ditemukan
```
SQLSTATE[42000]: Syntax error or access violation: 1064 
You have an error in your SQL syntax near 'condition, COUNT(*) as count from `assets` group by `condition`'
```

**Penyebab:** Kolom `condition` di tabel `assets` adalah **reserved keyword** di MySQL, sehingga menyebabkan error saat digunakan dalam query raw SQL tanpa proper escaping.

## Lokasi Error
File: `app/Http/Controllers/DashboardController.php`
Baris: 41-43

Query yang bermasalah:
```php
$data['asset_by_condition'] = Asset::selectRaw('condition, COUNT(*) as count')
    ->groupBy('condition')
    ->get();
```

## Perbaikan yang Dilakukan

### 1. **Menggunakan DB::table() dengan DB::raw()**
Mengubah query untuk menggunakan `DB::table()` dan `DB::raw()` dengan backticks untuk escape reserved keyword:

**Sebelum:**
```php
$data['asset_by_condition'] = Asset::selectRaw('condition, COUNT(*) as count')
    ->groupBy('condition')
    ->get();
```

**Sesudah:**
```php
$data['asset_by_condition'] = \DB::table('assets')
    ->select(\DB::raw('`condition`, COUNT(*) as count'))
    ->groupBy(\DB::raw('`condition`'))
    ->get();
```

### 2. **Menambahkan Sample Data Assets**
Menambahkan 4 sample assets ke `DatabaseSeeder.php` untuk testing:
- Laptop Dell Latitude 5420 (condition: good)
- Monitor LG 27 inch (condition: good)
- Meja Kerja Kayu Jati (condition: acceptable)
- Printer HP LaserJet (condition: poor, status: maintenance)

## Penjelasan Teknis

### Reserved Keywords di MySQL
Beberapa kata adalah reserved keywords di MySQL yang tidak bisa digunakan langsung sebagai nama kolom tanpa escaping:
- `condition`
- `order`
- `group`
- `select`
- `where`
- dll.

### Cara Mengatasi Reserved Keywords

#### Metode 1: Backticks (`)
```sql
SELECT `condition`, COUNT(*) FROM assets GROUP BY `condition`
```

#### Metode 2: Menggunakan DB::raw() di Laravel
```php
\DB::table('assets')
    ->select(\DB::raw('`condition`, COUNT(*) as count'))
    ->groupBy(\DB::raw('`condition`'))
    ->get();
```

#### Metode 3: Eloquent where() - Otomatis Safe
```php
Asset::where('condition', 'good')->count(); // Aman, Laravel auto-escape
```

## Best Practices

1. **Hindari Reserved Keywords** sebagai nama kolom jika memungkinkan
2. **Gunakan Backticks** saat menggunakan raw SQL
3. **Gunakan DB::raw()** untuk query kompleks dengan reserved keywords
4. **Eloquent Methods** (where, select, dll) sudah otomatis safe dari reserved keywords

## Testing

Setelah perbaikan, jalankan:
```bash
php artisan migrate:fresh --seed
```

Kemudian akses dashboard untuk memastikan:
- ✅ Chart "Asset by Condition" muncul tanpa error
- ✅ Data assets tampil dengan benar
- ✅ Statistik dashboard berfungsi normal

## File yang Diubah

1. ✅ `app/Http/Controllers/DashboardController.php` - Fixed SQL query
2. ✅ `database/seeders/DatabaseSeeder.php` - Added sample assets

## Kredensial Login

**Email:** `superadmin@aset-imc.local`  
**Password:** `password123`
