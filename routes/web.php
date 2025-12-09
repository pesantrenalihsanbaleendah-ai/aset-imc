<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Asset Management
    Route::prefix('assets')->group(function () {
        Route::resource('/', AssetController::class)->names('assets');
        Route::post('/{asset}/qr', [AssetController::class, 'generateQR'])->name('assets.qr');
        Route::post('/import', [AssetController::class, 'import'])->name('assets.import');
    });

    // Asset Categories
    Route::resource('categories', AssetCategoryController::class)->names('categories');

    // Locations
    Route::resource('locations', LocationController::class)->names('locations');

    // Loans
    Route::prefix('loans')->group(function () {
        Route::resource('/', LoanController::class)->names('loans');
        Route::post('/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
        Route::get('/{loan}/print', [LoanController::class, 'print'])->name('loans.print');
    });

    // Maintenance
    Route::prefix('maintenance')->group(function () {
        Route::resource('/', MaintenanceController::class)->names('maintenance');
        Route::post('/{maintenance}/approve', [MaintenanceController::class, 'approve'])->name('maintenance.approve');
        Route::post('/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/asset', [ReportController::class, 'assetReport'])->name('reports.asset');
        Route::get('/loan', [ReportController::class, 'loanReport'])->name('reports.loan');
        Route::get('/maintenance', [ReportController::class, 'maintenanceReport'])->name('reports.maintenance');
        Route::get('/depreciation', [ReportController::class, 'depreciationReport'])->name('reports.depreciation');
        Route::post('/export', [ReportController::class, 'export'])->name('reports.export');
    });
});

require __DIR__.'/auth.php';
