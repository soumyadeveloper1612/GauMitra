<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminReportCaseController extends Controller
{
    private array $statuses = [
        'reported',
        'alerted',
        'accepted',
        'en_route',
        'reached_site',
        'rescue_in_progress',
        'needs_backup',
        'treatment_started',
        'shifted_to_gaushala',
        'resolved',
        'closed',
        'duplicate_case',
        'false_report',
        'unable_to_locate',
        'cancelled',
        'escalated',
    ];

    public function index(Request $request)
    {
        $statuses = [
            'reported',
            'alerted',
            'accepted',
            'en_route',
            'reached_site',
            'rescue_in_progress',
            'needs_backup',
            'treatment_started',
            'shifted_to_gaushala',
            'escalated',
            'resolved',
            'closed',
            'false_report',
            'cancelled',
            'duplicate_case',
            'unable_to_locate',
        ];

        $responders = User::where('status', 'active')
            ->select('id', 'name', 'mobile')
            ->orderBy('name')
            ->get();

        $query = EmergencyCase::query()
            ->leftJoin('users as reporter', 'emergency_cases.reporter_id', '=', 'reporter.id')
            ->leftJoin('users as handler', 'emergency_cases.current_handler_id', '=', 'handler.id')
            ->select(
                'emergency_cases.*',
                'reporter.name as reporter_name',
                'reporter.mobile as reporter_mobile',
                'handler.name as handler_name',
                'handler.mobile as handler_mobile'
            );

        // Card filter
        $card = $request->card;

        if ($card === 'pending') {
            $query->whereIn('emergency_cases.status', ['reported', 'alerted']);
        } elseif ($card === 'active') {
            $query->whereIn('emergency_cases.status', [
                'accepted',
                'en_route',
                'reached_site',
                'rescue_in_progress',
                'needs_backup',
                'treatment_started',
                'shifted_to_gaushala',
                'escalated',
            ]);
        } elseif ($card === 'critical') {
            $query->where('emergency_cases.severity', 'critical');
        } elseif ($card === 'resolved') {
            $query->whereIn('emergency_cases.status', ['resolved', 'closed']);
        }

        // Normal filters
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('emergency_cases.case_uid', 'like', "%{$keyword}%")
                    ->orWhere('emergency_cases.title', 'like', "%{$keyword}%")
                    ->orWhere('emergency_cases.contact_number', 'like', "%{$keyword}%")
                    ->orWhere('emergency_cases.district', 'like', "%{$keyword}%")
                    ->orWhere('reporter.name', 'like', "%{$keyword}%")
                    ->orWhere('reporter.mobile', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('emergency_cases.status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('emergency_cases.severity', $request->severity);
        }

        if ($request->filled('case_type')) {
            $query->where('emergency_cases.case_type', $request->case_type);
        }

        if ($request->filled('district')) {
            $query->where('emergency_cases.district', 'like', '%' . $request->district . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('emergency_cases.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('emergency_cases.created_at', '<=', $request->date_to);
        }

        $reports = $query->orderByDesc('emergency_cases.id')->get();

        $summary = [
            'total' => EmergencyCase::count(),
            'pending' => EmergencyCase::whereIn('status', ['reported', 'alerted'])->count(),
            'active' => EmergencyCase::whereIn('status', [
                'accepted',
                'en_route',
                'reached_site',
                'rescue_in_progress',
                'needs_backup',
                'treatment_started',
                'shifted_to_gaushala',
                'escalated',
            ])->count(),
            'critical' => EmergencyCase::where('severity', 'critical')->count(),
            'resolved' => EmergencyCase::whereIn('status', ['resolved', 'closed'])->count(),
        ];

        $filters = $request->only([
            'keyword',
            'status',
            'severity',
            'case_type',
            'district',
            'date_from',
            'date_to',
            'card',
        ]);

        return view('admin.report_cases.index', compact(
            'reports',
            'summary',
            'filters',
            'statuses',
            'responders'
        ));
    }


    public function show($id)
    {
        abort_unless(Schema::hasTable('emergency_cases'), 404);

        $report = DB::table('emergency_cases as ec')
            ->leftJoin('users as reporter', 'reporter.id', '=', 'ec.reporter_id')
            ->leftJoin('users as handler', 'handler.id', '=', 'ec.current_handler_id')
            ->select(
                'ec.*',
                'reporter.name as reporter_name',
                'reporter.mobile as reporter_mobile',
                'handler.name as handler_name',
                'handler.mobile as handler_mobile'
            )
            ->where('ec.id', $id)
            ->first();

        abort_if(!$report, 404);

        $media = Schema::hasTable('emergency_case_media')
            ? DB::table('emergency_case_media')
                ->where('emergency_case_id', $id)
                ->latest()
                ->get()
            : collect();

        $logs = Schema::hasTable('emergency_case_logs')
            ? DB::table('emergency_case_logs as l')
                ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
                ->select('l.*', 'u.name as user_name')
                ->where('l.emergency_case_id', $id)
                ->orderByDesc('l.id')
                ->get()
            : collect();

        $assignments = Schema::hasTable('emergency_case_assignments')
            ? DB::table('emergency_case_assignments as a')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->select('a.*', 'u.name as user_name', 'u.mobile as user_mobile')
                ->where('a.emergency_case_id', $id)
                ->latest('a.id')
                ->get()
            : collect();

        return view('admin.report-cases.show', [
            'report' => $report,
            'media' => $media,
            'logs' => $logs,
            'assignments' => $assignments,
            'statuses' => $this->statuses,
            'responders' => $this->getResponderUsers(),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        abort_unless(Schema::hasTable('emergency_cases'), 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in($this->statuses)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report = DB::table('emergency_cases')->where('id', $id)->first();
        abort_if(!$report, 404);

        $updateData = [
            'status' => $validated['status'],
            'updated_at' => now(),
        ];

        if ($validated['status'] === 'accepted' && empty($report->accepted_at)) {
            $updateData['accepted_at'] = now();
        }

        if ($validated['status'] === 'en_route' && empty($report->en_route_at)) {
            $updateData['en_route_at'] = now();
        }

        if ($validated['status'] === 'reached_site' && empty($report->reached_at)) {
            $updateData['reached_at'] = now();
        }

        if ($validated['status'] === 'rescue_in_progress' && empty($report->rescue_started_at)) {
            $updateData['rescue_started_at'] = now();
        }

        if ($validated['status'] === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        if ($validated['status'] === 'closed') {
            $updateData['closed_at'] = now();
        }

        DB::table('emergency_cases')->where('id', $id)->update($updateData);

        $this->logCaseAction(
            caseId: $id,
            action: 'status_changed',
            oldStatus: $report->status,
            newStatus: $validated['status'],
            notes: $validated['notes'] ?? 'Status updated from admin panel'
        );

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Case status updated successfully.',
            ]);
        }

        return back()->with('success', 'Case status updated successfully.');
    }

    public function assignHandler(Request $request, $id)
    {
        abort_unless(Schema::hasTable('emergency_cases'), 404);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report = DB::table('emergency_cases')->where('id', $id)->first();
        abort_if(!$report, 404);

        $handler = DB::table('users')
            ->select('id', 'name', 'mobile')
            ->where('id', $validated['user_id'])
            ->first();

        abort_if(!$handler, 404);

        $caseUpdate = [
            'current_handler_id' => $handler->id,
            'updated_at' => now(),
        ];

        if (in_array($report->status, ['reported', 'alerted'])) {
            $caseUpdate['status'] = 'accepted';
        }

        if (empty($report->accepted_at)) {
            $caseUpdate['accepted_at'] = now();
        }

        DB::table('emergency_cases')->where('id', $id)->update($caseUpdate);

        if (Schema::hasTable('emergency_case_assignments')) {
            $existing = DB::table('emergency_case_assignments')
                ->where('emergency_case_id', $id)
                ->where('user_id', $handler->id)
                ->first();

            if ($existing) {
                DB::table('emergency_case_assignments')
                    ->where('id', $existing->id)
                    ->update([
                        'assignment_role' => 'primary_handler',
                        'status' => 'accepted',
                        'accepted_at' => $existing->accepted_at ?: now(),
                        'notes' => $validated['notes'] ?? 'Assigned from admin panel',
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('emergency_case_assignments')->insert([
                    'emergency_case_id' => $id,
                    'user_id' => $handler->id,
                    'assignment_role' => 'primary_handler',
                    'status' => 'accepted',
                    'accepted_at' => now(),
                    'notes' => $validated['notes'] ?? 'Assigned from admin panel',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->logCaseAction(
            caseId: $id,
            action: 'handler_assigned',
            oldStatus: $report->status,
            newStatus: in_array($report->status, ['reported', 'alerted']) ? 'accepted' : $report->status,
            notes: 'Primary handler assigned: ' . ($handler->name ?? 'User')
        );

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Handler assigned successfully.',
            ]);
        }

        return back()->with('success', 'Handler assigned successfully.');
    }

    private function getResponderUsers()
    {
        if (!Schema::hasTable('users')) {
            return collect();
        }

        $query = DB::table('users')->select('id', 'name', 'mobile');

        if (Schema::hasColumn('users', 'status')) {
            $query->where('status', 'active');
        }

        return $query->orderBy('name')->get();
    }

    private function logCaseAction(
        int $caseId,
        string $action,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?string $notes = null
        ): void {
        if (!Schema::hasTable('emergency_case_logs')) {
            return;
        }

        DB::table('emergency_case_logs')->insert([
            'emergency_case_id' => $caseId,
            'user_id' => null,
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}