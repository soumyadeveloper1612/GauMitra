<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GaushalaController;
use App\Http\Controllers\Admin\AdminReportCaseController;
use App\Http\Controllers\Admin\NewsNoticeController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['admin.auth'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');


        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])
                ->middleware('admin.permission:users.view')
                ->name('index');

            Route::get('/{id}', [UserController::class, 'show'])
                ->middleware('admin.permission:users.view')
                ->name('show');
        });


Route::prefix('super-admin')->name('superadmin.')->middleware(['admin.auth'])->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
});

        Route::prefix('gaushalas')->name('gaushalas.')->group(function () {
            Route::get('/', [GaushalaController::class, 'index'])
                ->middleware('admin.permission:gaushala.view')
                ->name('index');

            Route::get('/create', [GaushalaController::class, 'create'])
                ->middleware('admin.permission:gaushala.create')
                ->name('create');

            Route::post('/store', [GaushalaController::class, 'store'])
                ->middleware('admin.permission:gaushala.create')
                ->name('store');

            Route::get('/{id}', [GaushalaController::class, 'show'])
                ->middleware('admin.permission:gaushala.view')
                ->name('show');
        });

        Route::prefix('report-cases')->name('report-cases.')->group(function () {
            Route::get('/', [AdminReportCaseController::class, 'index'])
                ->middleware('admin.permission:report_cases.view')
                ->name('index');

            Route::get('/{id}', [AdminReportCaseController::class, 'show'])
                ->middleware('admin.permission:report_cases.view')
                ->name('show');

            Route::post('/{id}/status', [AdminReportCaseController::class, 'updateStatus'])
                ->middleware('admin.permission:report_cases.update')
                ->name('update-status');

            Route::post('/{id}/assign-handler', [AdminReportCaseController::class, 'assignHandler'])
                ->middleware('admin.permission:report_cases.assign')
                ->name('assign-handler');
        });

        Route::prefix('news-notices')->name('news-notices.')->group(function () {
            Route::get('/', [NewsNoticeController::class, 'index'])
                ->middleware('admin.permission:news_notice.view')
                ->name('index');

            Route::get('/create', [NewsNoticeController::class, 'create'])
                ->middleware('admin.permission:news_notice.create')
                ->name('create');

            Route::post('/store', [NewsNoticeController::class, 'store'])
                ->middleware('admin.permission:news_notice.create')
                ->name('store');

            Route::put('/{id}', [NewsNoticeController::class, 'update'])
                ->middleware('admin.permission:news_notice.edit')
                ->name('update');

            Route::delete('/{id}', [NewsNoticeController::class, 'destroy'])
                ->middleware('admin.permission:news_notice.delete')
                ->name('destroy');
        });

        Route::prefix('admins')->name('admins.')->middleware('super.admin')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/store', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{admin}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{admin}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{admin}', [AdminUserController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('roles')->name('roles.')->middleware('super.admin')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/store', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });
});

Route::prefix('super-admin')->name('superadmin.')->middleware(['admin.auth', 'super.admin'])->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
});