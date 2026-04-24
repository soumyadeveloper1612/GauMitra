<?php

namespace App\Services;

use App\Models\EmergencyCaseAlert;
use App\Models\LoginOtp;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmergencyCaseService
{
    public function generateCaseUid()
    {
        return 'CASE-' . now()->format('YmdHis') . rand(100, 999);
    }

    public function log($case, $userId, $action, $oldStatus = null, $newStatus = null, $notes = null, $meta = [], $latitude = null, $longitude = null)
    {
        return $case->logs()->create([
            'user_id'    => $userId,
            'action'     => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes'      => $notes,
            'latitude'   => $latitude,
            'longitude'  => $longitude,
            'meta'       => $meta,
        ]);
    }

    public function sendCaseAlerts($case, $radiusKm = 20)
    {
        $lat = (float) $case->latitude;
        $lng = (float) $case->longitude;

        $nearbyUsers = UserAddress::select(
                'user_addresses.*',
                DB::raw("(
                    6371 * acos(
                        cos(radians(?))
                        * cos(radians(user_addresses.latitude))
                        * cos(radians(user_addresses.longitude) - radians(?))
                        + sin(radians(?))
                        * sin(radians(user_addresses.latitude))
                    )
                ) as distance")
            )
            ->addBinding([$lat, $lng, $lat], 'select')
            ->join('users', 'users.id', '=', 'user_addresses.user_id')
            ->whereNotNull('user_addresses.latitude')
            ->whereNotNull('user_addresses.longitude')
            ->where('users.id', '!=', $case->reporter_id)
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance', 'asc')
            ->get();

        $alertCount = 0;

        foreach ($nearbyUsers as $address) {
            if (class_exists(EmergencyCaseAlert::class)) {
                EmergencyCaseAlert::create([
                    'emergency_case_id' => $case->id,
                    'user_id'           => $address->user_id,
                    'alert_type'        => 'nearby_emergency',
                    'status'            => 'sent',
                    'sent_at'           => now(),
                    'meta'              => [
                        'distance_km' => round($address->distance, 2),
                        'case_type'   => $case->case_type,
                        'severity'    => $case->severity,
                    ],
                ]);
            }

            $tokens = LoginOtp::where('user_id', $address->user_id)
                ->whereNotNull('verified_at')
                ->where('is_used', true)
                ->whereNotNull('device_id')
                ->latest('verified_at')
                ->pluck('device_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (!empty($tokens)) {
                try {
                    app(FirebasePushService::class)->sendToTokens(
                        $tokens,
                        'Nearby Emergency Case',
                        'An emergency animal rescue case has been reported near your location.',
                        [
                            'type'              => 'nearby_emergency',
                            'emergency_case_id' => $case->id,
                            'case_uid'          => $case->case_uid,
                            'case_type'         => $case->case_type,
                            'severity'          => $case->severity,
                            'latitude'          => $case->latitude,
                            'longitude'         => $case->longitude,
                            'distance_km'       => round($address->distance, 2),
                        ]
                    );
                } catch (\Throwable $e) {
                    Log::error('Emergency push notification failed', [
                        'user_id' => $address->user_id,
                        'case_id' => $case->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }

            $alertCount++;
        }

        return $alertCount;
    }
}