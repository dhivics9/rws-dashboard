<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NcxController;
use App\Http\Controllers\RevenueAnalyticsController;
use App\Http\Controllers\TargetAnalyticsController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncController;

Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/login', [AuthController::class, 'loginAuth'])->name('login');
Route::post('/login', [AuthController::class, 'loginProcess']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


// Admin only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/register', [AuthController::class, 'registerAuth'])->name('register.form');
    Route::post('/admin/register', [AuthController::class, 'registerProcess'])->name('register.process');
});

// Inputter routes (CRUD operations)
Route::middleware(['auth', 'role:inputter,admin'])->group(function () {
    Route::prefix('target-analytics')->group(function () {
        Route::get('/import', [TargetAnalyticsController::class, 'getImport'])->name('target-analytics.import.form');
        Route::post('/import', [TargetAnalyticsController::class, 'postImport'])->name('target-analytics.import');
    });
    Route::prefix('revenue-analytics')->group(function () {
        Route::get('/import', [RevenueAnalyticsController::class, 'getImport']);
        Route::post('/import', [RevenueAnalyticsController::class, 'postImport'])->name("revenue-analytics.import");
    });
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{slug}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{slug}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::prefix('ncx')->group(function () {
        Route::get('/import', [NcxController::class, 'getImport']);
        Route::post('/import', [NcxController::class, 'postImport'])->name("ncx-status.import");
    });
});

// General user routes (view only)
Route::middleware(['auth'])->group(function () {
    Route::prefix('target-analytics')->group(function () {
        Route::get('/regional-report', [TargetAnalyticsController::class, 'regionalPerformance']);
        Route::get('/product-summary', [TargetAnalyticsController::class, 'productSummary']);
        Route::get('/revenue-table', [TargetAnalyticsController::class, 'revenueTable']);
    });
    Route::prefix('ncx')->group(function () {
        Route::get('/ncx-status', action: [NcxController::class, 'ncxStatus']);
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{slug}', [DocumentController::class, 'show'])->name('documents.show');
});

// Admin CRUD Users
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
});


Route::prefix('syncronize')->group(function () {
    Route::get('/', [SyncController::class, 'index'])->name('sync.index');
    Route::post('/run', [SyncController::class, 'sync'])->name('sync.run');
});
