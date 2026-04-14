<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseAlert;
use App\Models\EmergencyCaseAssignment;
use App\Models\EmergencyCaseLog;
use App\Models\UserLocation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmergencyCaseService
{
    public function __construct(protected FcmService $fcmService)
    {
    }

    public function generateCaseUid(): string
    {
        return 'EC-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
    }

    public function log(
        EmergencyCase $case,
        ?int $userId,
        string $action,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?string $notes = null,
        ?array $meta = null,
        ?float $latitude = null,
        ?float $longitude = null
    ): void {
        EmergencyCaseLog::create([
            'emergency_case_id' => $case->id,
            'user_id' => $userId,
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'meta' => $meta,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function findNearbyUsers(float $latitude, float $longitude, float $radiusKm = 20, ?int $excludeUserId = null)
    {
        $haversine = "(6371 * acos(cos(radians(?)) 
            * cos(radians(user_locations.latitude)) 
            * cos(radians(user_locations.longitude) - radians(?)) 
            + sin(radians(?)) 
            * sin(radians(user_locations.latitude))))";

        return UserLocation::query()
            ->join('users', 'users.id', '=', 'user_locations.user_id')
            ->select(
                'user_locations.*',
                DB::raw("$haversine as distance")
            )
            ->where('user_locations.is_available', true)
            ->where('user_locations.notification_enabled', true)
            ->when($excludeUserId, fn ($q) => $q->where('user_locations.user_id', '!=', $excludeUserId))
            ->setBindings([$latitude, $longitude, $latitude], 'select')
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance')
            ->get();
    }

    public function sendCaseAlerts(EmergencyCase $case, float $radiusKm = 20): int
    {
        $nearbyUsers = $this->findNearbyUsers(
            (float) $case->latitude,
            (float) $case->longitude,
            $radiusKm,
            $case->reporter_id
        );

        $sentCount = 0;

        foreach ($nearbyUsers as $nearbyUser) {
            $deviceTokens = DeviceToken::where('user_id', $nearbyUser->user_id)
                ->where('is_active', true)
                ->get();

            foreach ($deviceTokens as $deviceToken) {
                EmergencyCaseAlert::create([
                    'emergency_case_id' => $case->id,
                    'user_id' => $nearbyUser->user_id,
                    'device_token_id' => $deviceToken->id,
                    'notification_type' => 'new_case',
                    'radius_km' => $radiusKm,
                    'distance_km' => round($nearbyUser->distance, 2),
                    'status' => 'queued',
                ]);
            }

            $tokens = $deviceTokens->pluck('token')->toArray();

            if (!empty($tokens)) {
                $title = $case->case_type === 'accident'
                    ? 'Emergency Accident Alert'
                    : 'Emergency Case Near You';

                $body = "New {$case->case_type} case reported within {$radiusKm} km.";

                $response = $this->fcmService->sendToMany($tokens, $title, $body, [
                    'case_id' => $case->id,
                    'case_uid' => $case->case_uid,
                    'case_type' => $case->case_type,
                    'severity' => $case->severity,
                    'latitude' => $case->latitude,
                    'longitude' => $case->longitude,
                ]);

                EmergencyCaseAlert::where('emergency_case_id', $case->id)
                    ->where('user_id', $nearbyUser->user_id)
                    ->update([
                        'status' => $response['success'] ? 'sent' : 'failed',
                        'sent_at' => $response['success'] ? now() : null,
                        'error_message' => $response['success'] ? null : ($response['message'] ?? 'Push failed'),
                    ]);

                if ($response['success']) {
                    $sentCount++;
                }
            }
        }

        if ($sentCount > 0 && $case->status === 'reported') {
            $oldStatus = $case->status;
            $case->update([
                'status' => 'alerted',
                'notified_radius_km' => $radiusKm,
            ]);

            $this->log(
                $case,
                null,
                'alert_sent',
                $oldStatus,
                'alerted',
                "Nearby users alerted within {$radiusKm} km",
                ['alerted_users_count' => $sentCount]
            );
        }

        return $sentCount;
    }

    public function acceptCase(EmergencyCase $case, User $user): EmergencyCaseAssignment
    {
        return DB::transaction(function () use ($case, $user) {
            $assignment = EmergencyCaseAssignment::firstOrNew([
                'emergency_case_id' => $case->id,
                'user_id' => $user->id,
            ]);

            $oldStatus = $case->status;

            if (!$case->current_handler_id) {
                $assignment->assignment_role = 'primary_handler';
                $case->current_handler_id = $user->id;
                $case->status = 'accepted';
                $case->accepted_at = now();
                $case->save();
            } else {
                $assignment->assignment_role = 'support_handler';
            }

            $assignment->status = 'accepted';
            $assignment->accepted_at = now();
            $assignment->save();

            $this->log(
                $case,
                $user->id,
                'case_accepted',
                $oldStatus,
                $case->status,
                $assignment->assignment_role === 'primary_handler'
                    ? 'Primary handler accepted case'
                    : 'Support handler joined case'
            );

            EmergencyCaseAlert::where('emergency_case_id', $case->id)
                ->where('user_id', $user->id)
                ->update([
                    'status' => 'accepted',
                    'response' => 'accepted',
                    'responded_at' => now(),
                ]);

            return $assignment;
        });
    }

    public function updateCaseStatus(EmergencyCase $case, string $newStatus, ?int $userId = null, ?string $notes = null): EmergencyCase
    {
        $oldStatus = $case->status;

        $payload = ['status' => $newStatus];

        if ($newStatus === 'en_route') {
            $payload['en_route_at'] = now();
        } elseif ($newStatus === 'reached_site') {
            $payload['reached_at'] = now();
        } elseif ($newStatus === 'rescue_in_progress') {
            $payload['rescue_started_at'] = now();
        } elseif ($newStatus === 'resolved') {
            $payload['resolved_at'] = now();
        } elseif ($newStatus === 'closed') {
            $payload['closed_at'] = now();
        }

        $case->update($payload);

        $this->log(
            $case,
            $userId,
            'status_changed',
            $oldStatus,
            $newStatus,
            $notes
        );

        return $case->fresh();
    }

    public function requestBackup(EmergencyCase $case, ?int $requestedBy = null, float $extraRadius = 30): int
    {
        $oldStatus = $case->status;
        $case->update([
            'status' => 'needs_backup',
            'escalation_level' => $case->escalation_level + 1,
            'notified_radius_km' => $extraRadius,
        ]);

        $this->log(
            $case,
            $requestedBy,
            'backup_requested',
            $oldStatus,
            'needs_backup',
            "Backup requested. Radius expanded to {$extraRadius} km"
        );

        return $this->sendCaseAlerts($case, $extraRadius);
    }
}