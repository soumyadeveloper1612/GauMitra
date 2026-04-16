<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseAlert;
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
                        cos(radians({$lat}))
                        * cos(radians(user_addresses.latitude))
                        * cos(radians(user_addresses.longitude) - radians({$lng}))
                        + sin(radians({$lat}))
                        * sin(radians(user_addresses.latitude))
                    )
                ) as distance")
            )
            ->join('users', 'users.id', '=', 'user_addresses.user_id')
            ->whereNotNull('user_addresses.latitude')
            ->whereNotNull('user_addresses.longitude')
            ->where('users.id', '!=', $case->reporter_id)
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance', 'asc')
            ->get();

        $alertCount = 0;

        foreach ($nearbyUsers as $address) {
            // save alert record
            if (class_exists(\App\Models\EmergencyCaseAlert::class)) {
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

            $alertCount++;

            // device token notification
            if (class_exists(\App\Models\DeviceToken::class)) {
                $tokens = DeviceToken::where('user_id', $address->user_id)
                    ->where('is_active', 1)
                    ->pluck('token')
                    ->toArray();

                if (!empty($tokens)) {
                    try {
                        // You can integrate FCM or notification service here
                        Log::info('Emergency push notification pending implementation', [
                            'user_id' => $address->user_id,
                            'case_id' => $case->id,
                            'tokens'  => $tokens,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Push notification failed', [
                            'user_id' => $address->user_id,
                            'case_id' => $case->id,
                            'error'   => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return $alertCount;
    }
}