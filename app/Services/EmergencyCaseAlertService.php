<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseAlert;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmergencyCaseAlertService
{
    public function __construct(
        protected FirebasePushService $pushService
    ) {
    }

    public function sendSeverityWiseAlert(EmergencyCase $case): array
    {
        try {
            $areaName = $this->normalize($case->area_name);
            $city = $this->normalize($case->city);
            $district = $this->normalize($case->district);

            if (!$areaName || !$city || !$district) {
                return [
                    'success_count' => 0,
                    'failure_count' => 0,
                    'results'       => [],
                    'message'       => 'Area name, city or district missing. Area alert skipped.',
                    'first_error'   => null,
                    'matched_users' => 0,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Find same area + city + district users
            |--------------------------------------------------------------------------
            | Example:
            | case area_name = Niladri Bihar
            | case city      = Bhubaneswar
            | case district  = Khordha
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
                    /*
                    |--------------------------------------------------------------------------
                    | Reporter already gets self-success notification.
                    | So area alert will go to other same-area users.
                    |--------------------------------------------------------------------------
                    */
                    $q->where('user_id', '!=', $case->reporter_id);
                })
                ->distinct()
                ->pluck('user_id')
                ->filter()
                ->unique()
                ->values();

            if ($matchedUserIds->isEmpty()) {
                return [
                    'success_count' => 0,
                    'failure_count' => 0,
                    'results'       => [],
                    'message'       => 'No same area registered users found.',
                    'first_error'   => null,
                    'matched_users' => 0,
                ];
            }

            $deviceTokens = DeviceToken::query()
                ->whereIn('user_id', $matchedUserIds)
                ->active()
                ->withFcmToken()
                ->get();

            if ($deviceTokens->isEmpty()) {
                return [
                    'success_count' => 0,
                    'failure_count' => 0,
                    'results'       => [],
                    'message'       => 'Same-area users found, but no active FCM token found.',
                    'first_error'   => null,
                    'matched_users' => $matchedUserIds->count(),
                ];
            }

            $tokens = $deviceTokens
                ->pluck('fcm_token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            foreach ($deviceTokens as $deviceToken) {
                EmergencyCaseAlert::create([
                    'emergency_case_id' => $case->id,
                    'user_id'           => $deviceToken->user_id,
                    'fcm_token'         => $deviceToken->fcm_token,
                    'platform'          => $deviceToken->platform,
                    'area_name'         => $case->area_name,
                    'city'              => $case->city,
                    'district'          => $case->district,
                    'status'            => 'pending',
                ]);
            }

            $title = $this->makeAlertTitle($case);

            $body = 'Emergency case reported near '
                . ($case->area_name ?: 'your area')
                . ', '
                . ($case->city ?: '')
                . ', '
                . ($case->district ?: '')
                . '. Tap to view and help.';

            $result = $this->pushService->sendToTokens(
                tokens: $tokens,
                title: $title,
                body: $body,
                data: [
                    'type'       => 'emergency_case_alert',
                    'case_id'    => (string) $case->id,
                    'case_uid'   => (string) $case->case_uid,
                    'severity'   => (string) $case->severity,
                    'status'     => (string) $case->status,
                    'area_name'  => (string) $case->area_name,
                    'city'       => (string) $case->city,
                    'district'   => (string) $case->district,
                    'screen'     => 'EmergencyCaseDetails',
                    'latitude'   => (string) ($case->latitude ?? ''),
                    'longitude'  => (string) ($case->longitude ?? ''),
                ],
                sound: 'gau_alert',
                androidChannelId: 'gau_mitra_emergency_alerts'
            );

            $sentStatus = ($result['success_count'] ?? 0) > 0 ? 'sent' : 'failed';

            EmergencyCaseAlert::where('emergency_case_id', $case->id)
                ->whereIn('fcm_token', $tokens)
                ->update([
                    'status'        => $sentStatus,
                    'sent_at'       => $sentStatus === 'sent' ? now() : null,
                    'error_message' => $result['first_error'] ?? null,
                    'updated_at'    => now(),
                ]);

            $result['matched_users'] = $matchedUserIds->count();

            return $result;
        } catch (\Throwable $e) {
            Log::error('Same area emergency alert failed', [
                'case_id' => $case->id,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return [
                'success_count' => 0,
                'failure_count' => 1,
                'results'       => [],
                'message'       => $e->getMessage(),
                'first_error'   => $e->getMessage(),
                'matched_users' => 0,
            ];
        }
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

    private function makeAlertTitle(EmergencyCase $case): string
    {
        $severity = strtoupper($case->severity ?? 'EMERGENCY');

        return "GauMitra {$severity} Alert";
    }
}