<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Product routes
    Route::apiResource('products', ProductController::class);
    
    // Transaction routes
    Route::apiResource('transactions', TransactionController::class)->only([
        'index', 'store', 'show'
    ]);
    
    // Report routes
    Route::prefix('reports')->group(function () {
        Route::get('/inventory-summary', [ReportController::class, 'inventorySummary']);
        Route::get('/by-category', [ReportController::class, 'byCategory']);
        Route::get('/by-type', [ReportController::class, 'byType']);
    });
});