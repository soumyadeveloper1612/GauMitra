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
use App\Http\Controllers\Admin\SidebarMenuController;
use App\Http\Controllers\Admin\MenuAccessController;
use App\Http\Controllers\Admin\AnimalTreatmentGuideController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('website.home');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware(['admin.auth'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | App Users
        |--------------------------------------------------------------------------
        */
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])
                ->middleware('admin.permission:users.view')
                ->name('index');

            Route::get('/export', [UserController::class, 'export'])
                ->middleware('admin.permission:users.view')
                ->name('export');

            Route::get('/{id}/addresses', [UserController::class, 'addresses'])
                ->middleware('admin.permission:users.view')
                ->name('addresses');

            Route::get('/{id}', [UserController::class, 'show'])
                ->middleware('admin.permission:users.view')
                ->name('show');
        });

        /*
        |--------------------------------------------------------------------------
        | Gaushala
        |--------------------------------------------------------------------------
        */
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

      
        /*
        |--------------------------------------------------------------------------
        | Report Cases
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | News & Notices
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Admin Management
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Sidebar Menus
        |--------------------------------------------------------------------------
        */
        Route::prefix('sidebar-menus')->name('sidebar-menus.')->middleware('super.admin')->group(function () {
            Route::get('/', [SidebarMenuController::class, 'index'])->name('index');
            Route::get('/create', [SidebarMenuController::class, 'create'])->name('create');
            Route::post('/store', [SidebarMenuController::class, 'store'])->name('store');
            Route::get('/{sidebar_menu}/edit', [SidebarMenuController::class, 'edit'])->name('edit');
            Route::put('/{sidebar_menu}', [SidebarMenuController::class, 'update'])->name('update');
            Route::delete('/{sidebar_menu}', [SidebarMenuController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/preview', [NotificationController::class, 'preview'])->name('preview');
            Route::post('/send', [NotificationController::class, 'send'])->name('send');

            // New AJAX routes
            Route::get('/address-options', [NotificationController::class, 'addressOptions'])->name('address-options');
            Route::get('/search-users', [NotificationController::class, 'searchUsers'])->name('search-users');
        });
    });
});

Route::prefix('admin')->name('admin.')->middleware(['admin.auth'])->group(function () {

    Route::prefix('menu-access')->name('menu-access.')->middleware('super.admin')->group(function () {
        Route::get('/', [MenuAccessController::class, 'index'])->name('index');
        Route::get('/{admin}/edit', [MenuAccessController::class, 'edit'])->name('edit');
        Route::put('/{admin}', [MenuAccessController::class, 'update'])->name('update');
    });

});


Route::get('/terms-and-conditions', [PageController::class, 'termsAndConditions'])
    ->name('terms.conditions');



Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    Route::get('/animal-treatment-guides', [AnimalTreatmentGuideController::class, 'index'])->name('animal-treatment-guides.index');
    Route::get('/animal-treatment-guides/create', [AnimalTreatmentGuideController::class, 'create'])->name('animal-treatment-guides.create');
    Route::post('/animal-treatment-guides/store', [AnimalTreatmentGuideController::class, 'store'])->name('animal-treatment-guides.store');
    Route::put('/animal-treatment-guides/update/{id}', [AnimalTreatmentGuideController::class, 'update'])->name('animal-treatment-guides.update');
    Route::delete('/animal-treatment-guides/delete/{id}', [AnimalTreatmentGuideController::class, 'destroy'])->name('animal-treatment-guides.destroy');
});

Route::prefix('super-admin')->name('superadmin.')->middleware(['admin.auth', 'super.admin'])->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
});