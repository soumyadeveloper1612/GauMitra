<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseAlert;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmergencyCaseAlertService
{
    public function __construct(
        private readonly FirebasePushService $firebasePushService
    ) {
    }

    public function notifySameAreaUsers(EmergencyCase $case): array
    {
        $areaName = $this->normalize($case->area_name);
        $city = $this->normalize($case->city);
        $district = $this->normalize($case->district);

        if (!$areaName || !$city || !$district) {
            Log::warning('Emergency alert skipped because location data is incomplete.', [
                'case_id' => $case->id,
                'area_name' => $case->area_name,
                'city' => $case->city,
                'district' => $case->district,
            ]);

            return [
                'status' => false,
                'message' => 'Case location incomplete. area_name, city and district are required.',
                'matched_users' => 0,
                'tokens' => 0,
            ];
        }

        /**
         * Find users whose saved address matches:
         * same area_name + same city + same district.
         */
        $matchedUserIds = UserAddress::query()
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', '!=', 'deleted');
            })
            ->whereRaw('LOWER(TRIM(area_name)) = ?', [$areaName])
            ->whereRaw('LOWER(TRIM(city)) = ?', [$city])
            ->whereRaw('LOWER(TRIM(district)) = ?', [$district])
            ->when($case->reporter_id, function ($q) use ($case) {
                $q->where('user_id', '!=', $case->reporter_id);
            })
            ->distinct()
            ->pluck('user_id')
            ->filter()
            ->values();

        if ($matchedUserIds->isEmpty()) {
            return [
                'status' => true,
                'message' => 'No same-area registered users found.',
                'matched_users' => 0,
                'tokens' => 0,
            ];
        }

        $users = User::query()
            ->whereIn('id', $matchedUserIds)
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', 'active');
            })
            ->with(['deviceTokens' => function ($q) {
                $q->active()->withFcmToken();
            }])
            ->get();

        $tokens = [];
        $alertRows = [];

        foreach ($users as $user) {
            foreach ($user->deviceTokens as $deviceToken) {
                $tokens[] = $deviceToken->fcm_token;

                $alertRows[] = [
                    'emergency_case_id' => $case->id,
                    'user_id' => $user->id,
                    'fcm_token' => $deviceToken->fcm_token,
                    'platform' => $deviceToken->platform,
                    'area_name' => $case->area_name,
                    'city' => $case->city,
                    'district' => $case->district,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $tokens = array_values(array_unique(array_filter($tokens)));

        if (empty($tokens)) {
            return [
                'status' => true,
                'message' => 'Same-area users found, but no active FCM tokens found.',
                'matched_users' => $users->count(),
                'tokens' => 0,
            ];
        }

        if (!empty($alertRows)) {
            EmergencyCaseAlert::insert($alertRows);
        }

        $title = $this->makeTitle($case);
        $body = $this->makeBody($case);

        $result = $this->firebasePushService->sendEmergencyAlertToTokens(
            tokens: $tokens,
            title: $title,
            body: $body,
            data: [
                'type' => 'emergency_case_alert',
                'case_id' => $case->id,
                'case_uid' => $case->case_uid,
                'severity' => $case->severity,
                'status' => $case->status,
                'area_name' => $case->area_name,
                'city' => $case->city,
                'district' => $case->district,
                'latitude' => $case->latitude,
                'longitude' => $case->longitude,
            ]
        );

        EmergencyCaseAlert::where('emergency_case_id', $case->id)
            ->whereIn('fcm_token', $tokens)
            ->update([
                'status' => $result['success'] ? 'sent' : 'failed',
                'sent_at' => $result['success'] ? now() : null,
                'error_message' => $result['success']
                    ? null
                    : json_encode($result['failed_tokens'] ?? []),
                'updated_at' => now(),
            ]);

        return [
            'status' => true,
            'message' => 'Emergency alert process completed.',
            'matched_users' => $users->count(),
            'tokens' => count($tokens),
            'firebase_result' => $result,
        ];
    }

    private function normalize(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        return Str::of($value)
            ->lower()
            ->trim()
            ->replaceMatches('/\s+/', ' ')
            ->toString();
    }

    private function makeTitle(EmergencyCase $case): string
    {
        $severity = strtoupper($case->severity ?? 'ALERT');

        return "GauMitra {$severity} Emergency Alert";
    }

    private function makeBody(EmergencyCase $case): string
    {
        $area = $case->area_name ?: 'your area';
        $city = $case->city ?: '';
        $district = $case->district ?: '';

        return "Emergency case reported near {$area}, {$city}, {$district}. Tap to view and help.";
    }
}