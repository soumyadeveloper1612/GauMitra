<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = Schema::hasTable('users') ? DB::table('users')->count() : 0;

        $totalAdmins = Schema::hasTable('admin_users')
            ? DB::table('admin_users')->count()
            : 0;

        $activeAdmins = Schema::hasTable('admin_users')
            ? DB::table('admin_users')->where('status', 'active')->count()
            : 0;

        $activeSessions = Schema::hasTable('sessions')
            ? DB::table('sessions')->count()
            : 0;

        $totalGaushalas = Schema::hasTable('gaushalas')
            ? DB::table('gaushalas')->count()
            : 0;

        $totalReports = 0;
        $pendingReports = 0;
        $activeRescueReports = 0;
        $criticalReports = 0;
        $resolvedToday = 0;
        $falseReports = 0;
        $caseTypeStats = collect();
        $statusStats = collect();
        $recentReports = collect();

        if (Schema::hasTable('emergency_cases')) {
            $totalReports = DB::table('emergency_cases')->count();

            $pendingReports = DB::table('emergency_cases')
                ->whereIn('status', ['reported', 'alerted'])
                ->count();

            $activeRescueReports = DB::table('emergency_cases')
                ->whereIn('status', [
                    'accepted',
                    'en_route',
                    'reached_site',
                    'rescue_in_progress',
                    'needs_backup',
                    'treatment_started',
                    'shifted_to_gaushala',
                    'escalated',
                ])
                ->count();

            $criticalReports = DB::table('emergency_cases')
                ->where(function ($q) {
                    $q->where('severity', 'critical')
                        ->orWhere(function ($sub) {
                            $sub->where('case_type', 'accident')
                                ->whereIn('status', [
                                    'reported',
                                    'alerted',
                                    'accepted',
                                    'en_route',
                                    'reached_site',
                                    'rescue_in_progress',
                                    'needs_backup',
                                    'escalated',
                                ]);
                        });
                })
                ->count();

            $resolvedToday = DB::table('emergency_cases')
                ->whereDate('resolved_at', Carbon::today())
                ->count();

            $falseReports = DB::table('emergency_cases')
                ->where('status', 'false_report')
                ->count();

            $caseTypeStats = DB::table('emergency_cases')
                ->select('case_type', DB::raw('COUNT(*) as total'))
                ->groupBy('case_type')
                ->orderByDesc('total')
                ->get();

            $statusStats = DB::table('emergency_cases')
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->orderByDesc('total')
                ->get();

            $recentReports = DB::table('emergency_cases as ec')
                ->leftJoin('users as reporter', 'reporter.id', '=', 'ec.reporter_id')
                ->leftJoin('users as handler', 'handler.id', '=', 'ec.current_handler_id')
                ->select(
                    'ec.id',
                    'ec.case_uid',
                    'ec.case_type',
                    'ec.severity',
                    'ec.status',
                    'ec.full_address',
                    'ec.district',
                    'ec.created_at',
                    'reporter.name as reporter_name',
                    'reporter.mobile as reporter_mobile',
                    'handler.name as handler_name'
                )
                ->latest('ec.id')
                ->take(8)
                ->get();
        }

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalAdmins',
            'activeAdmins',
            'activeSessions',
            'totalGaushalas',
            'totalReports',
            'pendingReports',
            'activeRescueReports',
            'criticalReports',
            'resolvedToday',
            'falseReports',
            'caseTypeStats',
            'statusStats',
            'recentReports'
        ));
    }
}