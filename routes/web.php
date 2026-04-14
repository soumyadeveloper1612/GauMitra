<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\GaushalaController;
use App\Http\Controllers\Admin\AdminReportCaseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['admin.auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/addresses', [AdminUserController::class, 'addresses'])->name('users.addresses');

    Route::get('/gaushalas', [GaushalaController::class, 'index'])->name('gaushalas.index');
    Route::get('/gaushalas/create', [GaushalaController::class, 'create'])->name('gaushalas.create');
    Route::post('/gaushalas/store', [GaushalaController::class, 'store'])->name('gaushalas.store');
    Route::get('/gaushalas/{id}', [GaushalaController::class, 'show'])->name('gaushalas.show');
});

 Route::prefix('report-cases')->name('report-cases.')->group(function () {
            Route::get('/', [AdminReportCaseController::class, 'index'])->name('index');
            Route::get('/{id}', [AdminReportCaseController::class, 'show'])->name('show');
            Route::post('/{id}/status', [AdminReportCaseController::class, 'updateStatus'])->name('update-status');
        });

Route::middleware(['admin.auth', 'role:superadmin'])->get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'index'])
    ->name('superadmin.dashboard');