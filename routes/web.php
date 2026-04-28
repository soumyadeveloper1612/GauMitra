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

use App\Http\Controllers\Admin\ReportTypeController;
use App\Http\Controllers\Admin\CowConditionController;
use App\Http\Controllers\Admin\AnimalTypeController;

use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\HomeController;

/*
|--------------------------------------------------------------------------
| Website Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('website.home');

Route::get('/terms-and-conditions', [PageController::class, 'termsAndConditions'])
    ->name('terms.conditions');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Admin Auth
    |--------------------------------------------------------------------------
    */

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

            Route::put('/update/{id}', [GaushalaController::class, 'update'])
                ->middleware('admin.permission:gaushala.create')
                ->name('update');

            Route::delete('/delete/{id}', [GaushalaController::class, 'destroy'])
                ->middleware('admin.permission:gaushala.create')
                ->name('destroy');

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
        | Report Types Master
        |--------------------------------------------------------------------------
        | Final route names:
        | admin.report-types.index
        | admin.report-types.create
        | admin.report-types.store
        | admin.report-types.update
        | admin.report-types.destroy
        */

        Route::prefix('report-types')->name('report-types.')->group(function () {
            Route::get('/', [ReportTypeController::class, 'index'])->name('index');
            Route::get('/create', [ReportTypeController::class, 'create'])->name('create');
            Route::post('/store', [ReportTypeController::class, 'store'])->name('store');
            Route::put('/update/{id}', [ReportTypeController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [ReportTypeController::class, 'destroy'])->name('destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | Cow Conditions Master
        |--------------------------------------------------------------------------
        | Final route names:
        | admin.cow-conditions.index
        | admin.cow-conditions.create
        | admin.cow-conditions.store
        | admin.cow-conditions.update
        | admin.cow-conditions.destroy
        */

        Route::prefix('cow-conditions')->name('cow-conditions.')->group(function () {
            Route::get('/', [CowConditionController::class, 'index'])->name('index');
            Route::get('/create', [CowConditionController::class, 'create'])->name('create');
            Route::post('/store', [CowConditionController::class, 'store'])->name('store');
            Route::put('/update/{id}', [CowConditionController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [CowConditionController::class, 'destroy'])->name('destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | Animal Types Master
        |--------------------------------------------------------------------------
        | Final route names:
        | admin.animal-types.index
        | admin.animal-types.create
        | admin.animal-types.store
        | admin.animal-types.update
        | admin.animal-types.destroy
        */

        Route::prefix('animal-types')->name('animal-types.')->group(function () {
            Route::get('/', [AnimalTypeController::class, 'index'])->name('index');
            Route::get('/create', [AnimalTypeController::class, 'create'])->name('create');
            Route::post('/store', [AnimalTypeController::class, 'store'])->name('store');
            Route::put('/update/{id}', [AnimalTypeController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [AnimalTypeController::class, 'destroy'])->name('destroy');
        });


        /*
        |--------------------------------------------------------------------------
        | Animal Treatment Guides
        |--------------------------------------------------------------------------
        */

        Route::prefix('animal-treatment-guides')->name('animal-treatment-guides.')->group(function () {
            Route::get('/', [AnimalTreatmentGuideController::class, 'index'])->name('index');
            Route::get('/create', [AnimalTreatmentGuideController::class, 'create'])->name('create');
            Route::post('/store', [AnimalTreatmentGuideController::class, 'store'])->name('store');
            Route::put('/update/{id}', [AnimalTreatmentGuideController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [AnimalTreatmentGuideController::class, 'destroy'])->name('destroy');
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
        | Notifications
        |--------------------------------------------------------------------------
        */

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/preview', [NotificationController::class, 'preview'])->name('preview');
            Route::post('/send', [NotificationController::class, 'send'])->name('send');

            Route::get('/address-options', [NotificationController::class, 'addressOptions'])->name('address-options');
            Route::get('/search-users', [NotificationController::class, 'searchUsers'])->name('search-users');
        });


        /*
        |--------------------------------------------------------------------------
        | Admin Management - Super Admin Only
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


        /*
        |--------------------------------------------------------------------------
        | Roles - Super Admin Only
        |--------------------------------------------------------------------------
        */

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
        | Sidebar Menus - Super Admin Only
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


        /*
        |--------------------------------------------------------------------------
        | Menu Access - Super Admin Only
        |--------------------------------------------------------------------------
        */

        Route::prefix('menu-access')->name('menu-access.')->middleware('super.admin')->group(function () {
            Route::get('/', [MenuAccessController::class, 'index'])->name('index');
            Route::get('/{admin}/edit', [MenuAccessController::class, 'edit'])->name('edit');
            Route::put('/{admin}', [MenuAccessController::class, 'update'])->name('update');
        });
    });
});


/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('super-admin')
    ->name('superadmin.')
    ->middleware(['admin.auth', 'super.admin'])
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    });