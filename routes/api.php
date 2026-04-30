<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\EmergencyCaseController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MasterDataController;


    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

    Route::get('/manage-animal-types', [MasterDataController::class, 'animalTypes']);
    Route::get('/manage-report-types', [MasterDataController::class, 'reportTypes']);
    Route::get('/manage-conditions', [MasterDataController::class, 'animalConditions']);
    Route::get('/emergency-case-options', [MasterDataController::class, 'emergencyCaseOptions']);


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

    Route::post('/emergency-cases', [EmergencyCaseController::class, 'store']);


    Route::post('/emergency-cases/{id}/accept', [EmergencyCaseController::class, 'acceptReport']);
    Route::post('/emergency-cases/{id}/reject', [EmergencyCaseController::class, 'rejectReport']);
    Route::post('/emergency-cases/{id}/status', [EmergencyCaseController::class, 'updateStatus']);
    Route::post('/emergency-cases/{id}/request-backup', [EmergencyCaseController::class, 'requestBackup']);
    Route::post('/emergency-cases/{id}/resolve', [EmergencyCaseController::class, 'resolve']);
    Route::post('/emergency-cases/{id}/close', [EmergencyCaseController::class, 'close']);

    Route::get('/my-emergency-cases', [EmergencyCaseController::class, 'myReports']);
    Route::get('/my-emergency-cases/{id}', [EmergencyCaseController::class, 'myReportDetails']);
    Route::get('/my-reported-emergency-cases', [EmergencyCaseController::class, 'myReportedCases']);
    Route::get('/address-wise-emergency-cases', [EmergencyCaseController::class, 'addressWiseCases']);
});

Route::get('/emergency-cases/area-wise', [EmergencyCaseController::class, 'areaWiseReports']);
Route::get('/emergency-cases/area-wise/{id}', [EmergencyCaseController::class, 'areaWiseReportDetails']);
Route::get('/all-emergency-cases', [EmergencyCaseController::class, 'allEmergencyCases']);

