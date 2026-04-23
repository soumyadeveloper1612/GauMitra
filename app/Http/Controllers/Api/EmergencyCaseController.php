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

use App\Services\FirebasePushService;
use Illuminate\Support\Facades\Log;

class EmergencyCaseController extends Controller
{
   public function __construct(
    protected EmergencyCaseService $caseService,
    protected FirebasePushService $pushService
) {
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
        try {
            $case = DB::transaction(function () use ($request) {
                $case = EmergencyCase::create([
                    'case_uid'       => $this->caseService->generateCaseUid(),
                    'reporter_id'    => auth()->id(),
                    'case_type'      => $request->case_type,
                    'title'          => $request->title ?? 'Emergency Case',
                    'description'    => $request->description ?? null,
                    'severity'       => $request->severity,
                    'contact_number' => $request->contact_number,
                    'full_address'   => $request->full_address,
                    'area_name'      => $request->area_name,
                    'land_mark'      => $request->land_mark,
                    'road_name'      => $request->road_name,
                    'city'           => $request->city,
                    'latitude'       => $request->latitude,
                    'longitude'      => $request->longitude,
                    'status'         => 'reported',
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
                        'severity'  => $case->severity,
                    ],
                    (float) $case->latitude,
                    (float) $case->longitude
                );

                if ($request->hasFile('photos')) {
                    $photos = $request->file('photos');

                    if (!is_array($photos)) {
                        $photos = [$photos];
                    }

                    foreach ($photos as $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('emergency_cases/photos', 'public');

                            EmergencyCaseMedia::create([
                                'emergency_case_id' => $case->id,
                                'user_id'           => auth()->id(),
                                'media_type'        => 'photo',
                                'file_path'         => $path,
                                'file_name'         => $file->getClientOriginalName(),
                                'mime_type'         => $file->getMimeType(),
                                'file_size'         => $file->getSize(),
                            ]);
                        }
                    }
                }

                if ($request->hasFile('videos')) {
                    $videos = $request->file('videos');

                    if (!is_array($videos)) {
                        $videos = [$videos];
                    }

                    foreach ($videos as $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('emergency_cases/videos', 'public');

                            EmergencyCaseMedia::create([
                                'emergency_case_id' => $case->id,
                                'user_id'           => auth()->id(),
                                'media_type'        => 'video',
                                'file_path'         => $path,
                                'file_name'         => $file->getClientOriginalName(),
                                'mime_type'         => $file->getMimeType(),
                                'file_size'         => $file->getSize(),
                            ]);
                        }
                    }
                }

                return $case;
            });

            $alertCount = 0;

            try {
                $shouldAlertImmediately =
                    in_array($case->case_type, ['accident', 'illegal_transport']) ||
                    in_array($case->severity, ['high', 'critical']);

                if ($shouldAlertImmediately) {
                    $alertCount = $this->caseService->sendCaseAlerts($case, 20);
                }
            } catch (\Throwable $e) {
                Log::error('Emergency alert failed', [
                    'case_id' => $case->id,
                    'error'   => $e->getMessage(),
                ]);
            }

            return response()->json([
                'status'        => true,
                'message'       => 'Emergency case reported successfully',
                'data'          => $case->load('media'),
                'alerted_users' => $alertCount,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Server Error',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ], 500);
        }
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
        $case = EmergencyCase::with('reporter')->findOrFail($id);

        if (in_array($case->status, ['resolved', 'closed', 'cancelled', 'false_report'])) {
            return response()->json([
                'status'  => false,
                'message' => 'This case can no longer be accepted',
            ], 422);
        }

        $assignment = $this->caseService->acceptCase($case, auth()->user());

        $case->refresh()->load('reporter');

        $this->notifyReporterAboutCaseUpdate($case, 'Your case has been accepted by the rescue team.');

        return response()->json([
            'status'  => true,
            'message' => 'Case accepted successfully',
            'data'    => $assignment->load('user'),
        ]);
    }
        
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(EmergencyCase::STATUSES)],
            'notes'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $case = EmergencyCase::with('reporter')->findOrFail($id);

        $updatedCase = $this->caseService->updateCaseStatus(
            $case,
            $request->status,
            auth()->id(),
            $request->notes
        );

        $this->notifyReporterAboutCaseUpdate($updatedCase, $request->notes);

        return response()->json([
            'status'  => true,
            'message' => 'Case status updated successfully',
            'data'    => $updatedCase,
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
        $case = EmergencyCase::with('reporter')->findOrFail($id);

        $case->update([
            'resolution_notes' => $request->resolution_notes,
        ]);

        $updatedCase = $this->caseService->updateCaseStatus(
            $case,
            'resolved',
            auth()->id(),
            $request->resolution_notes
        );

        $this->notifyReporterAboutCaseUpdate($updatedCase, $request->resolution_notes);

        return response()->json([
            'status'  => true,
            'message' => 'Case resolved successfully',
            'data'    => $updatedCase,
        ]);
    }
        
    public function close(Request $request, $id)
    {
        $case = EmergencyCase::with('reporter')->findOrFail($id);

        $case->update([
            'closed_by' => auth()->id(),
        ]);

        $updatedCase = $this->caseService->updateCaseStatus(
            $case,
            'closed',
            auth()->id(),
            $request->notes
        );

        $this->notifyReporterAboutCaseUpdate($updatedCase, $request->notes);

        return response()->json([
            'status'  => true,
            'message' => 'Case closed successfully',
            'data'    => $updatedCase,
        ]);
    }

    protected function notifyReporterAboutCaseUpdate(EmergencyCase $case, ?string $notes = null): void
{
    $case->loadMissing('reporter');

    if (!$case->reporter) {
        return;
    }

    $title = 'Emergency Case Update';

    $body = match ($case->status) {
        'reported'    => "Your report {$case->case_uid} has been created.",
        'accepted'    => "Your report {$case->case_uid} has been accepted.",
        'in_progress' => "Your report {$case->case_uid} is now in progress.",
        'resolved'    => "Your report {$case->case_uid} has been resolved.",
        'closed'      => "Your report {$case->case_uid} has been closed.",
        'cancelled'   => "Your report {$case->case_uid} has been cancelled.",
        default       => "Your report {$case->case_uid} status changed to {$case->status}.",
    };

    if (!empty($notes)) {
        $body .= " Notes: {$notes}";
    }

    $this->pushService->sendToUser(
        $case->reporter,
        $title,
        $body,
        [
            'type'      => 'emergency_case_update',
            'case_id'   => (string) $case->id,
            'case_uid'  => (string) $case->case_uid,
            'status'    => (string) $case->status,
            'screen'    => 'EmergencyCaseDetails',
        ]
    );
}
}