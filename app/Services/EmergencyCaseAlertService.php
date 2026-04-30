<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\EmergencyCase;
use App\Models\EmergencyCaseAlert;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmergencyCaseAlertService
{
    public function __construct(
        protected FirebasePushService $pushService
    ) {
    }

    public function sendReportCreatedAlert(EmergencyCase $case): array
    {
        $scope = $this->scopeForSeverity($case);

        return $this->sendLocationAlert(
            case: $case,
            notificationType: 'case_reported',
            scope: $scope,
            title: $this->titleForEvent($scope, 'reported'),
            body: $this->bodyForEvent($case, 'reported', null, null),
            data: $this->basePayload($case, 'emergency_case_alert', $scope),
            actor: null,
            excludeUserIds: array_filter([$case->reporter_id])
        );
    }

    public function sendSeverityWiseAlert(EmergencyCase $case): array
    {
        return $this->sendReportCreatedAlert($case);
    }

    public function sendCaseAcceptedAlert(EmergencyCase $case, ?User $acceptedByUser = null): array
    {
        $scope = $this->scopeForSeverity($case);

        return $this->sendLocationAlert(
            case: $case,
            notificationType: 'case_accepted',
            scope: $scope,
            title: $this->titleForEvent($scope, 'accepted'),
            body: $this->bodyForEvent($case, 'accepted', $acceptedByUser, null),
            data: array_merge($this->basePayload($case, 'emergency_case_accepted_alert', $scope), [
                'accepted_by_user_id'   => (string) ($acceptedByUser?->id ?? ''),
                'accepted_by_user_name' => (string) ($acceptedByUser?->name ?? ''),
            ]),
            actor: $acceptedByUser,
            excludeUserIds: array_filter([$case->reporter_id, $acceptedByUser?->id])
        );
    }

    public function sendCaseAcceptedSeverityWiseAlert(EmergencyCase $case, ?User $acceptedByUser = null): array
    {
        return $this->sendCaseAcceptedAlert($case, $acceptedByUser);
    }

    public function sendCaseRejectedAlert(EmergencyCase $case, ?User $rejectedByUser = null, ?string $reason = null): array
    {
        $scope = $this->scopeForSeverity($case);

        return $this->sendLocationAlert(
            case: $case,
            notificationType: 'case_rejected',
            scope: $scope,
            title: $this->titleForEvent($scope, 'rejected'),
            body: $this->bodyForEvent($case, 'rejected', $rejectedByUser, $reason),
            data: array_merge($this->basePayload($case, 'emergency_case_rejected_alert', $scope), [
                'rejected_by_user_id'   => (string) ($rejectedByUser?->id ?? ''),
                'rejected_by_user_name' => (string) ($rejectedByUser?->name ?? ''),
                'reason'                => (string) ($reason ?? ''),
            ]),
            actor: $rejectedByUser,
            excludeUserIds: array_filter([$case->reporter_id, $rejectedByUser?->id])
        );
    }

    public function sendCaseStatusChangedAlert(EmergencyCase $case, ?User $changedByUser = null, ?string $notes = null): array
    {
        $scope = $this->scopeForSeverity($case);

        return $this->sendLocationAlert(
            case: $case,
            notificationType: 'case_status_changed',
            scope: $scope,
            title: $this->titleForEvent($scope, 'status_changed'),
            body: $this->bodyForEvent($case, 'status_changed', $changedByUser, $notes),
            data: array_merge($this->basePayload($case, 'emergency_case_status_changed_alert', $scope), [
                'changed_by_user_id'   => (string) ($changedByUser?->id ?? ''),
                'changed_by_user_name' => (string) ($changedByUser?->name ?? ''),
                'notes'                => (string) ($notes ?? ''),
            ]),
            actor: $changedByUser,
            excludeUserIds: array_filter([$case->reporter_id, $changedByUser?->id])
        );
    }

    public function sendCaseClosedAlert(EmergencyCase $case, ?User $closedByUser = null, ?string $reason = null): array
    {
        /*
        | Requirement:
        | close report time area-wise users notify.
        */
        $scope = 'area';

        return $this->sendLocationAlert(
            case: $case,
            notificationType: 'case_closed',
            scope: $scope,
            title: 'Nearby Case Closed',
            body: $this->bodyForEvent($case, 'closed', $closedByUser, $reason),
            data: array_merge($this->basePayload($case, 'emergency_case_closed_alert', $scope), [
                'closed_by_user_id'   => (string) ($closedByUser?->id ?? ''),
                'closed_by_user_name' => (string) ($closedByUser?->name ?? ''),
                'reason'              => (string) ($reason ?? ''),
            ]),
            actor: $closedByUser,
            excludeUserIds: array_filter([$case->reporter_id, $closedByUser?->id])
        );
    }

    private function sendLocationAlert(
        EmergencyCase $case,
        string $notificationType,
        string $scope,
        string $title,
        string $body,
        array $data,
        ?User $actor = null,
        array $excludeUserIds = []
    ): array {
        try {
            $devices = $this->getDevicesByAddressScope($case, $scope, $excludeUserIds);

            if ($devices->isEmpty()) {
                return [
                    'success_count'     => 0,
                    'failure_count'     => 0,
                    'queued_count'      => 0,
                    'notification_type' => $notificationType,
                    'scope'             => $scope,
                    'results'           => [],
                    'message'           => 'No matching users with valid Firebase tokens found.',
                    'first_error'       => null,
                ];
            }

            $alertMap = [];
            $tokens = [];

            foreach ($devices as $device) {
                $tokens[] = $device->fcm_token;

                $distanceKm = null;

                if ($device->user && $device->user->latestAddress) {
                    $distanceKm = $this->distanceKm(
                        (float) $case->latitude,
                        (float) $case->longitude,
                        (float) $device->user->latestAddress->latitude,
                        (float) $device->user->latestAddress->longitude
                    );
                }

                $alert = EmergencyCaseAlert::updateOrCreate(
                    [
                        'emergency_case_id' => $case->id,
                        'user_id'           => $device->user_id,
                        'device_token_id'   => $device->id,
                        'notification_type' => $notificationType,
                    ],
                    [
                        'radius_km'     => $this->radiusForScope($scope),
                        'distance_km'   => $distanceKm,
                        'status'        => 'pending',
                        'error_message' => null,
                    ]
                );

                $alertMap[$device->fcm_token] = $alert;
            }

            $pushResult = $this->pushService->sendToTokens(
                tokens: $tokens,
                title: $title,
                body: $body,
                data: array_merge($data, [
                    'notification_type' => $notificationType,
                    'alert_scope'       => $scope,
                    'actor_user_id'     => (string) ($actor?->id ?? ''),
                    'actor_user_name'   => (string) ($actor?->name ?? ''),
                ]),
                imageUrl: null,
                sound: $this->soundForScope($scope),
                androidChannelId: $this->channelForScope($scope)
            );

            foreach ($pushResult['results'] ?? [] as $result) {
                $token = $result['token'] ?? null;

                if (!$token || !isset($alertMap[$token])) {
                    continue;
                }

                $status = ($result['status'] ?? '') === 'sent' ? 'sent' : 'failed';

                $alertMap[$token]->update([
                    'status'        => $status,
                    'sent_at'       => $status === 'sent' ? now() : null,
                    'error_message' => $status === 'failed'
                        ? ($result['message'] ?? 'Notification failed')
                        : null,
                ]);
            }

            return array_merge($pushResult, [
                'queued_count'      => $devices->count(),
                'notification_type' => $notificationType,
                'scope'             => $scope,
            ]);
        } catch (\Throwable $e) {
            Log::error('Emergency location alert failed', [
                'case_id'           => $case->id,
                'case_uid'          => $case->case_uid,
                'notification_type' => $notificationType,
                'scope'             => $scope,
                'error'             => $e->getMessage(),
                'line'              => $e->getLine(),
            ]);

            return [
                'success_count'     => 0,
                'failure_count'     => 1,
                'queued_count'      => 0,
                'notification_type' => $notificationType,
                'scope'             => $scope,
                'results'           => [],
                'message'           => $e->getMessage(),
                'first_error'       => $e->getMessage(),
            ];
        }
    }

    private function getDevicesByAddressScope(EmergencyCase $case, string $scope, array $excludeUserIds = []): Collection
    {
        $excludeUserIds = array_values(array_unique(array_filter($excludeUserIds)));

        return DeviceToken::query()
            ->with([
                'user:id,name,mobile,status',
                'user.latestAddress',
            ])
            ->active()
            ->withFcmToken()
            ->whereHas('user', function (Builder $userQuery) use ($case, $scope, $excludeUserIds) {
                $userQuery->where('status', 'active');

                if (!empty($excludeUserIds)) {
                    $userQuery->whereNotIn('id', $excludeUserIds);
                }

                $userQuery->whereHas('addresses', function (Builder $addressQuery) use ($case, $scope) {
                    $this->notDeletedAddress($addressQuery);

                    if ($scope === 'area') {
                        $this->applyAreaFilter($addressQuery, $case);
                    } elseif ($scope === 'city') {
                        $this->applyCityFilter($addressQuery, $case);
                    } else {
                        $this->applyDistrictFilter($addressQuery, $case);
                    }
                });
            })
            ->orderByDesc('last_used_at')
            ->orderByDesc('id')
            ->get();
    }

    private function applyAreaFilter(Builder $query, EmergencyCase $case): void
    {
        /*
        | Low severity / close report:
        | same district + same city + same area/village/pincode.
        */

        if (!empty($case->district)) {
            $this->whereNormalizedEquals($query, 'district', $case->district);
        }

        if (!empty($case->city)) {
            $this->whereNormalizedEquals($query, 'city', $case->city);
        }

        $query->where(function ($q) use ($case) {
            $matched = false;

            if (!empty($case->area_name)) {
                $this->orWhereNormalizedEquals($q, 'area_name', $case->area_name);
                $this->orWhereNormalizedEquals($q, 'village', $case->area_name);
                $matched = true;
            }

            if (!empty($case->pincode)) {
                $q->orWhere('pincode', $case->pincode);
                $matched = true;
            }

            if (!$matched && !empty($case->city)) {
                $this->orWhereNormalizedEquals($q, 'city', $case->city);
            }
        });
    }

    private function applyCityFilter(Builder $query, EmergencyCase $case): void
    {
        /*
        | Medium severity:
        | same district + same city.
        */

        if (!empty($case->district)) {
            $this->whereNormalizedEquals($query, 'district', $case->district);
        }

        if (!empty($case->city)) {
            $this->whereNormalizedEquals($query, 'city', $case->city);
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    private function applyDistrictFilter(Builder $query, EmergencyCase $case): void
    {
        /*
        | High / critical severity:
        | same district.
        */

        if (!empty($case->district)) {
            $this->whereNormalizedEquals($query, 'district', $case->district);
            return;
        }

        if (!empty($case->city)) {
            $this->whereNormalizedEquals($query, 'city', $case->city);
            return;
        }

        $query->whereRaw('1 = 0');
    }

    private function notDeletedAddress(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNull('status')
                ->orWhere('status', '!=', 'deleted');
        });
    }

    private function whereNormalizedEquals(Builder $query, string $column, ?string $value): void
    {
        $value = $this->cleanText($value);

        if ($value === null) {
            return;
        }

        $query->whereRaw("LOWER(TRIM($column)) = ?", [$value]);
    }

    private function orWhereNormalizedEquals($query, string $column, ?string $value): void
    {
        $value = $this->cleanText($value);

        if ($value === null) {
            return;
        }

        $query->orWhereRaw("LOWER(TRIM($column)) = ?", [$value]);
    }

    private function cleanText(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return Str::lower($value);
    }

    private function scopeForSeverity(EmergencyCase $case): string
    {
        $severity = strtolower((string) ($case->severity ?? 'medium'));

        if (in_array($severity, ['high', 'critical'])) {
            return 'district';
        }

        if ($severity === 'medium') {
            return 'city';
        }

        return 'area';
    }

    private function radiusForScope(string $scope): int
    {
        return match ($scope) {
            'district' => 75,
            'city'     => 35,
            default    => 20,
        };
    }

    private function soundForScope(string $scope): string
    {
        return match ($scope) {
            'district' => 'gau_alert_high',
            'city'     => 'gau_alert_medium',
            default    => 'gau_alert_low',
        };
    }

    private function channelForScope(string $scope): string
    {
        return match ($scope) {
            'district' => 'emergency_high_alerts',
            'city'     => 'emergency_medium_alerts',
            default    => 'emergency_low_alerts',
        };
    }

    private function titleForEvent(string $scope, string $event): string
    {
        if ($event === 'reported') {
            return match ($scope) {
                'district' => 'District High Emergency Alert',
                'city'     => 'City Emergency Alert',
                default    => 'Nearby Emergency Alert',
            };
        }

        if ($event === 'accepted') {
            return match ($scope) {
                'district' => 'High Alert Case Accepted',
                'city'     => 'City Case Accepted',
                default    => 'Nearby Case Accepted',
            };
        }

        if ($event === 'rejected') {
            return match ($scope) {
                'district' => 'High Alert Case Rejected',
                'city'     => 'City Case Rejected',
                default    => 'Nearby Case Rejected',
            };
        }

        return match ($scope) {
            'district' => 'District Case Status Updated',
            'city'     => 'City Case Status Updated',
            default    => 'Nearby Case Status Updated',
        };
    }

    private function bodyForEvent(EmergencyCase $case, string $event, ?User $actor = null, ?string $reason = null): string
    {
        $actorName = $actor?->name ?? 'A responder';

        if ($event === 'reported') {
            return 'Emergency case ' . $case->case_uid . ' reported near ' .
                ($case->area_name ?: $case->city ?: $case->district ?: 'your area') . '.';
        }

        if ($event === 'accepted') {
            return $actorName . ' accepted case ' . $case->case_uid . ' and is going to the spot.';
        }

        if ($event === 'rejected') {
            return $actorName . ' rejected case ' . $case->case_uid . '. Reason: ' . ($reason ?: 'Not provided');
        }

        if ($event === 'closed') {
            return $actorName . ' closed case ' . $case->case_uid . '. Reason: ' . ($reason ?: 'Not provided');
        }

        return 'Case ' . $case->case_uid . ' status updated to ' . $case->status . '.';
    }

    private function basePayload(EmergencyCase $case, string $type, string $scope): array
    {
        return [
            'type'        => $type,
            'case_id'     => (string) $case->id,
            'case_uid'    => (string) $case->case_uid,
            'status'      => (string) $case->status,
            'case_type'   => (string) $case->case_type,
            'severity'    => (string) $case->severity,
            'alert_scope' => (string) $scope,
            'screen'      => 'EmergencyCaseDetails',
            'area_name'   => (string) ($case->area_name ?? ''),
            'city'        => (string) ($case->city ?? ''),
            'district'    => (string) ($case->district ?? ''),
            'pincode'     => (string) ($case->pincode ?? ''),
            'latitude'    => (string) ($case->latitude ?? ''),
            'longitude'   => (string) ($case->longitude ?? ''),
        ];
    }

    private function distanceKm(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): ?float
    {
        if (!$lat1 || !$lng1 || !$lat2 || !$lng2) {
            return null;
        }

        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}