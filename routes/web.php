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
});

Route::prefix('ncx')->group(function () {
    Route::get('/ncx-status', [NcxController::class, 'ncxStatus']);
});

Route::resource('documents', DocumentController::class);
Route::get('/documents/{slug}', [DocumentController::class, 'show'])->name('documents.show');
