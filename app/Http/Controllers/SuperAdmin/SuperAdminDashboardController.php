<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalAdmins'      => Schema::hasTable('admin_users') ? DB::table('admin_users')->count() : 0,
            'activeAdmins'     => Schema::hasTable('admin_users') ? DB::table('admin_users')->where('status', 'active')->count() : 0,
            'totalUsers'       => Schema::hasTable('users') ? DB::table('users')->count() : 0,
            'totalGaushalas'   => Schema::hasTable('gaushalas') ? DB::table('gaushalas')->count() : 0,
            'totalCases'       => Schema::hasTable('emergency_cases') ? DB::table('emergency_cases')->count() : 0,
            'totalNewsNotices' => Schema::hasTable('news_notices') ? DB::table('news_notices')->count() : 0,
            'totalRoles'       => Schema::hasTable('roles') ? DB::table('roles')->count() : 0,
            'totalPermissions' => Schema::hasTable('permissions') ? DB::table('permissions')->count() : 0,
        ];

        return view('superadmin.dashboard.index', $data);
    }
}