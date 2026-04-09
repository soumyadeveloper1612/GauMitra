<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;

Route::prefix('mobile-auth')->group(function () {
    Route::post('/send-otp', [MobileAuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [MobileAuthController::class, 'verifyOtp']);
    Route::post('/password-login', [MobileAuthController::class, 'passwordLogin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [MobileAuthController::class, 'me']);
        Route::post('/set-password', [MobileAuthController::class, 'setPassword']);
        Route::post('/logout', [MobileAuthController::class, 'logout']);
    });
});