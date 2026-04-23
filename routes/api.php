<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\UserLocationController;
use App\Http\Controllers\Api\EmergencyCaseController;


Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/save-user-address', [UserAddressController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/emergency-cases', [EmergencyCaseController::class, 'index']);
    Route::post('/emergency-cases', [EmergencyCaseController::class, 'store']);
    Route::get('/emergency-cases/{id}', [EmergencyCaseController::class, 'show']);

    Route::post('/emergency-cases/{id}/accept', [EmergencyCaseController::class, 'accept']);
    Route::post('/emergency-cases/{id}/status', [EmergencyCaseController::class, 'updateStatus']);
    Route::post('/emergency-cases/{id}/request-backup', [EmergencyCaseController::class, 'requestBackup']);
    Route::post('/emergency-cases/{id}/resolve', [EmergencyCaseController::class, 'resolve']);
    Route::post('/emergency-cases/{id}/close', [EmergencyCaseController::class, 'close']);
});
