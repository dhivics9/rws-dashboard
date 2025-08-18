<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NcxController;
use App\Http\Controllers\RevenueAnalyticsController;
use App\Http\Controllers\TargetAnalyticsController;
use App\Models\NcxApi;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index']);

Route::get("/login", [AuthController::class, 'loginAuth']);

Route::prefix('target-analytics')->group(function () {
    Route::get('/regional-report', [TargetAnalyticsController::class, 'regionalPerformance']);
    Route::get('/product-summary', [TargetAnalyticsController::class, 'productSummary']);
    Route::get('/revenue-table', [TargetAnalyticsController::class, 'revenueTable']);
});

Route::prefix('revenue-analytics')->group(function () {
    Route::get('/revenue-data', [RevenueAnalyticsController::class, 'revenueData']);
    Route::get('/product-summary', [TargetAnalyticsController::class, 'productSummary']);
    Route::get('/revenue-table', [TargetAnalyticsController::class, 'revenueTable']);
});

Route::prefix('ncx')->group(function () {
    Route::get('/ncx-status', [NcxController::class, 'ncxStatus']);
});
