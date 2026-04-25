<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\EmergencyCaseController;
use App\Http\Controllers\Api\UserController;

Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | User Profile APIs
    |--------------------------------------------------------------------------
    */
    Route::get('/user-profile', [UserController::class, 'profile']);
    Route::put('/user-update-profile', [UserController::class, 'update']);
    Route::patch('/user-profile', [UserController::class, 'update']);
    Route::delete('/delete-user', [UserController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | User Address APIs
    |--------------------------------------------------------------------------
    */

    Route::get('/user-addresses', [UserAddressController::class, 'index']);
    Route::post('/save-user-address', [UserAddressController::class, 'store']);
    Route::get('/user-addresses/{id}', [UserAddressController::class, 'show']);
    Route::put('/user-edit-address/{id}', [UserAddressController::class, 'update']);
    Route::delete('/user-delete-addresses/{id}', [UserAddressController::class, 'destroy']);
    
    /*
    |--------------------------------------------------------------------------
    | Emergency Case APIs
    |--------------------------------------------------------------------------
    */

    Route::get('/get-emergency-cases', [EmergencyCaseController::class, 'index']);
    Route::post('/emergency-cases', [EmergencyCaseController::class, 'store']);
    Route::get('/emergency-cases/{id}', [EmergencyCaseController::class, 'show']);


    Route::post('/emergency-cases/{id}/accept', [EmergencyCaseController::class, 'accept']);
    Route::post('/emergency-cases/{id}/status', [EmergencyCaseController::class, 'updateStatus']);
    Route::post('/emergency-cases/{id}/request-backup', [EmergencyCaseController::class, 'requestBackup']);
    Route::post('/emergency-cases/{id}/resolve', [EmergencyCaseController::class, 'resolve']);
    Route::post('/emergency-cases/{id}/close', [EmergencyCaseController::class, 'close']);
});