<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseMedia;
use App\Services\EmergencyCaseService;
use App\Services\FirebasePushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\UserAddress;

class EmergencyCaseController extends Controller
{
    public function __construct(
        protected EmergencyCaseService $caseService,
        protected FirebasePushService $pushService
    ) {
    }


    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $perPage = (int) $request->query('per_page', 20);
            $perPage = $perPage > 100 ? 100 : $perPage;

            $radiusKm = (float) $request->query('radius_km', 20);

            $userAddress = UserAddress::where('user_id', $user->id)
                ->where('status', '!=', 'deleted')
                ->latest()
                ->first();

            $query = EmergencyCase::query()
                ->with([
                    'reporter:id,name,mobile',
                    'currentHandler:id,name,mobile',
                    'media',
                ])
                ->select('emergency_cases.*');

            /*
            |--------------------------------------------------------------------------
            | Optional Filters
            |--------------------------------------------------------------------------
            */

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('case_type')) {
                $query->where('case_type', $request->case_type);
            }

            if ($request->filled('severity')) {
                $query->where('severity', $request->severity);
            }

            /*
            |--------------------------------------------------------------------------
            | If User Has No Address
            |--------------------------------------------------------------------------
            */

            if (!$userAddress) {
                $cases = $query
                    ->selectRaw('NULL as distance_km')
                    ->selectRaw('5 as address_priority')
                    ->latest()
                    ->paginate($perPage);

                return response()->json([
                    'status' => true,
                    'message' => 'Emergency cases fetched successfully. User address not found, showing latest reports.',
                    'user_address' => null,
                    'data' => $cases,
                ]);
            }

            $area = strtolower(trim((string) $userAddress->area_name));
            $city = strtolower(trim((string) $userAddress->city));
            $district = strtolower(trim((string) $userAddress->district));
            $state = strtolower(trim((string) $userAddress->state));

            $hasCoordinates = !empty($userAddress->latitude) && !empty($userAddress->longitude);

            /*
            |--------------------------------------------------------------------------
            | Same Area / City / District Priority
            |--------------------------------------------------------------------------
            */

            $query->selectRaw(
                "CASE 
                    WHEN ? != '' AND LOWER(TRIM(COALESCE(area_name, ''))) = ? THEN 1 
                    ELSE 0 
                END as same_area",
                [$area, $area]
            );

            $query->selectRaw(
                "CASE 
                    WHEN ? != '' AND LOWER(TRIM(COALESCE(city, ''))) = ? THEN 1 
                    ELSE 0 
                END as same_city",
                [$city, $city]
            );

            $query->selectRaw(
                "CASE 
                    WHEN ? != '' AND LOWER(TRIM(COALESCE(district, ''))) = ? THEN 1 
                    ELSE 0 
                END as same_district",
                [$district, $district]
            );

            $query->selectRaw(
                "CASE 
                    WHEN ? != '' AND LOWER(TRIM(COALESCE(state, ''))) = ? THEN 1 
                    ELSE 0 
                END as same_state",
                [$state, $state]
            );

            /*
            |--------------------------------------------------------------------------
            | Distance Calculation Using Latitude / Longitude
            |--------------------------------------------------------------------------
            */

            if ($hasCoordinates) {
                $lat = (float) $userAddress->latitude;
                $lng = (float) $userAddress->longitude;

                $distanceSql = "
                    (
                        6371 * ACOS(
                            LEAST(1, GREATEST(-1,
                                COS(RADIANS(?)) 
                                * COS(RADIANS(latitude)) 
                                * COS(RADIANS(longitude) - RADIANS(?)) 
                                + SIN(RADIANS(?)) 
                                * SIN(RADIANS(latitude))
                            ))
                        )
                    )
                ";

                $query->selectRaw("$distanceSql as distance_km", [$lat, $lng, $lat]);

                $query->selectRaw(
                    "CASE
                        WHEN ? != '' AND LOWER(TRIM(COALESCE(area_name, ''))) = ? THEN 1
                        WHEN ? != '' AND LOWER(TRIM(COALESCE(city, ''))) = ? THEN 2
                        WHEN ? != '' AND LOWER(TRIM(COALESCE(district, ''))) = ? THEN 3
                        WHEN latitude IS NOT NULL 
                            AND longitude IS NOT NULL 
                            AND $distanceSql <= ? THEN 4
                        ELSE 5
                    END as address_priority",
                    [
                        $area, $area,
                        $city, $city,
                        $district, $district,
                        $lat, $lng, $lat,
                        $radiusKm,
                    ]
                );

                $query->orderBy('address_priority', 'asc')
                    ->orderByRaw('distance_km IS NULL asc')
                    ->orderBy('distance_km', 'asc')
                    ->latest('created_at');
            } else {
                $query->selectRaw('NULL as distance_km');

                $query->selectRaw(
                    "CASE
                        WHEN ? != '' AND LOWER(TRIM(COALESCE(area_name, ''))) = ? THEN 1
                        WHEN ? != '' AND LOWER(TRIM(COALESCE(city, ''))) = ? THEN 2
                        WHEN ? != '' AND LOWER(TRIM(COALESCE(district, ''))) = ? THEN 3
                        ELSE 5
                    END as address_priority",
                    [
                        $area, $area,
                        $city, $city,
                        $district, $district,
                    ]
                );

                $query->orderBy('address_priority', 'asc')
                    ->latest('created_at');
            }

            $cases = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Emergency cases fetched address wise successfully',
                'user_address' => [
                    'id' => $userAddress->id,
                    'area_name' => $userAddress->area_name,
                    'city' => $userAddress->city,
                    'district' => $userAddress->district,
                    'state' => $userAddress->state,
                    'latitude' => $userAddress->latitude,
                    'longitude' => $userAddress->longitude,
                ],
                'priority_meaning' => [
                    1 => 'Same area',
                    2 => 'Same city',
                    3 => 'Same district',
                    4 => 'Nearby location',
                    5 => 'Other cases',
                ],
                'data' => $cases,
            ]);

        } catch (\Throwable $e) {
            Log::error('Emergency case address wise fetch failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'case_type'      => ['required', Rule::in(EmergencyCase::TYPES)],
            'severity'       => ['required', Rule::in(EmergencyCase::SEVERITIES)],
            'title'          => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'full_address'   => 'nullable|string|max:500',
            'area_name'      => 'nullable|string|max:255',
            'land_mark'      => 'nullable|string|max:255',
            'road_name'      => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:150',
            'latitude'       => 'required|numeric|between:-90,90',
            'longitude'      => 'required|numeric|between:-180,180',
            'photos'         => 'nullable|array',
            'photos.*'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'videos'         => 'nullable|array',
            'videos.*'       => 'nullable|mimes:mp4,mov,avi,mkv|max:40960',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $case = DB::transaction(function () use ($request) {
                $case = EmergencyCase::create([
                    'case_uid'       => $this->caseService->generateCaseUid(),
                    'reporter_id'    => auth()->id(),
                    'case_type'      => $request->case_type,
                    'title'          => $request->title ?? 'Emergency Case',
                    'description'    => $request->description,
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

            $case->load('reporter', 'media');

            /*
            |--------------------------------------------------------------------------
            | Firebase Notification Send At Store Time
            |--------------------------------------------------------------------------
            */

            $pushResult = [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'Notification not sent',
            ];

            try {
                if ($case->reporter) {
                    $pushResult = $this->pushService->sendToUser(
                        $case->reporter,
                        'Emergency Case Submitted',
                        'Your emergency report ' . $case->case_uid . ' has been submitted successfully.',
                        [
                            'type'      => 'emergency_case_created',
                            'case_id'   => (string) $case->id,
                            'case_uid'  => (string) $case->case_uid,
                            'status'    => (string) $case->status,
                            'case_type' => (string) $case->case_type,
                            'severity'  => (string) $case->severity,
                            'screen'    => 'EmergencyCaseDetails',
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Emergency case Firebase notification failed', [
                    'case_id' => $case->id,
                    'user_id' => $case->reporter_id,
                    'error'   => $e->getMessage(),
                ]);

                $pushResult = [
                    'success_count' => 0,
                    'failure_count' => 1,
                    'results'       => [],
                    'message'       => $e->getMessage(),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Nearby Emergency Alert Logic
            |--------------------------------------------------------------------------
            */

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
                'data'          => $case,
                'alerted_users' => $alertCount,
                'push_result'   => $pushResult,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Emergency case store failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

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
            'status'              => true,
            'message'             => 'Backup request sent successfully',
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
                'type'     => 'emergency_case_update',
                'case_id'  => (string) $case->id,
                'case_uid' => (string) $case->case_uid,
                'status'   => (string) $case->status,
                'screen'   => 'EmergencyCaseDetails',
            ]
        );
    }
}