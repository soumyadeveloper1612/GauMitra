<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\NotificationCampaign;
use App\Models\NotificationRecipient;
use App\Models\UserAddress;
use App\Services\FirebasePushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Throwable;

class NotificationController extends Controller
{
    public function __construct(
        protected FirebasePushService $firebasePushService
    ) {
    }

    public function index()
    {
        $campaigns = NotificationCampaign::latest()->paginate(10);

        $stats = [
            'total' => NotificationCampaign::count(),
            'sent' => NotificationCampaign::where('status', 'sent')->count(),
            'failed' => NotificationCampaign::where('status', 'failed')->count(),
            'partially_failed' => NotificationCampaign::where('status', 'partially_failed')->count(),
        ];

        $states = $this->addressOptions('state');
        $districts = $this->addressOptions('district');
        $cities = $this->addressOptions('city');
        $policeStations = $this->addressOptions('police_station');
        $villages = $this->addressOptions('village');
        $pincodes = $this->addressOptions('pincode');
        $areas = $this->addressOptions('area_name');

        return view('admin.notifications.index', compact(
            'campaigns',
            'stats',
            'states',
            'districts',
            'cities',
            'policeStations',
            'villages',
            'pincodes',
            'areas'
        ));
    }

    public function preview(Request $request)
    {
        $validator = $this->notificationValidator($request);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $devices = $this->targetDeviceQuery($request)->get([
            'id',
            'user_id',
            'platform',
            'fcm_token',
        ]);

        $uniqueDevices = $devices->unique('fcm_token')->values();

        return response()->json([
            'status' => true,
            'message' => 'Preview generated successfully',
            'data' => [
                'total_users' => $uniqueDevices->pluck('user_id')->filter()->unique()->count(),
                'total_devices' => $uniqueDevices->count(),
                'android_devices' => $uniqueDevices->where('platform', 'android')->count(),
                'ios_devices' => $uniqueDevices->where('platform', 'ios')->count(),
                'web_devices' => $uniqueDevices->where('platform', 'web')->count(),
            ],
        ]);
    }

    public function send(Request $request)
    {
        $validator = $this->notificationValidator($request);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $devices = $this->targetDeviceQuery($request)->get();

        $uniqueDevices = $devices
            ->filter(fn ($device) => !empty($device->fcm_token))
            ->unique('fcm_token')
            ->values();

        if ($uniqueDevices->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'No active device token found for selected target area.');
        }

        $campaign = NotificationCampaign::create([
            'notification_type' => $request->notification_type,
            'title' => $request->title,
            'message' => $request->message,
            'target_scope' => $request->target_scope,
            'target_filters' => $this->targetFiltersFromRequest($request),
            'image_url' => $request->image_url,
            'action_url' => $request->action_url,
            'related_type' => $request->related_type,
            'related_id' => $request->related_id,
            'status' => 'sending',
            'total_users' => $uniqueDevices->pluck('user_id')->filter()->unique()->count(),
            'total_devices' => $uniqueDevices->count(),
            'sent_by' => $this->currentAdminId(),
        ]);

