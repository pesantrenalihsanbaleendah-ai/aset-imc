# 👨‍💻 ASET IMC - Development Guide

## Getting Started with Development

Panduan ini untuk developer yang akan melanjutkan development ASET IMC.

---

## 📋 Prerequisites

Sebelum mulai development, pastikan sudah:

1. ✅ Baca `QUICK_START.md` untuk setup awal
2. ✅ Baca `DATABASE.md` untuk understand struktur database
3. ✅ Baca `API_TESTING.md` untuk understand routes
4. ✅ Baca `IMPLEMENTATION.md` untuk checklist fitur

---

## 🏗️ Project Architecture

### Folder Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php         (✅ Done)
│   │   ├── AssetController.php             (⏳ Scaffold)
│   │   ├── LoanController.php              (⏳ Scaffold)
│   │   └── ... (other controllers)
│   └── Middleware/
│       ├── CheckRole.php                  (✅ Done)
│       └── CheckPermission.php            (✅ Done)
├── Models/
│   ├── User.php                           (✅ Done)
│   ├── Asset.php                          (✅ Done)
│   ├── Loan.php                           (✅ Done)
│   └── ... (11 models total)
└── Traits/
    └── Auditable.php                      (✅ Done)

database/
├── migrations/
│   ├── 0001_01_01_*.php
│   ├── 2025_12_09_*.php                   (✅ 12 migrations)
│   └── (Add new migrations here)
├── seeders/
│   ├── DatabaseSeeder.php                 (✅ Done)
│   └── (Add specialized seeders here)
└── factories/
    └── (Add factories for testing)

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php                  (✅ Done)
│   ├── dashboard/
│   │   └── index.blade.php                (✅ Done)
│   ├── assets/                            (⏳ To be created)
│   ├── loans/                             (⏳ To be created)
│   └── ... (other feature views)
│
└── (CSS & JS files)

