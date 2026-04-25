<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\NotificationCampaign;
use App\Models\NotificationRecipient;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\FirebasePushService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
            'total'            => NotificationCampaign::count(),
            'sent'             => NotificationCampaign::where('status', 'sent')->count(),
            'failed'           => NotificationCampaign::where('status', 'failed')->count(),
            'partially_failed' => NotificationCampaign::where('status', 'partially_failed')->count(),
        ];

        $states = $this->getAddressOptions('state');
        $districts = $this->getAddressOptions('district');
        $areas = $this->getAddressOptions('area_name');

        return view('admin.notifications.index', compact(
            'campaigns',
            'stats',
            'states',
            'districts',
            'areas'
        ));
    }

    public function addressOptions(Request $request)
    {
        $request->validate([
            'column'   => 'required|string|in:state,district,area_name',
            'state'    => 'nullable|string|max:120',
            'district' => 'nullable|string|max:120',
        ]);

        $options = $this->getAddressOptions(
            $request->column,
            $request->only(['state', 'district'])
        );

        return response()->json([
            'status' => true,
            'data'   => $options,
        ]);
    }

    public function searchUsers(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $search = trim($request->q);

        $users = User::query()
            ->select('id', 'name', 'mobile', 'status')
            ->with([
                'latestAddress:id,user_id,state,district,city,village,area_name,pincode',
            ])
            ->withCount([
                'deviceTokens as active_devices_count' => function ($query) {
                    $query->active()->withNotificationToken();
                },
            ])
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            })
            ->orderByDesc('active_devices_count')
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function ($user) {
                $address = $user->latestAddress;

                $addressText = collect([
                    $address?->area_name,
                    $address?->village,
                    $address?->city,
                    $address?->district,
                    $address?->state,
                    $address?->pincode,
                ])->filter()->implode(', ');

                return [
                    'id'                   => $user->id,
                    'name'                 => $user->name ?: 'No Name',
                    'mobile'               => $user->mobile,
                    'status'               => $user->status,
                    'address'              => $addressText ?: 'Address not available',
                    'active_devices_count' => $user->active_devices_count,
                    'can_receive'          => $user->active_devices_count > 0,
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $users,
        ]);
    }

    public function preview(Request $request)
    {
        $validator = $this->notificationValidator($request, false);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $uniqueDevices = $this->getUniqueLatestDevices($request);

        return response()->json([
            'status'  => true,
            'message' => 'Preview generated successfully',
            'data'    => [
                'total_users'     => $uniqueDevices->pluck('user_id')->filter()->unique()->count(),
                'total_devices'   => $uniqueDevices->count(),
                'android_devices' => $uniqueDevices->where('platform', 'android')->count(),
                'ios_devices'     => $uniqueDevices->where('platform', 'ios')->count(),
                'web_devices'     => $uniqueDevices->where('platform', 'web')->count(),
            ],
        ]);
    }

    public function send(Request $request)
    {
        $validator = $this->notificationValidator($request, true);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $uniqueDevices = $this->getUniqueLatestDevices($request);

        if ($uniqueDevices->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'No active device token found for selected target.');
        }

        $imageUrl = $this->uploadNotificationImage($request);

        $campaign = NotificationCampaign::create([
            'notification_type' => $request->notification_type,
            'title'             => $request->title,
            'message'           => $request->message,
            'target_scope'      => $request->target_scope,
            'target_filters'    => $this->targetFiltersFromRequest($request),
            'image_url'         => $imageUrl,
            'action_url'        => $request->action_url,
            'related_type'      => $request->related_type,
            'related_id'        => $request->related_id,
            'status'            => 'sending',
            'total_users'       => $uniqueDevices->pluck('user_id')->filter()->unique()->count(),
            'total_devices'     => $uniqueDevices->count(),
            'sent_by'           => $this->currentAdminId(),
        ]);

        try {
            $tokens = $uniqueDevices
                ->pluck('notification_token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $payload = [
                'notification_type' => $campaign->notification_type,
                'campaign_id'       => (string) $campaign->id,
                'target_scope'      => $campaign->target_scope,
                'related_type'      => $campaign->related_type ?? '',
                'related_id'        => $campaign->related_id ? (string) $campaign->related_id : '',
                'action_url'        => $campaign->action_url ?? '',
                'image_url'         => $campaign->image_url ?? '',
            ];

            $result = $this->firebasePushService->sendToTokens(
                tokens: $tokens,
                title: $campaign->title,
                body: $campaign->message,
                data: $payload,
                imageUrl: $campaign->image_url
            );

            $deviceByToken = $uniqueDevices->keyBy('notification_token');

            foreach (($result['results'] ?? []) as $item) {
                $token = $item['token'] ?? null;
                $device = $token ? $deviceByToken->get($token) : null;

                NotificationRecipient::create([
                    'notification_campaign_id' => $campaign->id,
                    'user_id'                  => $device?->user_id,
                    'device_token_id'          => $device?->id,
                    'platform'                 => $device?->platform,
                    'fcm_token'                => $token,
                    'status'                   => ($item['status'] ?? null) === 'sent' ? 'sent' : 'failed',
                    'error_message'            => $item['message'] ?? null,
                    'sent_at'                  => ($item['status'] ?? null) === 'sent' ? now() : null,
                ]);
            }

            $successCount = (int) ($result['success_count'] ?? 0);
            $failureCount = (int) ($result['failure_count'] ?? 0);
            $firstError = $result['first_error'] ?? null;

            $status = 'failed';

            if ($successCount > 0 && $failureCount > 0) {
                $status = 'partially_failed';
            } elseif ($successCount > 0) {
                $status = 'sent';
            }

            $campaign->update([
                'status'        => $status,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'sent_at'       => now(),
                'error_message' => $failureCount > 0
                    ? ($firstError ?: ($result['message'] ?? 'Notification sending failed.'))
                    : null,
            ]);

            if ($successCount > 0) {
                return redirect()
                    ->route('admin.notifications.index')
                    ->with('success', "Notification sent. Success: {$successCount}, Failed: {$failureCount}");
            }

            return redirect()
                ->route('admin.notifications.index')
                ->with('error', 'Notification failed: ' . ($firstError ?: 'Firebase rejected all tokens.'));
        } catch (Throwable $e) {
            $campaign->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at'       => now(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Notification sending failed: ' . $e->getMessage());
        }
    }

    private function getUniqueLatestDevices(Request $request)
    {
        return $this->targetDeviceQuery($request)
            ->get()
            ->filter(fn ($device) => !empty($device->notification_token))
            ->sortByDesc(fn ($device) => optional($device->last_used_at)->timestamp ?: $device->id)
            ->unique('user_id')
            ->unique(fn ($device) => $device->notification_token)
            ->values();
    }


    private function uploadNotificationImage(Request $request): ?string
    {
        if ($request->hasFile('notification_image')) {
            $file = $request->file('notification_image');

            $folder = public_path('uploads/notification-images');

            if (!is_dir($folder)) {
                mkdir($folder, 0775, true);
            }

            $fileName = 'notification_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

            $file->move($folder, $fileName);

            return asset('uploads/notification-images/' . $fileName);
        }

        if ($request->filled('image_url')) {
            return $request->image_url;
        }

        return null;
    }

    private function notificationValidator(Request $request, bool $validateImage = true)
    {
        $rules = [
            'notification_type' => 'required|string|in:general,case_report,news_notice,rescue_alert,legal_awareness,gaushala_requirement,weather_alert,custom',
            'title'             => 'required|string|max:150',
            'message'           => 'required|string|max:500',
            'target_scope'      => 'required|string|in:all,area,users',
            'platform'          => 'nullable|string|in:android,ios,web',
            'state'             => 'nullable|string|max:120',
            'district'          => 'nullable|string|max:120',
            'area_name'         => 'nullable|string|max:150',
            'selected_user_ids'   => 'nullable|array',
            'selected_user_ids.*' => 'integer|exists:users,id',
            'image_url'         => 'nullable|url|max:500',
            'action_url'        => 'nullable|string|max:255',
            'related_type'      => 'nullable|string|in:custom,emergency_case,news_notice',
            'related_id'        => 'nullable|integer',
        ];

        if ($validateImage) {
            $rules['notification_image'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            if ($request->target_scope === 'area') {
                $filters = collect($request->only([
                    'state',
                    'district',
                    'area_name',
                ]))->filter(fn ($value) => $value !== null && $value !== '');

                if ($filters->isEmpty()) {
                    $validator->errors()->add(
                        'target_scope',
                        'Please select State, District or Area for area-wise notification.'
                    );
                }
            }

            if ($request->target_scope === 'users') {
                $selectedUsers = collect($request->selected_user_ids ?? [])->filter();

                if ($selectedUsers->isEmpty()) {
                    $validator->errors()->add(
                        'selected_user_ids',
                        'Please search and select at least one registered user.'
                    );
                }
            }
        });

        return $validator;
    }

    private function targetDeviceQuery(Request $request)
    {
        $query = DeviceToken::query()
            ->active()
            ->withNotificationToken();

        if ($request->filled('platform')) {
            $query->platform($request->platform);
        }

        if ($request->target_scope === 'users') {
            $userIds = collect($request->selected_user_ids ?? [])
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $query->whereIn('user_id', $userIds);
        }

        if ($request->target_scope === 'area') {
            $filters = collect($request->only([
                'state',
                'district',
                'area_name',
            ]))
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->toArray();

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

                foreach ($filters as $column => $value) {
                    if (Schema::hasColumn('user_addresses', $column)) {
                        $q->where("user_addresses.$column", $value);
                    }
                }
            });
        }

        return $query
            ->orderByDesc('last_used_at')
            ->orderByDesc('id');
    }

    private function targetFiltersFromRequest(Request $request): array
    {
        $filters = collect($request->only([
            'state',
            'district',
            'area_name',
            'platform',
        ]))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->toArray();

        if ($request->target_scope === 'users') {
            $filters['selected_user_ids'] = collect($request->selected_user_ids ?? [])
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }

        return $filters;
    }

    private function getAddressOptions(string $column, array $filters = [])
    {
        if (!Schema::hasTable('user_addresses') || !Schema::hasColumn('user_addresses', $column)) {
            return collect();
        }

        $query = UserAddress::query()
            ->whereNotNull($column)
            ->where($column, '!=', '');

        if (Schema::hasColumn('user_addresses', 'status')) {
            $query->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', '!=', 'deleted');
            });
        }

        if (!empty($filters['state']) && Schema::hasColumn('user_addresses', 'state')) {
            $query->where('state', $filters['state']);
        }

        if (!empty($filters['district']) && Schema::hasColumn('user_addresses', 'district')) {
            $query->where('district', $filters['district']);
        }

        return $query
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