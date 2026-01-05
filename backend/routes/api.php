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
});
