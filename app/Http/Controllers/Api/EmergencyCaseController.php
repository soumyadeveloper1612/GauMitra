<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnimalCondition;
use App\Models\AnimalType;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseLog;
use App\Models\EmergencyCaseMedia;
use App\Models\EmergencyCaseAssignment;
use App\Models\ReportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Services\FirebasePushService;
use App\Services\EmergencyCaseAlertService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EmergencyCaseController extends Controller
{

    public function __construct(
        protected FirebasePushService $pushService,
        protected EmergencyCaseAlertService $caseAlertService
    ) {
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'animal_type_id'       => ['required', 'integer', 'exists:animal_types,id'],
    //         'report_type_id'       => ['required', 'integer', 'exists:report_types,id'],
    //         'animal_condition_id'  => ['nullable', 'integer', 'exists:animal_conditions,id'],

    //         'severity'             => ['nullable', Rule::in(EmergencyCase::SEVERITIES)],
    //         'contact_number'       => ['required', 'digits:10'],

    //         'title'                => ['nullable', 'string', 'max:255'],
    //         'description'          => ['nullable', 'string'],
    //         'cattle_count'         => ['nullable', 'integer', 'min:1'],

    //         'vehicle_number'       => ['nullable', 'string', 'max:50'],
    //         'vehicle_details'      => ['nullable', 'string', 'max:500'],

    //         'full_address'         => ['required', 'string'],
    //         'area_name'            => ['nullable', 'string', 'max:150'],
    //         'land_mark'            => ['nullable', 'string', 'max:150'],
    //         'road_name'            => ['nullable', 'string', 'max:150'],
    //         'city'                 => ['required', 'string', 'max:150'],
    //         'district'             => ['nullable', 'string', 'max:150'],
    //         'state'                => ['nullable', 'string', 'max:150'],
    //         'pincode'              => ['nullable', 'digits:6'],

    //         'latitude'             => ['required', 'numeric', 'between:-90,90'],
    //         'longitude'            => ['required', 'numeric', 'between:-180,180'],

    //         // keep nullable only. File validation is handled inside saveEmergencyCaseMedia()
    //         'photos'               => ['nullable'],
    //         'photos.*'             => ['nullable'],
    //         'videos'               => ['nullable'],
    //         'videos.*'             => ['nullable'],
    //     ], [
    //         'animal_type_id.required'     => 'Animal type is required.',
    //         'animal_type_id.exists'       => 'Selected animal type is invalid.',
    //         'report_type_id.required'     => 'Report type is required.',
    //         'report_type_id.exists'       => 'Selected report type is invalid.',
    //         'animal_condition_id.exists'  => 'Selected animal condition is invalid.',
    //         'severity.in'                 => 'Severity must be low, medium, high or critical.',
    //         'contact_number.required'     => 'Contact number is required.',
    //         'contact_number.digits'       => 'Contact number must be 10 digits.',
    //         'full_address.required'       => 'Full address is required.',
    //         'city.required'               => 'City is required.',
    //         'latitude.required'           => 'Latitude is required.',
    //         'latitude.between'            => 'Latitude must be between -90 and 90.',
    //         'longitude.required'          => 'Longitude is required.',
    //         'longitude.between'           => 'Longitude must be between -180 and 180.',
    //     ]);

    //     $validator->after(function ($validator) use ($request) {
    //         if ($request->filled('animal_condition_id') && $request->filled('report_type_id')) {
    //             $conditionExists = AnimalCondition::where('id', $request->animal_condition_id)
    //                 ->where('report_type_id', $request->report_type_id)
    //                 ->where('status', 'active')
    //                 ->exists();

    //             if (!$conditionExists) {
    //                 $validator->errors()->add(
    //                     'animal_condition_id',
    //                     'Selected animal condition does not belong to selected report type.'
    //                 );
    //             }
    //         }

    //         if ($request->filled('animal_type_id')) {
    //             $animalActive = AnimalType::where('id', $request->animal_type_id)
    //                 ->where('status', 'active')
    //                 ->exists();

    //             if (!$animalActive) {
    //                 $validator->errors()->add('animal_type_id', 'Selected animal type is inactive.');
    //             }
    //         }

    //         if ($request->filled('report_type_id')) {
    //             $reportTypeActive = ReportType::where('id', $request->report_type_id)
    //                 ->where('status', 'active')
    //                 ->exists();

    //             if (!$reportTypeActive) {
    //                 $validator->errors()->add('report_type_id', 'Selected report type is inactive.');
    //             }
    //         }
    //     });

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Validation failed.',
    //             'errors'  => $validator->errors(),
    //         ], 422);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $userId = auth('sanctum')->id();

    //         $animalType = AnimalType::findOrFail($request->animal_type_id);
    //         $reportType = ReportType::findOrFail($request->report_type_id);

    //         $animalCondition = null;

    //         if ($request->filled('animal_condition_id')) {
    //             $animalCondition = AnimalCondition::findOrFail($request->animal_condition_id);
    //         }

    //         $severity = $request->severity;

    //         if (!$severity && $animalCondition) {
    //             $severity = $animalCondition->severity_level;
    //         }

    //         if (!$severity) {
    //             $severity = 'medium';
    //         }

    //         $case = EmergencyCase::create([
    //             'case_uid'             => $this->generateCaseUid(),
    //             'reporter_id'          => $userId,

    //             'animal_type_id'       => $animalType->id,
    //             'report_type_id'       => $reportType->id,
    //             'animal_condition_id'  => $animalCondition?->id,

    //             'case_type'            => $reportType->slug,
    //             'title'                => $request->title ?: $reportType->name,
    //             'description'          => $request->description,
    //             'severity'             => $severity,
    //             'cattle_count'         => $request->cattle_count ?? 1,
    //             'contact_number'       => $request->contact_number,

    //             'vehicle_number'       => $request->vehicle_number,
    //             'vehicle_details'      => $request->vehicle_details,

    //             'full_address'         => $request->full_address,
    //             'area_name'            => $request->area_name,
    //             'land_mark'            => $request->land_mark,
    //             'road_name'            => $request->road_name,
    //             'city'                 => $request->city,
    //             'district'             => $request->district,
    //             'state'                => $request->state,
    //             'pincode'              => $request->pincode,

    //             'latitude'             => $request->latitude,
    //             'longitude'            => $request->longitude,

    //             'status'               => 'reported',
    //             'is_duplicate'         => false,
    //             'notified_radius_km'   => 20,
    //             'escalation_level'     => 0,
    //         ]);

    //         $mediaResult = $this->saveEmergencyCaseMedia($request, $case);
    //         $mediaSavedCount = $mediaResult['saved_count'];

    //         EmergencyCaseLog::create([
    //             'emergency_case_id' => $case->id,
    //             'user_id'           => $userId,
    //             'action'            => 'case_reported',
    //             'old_status'        => null,
    //             'new_status'        => 'reported',
    //             'notes'             => 'Emergency case reported from mobile app.',
    //             'latitude'          => $request->latitude,
    //             'longitude'         => $request->longitude,
    //             'meta'              => [
    //                 'animal_type_id'      => $animalType->id,
    //                 'report_type_id'      => $reportType->id,
    //                 'animal_condition_id' => $animalCondition?->id,
    //                 'media_saved_count'   => $mediaSavedCount,
    //                 'upload_debug'        => $mediaResult['debug'],
    //             ],
    //         ]);

    //         DB::commit();

    //         $case->load([
    //             'animalType:id,name,slug,icon_class,color_code',
    //             'reportType:id,name,slug,icon_class,color_code',
    //             'animalCondition:id,name,slug,severity_level,icon_class,color_code',
    //             'reporter:id,name,mobile',
    //             'media',
    //         ]);

    //         $pushResult = [
    //             'success_count' => 0,
    //             'failure_count' => 0,
    //             'results'       => [],
    //             'message'       => 'Reporter notification not sent',
    //         ];

    //         try {
    //             $case->loadMissing('reporter');

    //             if ($case->reporter) {
    //                 $pushResult = $this->pushService->sendToUser(
    //                     user: $case->reporter,
    //                     title: 'Emergency Case Submitted',
    //                     body: 'Your emergency report ' . $case->case_uid . ' has been submitted successfully.',
    //                     data: [
    //                         'type'      => 'emergency_case_created',
    //                         'case_id'   => (string) $case->id,
    //                         'case_uid'  => (string) $case->case_uid,
    //                         'status'    => (string) $case->status,
    //                         'case_type' => (string) $case->case_type,
    //                         'severity'  => (string) $case->severity,
    //                         'screen'    => 'EmergencyCaseDetails',
    //                     ],
    //                     imageUrl: null,
    //                     platform: null,
    //                     sound: 'default',
    //                     androidChannelId: 'default'
    //                 );
    //             }
    //         } catch (\Throwable $e) {
    //             Log::error('Reporter Firebase notification failed', [
    //                 'case_id' => $case->id,
    //                 'user_id' => $case->reporter_id,
    //                 'error'   => $e->getMessage(),
    //             ]);

    //             $pushResult = [
    //                 'success_count' => 0,
    //                 'failure_count' => 1,
    //                 'results'       => [],
    //                 'message'       => $e->getMessage(),
    //             ];
    //         }

    //         $severityAlertResult = [
    //             'success_count' => 0,
    //             'failure_count' => 0,
    //             'results'       => [],
    //             'message'       => 'Severity alert not sent',
    //         ];

    //         try {
    //             $severityAlertResult = $this->caseAlertService->sendSeverityWiseAlert($case);
    //         } catch (\Throwable $e) {
    //             Log::error('Severity wise emergency alert failed', [
    //                 'case_id'  => $case->id,
    //                 'severity' => $case->severity,
    //                 'error'    => $e->getMessage(),
    //             ]);

    //             $severityAlertResult = [
    //                 'success_count' => 0,
    //                 'failure_count' => 1,
    //                 'results'       => [],
    //                 'message'       => $e->getMessage(),
    //             ];
    //         }

    //         return response()->json([
    //             'status'                => true,
    //             'message'               => 'Emergency case reported successfully.',
    //             'data'                  => $case,
    //             'media_count'           => $case->media->count(),
    //             'media_saved_count'     => $mediaSavedCount,
    //             'upload_debug'          => $mediaResult['debug'],
    //             'reporter_push_result'  => $pushResult,
    //             'severity_alert_result' => $severityAlertResult,
    //             'alerted_users'         => $severityAlertResult['success_count'] ?? 0,
    //         ], 201);

    //     } catch (\Throwable $e) {
    //         DB::rollBack();

    //         Log::error('Emergency case store failed', [
    //             'user_id' => auth('sanctum')->id(),
    //             'message' => $e->getMessage(),
    //             'file'    => $e->getFile(),
    //             'line'    => $e->getLine(),
    //         ]);

    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Server error. Emergency case could not be saved.',
    //             'error'   => config('app.debug') ? $e->getMessage() : null,
    //             'line'    => config('app.debug') ? $e->getLine() : null,
    //         ], 500);
    //     }
    // }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $userId = auth('sanctum')->id();

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
                'reporter_id'          => $userId,

                'animal_type_id'       => $animalType->id,
                'report_type_id'       => $reportType->id,
                'animal_condition_id'  => $animalCondition?->id,

                'case_type'            => $reportType->slug,
                'title'                => $request->title ?: $reportType->name,
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

            $mediaResult = $this->saveEmergencyCaseMedia($request, $case);
            $mediaSavedCount = $mediaResult['saved_count'];

            EmergencyCaseLog::create([
                'emergency_case_id' => $case->id,
                'user_id'           => $userId,
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
                    'media_saved_count'   => $mediaSavedCount,
                    'upload_debug'        => $mediaResult['debug'],
                ],
            ]);

            DB::commit();

            $case->load([
                'animalType:id,name,slug,icon_class,color_code',
                'reportType:id,name,slug,icon_class,color_code',
                'animalCondition:id,name,slug,severity_level,icon_class,color_code',
                'reporter:id,name,mobile',
                'media',
            ]);

            $pushResult = [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'Reporter notification not sent',
            ];

            try {
                $case->loadMissing('reporter');

                if ($case->reporter) {
                    $pushResult = $this->pushService->sendToUser(
                        user: $case->reporter,
                        title: 'Emergency Case Submitted',
                        body: 'Your emergency report ' . $case->case_uid . ' has been submitted successfully.',
                        data: [
                            'type'      => 'emergency_case_created',
                            'case_id'   => (string) $case->id,
                            'case_uid'  => (string) $case->case_uid,
                            'status'    => (string) $case->status,
                            'case_type' => (string) $case->case_type,
                            'severity'  => (string) $case->severity,
                            'screen'    => 'EmergencyCaseDetails',
                        ],
                        imageUrl: null,
                        platform: null,
                        sound: 'default',
                        androidChannelId: 'default'
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Reporter Firebase notification failed', [
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

            $severityAlertResult = [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'Severity alert not sent',
            ];

            try {
                $severityAlertResult = $this->caseAlertService->sendSeverityWiseAlert($case);
            } catch (\Throwable $e) {
                Log::error('Severity wise emergency alert failed', [
                    'case_id'  => $case->id,
                    'severity' => $case->severity,
                    'error'    => $e->getMessage(),
                ]);

                $severityAlertResult = [
                    'success_count' => 0,
                    'failure_count' => 1,
                    'results'       => [],
                    'message'       => $e->getMessage(),
                ];
            }

            return response()->json([
                'status'                => true,
                'message'               => 'Emergency case reported successfully.',
                'data'                  => $case,
                'media_count'           => $case->media->count(),
                'media_saved_count'     => $mediaSavedCount,
                'upload_debug'          => $mediaResult['debug'],
                'reporter_push_result'  => $pushResult,
                'severity_alert_result' => $severityAlertResult,
                'alerted_users'         => $severityAlertResult['success_count'] ?? 0,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Emergency case store failed', [
                'user_id' => auth('sanctum')->id(),
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error. Emergency case could not be saved.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    private function saveEmergencyCaseMedia(Request $request, EmergencyCase $case): array
    {
        $savedCount = 0;
        $userId = auth('sanctum')->id();

        $allFiles = $request->allFiles();

        $debug = [
            'content_type'           => $request->header('Content-Type'),
            'content_length'         => $request->header('Content-Length'),
            'all_file_keys'          => array_keys($allFiles),
            'has_photos'             => $request->hasFile('photos'),
            'has_videos'             => $request->hasFile('videos'),
            'php_file_uploads'       => ini_get('file_uploads'),
            'php_upload_max_filesize'=> ini_get('upload_max_filesize'),
            'php_post_max_size'      => ini_get('post_max_size'),
            'php_max_file_uploads'   => ini_get('max_file_uploads'),
            'saved_files'            => [],
            'skipped_files'          => [],
        ];

        Log::info('Emergency media request files received', [
            'case_id' => $case->id,
            'case_uid' => $case->case_uid,
            'debug' => $debug,
        ]);

        $normalizeFiles = function ($files) use (&$normalizeFiles): array {
            $normalized = [];

            if ($files instanceof UploadedFile) {
                return [$files];
            }

            if (is_array($files)) {
                foreach ($files as $file) {
                    $normalized = array_merge($normalized, $normalizeFiles($file));
                }
            }

            return $normalized;
        };

        $isPhoto = function (UploadedFile $file): bool {
            $mime = strtolower((string) $file->getMimeType());
            $ext = strtolower((string) $file->getClientOriginalExtension());

            return str_starts_with($mime, 'image/')
                || in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
        };

        $isVideo = function (UploadedFile $file): bool {
            $mime = strtolower((string) $file->getMimeType());
            $ext = strtolower((string) $file->getClientOriginalExtension());

            return str_starts_with($mime, 'video/')
                || in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv']);
        };

        $saveFile = function (UploadedFile $file, string $mediaType) use ($case, $userId, &$savedCount, &$debug, $isPhoto, $isVideo): void {
            if (!$file->isValid()) {
                $debug['skipped_files'][] = [
                    'reason'      => 'invalid_upload',
                    'media_type'  => $mediaType,
                    'error_code'  => $file->getError(),
                    'error_msg'   => $file->getErrorMessage(),
                    'file_name'   => $file->getClientOriginalName(),
                ];

                Log::warning('Invalid emergency media file skipped', [
                    'case_id'    => $case->id,
                    'case_uid'   => $case->case_uid,
                    'media_type' => $mediaType,
                    'error_code' => $file->getError(),
                    'error_msg'  => $file->getErrorMessage(),
                ]);

                return;
            }

            $fileSize = (int) $file->getSize();

            if ($mediaType === 'photo') {
                if (!$isPhoto($file)) {
                    $debug['skipped_files'][] = [
                        'reason'    => 'not_photo_file',
                        'file_name' => $file->getClientOriginalName(),
                        'mime'      => $file->getMimeType(),
                    ];
                    return;
                }

                if ($fileSize > 5 * 1024 * 1024) {
                    $debug['skipped_files'][] = [
                        'reason'    => 'photo_too_large',
                        'file_name' => $file->getClientOriginalName(),
                        'size'      => $fileSize,
                    ];
                    return;
                }
            }

            if ($mediaType === 'video') {
                if (!$isVideo($file)) {
                    $debug['skipped_files'][] = [
                        'reason'    => 'not_video_file',
                        'file_name' => $file->getClientOriginalName(),
                        'mime'      => $file->getMimeType(),
                    ];
                    return;
                }

                if ($fileSize > 50 * 1024 * 1024) {
                    $debug['skipped_files'][] = [
                        'reason'    => 'video_too_large',
                        'file_name' => $file->getClientOriginalName(),
                        'size'      => $fileSize,
                    ];
                    return;
                }
            }

            $folder = $mediaType === 'photo'
                ? 'emergency-cases/photos'
                : 'emergency-cases/videos';

            Storage::disk('public')->makeDirectory($folder);

            $originalName = $file->getClientOriginalName();

            $extension = strtolower(
                $file->getClientOriginalExtension()
                    ?: $file->guessExtension()
                    ?: ($mediaType === 'photo' ? 'jpg' : 'mp4')
            );

            $storedFileName = $case->case_uid
                . '-' . $mediaType
                . '-' . now()->format('YmdHis')
                . '-' . Str::random(10)
                . '.' . $extension;

            $path = $file->storeAs($folder, $storedFileName, 'public');

            if (!$path) {
                $debug['skipped_files'][] = [
                    'reason'    => 'storage_failed',
                    'file_name' => $originalName,
                ];
                return;
            }

            $media = EmergencyCaseMedia::create([
                'emergency_case_id' => $case->id,
                'user_id'           => $userId,
                'media_type'        => $mediaType,
                'file_path'         => $path,
                'file_name'         => $originalName,
                'mime_type'         => $file->getClientMimeType() ?: $file->getMimeType(),
                'file_size'         => $fileSize,
            ]);

            $savedCount++;

            $debug['saved_files'][] = [
                'id'         => $media->id,
                'media_type' => $mediaType,
                'file_path'  => $path,
                'file_name'  => $originalName,
                'mime_type'  => $file->getClientMimeType() ?: $file->getMimeType(),
                'file_size'  => $fileSize,
                'file_url'   => asset('storage/' . $path),
            ];

            Log::info('Emergency media file saved successfully', [
                'case_id'    => $case->id,
                'case_uid'   => $case->case_uid,
                'media_id'   => $media->id,
                'media_type' => $mediaType,
                'file_path'  => $path,
                'file_name'  => $originalName,
                'mime_type'  => $file->getClientMimeType() ?: $file->getMimeType(),
                'file_size'  => $fileSize,
            ]);
        };

        /*
        |--------------------------------------------------------------------------
        | Method 1: Standard expected fields
        |--------------------------------------------------------------------------
        */
        $photoFiles = [];
        $videoFiles = [];

        foreach (['photos', 'photos[]', 'photo', 'image', 'images', 'media_photos'] as $key) {
            if ($request->file($key)) {
                $photoFiles = array_merge($photoFiles, $normalizeFiles($request->file($key)));
            }
        }

        foreach (['videos', 'videos[]', 'video', 'media_videos'] as $key) {
            if ($request->file($key)) {
                $videoFiles = array_merge($videoFiles, $normalizeFiles($request->file($key)));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Method 2: Fallback from allFiles()
        |--------------------------------------------------------------------------
        */
        if (empty($photoFiles) && empty($videoFiles)) {
            foreach ($allFiles as $fieldKey => $files) {
                $flatFiles = $normalizeFiles($files);
                $fieldKeyLower = strtolower((string) $fieldKey);

                foreach ($flatFiles as $file) {
                    if (!$file instanceof UploadedFile) {
                        continue;
                    }

                    if (str_contains($fieldKeyLower, 'photo') || str_contains($fieldKeyLower, 'image')) {
                        $photoFiles[] = $file;
                    } elseif (str_contains($fieldKeyLower, 'video')) {
                        $videoFiles[] = $file;
                    } else {
                        if ($isPhoto($file)) {
                            $photoFiles[] = $file;
                        } elseif ($isVideo($file)) {
                            $videoFiles[] = $file;
                        }
                    }
                }
            }
        }

        foreach ($photoFiles as $photo) {
            if ($photo instanceof UploadedFile) {
                $saveFile($photo, 'photo');
            }
        }

        foreach ($videoFiles as $video) {
            if ($video instanceof UploadedFile) {
                $saveFile($video, 'video');
            }
        }

        $debug['final_photo_count_received'] = count($photoFiles);
        $debug['final_video_count_received'] = count($videoFiles);
        $debug['final_saved_count'] = $savedCount;

        Log::info('Emergency media upload completed', [
            'case_id'     => $case->id,
            'case_uid'    => $case->case_uid,
            'saved_count' => $savedCount,
            'debug'       => $debug,
        ]);

        return [
            'saved_count' => $savedCount,
            'debug'       => $debug,
        ];
    }

    public function acceptReport(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes'     => 'nullable|string|max:1000',
            'remarks'   => 'nullable|string|max:1000',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $userId = auth('sanctum')->id();

        try {
            /*
            |--------------------------------------------------------------------------
            | Accept Case Transaction
            |--------------------------------------------------------------------------
            */

            $case = DB::transaction(function () use ($request, $id, $userId) {
                $case = EmergencyCase::where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | Already Accepted By Same User
                |--------------------------------------------------------------------------
                */

                if ($case->status === 'accepted' && (int) $case->current_handler_id === (int) $userId) {
                    return $case;
                }

                /*
                |--------------------------------------------------------------------------
                | Already Accepted By Another User
                |--------------------------------------------------------------------------
                */

                if ($case->status === 'accepted' && (int) $case->current_handler_id !== (int) $userId) {
                    throw new \Illuminate\Http\Exceptions\HttpResponseException(
                        response()->json([
                            'status'  => false,
                            'message' => 'This case has already been accepted by another responder.',
                        ], 409)
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Status Validation
                |--------------------------------------------------------------------------
                */

                if (!in_array($case->status, ['reported', 'alerted', 'escalated'])) {
                    throw new \Illuminate\Http\Exceptions\HttpResponseException(
                        response()->json([
                            'status'  => false,
                            'message' => 'This case cannot be accepted now.',
                        ], 422)
                    );
                }

                $oldStatus = $case->status;
                $notes = $request->notes ?? $request->remarks ?? 'Case accepted.';

                /*
                |--------------------------------------------------------------------------
                | Create / Update Assignment
                |--------------------------------------------------------------------------
                */

                $assignment = EmergencyCaseAssignment::updateOrCreate(
                    [
                        'emergency_case_id' => $case->id,
                        'user_id'           => $userId,
                    ],
                    [
                        'assignment_role' => 'responder',
                        'status'          => 'accepted',
                        'accepted_at'     => now(),
                        'rejected_at'     => null,
                        'cancelled_at'    => null,
                        'notes'           => $notes,
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Update Emergency Case
                |--------------------------------------------------------------------------
                */

                $case->update([
                    'current_handler_id' => $userId,
                    'status'             => 'accepted',
                    'accepted_at'        => now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Cancel Other Pending Assignments
                |--------------------------------------------------------------------------
                */

                EmergencyCaseAssignment::where('emergency_case_id', $case->id)
                    ->where('user_id', '!=', $userId)
                    ->whereIn('status', ['pending', 'assigned', 'alerted'])
                    ->update([
                        'status'       => 'cancelled',
                        'cancelled_at' => now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Case Log
                |--------------------------------------------------------------------------
                */

                EmergencyCaseLog::create([
                    'emergency_case_id' => $case->id,
                    'user_id'           => $userId,
                    'action'            => 'case_accepted',
                    'old_status'        => $oldStatus,
                    'new_status'        => 'accepted',
                    'notes'             => $notes,
                    'latitude'          => $request->latitude,
                    'longitude'         => $request->longitude,
                    'meta'              => [
                        'assignment_id' => $assignment->id,
                    ],
                ]);

                return $case;
            });

            /*
            |--------------------------------------------------------------------------
            | Reload Case With Relations
            |--------------------------------------------------------------------------
            */

            $case = $case->fresh([
                'animalType',
                'reportType',
                'animalCondition',
                'media',
                'assignments.user:id,name,mobile',
                'currentHandler:id,name,mobile',
                'reporter:id,name,mobile',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Reporter Notification: Case Accepted
            |--------------------------------------------------------------------------
            */

            $reporterPushResult = [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'Reporter notification not sent',
            ];

            try {
                if ($case->reporter) {
                    $responderName = $case->currentHandler?->name ?? 'A responder';

                    $reporterPushResult = $this->pushService->sendToUser(
                        user: $case->reporter,
                        title: 'Emergency Case Accepted',
                        body: $responderName . ' has accepted your emergency case ' . $case->case_uid . ' and is going to the spot.',
                        data: [
                            'type'                 => 'emergency_case_accepted',
                            'case_id'              => (string) $case->id,
                            'case_uid'             => (string) $case->case_uid,
                            'status'               => (string) $case->status,
                            'case_type'            => (string) $case->case_type,
                            'severity'             => (string) $case->severity,
                            'current_handler_id'   => (string) $case->current_handler_id,
                            'current_handler_name' => (string) $responderName,
                            'screen'               => 'EmergencyCaseDetails',
                        ],
                        imageUrl: null,
                        platform: null,
                        sound: 'default',
                        androidChannelId: 'case_status_updates'
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Reporter accepted case Firebase notification failed', [
                    'case_id' => $case->id,
                    'user_id' => $case->reporter_id,
                    'error'   => $e->getMessage(),
                ]);

                $reporterPushResult = [
                    'success_count' => 0,
                    'failure_count' => 1,
                    'results'       => [],
                    'message'       => $e->getMessage(),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Severity Wise Location Alert: Case Accepted And Responder Going To Spot
            |--------------------------------------------------------------------------
            */

            $severityAcceptedAlertResult = [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'Severity accepted alert not sent',
            ];

            try {
                $severityAcceptedAlertResult = $this->caseAlertService
                    ->sendCaseAcceptedSeverityWiseAlert($case, $case->currentHandler);
            } catch (\Throwable $e) {
                Log::error('Severity wise accepted case alert failed', [
                    'case_id'  => $case->id,
                    'severity' => $case->severity,
                    'error'    => $e->getMessage(),
                ]);

                $severityAcceptedAlertResult = [
                    'success_count' => 0,
                    'failure_count' => 1,
                    'results'       => [],
                    'message'       => $e->getMessage(),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Final Response
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'status'                         => true,
                'message'                        => 'Case accepted successfully.',
                'data'                           => $case,
                'reporter_push_result'           => $reporterPushResult,
                'severity_accepted_alert_result' => $severityAcceptedAlertResult,
                'alerted_users'                  => $severityAcceptedAlertResult['success_count'] ?? 0,
            ]);

        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Accept report failed', [
                'case_id' => $id,
                'user_id' => $userId,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while accepting the case.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    public function rejectReport(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes'     => 'nullable|string|max:1000',
            'remarks'   => 'nullable|string|max:1000',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $userId = auth('sanctum')->id();

        try {
            $case = DB::transaction(function () use ($request, $id, $userId) {
                $case = EmergencyCase::where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (in_array($case->status, ['resolved', 'closed', 'deleted'])) {
                    abort(response()->json([
                        'status'  => false,
                        'message' => 'This case cannot be rejected now.',
                    ], 422));
                }

                $oldStatus = $case->status;
                $newStatus = $case->status;
                $notes = $request->notes ?? $request->remarks ?? 'Case rejected.';

                $assignment = EmergencyCaseAssignment::updateOrCreate(
                    [
                        'emergency_case_id' => $case->id,
                        'user_id'           => $userId,
                    ],
                    [
                        'assignment_role' => 'responder',
                        'status'          => 'rejected',
                        'rejected_at'     => now(),
                        'accepted_at'     => null,
                        'notes'           => $notes,
                    ]
                );

                /*
                * If the current accepted handler rejects the case,
                * release the case again so another responder can accept it.
                */
                if ($case->status === 'accepted' && (int) $case->current_handler_id === (int) $userId) {
                    $case->update([
                        'current_handler_id' => null,
                        'status'             => 'alerted',
                        'accepted_at'        => null,
                    ]);

                    $newStatus = 'alerted';
                }

                /*
                * Optional escalation:
                * If all assigned responders rejected and nobody accepted,
                * mark the case as escalated.
                */
                $acceptedCount = EmergencyCaseAssignment::where('emergency_case_id', $case->id)
                    ->where('status', 'accepted')
                    ->count();

                $pendingCount = EmergencyCaseAssignment::where('emergency_case_id', $case->id)
                    ->whereIn('status', ['pending', 'assigned', 'alerted'])
                    ->count();

                if ($acceptedCount === 0 && $pendingCount === 0 && in_array($case->status, ['reported', 'alerted'])) {
                    $case->update([
                        'status' => 'escalated',
                    ]);

                    $newStatus = 'escalated';
                }

                EmergencyCaseLog::create([
                    'emergency_case_id' => $case->id,
                    'user_id'           => $userId,
                    'action'            => 'case_rejected',
                    'old_status'        => $oldStatus,
                    'new_status'        => $newStatus,
                    'notes'             => $notes,
                    'latitude'          => $request->latitude,
                    'longitude'         => $request->longitude,
                    'meta'              => [
                        'assignment_id' => $assignment->id,
                    ],
                ]);

                return $case;
            });

            return response()->json([
                'status'  => true,
                'message' => 'Case rejected successfully.',
                'data'    => $case->fresh([
                    'animalType',
                    'reportType',
                    'animalCondition',
                    'media',
                    'assignments.user:id,name,mobile',
                    'currentHandler:id,name,mobile',
                ]),
            ]);
        } catch (\Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while rejecting the case.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
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


    public function myReports(Request $request)
    {
        try {
            $userId = auth('sanctum')->id();

            $query = EmergencyCase::with($this->caseRelations(false, true))
                ->where('reporter_id', $userId)
                ->latest();

            $this->applyCommonFilters($query, $request);

            $perPage = $this->getPerPage($request);

            $cases = $query->paginate($perPage);

            $cases->getCollection()->transform(function ($case) {
                return $this->appendMediaSummary($case);
            });

            return response()->json([
                'status'  => true,
                'message' => 'My emergency reports fetched successfully.',
                'data'    => $cases,
            ]);

        } catch (\Throwable $e) {
            Log::error('My emergency reports fetch failed', [
                'user_id' => auth('sanctum')->id(),
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error while fetching my emergency reports.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    public function myReportDetails($id)
    {
        try {
            $userId = auth('sanctum')->id();

            $case = EmergencyCase::with($this->caseRelations(true, true))
                ->where('reporter_id', $userId)
                ->find($id);

            if (!$case) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Emergency report not found.',
                ], 404);
            }

            $case = $this->appendMediaSummary($case);

            return response()->json([
                'status'  => true,
                'message' => 'My emergency report details fetched successfully.',
                'data'    => $case,
            ]);

        } catch (\Throwable $e) {
            Log::error('My emergency report details fetch failed', [
                'case_id' => $id,
                'user_id' => auth('sanctum')->id(),
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error while fetching report details.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    public function areaWiseReports(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'district'  => ['required', 'string', 'max:150'],
                'city'      => ['nullable', 'string', 'max:150'],
                'area_name' => ['nullable', 'string', 'max:150'],
                'pincode'   => ['nullable', 'digits:6'],
                'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'radius_km' => ['nullable', 'numeric', 'min:1', 'max:100'],
                'severity'  => ['nullable', Rule::in(EmergencyCase::SEVERITIES)],
                'status'    => ['nullable', Rule::in(EmergencyCase::STATUSES)],
                'per_page'  => ['nullable', 'integer', 'min:1', 'max:100'],
            ], [
                'district.required' => 'District is required for area wise reports.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $query = EmergencyCase::with($this->caseRelations(false, false))
                ->open()
                ->where(function ($q) use ($request) {
                    $this->whereTextEquals($q, 'district', $request->district);

                    if ($request->filled('pincode')) {
                        $q->orWhere('pincode', $request->pincode);
                    }
                })
                ->latest();

            if ($request->filled('city')) {
                $query->whereRaw('LOWER(TRIM(city)) = ?', [
                    strtolower(trim($request->city)),
                ]);
            }

            if ($request->filled('area_name')) {
                $query->whereRaw('LOWER(TRIM(area_name)) = ?', [
                    strtolower(trim($request->area_name)),
                ]);
            }

            if ($request->filled('severity')) {
                $query->where('severity', $request->severity);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('latitude') && $request->filled('longitude')) {
                $radiusKm = (float) $request->get('radius_km', 25);

                $this->applyRadiusFilter(
                    $query,
                    (float) $request->latitude,
                    (float) $request->longitude,
                    $radiusKm
                );
            }

            $perPage = $this->getPerPage($request);

            $cases = $query->paginate($perPage);

            $cases->getCollection()->transform(function ($case) {
                return $this->appendMediaSummary($case);
            });

            return response()->json([
                'status'  => true,
                'message' => 'Area wise emergency reports fetched successfully.',
                'filters' => [
                    'district'  => $request->district,
                    'city'      => $request->city,
                    'area_name' => $request->area_name,
                    'pincode'   => $request->pincode,
                    'latitude'  => $request->latitude,
                    'longitude' => $request->longitude,
                    'radius_km' => $request->radius_km,
                ],
                'data'    => $cases,
            ]);

        } catch (\Throwable $e) {
            Log::error('Area wise emergency reports fetch failed', [
                'district' => $request->district,
                'error'    => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error while fetching area wise reports.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }


    public function areaWiseReportDetails(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'district' => ['required', 'string', 'max:150'],
            ], [
                'district.required' => 'District is required for area wise report details.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $case = EmergencyCase::with($this->caseRelations(true, false))
                ->open()
                ->where(function ($q) use ($request) {
                    $this->whereTextEquals($q, 'district', $request->district);

                    if ($request->filled('pincode')) {
                        $q->orWhere('pincode', $request->pincode);
                    }
                })
                ->find($id);

            if (!$case) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Emergency report not found in this district.',
                ], 404);
            }

            $case = $this->appendMediaSummary($case);

            return response()->json([
                'status'  => true,
                'message' => 'Area wise emergency report details fetched successfully.',
                'data'    => $case,
            ]);

        } catch (\Throwable $e) {
            Log::error('Area wise emergency report details fetch failed', [
                'case_id'  => $id,
                'district' => $request->district,
                'error'    => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error while fetching area wise report details.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
                'line'    => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    private function caseRelations(bool $withLogs = false, bool $privateUserData = true): array
    {
        $relations = [
            'animalType:id,name,slug,icon_class,color_code',
            'reportType:id,name,slug,icon_class,color_code',
            'animalCondition:id,name,slug,severity_level,icon_class,color_code,symptoms,first_aid_steps,description',
            $privateUserData
                ? 'reporter:id,name,mobile'
                : 'reporter:id,name',
            $privateUserData
                ? 'currentHandler:id,name,mobile'
                : 'currentHandler:id,name',
            'media:id,emergency_case_id,user_id,media_type,file_path,file_name,mime_type,file_size,created_at',
        ];

        if ($withLogs) {
            $relations['logs'] = function ($query) {
                $query->latest();
            };
        }

        return $relations;
    }

    private function applyCommonFilters($query, Request $request): void
    {
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

        if ($request->filled('district')) {
            $query->where('district', 'LIKE', '%' . $request->district . '%');
        }

        if ($request->filled('pincode')) {
            $query->where('pincode', $request->pincode);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
    }

    private function appendMediaSummary(EmergencyCase $case): EmergencyCase
    {
        $media = $case->media ?? collect();

        $photos = $media->where('media_type', 'photo')->values();
        $videos = $media->where('media_type', 'video')->values();

        $case->setAttribute('media_count', $media->count());
        $case->setAttribute('photo_count', $photos->count());
        $case->setAttribute('video_count', $videos->count());

        $case->setAttribute('photos', $photos);
        $case->setAttribute('videos', $videos);

        $case->setAttribute('photo_urls', $photos->pluck('file_url')->filter()->values());
        $case->setAttribute('video_urls', $videos->pluck('file_url')->filter()->values());

        return $case;
    }

    private function getPerPage(Request $request): int
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($perPage <= 0) {
            return 15;
        }

        return min($perPage, 100);
    }

    private function whereTextEquals($query, string $column, ?string $value): void
    {
        $value = trim((string) $value);

        if ($value === '') {
            return;
        }

        $query->whereRaw("LOWER(TRIM($column)) = ?", [
            strtolower($value),
        ]);
    }


    private function applyRadiusFilter($query, float $latitude, float $longitude, float $radiusKm): void
    {
        $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereRaw(
                '(6371 * ACOS(LEAST(1, GREATEST(-1,
                    COS(RADIANS(?)) *
                    COS(RADIANS(latitude)) *
                    COS(RADIANS(longitude) - RADIANS(?)) +
                    SIN(RADIANS(?)) *
                    SIN(RADIANS(latitude))
                )))) <= ?',
                [$latitude, $longitude, $latitude, $radiusKm]
            );
    }

}