routes/
├── web.php                                (✅ Done)
└── auth.php                               (✅ Done)
```

---

## 🎯 Development Workflow

### Step 1: Create Migration
```bash
php artisan make:migration create_table_name
```

Edit file di `database/migrations/` untuk define schema:
```php
Schema::create('table_name', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

### Step 2: Create Model
```bash
php artisan make:model ModelName --migration
```

Edit `app/Models/ModelName.php` untuk define relationships:
```php
class Asset extends Model
{
    protected $fillable = ['name', 'code', 'category_id'];
    
    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }
}
```

### Step 3: Create Controller
```bash
php artisan make:controller ControllerName --resource
```

Edit `app/Http/Controllers/ControllerName.php` dengan methods:
```php
public function index() { ... }
public function create() { ... }
public function store(Request $request) { ... }
public function edit($id) { ... }
public function update(Request $request, $id) { ... }
public function destroy($id) { ... }
```

### Step 4: Create Views
Buat di `resources/views/feature_name/`:
```
views/
├── index.blade.php        (List view dengan pagination)
├── create.blade.php       (Create form)
├── edit.blade.php         (Edit form)
└── show.blade.php         (Detail view)
```

### Step 5: Define Routes
Edit `routes/web.php`:
```php
Route::middleware('auth')->group(function () {
    Route::resource('assets', AssetController::class);
    
    // Custom routes
    Route::post('assets/{id}/approve', [AssetController::class, 'approve'])->name('assets.approve');
});
```

### Step 6: Test
```bash
php artisan tinker
>>> // Test your code

# atau buat test file
php artisan make:test FeatureTest
```

---

## 🔐 Authorization Pattern

### Using Middleware (Route Level)
```php
Route::resource('assets', AssetController::class)
    ->middleware('checkPermission:asset.view');
```

### Using Gate (Method Level)
```php
public function destroy($id)
{
    $this->authorize('asset.delete');  // Throws 403 if not authorized
    // ... delete logic
}
```

### Using Helper Method (View Level)
```blade
@if(auth()->user()->hasPermission('asset.create'))
    <a href="{{ route('assets.create') }}" class="btn btn-primary">
        Create Asset
    </a>
@endif
```

### Using Gate Facade
```php
if (Gate::denies('asset.delete')) {
    abort(403);
}
```

---

## 📝 Naming Conventions

### Model Names
```php
User, Asset, AssetCategory, Loan, Maintenance
// Singular, PascalCase
```

### Table Names
```sql
users, assets, asset_categories, loans, maintenances
// Plural, snake_case
```

### Controller Names
```php
UserController, AssetController, LoanController
// Singular resource name + "Controller", PascalCase
```

### View Files
```
users/index.blade.php          (List view)
users/create.blade.php         (Create form)
users/edit.blade.php           (Edit form)
users/show.blade.php           (Detail view)
// Feature name (plural), action name (lowercase)
```

### Method Names
```php
// Restful methods
index(), create(), store(), show(), edit(), update(), destroy()

// Custom methods (snake_case)
public function approve_loan() { ... }
public function export_pdf() { ... }
```

### Variable Names
```php
$user, $asset, $assetId
// camelCase untuk variables
// Use descriptive names
```

### Route Names
```php
Route::get('/assets', [...]) ->name('assets.index');
Route::get('/assets/{id}', [...]) ->name('assets.show');
Route::post('/assets', [...]) ->name('assets.store');
// resource_name.method_name
```

---

## 🎨 View Template Structure

### Master Layout (`resources/views/layouts/app.blade.php`)
```blade
<!DOCTYPE html>
<html>
<head>
    {{-- Meta tags --}}
    <title>@yield('title', 'ASET IMC')</title>
    {{-- CSS files --}}
</head>
<body>
    <div class="wrapper">
        @include('layouts.sidebar')
        <div class="main-content">
            @include('layouts.topbar')
            <main>
                @if($errors->any())
                    @include('components.errors')
                @endif
                @if(session('success'))
                    @include('components.success')
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
```

### Feature Index View
```blade
@extends('layouts.app')

@section('title', 'Assets')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Asset List</h2>
        </div>
        <div class="col-md-6 text-right">
            @if(auth()->user()->hasPermission('asset.create'))
                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                    <i class="icon"></i> Create Asset
                </a>
            @endif
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
                <tr>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name }}</td>
                    <td>{{ $asset->location->name }}</td>
                    <td>
                        <span class="badge bg-{{ $asset->status === 'active' ? 'success' : 'warning' }}">
                            {{ $asset->status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-info">View</a>
                        @if(auth()->user()->hasPermission('asset.edit'))
                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-warning">Edit</a>
                        @endif
                        @if(auth()->user()->hasPermission('asset.delete'))
                            <form method="POST" action="{{ route('assets.destroy', $asset) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Sure?')">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No assets found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $assets->links() }}
</div>
@endsection
```

### Feature Create/Edit View
```blade
@extends('layouts.app')

@section('title', isset($asset) ? 'Edit Asset' : 'Create Asset')

@section('content')
<div class="container">
    <form method="POST" action="{{ isset($asset) ? route('assets.update', $asset) : route('assets.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($asset))
            @method('PUT')
        @endif

        <div class="form-group mb-3">
            <label for="name">Asset Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ $asset->name ?? old('name') }}" required>
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="category_id">Category</label>
            <select class="form-control @error('category_id') is-invalid @enderror" 
                    id="category_id" name="category_id" required>
                <option value="">Choose Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                            {{ ($asset->category_id ?? old('category_id')) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-3">
            <button type="submit" class="btn btn-primary">
                {{ isset($asset) ? 'Update' : 'Create' }}
            </button>
            <a href="{{ route('assets.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
```

---

## 📊 Common Code Patterns

### Pagination
```php
public function index()
{
    $assets = Asset::paginate(15);
    return view('assets.index', compact('assets'));
}

// In view:
{{ $assets->links() }}
```

### Search & Filter
```php
public function index(Request $request)
{
    $query = Asset::query();
    
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('code', 'like', '%' . $request->search . '%');
    }
    
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    
    $assets = $query->paginate(15);
    return view('assets.index', compact('assets'));
}
```

### File Upload
```php
public function store(Request $request)
{
    $request->validate([
        'photo' => 'image|max:2048'
    ]);
    
    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('assets', 'public');
        $request->merge(['photo_path' => $path]);
    }
    
    Asset::create($request->all());
}
```

### Excel Export
```php
use PhpOffice\PhpSpreadsheet\Spreadsheet;

public function exportExcel()
{
    $assets = Asset::all();
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Code');
    
    $row = 2;
    foreach ($assets as $asset) {
        $sheet->setCellValue('A' . $row, $asset->name);
        $sheet->setCellValue('B' . $row, $asset->code);
        $row++;
    }
    
    // ... save to file
}
```

### PDF Generation
```php
use Barryvdh\DomPDF\Facade\Pdf;

public function exportPdf()
{
    $assets = Asset::all();
    
    $pdf = Pdf::loadView('reports.assets-pdf', compact('assets'));
    return $pdf->download('assets.pdf');
}
```

### Email Notification
```php
use Illuminate\Support\Facades\Mail;
use App\Mail\LoanApprovedMail;

Mail::to($user->email)->send(new LoanApprovedMail($loan));

// app/Mail/LoanApprovedMail.php
class LoanApprovedMail extends Mailable
{
    public function __construct(public Loan $loan) {}
    
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Loan Approved');
    }
    
    public function content(): Content
    {
        return new Content(view: 'emails.loan-approved');
    }
}
```

### API Resource (Future)
```php
// app/Http/Resources/AssetResource.php
class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'category' => $this->category->name,
            'created_at' => $this->created_at->format('Y-m-d')
        ];
    }
}

// Usage in controller
public function index()
{
    return AssetResource::collection(Asset::all());
}
```

---

## 🧪 Testing Best Practices

### Unit Test
```php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Asset;

class AssetTest extends TestCase
{
    public function test_asset_creation()
    {
        $asset = Asset::create([
            'name' => 'Laptop',
            'code' => 'AST-001',
            'category_id' => 1
        ]);
        
        $this->assertDatabaseHas('assets', ['code' => 'AST-001']);
    }
}
```

### Feature Test
```php
namespace Tests\Feature;

class AssetControllerTest extends TestCase
{
    public function test_can_view_assets()
    {
        $this->actingAs(User::factory()->create())
             ->get('/assets')
             ->assertOk()
             ->assertViewHas('assets');
    }
    
    public function test_unauthorized_user_cannot_create()
    {
        $this->actingAs(User::factory()->create())
             ->post('/assets', ['name' => 'Test'])
             ->assertForbidden();
    }
}
```

---

## 🚀 Performance Optimization

### Database Queries
```php
// ❌ Bad (N+1 queries)
$assets = Asset::all();
foreach ($assets as $asset) {
    echo $asset->category->name;
}

// ✅ Good (Eager loading)
$assets = Asset::with('category')->get();
foreach ($assets as $asset) {
    echo $asset->category->name;
}

// ✅ Better (Select only needed columns)
$assets = Asset::select('id', 'name', 'category_id')
              ->with('category:id,name')
              ->get();
```

### Caching
```php
// Cache query result
$assets = Cache::remember('assets', 3600, function () {
    return Asset::all();
});

// Cache busting
Cache::forget('assets');
```

### Indexing
```php
// Create migration with index
$table->index('category_id');
$table->index('status');
$table->unique('code');
```

---

## 🔧 Debugging Tools

### Laravel Debugbar
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Tinker Shell
```bash
php artisan tinker

>>> $asset = Asset::find(1);
>>> $asset->category;
>>> Asset::where('status', 'active')->count();
```

### Logs
```php
// Log messages
Log::info('Asset created', ['asset_id' => $asset->id]);
Log::error('Something went wrong', ['error' => $e->getMessage()]);

// View logs
tail -f storage/logs/laravel.log
```

### Database Profiler
```php
// Enable query logging
DB::listen(function ($query) {
    Log::info($query->sql, $query->bindings);
});
```

---

## 📚 Important Classes & Interfaces

### Base Classes
```php
Model              // Base Eloquent model
Controller         // Base controller
Request            // Form request
Middleware         // Middleware
Mailable           // Email class
Job                // Queue job
```

### Facades
```php
Auth::user()       // Get current user
Gate::authorize()  // Authorization check
Cache::get()       // Get from cache
Mail::to()         // Send email
DB::table()        // Query builder
```

### Collections
```php
collect($array)->map()
collect($array)->filter()
collect($array)->pluck()
collect($array)->groupBy()
```

---

## 🎯 Next Development Tasks

### Immediate (Priority 1)
1. ✅ Create Asset CRUD views
2. ✅ Add photo upload functionality
3. ✅ Implement QR code generation
4. ✅ Build asset list with search

### Short-term (Priority 2)
1. Implement Loan management workflow
2. Add email notifications
3. Create maintenance module
4. Build reports system

### Medium-term (Priority 3)
1. QR scanning feature
2. Dark mode UI
3. API endpoints
4. Advanced analytics

### Long-term (Priority 4)
1. PWA features
2. Mobile app
3. Advanced security
4. Integration with other systems

---

## 📞 Quick Reference

### Commands
```bash
php artisan migrate              # Run migrations
php artisan seed                 # Run seeders
php artisan make:model ModelName # Create model
php artisan serve                # Start server
php artisan tinker              # Interactive shell
php artisan route:list          # Show all routes
php artisan cache:clear         # Clear cache
```

### File Paths
```
Routes:         routes/web.php
Models:         app/Models/
Controllers:    app/Http/Controllers/
Views:          resources/views/
Migrations:     database/migrations/
Seeders:        database/seeders/
Tests:          tests/
Logs:           storage/logs/laravel.log
```

### Key URLs (Local)
```
http://127.0.0.1:8000           Dashboard
http://127.0.0.1:8000/assets    Assets list
http://127.0.0.1:8000/loans     Loans list
http://127.0.0.1/phpmyadmin     Database management
```

---

**Last Updated:** December 9, 2025
**Version:** 1.0
**Created for:** ASET IMC Development Team
