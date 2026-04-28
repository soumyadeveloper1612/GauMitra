<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnimalCondition;
use App\Models\AnimalType;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseLog;
use App\Models\EmergencyCaseMedia;
use App\Models\ReportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmergencyCaseController extends Controller
{
    public function index(Request $request)
    {
        $query = EmergencyCase::with([
                'animalType:id,name,slug,icon_class,color_code',
                'reportType:id,name,slug,icon_class,color_code',
                'animalCondition:id,name,slug,severity_level,icon_class,color_code',
                'reporter:id,name,mobile',
                'currentHandler:id,name,mobile',
                'media',
            ])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('city')) {
            $query->where('city', 'LIKE', '%' . $request->city . '%');
        }

        $cases = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status'  => true,
            'message' => 'Emergency cases fetched successfully.',
            'data'    => $cases,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'animal_type_id'       => ['required', 'integer', 'exists:animal_types,id'],
            'report_type_id'       => ['required', 'integer', 'exists:report_types,id'],
            'animal_condition_id'  => ['nullable', 'integer', 'exists:animal_conditions,id'],

            'severity'             => ['nullable', Rule::in(EmergencyCase::SEVERITIES)],
            'contact_number'       => ['required', 'digits:10'],

            'title'                => ['nullable', 'string', 'max:255'],
            'description'          => ['nullable', 'string'],
            'cattle_count'         => ['nullable', 'integer', 'min:1'],

            'vehicle_number'       => ['nullable', 'string', 'max:50'],
            'vehicle_details'      => ['nullable', 'string', 'max:500'],

            'full_address'         => ['required', 'string'],
            'area_name'            => ['nullable', 'string', 'max:150'],
            'land_mark'            => ['nullable', 'string', 'max:150'],
            'road_name'            => ['nullable', 'string', 'max:150'],
            'city'                 => ['required', 'string', 'max:150'],
            'district'             => ['nullable', 'string', 'max:150'],
            'state'                => ['nullable', 'string', 'max:150'],
            'pincode'              => ['nullable', 'digits:6'],

            'latitude'             => ['required', 'numeric', 'between:-90,90'],
            'longitude'            => ['required', 'numeric', 'between:-180,180'],

            'photos'               => ['nullable', 'array'],
            'photos.*'             => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'videos'               => ['nullable', 'array'],
            'videos.*'             => ['file', 'mimes:mp4,mov,avi,webm', 'max:20480'],
        ], [
            'animal_type_id.required'      => 'Animal type is required.',
            'animal_type_id.exists'        => 'Selected animal type is invalid.',

            'report_type_id.required'      => 'Report type is required.',
            'report_type_id.exists'        => 'Selected report type is invalid.',

            'animal_condition_id.exists'   => 'Selected animal condition is invalid.',

            'severity.in'                  => 'Severity must be low, medium, high or critical.',
            'contact_number.required'      => 'Contact number is required.',
            'contact_number.digits'        => 'Contact number must be 10 digits.',

            'full_address.required'        => 'Full address is required.',
            'city.required'                => 'City is required.',

            'latitude.required'            => 'Latitude is required.',
            'latitude.between'             => 'Latitude must be between -90 and 90.',
            'longitude.required'           => 'Longitude is required.',
            'longitude.between'            => 'Longitude must be between -180 and 180.',

            'photos.*.image'               => 'Photos must be valid image files.',
            'photos.*.mimes'               => 'Photos must be jpg, jpeg, png or webp.',
            'photos.*.max'                 => 'Each photo must be less than 5MB.',

            'videos.*.mimes'               => 'Videos must be mp4, mov, avi or webm.',
            'videos.*.max'                 => 'Each video must be less than 20MB.',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->filled('animal_condition_id') && $request->filled('report_type_id')) {
                $conditionExists = AnimalCondition::where('id', $request->animal_condition_id)
                    ->where('report_type_id', $request->report_type_id)
                    ->where('status', 'active')
                    ->exists();

                if (!$conditionExists) {
                    $validator->errors()->add(
                        'animal_condition_id',
                        'Selected animal condition does not belong to selected report type.'
                    );
                }
            }

            if ($request->filled('animal_type_id')) {
                $animalActive = AnimalType::where('id', $request->animal_type_id)
                    ->where('status', 'active')
                    ->exists();

                if (!$animalActive) {
                    $validator->errors()->add('animal_type_id', 'Selected animal type is inactive.');
                }
            }

            if ($request->filled('report_type_id')) {
                $reportTypeActive = ReportType::where('id', $request->report_type_id)
                    ->where('status', 'active')
                    ->exists();

                if (!$reportTypeActive) {
                    $validator->errors()->add('report_type_id', 'Selected report type is inactive.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $animalType = AnimalType::findOrFail($request->animal_type_id);
            $reportType = ReportType::findOrFail($request->report_type_id);
            $animalCondition = null;

            if ($request->filled('animal_condition_id')) {
                $animalCondition = AnimalCondition::findOrFail($request->animal_condition_id);
            }

            $severity = $request->severity;

            if (!$severity && $animalCondition) {
                $severity = $animalCondition->severity_level;
            }

            if (!$severity) {
                $severity = 'medium';
            }

            $case = EmergencyCase::create([
                'case_uid'             => $this->generateCaseUid(),
                'reporter_id'          => auth('sanctum')->id(),

                'animal_type_id'       => $animalType->id,
                'report_type_id'       => $reportType->id,
                'animal_condition_id'  => $animalCondition?->id,

                'case_type'            => $reportType->slug,

                'title'                => $request->title ?? $reportType->name,
                'description'          => $request->description,
                'severity'             => $severity,
                'cattle_count'         => $request->cattle_count ?? 1,
                'contact_number'       => $request->contact_number,

                'vehicle_number'       => $request->vehicle_number,
                'vehicle_details'      => $request->vehicle_details,

                'full_address'         => $request->full_address,
                'area_name'            => $request->area_name,
                'land_mark'            => $request->land_mark,
                'road_name'            => $request->road_name,
                'city'                 => $request->city,
                'district'             => $request->district,
                'state'                => $request->state,
                'pincode'              => $request->pincode,

                'latitude'             => $request->latitude,
                'longitude'            => $request->longitude,

                'status'               => 'reported',
                'is_duplicate'         => false,
                'notified_radius_km'   => 20,
                'escalation_level'     => 0,
            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('emergency-cases/photos', 'public');

                    EmergencyCaseMedia::create([
                        'emergency_case_id' => $case->id,
                        'media_type'        => 'photo',
                        'file_path'         => $path,
                        'mime_type'         => $photo->getClientMimeType(),
                        'file_size'         => $photo->getSize(),
                    ]);
                }
            }

            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $video) {
                    $path = $video->store('emergency-cases/videos', 'public');

                    EmergencyCaseMedia::create([
                        'emergency_case_id' => $case->id,
                        'media_type'        => 'video',
                        'file_path'         => $path,
                        'mime_type'         => $video->getClientMimeType(),
                        'file_size'         => $video->getSize(),
                    ]);
                }
            }

            EmergencyCaseLog::create([
                'emergency_case_id' => $case->id,
                'user_id'           => auth('sanctum')->id(),
                'action'            => 'case_reported',
                'old_status'        => null,
                'new_status'        => 'reported',
                'notes'             => 'Emergency case reported from mobile app.',
                'latitude'          => $request->latitude,
                'longitude'         => $request->longitude,
                'meta'              => [
                    'animal_type_id'      => $animalType->id,
                    'report_type_id'      => $reportType->id,
                    'animal_condition_id' => $animalCondition?->id,
                ],
            ]);

            DB::commit();

            $case->load([
                'animalType:id,name,slug,icon_class,color_code',
                'reportType:id,name,slug,icon_class,color_code',
                'animalCondition:id,name,slug,severity_level,icon_class,color_code',
                'media',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Emergency case reported successfully.',
                'data'    => $case,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Emergency case store failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error. Emergency case could not be saved.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show($id)
    {
        $case = EmergencyCase::with([
            'animalType:id,name,slug,icon_class,color_code',
            'reportType:id,name,slug,icon_class,color_code',
            'animalCondition:id,name,slug,severity_level,icon_class,color_code,symptoms,first_aid_steps',
            'reporter:id,name,mobile',
            'currentHandler:id,name,mobile',
            'media',
            'assignments.user:id,name,mobile',
            'logs.user:id,name,mobile',
            'alerts',
        ])->findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Emergency case details fetched successfully.',
            'data'    => $case,
        ]);
    }

    public function acceptReport(Request $request, $id)
    {
        $case = EmergencyCase::findOrFail($id);

        if (!in_array($case->status, ['reported', 'alerted', 'escalated'])) {
            return response()->json([
                'status'  => false,
                'message' => 'This case cannot be accepted now.',
            ], 422);
        }

        $oldStatus = $case->status;

        $case->update([
            'current_handler_id' => auth('sanctum')->id(),
            'status'             => 'accepted',
            'accepted_at'        => now(),
        ]);

        EmergencyCaseLog::create([
            'emergency_case_id' => $case->id,
            'user_id'           => auth('sanctum')->id(),
            'action'            => 'case_accepted',
            'old_status'        => $oldStatus,
            'new_status'        => 'accepted',
            'notes'             => $request->notes ?? $request->remarks ?? 'Case accepted.',
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'meta'              => null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Case accepted successfully.',
            'data'    => $case->fresh([
                'animalType',
                'reportType',
                'animalCondition',
                'media',
            ]),
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status'    => ['required', Rule::in(EmergencyCase::STATUSES)],
            'notes'     => ['nullable', 'string', 'max:1000'],
            'remarks'   => ['nullable', 'string', 'max:1000'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $case = EmergencyCase::findOrFail($id);
        $oldStatus = $case->status;
        $newStatus = $request->status;

        $updateData = [
            'status' => $newStatus,
        ];

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
            $updateData['closed_by'] = auth('sanctum')->id();
        }

        $case->update($updateData);

        EmergencyCaseLog::create([
            'emergency_case_id' => $case->id,
            'user_id'           => auth('sanctum')->id(),
            'action'            => 'status_changed',
            'old_status'        => $oldStatus,
            'new_status'        => $newStatus,
            'notes'             => $request->notes ?? $request->remarks,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'meta'              => null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Case status updated successfully.',
            'data'    => $case->fresh([
                'animalType',
                'reportType',
                'animalCondition',
                'media',
            ]),
        ]);
    }

    public function closeReport(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'resolution_notes'    => ['nullable', 'string'],
            'false_report_reason' => ['nullable', 'string'],
            'status'              => ['nullable', Rule::in(['closed', 'resolved', 'false_report', 'unable_to_locate', 'cancelled'])],
            'latitude'            => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'           => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $case = EmergencyCase::findOrFail($id);
        $oldStatus = $case->status;
        $newStatus = $request->status ?? 'closed';

        $case->update([
            'status'              => $newStatus,
            'closed_by'           => auth('sanctum')->id(),
            'closed_at'           => now(),
            'resolved_at'         => in_array($newStatus, ['closed', 'resolved']) ? now() : $case->resolved_at,
            'resolution_notes'    => $request->resolution_notes,
            'false_report_reason' => $request->false_report_reason,
        ]);

        EmergencyCaseLog::create([
            'emergency_case_id' => $case->id,
            'user_id'           => auth('sanctum')->id(),
            'action'            => 'case_closed',
            'old_status'        => $oldStatus,
            'new_status'        => $newStatus,
            'notes'             => $request->resolution_notes ?? $request->false_report_reason,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'meta'              => null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Case closed successfully.',
            'data'    => $case->fresh([
                'animalType',
                'reportType',
                'animalCondition',
                'media',
            ]),
        ]);
    }

    private function generateCaseUid(): string
    {
        do {
            $uid = 'GM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (EmergencyCase::where('case_uid', $uid)->exists());

        return $uid;
    }
}