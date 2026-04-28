<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimalCondition;
use App\Models\AnimalType;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseAssignment;
use App\Models\EmergencyCaseLog;
use App\Models\EmergencyCaseMedia;
use App\Models\ReportType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminReportCaseController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'keyword'             => $request->keyword,
            'status'              => $request->status,
            'severity'            => $request->severity,
            'animal_type_id'      => $request->animal_type_id,
            'report_type_id'      => $request->report_type_id,
            'animal_condition_id' => $request->animal_condition_id,
            'district'            => $request->district,
            'date_from'           => $request->date_from,
            'date_to'             => $request->date_to,
            'card'                => $request->card,
        ];

        $baseQuery = EmergencyCase::query();

        $summary = [
            'total'    => (clone $baseQuery)->count(),
            'pending'  => (clone $baseQuery)->whereIn('status', ['reported', 'alerted'])->count(),
            'active'   => (clone $baseQuery)->whereIn('status', [
                'accepted',
                'en_route',
                'reached_site',
                'rescue_in_progress',
                'needs_backup',
                'treatment_started',
                'shifted_to_gaushala',
                'escalated',
            ])->count(),
            'critical' => (clone $baseQuery)->where('severity', 'critical')->count(),
            'resolved' => (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count(),
        ];

        $query = EmergencyCase::with([
            'animalType:id,name,slug,icon_class,color_code',
            'reportType:id,name,slug,icon_class,color_code',
            'animalCondition:id,name,slug,severity_level,icon_class,color_code',
            'reporter:id,name,mobile',
            'currentHandler:id,name,mobile',
            'media',
        ])->latest();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('case_uid', 'LIKE', "%{$keyword}%")
                    ->orWhere('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('contact_number', 'LIKE', "%{$keyword}%")
                    ->orWhere('full_address', 'LIKE', "%{$keyword}%")
                    ->orWhere('city', 'LIKE', "%{$keyword}%")
                    ->orWhere('district', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('reporter', function ($rq) use ($keyword) {
                        $rq->where('name', 'LIKE', "%{$keyword}%")
                            ->orWhere('mobile', 'LIKE', "%{$keyword}%");
                    })
                    ->orWhereHas('currentHandler', function ($hq) use ($keyword) {
                        $hq->where('name', 'LIKE', "%{$keyword}%")
                            ->orWhere('mobile', 'LIKE', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('animal_type_id')) {
            $query->where('animal_type_id', $request->animal_type_id);
        }

        if ($request->filled('report_type_id')) {
            $query->where('report_type_id', $request->report_type_id);
        }

        if ($request->filled('animal_condition_id')) {
            $query->where('animal_condition_id', $request->animal_condition_id);
        }

        if ($request->filled('district')) {
            $query->where('district', 'LIKE', '%' . $request->district . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('card')) {
            match ($request->card) {
                'pending'  => $query->whereIn('status', ['reported', 'alerted']),
                'active'   => $query->whereIn('status', [
                    'accepted',
                    'en_route',
                    'reached_site',
                    'rescue_in_progress',
                    'needs_backup',
                    'treatment_started',
                    'shifted_to_gaushala',
                    'escalated',
                ]),
                'critical' => $query->where('severity', 'critical'),
                'resolved' => $query->whereIn('status', ['resolved', 'closed']),
                default    => null,
            };
        }

        $reports = $query->get();

        $statuses = EmergencyCase::STATUSES;

        $animalTypes = AnimalType::active()
            ->ordered()
            ->get();

        $reportTypes = ReportType::active()
            ->ordered()
            ->get();

        $animalConditions = AnimalCondition::active()
            ->ordered()
            ->get();

        $responders = User::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'mobile']);

        return view('admin.report-cases.manage-emergency-case', compact(
            'reports',
            'summary',
            'filters',
            'statuses',
            'animalTypes',
            'reportTypes',
            'animalConditions',
            'responders'
        ));
    }

    public function show($id)
    {
        $case = EmergencyCase::with([
            'animalType:id,name,slug,icon_class,color_code',
            'reportType:id,name,slug,icon_class,color_code',
            'animalCondition:id,name,slug,severity_level,icon_class,color_code,symptoms,first_aid_steps,description',
            'reporter:id,name,mobile',
            'currentHandler:id,name,mobile',
            'media',
            'assignments.user:id,name,mobile',
            'logs.user:id,name,mobile',
            'alerts',
        ])->findOrFail($id);

        $report = $case;

        $media = $case->media;

        $logs = $case->logs()
            ->with('user:id,name,mobile')
            ->latest()
            ->get();

        $assignments = $case->assignments()
            ->with('user:id,name,mobile')
            ->latest()
            ->get();

        $statuses = EmergencyCase::STATUSES;

        $responders = User::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'mobile']);

        return view('admin.report-cases.show', compact(
            'report',
            'media',
            'logs',
            'assignments',
            'statuses',
            'responders'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(EmergencyCase::STATUSES)],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();

        try {
            $case = EmergencyCase::findOrFail($id);

            $oldStatus = $case->status;
            $newStatus = $request->status;

            $updateData = [
                'status' => $newStatus,
            ];

            if ($newStatus === 'accepted') {
                $updateData['accepted_at'] = $case->accepted_at ?: now();
            }

            if ($newStatus === 'en_route') {
                $updateData['en_route_at'] = now();
            }

            if ($newStatus === 'reached_site') {
                $updateData['reached_at'] = now();
            }

            if ($newStatus === 'rescue_in_progress') {
                $updateData['rescue_started_at'] = now();
            }

            if ($newStatus === 'resolved') {
                $updateData['resolved_at'] = now();
            }

            if ($newStatus === 'closed') {
                $updateData['closed_at'] = now();
                $updateData['closed_by'] = session('admin_id');
            }

            $case->update($updateData);

            EmergencyCaseLog::create([
                'emergency_case_id' => $case->id,
                'user_id'           => null,
                'action'            => 'admin_status_changed',
                'old_status'        => $oldStatus,
                'new_status'        => $newStatus,
                'notes'             => $request->notes,
                'latitude'          => null,
                'longitude'         => null,
                'meta'              => [
                    'changed_by_admin_id' => session('admin_id'),
                ],
            ]);

            DB::commit();

            return back()->with('success', 'Case status updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Admin case status update failed', [
                'case_id' => $id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', config('app.debug') ? $e->getMessage() : 'Status update failed.');
        }
    }

    public function assignHandler(Request $request, $id)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'notes'   => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();

        try {
            $case = EmergencyCase::findOrFail($id);
            $handler = User::findOrFail($request->user_id);

            $oldHandlerId = $case->current_handler_id;

            $case->update([
                'current_handler_id' => $handler->id,
                'status'             => in_array($case->status, ['reported', 'alerted'])
                    ? 'accepted'
                    : $case->status,
                'accepted_at'        => $case->accepted_at ?: now(),
            ]);

            EmergencyCaseAssignment::create([
                'emergency_case_id' => $case->id,
                'user_id'           => $handler->id,
                'assignment_role'   => 'primary_handler',
                'status'            => 'assigned',
                'notes'             => $request->notes,
                'assigned_at'       => now(),
            ]);

            EmergencyCaseLog::create([
                'emergency_case_id' => $case->id,
                'user_id'           => $handler->id,
                'action'            => 'admin_handler_assigned',
                'old_status'        => null,
                'new_status'        => $case->status,
                'notes'             => $request->notes ?: 'Primary handler assigned by admin.',
                'latitude'          => null,
                'longitude'         => null,
                'meta'              => [
                    'changed_by_admin_id' => session('admin_id'),
                    'old_handler_id'      => $oldHandlerId,
                    'new_handler_id'      => $handler->id,
                ],
            ]);

            DB::commit();

            return back()->with('success', 'Handler assigned successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Admin handler assign failed', [
                'case_id' => $id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', config('app.debug') ? $e->getMessage() : 'Handler assignment failed.');
        }
    }
}