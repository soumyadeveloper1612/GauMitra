<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseMedia;
use App\Services\EmergencyCaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmergencyCaseController extends Controller
{
    public function __construct(protected EmergencyCaseService $caseService)
    {
    }

    public function index(Request $request)
    {
        $cases = EmergencyCase::with(['reporter:id,name,mobile', 'currentHandler:id,name,mobile'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->case_type, fn($q) => $q->where('case_type', $request->case_type))
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $cases,
        ]);
    }

    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
    'case_type' => ['required', Rule::in(EmergencyCase::TYPES)],
    'title' => 'required|string|max:255',
    'description' => 'nullable|string',
    'severity' => ['required', Rule::in(EmergencyCase::SEVERITIES)],
    'cattle_count' => 'nullable|integer|min:1',
    'contact_number' => 'nullable|string|max:20',
    'vehicle_number' => 'nullable|string|max:50',
    'vehicle_details' => 'nullable|string',
    'full_address' => 'nullable|string|max:500',
    'district' => 'nullable|string|max:150',
    'state' => 'nullable|string|max:150',
    'pincode' => 'nullable|string|max:20',
    'latitude' => 'required|numeric|between:-90,90',
    'longitude' => 'required|numeric|between:-180,180',

    'photos' => 'nullable|array',
    'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

    'videos' => 'nullable|array',
    'videos.*' => 'nullable|mimes:mp4,mov,avi,mkv|max:20480',
]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $case = DB::transaction(function () use ($request) {
            $case = EmergencyCase::create([
                'case_uid' => $this->caseService->generateCaseUid(),
                'reporter_id' => auth()->id(),
                'case_type' => $request->case_type,
                'title' => $request->title,
                'description' => $request->description,
                'severity' => $request->severity,
                'cattle_count' => $request->cattle_count,
                'contact_number' => $request->contact_number,
                'vehicle_number' => $request->vehicle_number,
                'vehicle_details' => $request->vehicle_details,
                'full_address' => $request->full_address,
                'district' => $request->district,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => 'reported',
            ]);

            $this->caseService->log(
                $case,
                auth()->id(),
                'case_reported',
                null,
                'reported',
                'Emergency case created',
                [
                    'case_type' => $case->case_type,
                    'severity' => $case->severity,
                ],
                (float) $case->latitude,
                (float) $case->longitude
            );

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $path = $file->store('emergency_cases/photos', 'public');

                    EmergencyCaseMedia::create([
                        'emergency_case_id' => $case->id,
                        'user_id' => auth()->id(),
                        'media_type' => 'photo',
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $file) {
                    $path = $file->store('emergency_cases/videos', 'public');

                    EmergencyCaseMedia::create([
                        'emergency_case_id' => $case->id,
                        'user_id' => auth()->id(),
                        'media_type' => 'video',
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            return $case;
        });

        $shouldAlertImmediately = in_array($case->case_type, ['accident', 'illegal_transport']) ||
            in_array($case->severity, ['high', 'critical']);

        $alertCount = 0;

        if ($shouldAlertImmediately) {
            $alertCount = $this->caseService->sendCaseAlerts($case, 20);
        }

        return response()->json([
            'status' => true,
            'message' => 'Emergency case reported successfully',
            'data' => $case->load('media'),
            'alerted_users' => $alertCount,
        ], 201);
    }

    public function show($id)
    {
        $case = EmergencyCase::with([
            'reporter:id,name,mobile',
            'currentHandler:id,name,mobile',
            'media',
            'assignments.user:id,name,mobile',
            'logs.user:id,name,mobile',
            'alerts',
        ])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $case,
        ]);
    }

    public function accept($id)
    {
        $case = EmergencyCase::findOrFail($id);

        if (in_array($case->status, ['resolved', 'closed', 'cancelled', 'false_report'])) {
            return response()->json([
                'status' => false,
                'message' => 'This case can no longer be accepted',
            ], 422);
        }

        $assignment = $this->caseService->acceptCase($case, auth()->user());

        return response()->json([
            'status' => true,
            'message' => 'Case accepted successfully',
            'data' => $assignment->load('user'),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(EmergencyCase::STATUSES)],
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $case = EmergencyCase::findOrFail($id);

        $updatedCase = $this->caseService->updateCaseStatus(
            $case,
            $request->status,
            auth()->id(),
            $request->notes
        );

        return response()->json([
            'status' => true,
            'message' => 'Case status updated successfully',
            'data' => $updatedCase,
        ]);
    }

    public function requestBackup($id)
    {
        $case = EmergencyCase::findOrFail($id);

        $count = $this->caseService->requestBackup($case, auth()->id(), 30);

        return response()->json([
            'status' => true,
            'message' => 'Backup request sent successfully',
            'extra_alerted_users' => $count,
        ]);
    }

    public function resolve(Request $request, $id)
    {
        $case = EmergencyCase::findOrFail($id);

        $case->update([
            'resolution_notes' => $request->resolution_notes,
        ]);

        $updatedCase = $this->caseService->updateCaseStatus(
            $case,
            'resolved',
            auth()->id(),
            $request->resolution_notes
        );

        return response()->json([
            'status' => true,
            'message' => 'Case resolved successfully',
            'data' => $updatedCase,
        ]);
    }

    public function close(Request $request, $id)
    {
        $case = EmergencyCase::findOrFail($id);

        $case->update([
            'closed_by' => auth()->id(),
        ]);

        $updatedCase = $this->caseService->updateCaseStatus(
            $case,
            'closed',
            auth()->id(),
            $request->notes
        );

        return response()->json([
            'status' => true,
            'message' => 'Case closed successfully',
            'data' => $updatedCase,
        ]);
    }
}