        try {
            $tokens = $uniqueDevices->pluck('fcm_token')->filter()->unique()->values()->toArray();

            $payload = [
                'notification_type' => $campaign->notification_type,
                'campaign_id' => (string) $campaign->id,
                'target_scope' => $campaign->target_scope,
                'related_type' => $campaign->related_type ?? '',
                'related_id' => $campaign->related_id ? (string) $campaign->related_id : '',
                'action_url' => $campaign->action_url ?? '',
            ];

            $result = $this->firebasePushService->sendToTokens(
                tokens: $tokens,
                title: $campaign->title,
                body: $campaign->message,
                data: $payload
            );

            $deviceByToken = $uniqueDevices->keyBy('fcm_token');

            foreach (($result['results'] ?? []) as $item) {
                $token = $item['token'] ?? null;
                $device = $token ? $deviceByToken->get($token) : null;

                NotificationRecipient::create([
                    'notification_campaign_id' => $campaign->id,
                    'user_id' => $device?->user_id,
                    'device_token_id' => $device?->id,
                    'platform' => $device?->platform,
                    'fcm_token' => $token,
                    'status' => ($item['status'] ?? null) === 'sent' ? 'sent' : 'failed',
                    'error_message' => $item['message'] ?? null,
                    'sent_at' => ($item['status'] ?? null) === 'sent' ? now() : null,
                ]);
            }

            $successCount = (int) ($result['success_count'] ?? 0);
            $failureCount = (int) ($result['failure_count'] ?? 0);

            $status = 'failed';

            if ($successCount > 0 && $failureCount > 0) {
                $status = 'partially_failed';
            } elseif ($successCount > 0) {
                $status = 'sent';
            }

            $campaign->update([
                'status' => $status,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'sent_at' => now(),
                'error_message' => $status === 'failed'
                    ? ($result['message'] ?? 'Notification sending failed.')
                    : null,
            ]);

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', "Notification sent. Success: {$successCount}, Failed: {$failureCount}");
        } catch (Throwable $e) {
            $campaign->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Notification sending failed: ' . $e->getMessage());
        }
    }

    private function notificationValidator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_type' => 'required|string|in:general,case_report,news_notice,rescue_alert,legal_awareness,gaushala_requirement,weather_alert,custom',
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:500',

            'target_scope' => 'required|string|in:all,area',
            'platform' => 'nullable|string|in:android,ios,web',

            'state' => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'police_station' => 'nullable|string|max:120',
            'village' => 'nullable|string|max:120',
            'pincode' => 'nullable|string|max:20',
            'area_name' => 'nullable|string|max:150',

            'image_url' => 'nullable|string|max:255',
            'action_url' => 'nullable|string|max:255',
            'related_type' => 'nullable|string|in:custom,emergency_case,news_notice',
            'related_id' => 'nullable|integer',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->target_scope === 'area') {
                $filters = collect($request->only([
                    'state',
                    'district',
                    'city',
                    'police_station',
                    'village',
                    'pincode',
                    'area_name',
                ]))->filter(fn ($value) => $value !== null && $value !== '');

                if ($filters->isEmpty()) {
                    $validator->errors()->add(
                        'target_scope',
                        'Please select at least one area filter for area-wise notification.'
                    );
                }
            }
        });

        return $validator;
    }

    private function targetDeviceQuery(Request $request)
    {
        $query = DeviceToken::query()
            ->where('is_active', true)
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '');

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->target_scope === 'area') {
            $filters = $this->targetFiltersFromRequest($request);
            unset($filters['platform']);

            $query->whereExists(function ($q) use ($filters) {
                $q->select(DB::raw(1))
                    ->from('user_addresses')
                    ->whereColumn('user_addresses.user_id', 'device_tokens.user_id');

                if (Schema::hasColumn('user_addresses', 'status')) {
                    $q->where(function ($statusQuery) {
                        $statusQuery->whereNull('user_addresses.status')
                            ->orWhere('user_addresses.status', '!=', 'deleted');
                    });
                }

                $applied = false;

                foreach ($filters as $column => $value) {
                    if (Schema::hasColumn('user_addresses', $column)) {
                        $q->where("user_addresses.$column", $value);
                        $applied = true;
                    }
                }

                if (!$applied) {
                    $q->whereRaw('1 = 0');
                }
            });
        }

        return $query->orderByDesc('last_used_at');
    }

    private function targetFiltersFromRequest(Request $request): array
    {
        return collect($request->only([
            'state',
            'district',
            'city',
            'police_station',
            'village',
            'pincode',
            'area_name',
            'platform',
        ]))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->toArray();
    }

    private function addressOptions(string $column)
    {
        if (!Schema::hasTable('user_addresses') || !Schema::hasColumn('user_addresses', $column)) {
            return collect();
        }

        return UserAddress::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column);
    }

    private function currentAdminId(): ?int
    {
        return session('admin_id')
            ?? session('admin_user_id')
            ?? auth()->id();
    }
}