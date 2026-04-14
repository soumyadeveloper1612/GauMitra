<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\GaushalaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/export', [AdminUserController::class, 'export'])->name('admin.users.export');
    Route::get('/admin/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('/admin/users/{id}/addresses', [AdminUserController::class, 'addresses'])->name('admin.users.addresses');

    Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'index'])
        ->middleware('role:superadmin')
        ->name('superadmin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/gaushalas', [GaushalaController::class, 'index'])->name('gaushalas.index');
    Route::get('/gaushalas/create', [GaushalaController::class, 'create'])->name('gaushalas.create');
    Route::post('/gaushalas/store', [GaushalaController::class, 'store'])->name('gaushalas.store');
    Route::get('/gaushalas/{id}', [GaushalaController::class, 'show'])->name('gaushalas.show');
});