<?php

use App\Http\Controllers\AuthController;
use App\Interfaces\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [HealthController::class, 'check']);

// Authentication routes (rate limited)
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Account routes
    Route::apiResource('accounts', \App\Http\Controllers\AccountController::class);
    Route::get('/accounts/{id}/balance', [\App\Http\Controllers\AccountController::class, 'balance']);
    Route::post('/accounts/{id}/lend', [\App\Http\Controllers\AccountController::class, 'lend']);
    Route::get('/accounts/{id}/loans', [\App\Http\Controllers\AccountController::class, 'loans']);
    Route::post('/accounts/{id}/repay', [\App\Http\Controllers\AccountController::class, 'repay']);
});
