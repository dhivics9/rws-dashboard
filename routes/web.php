<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\NcxController;
use App\Http\Controllers\RevenueAnalyticsController;
use App\Http\Controllers\TargetAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);

Route::get("/login", [AuthController::class, 'loginAuth']);

Route::prefix('target-analytics')->group(function () {
    Route::get('/regional-report', [TargetAnalyticsController::class, 'regionalPerformance']);
    Route::get('/product-summary', [TargetAnalyticsController::class, 'productSummary']);
    Route::get('/revenue-table', [TargetAnalyticsController::class, 'revenueTable']);
    Route::get('/import', [TargetAnalyticsController::class, 'getImport'])->name('target-analytics.import.form');
    Route::post('/import', [TargetAnalyticsController::class, 'postImport'])->name('target-analytics.import');
});

Route::prefix('revenue-analytics')->group(function () {
    Route::get('/revenue-data', [RevenueAnalyticsController::class, 'revenueData']);
    Route::get('/product-summary', [TargetAnalyticsController::class, 'productSummary']);
    Route::get('/import', [RevenueAnalyticsController::class, 'getImport']);
    Route::post('/import', [RevenueAnalyticsController::class, 'postImport'])->name("revenue-analytics.import");
});

Route::prefix('ncx')->group(function () {
    Route::get('/ncx-status', action: [NcxController::class, 'ncxStatus']);
    Route::get('/import', [NcxController::class, 'getImport']);
    Route::post('/import', [NcxController::class, 'postImport'])->name("ncx-status.import");
});

Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
Route::get('/documents/{slug}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documents/{slug}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
Route::put('/documents/{slug}', [DocumentController::class, 'update'])->name('documents.update');
Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
