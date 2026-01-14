<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('login', function () {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, request()->filled('remember'))) {
            request()->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    });
});

Route::post('logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Asset Management
    Route::resource('assets', AssetController::class);
    Route::post('/assets/{asset}/qr', [AssetController::class, 'generateQR'])->name('assets.qr');
    Route::post('/assets/import', [AssetController::class, 'import'])->name('assets.import');

    // Asset Categories
    Route::resource('categories', AssetCategoryController::class)->names('categories');

    // Locations
    Route::resource('locations', LocationController::class)->names('locations');

    // Loans
    Route::resource('loans', LoanController::class);
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::post('/loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
    Route::get('/loans/{loan}/print', [LoanController::class, 'print'])->name('loans.print');

    // Maintenance
    Route::resource('maintenance', MaintenanceController::class);
    Route::post('/maintenance/{maintenance}/approve', [MaintenanceController::class, 'approve'])->name('maintenance.approve');
    Route::post('/maintenance/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');


    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/asset', [ReportController::class, 'assetReport'])->name('reports.asset');
        Route::get('/loan', [ReportController::class, 'loanReport'])->name('reports.loan');
        Route::get('/maintenance', [ReportController::class, 'maintenanceReport'])->name('reports.maintenance');
        Route::get('/depreciation', [ReportController::class, 'depreciationReport'])->name('reports.depreciation');
        Route::post('/export', [ReportController::class, 'export'])->name('reports.export');
    });

    // Admin Routes (Super Admin Only)
    Route::prefix('admin')->group(function () {
        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('admin.users.index');
            Route::get('/create', [UserManagementController::class, 'create'])->name('admin.users.create');
            Route::post('/', [UserManagementController::class, 'store'])->name('admin.users.store');
            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
            Route::post('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('admin.users.toggle-status');
        });

        // Settings
        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('admin.settings.index');
            Route::put('/', [SettingController::class, 'update'])->name('admin.settings.update');
            Route::post('/test-whatsapp', [SettingController::class, 'testWhatsApp'])->name('admin.settings.test-whatsapp');
            Route::post('/reset', [SettingController::class, 'reset'])->name('admin.settings.reset');
        });
    });
});
