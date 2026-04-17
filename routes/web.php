<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\GaushalaController;
use App\Http\Controllers\Admin\AdminReportCaseController;
use App\Http\Controllers\Admin\NewsNoticeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['admin.auth'])->prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/export', [AdminUserController::class, 'export'])->name('export');
        Route::get('/{id}', [AdminUserController::class, 'show'])->name('show');
        Route::get('/{id}/addresses', [AdminUserController::class, 'addresses'])->name('addresses');
    });

    /*
    |--------------------------------------------------------------------------
    | Gaushalas
    |--------------------------------------------------------------------------
    */
    Route::prefix('gaushalas')->name('gaushalas.')->group(function () {
        Route::get('/', [GaushalaController::class, 'index'])->name('index');
        Route::get('/create', [GaushalaController::class, 'create'])->name('create');
        Route::post('/store', [GaushalaController::class, 'store'])->name('store');
        Route::get('/{id}', [GaushalaController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Report Cases
    |--------------------------------------------------------------------------
    */
    Route::prefix('report-cases')->name('report-cases.')->group(function () {
        Route::get('/', [AdminReportCaseController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminReportCaseController::class, 'show'])->name('show');
        Route::post('/{id}/status', [AdminReportCaseController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/assign-handler', [AdminReportCaseController::class, 'assignHandler'])->name('assign-handler');
    });

    /*
    |--------------------------------------------------------------------------
    | News & Notices
    |--------------------------------------------------------------------------
    */
    Route::prefix('news-notices')->name('news-notices.')->group(function () {
        Route::get('/', [NewsNoticeController::class, 'index'])->name('index');
        Route::get('/create', [NewsNoticeController::class, 'create'])->name('create');
        Route::post('/store', [NewsNoticeController::class, 'store'])->name('store');
        Route::put('/{id}', [NewsNoticeController::class, 'update'])->name('update');
        Route::delete('/{id}', [NewsNoticeController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['admin.auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    });