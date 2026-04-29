<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\EmergencyCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
        $severity = strtolower($case->severity ?? 'medium');

        if (in_array($severity, ['high', 'critical'])) {
            return $this->sendDistrictAlert($case);
        }

        if ($severity === 'medium') {
            return $this->sendCityAlert($case);
        }

        return $this->sendAreaAlert($case);
    }

    private function sendAreaAlert(EmergencyCase $case): array
    {
        $tokens = $this->getTokensByAddressScope($case, 'area');

        return $this->pushService->sendToTokens(
            tokens: $tokens,
            title: 'Nearby Gau Rescue Alert',
            body: 'A low severity case has been reported near ' . ($case->area_name ?: $case->city ?: 'your area') . '. Please check if you can help.',
            data: $this->casePayload($case, 'area'),
            imageUrl: null,
            sound: 'gau_alert_low',
            androidChannelId: 'emergency_low_alerts'
        );
    }

    private function sendCityAlert(EmergencyCase $case): array
    {
        $tokens = $this->getTokensByAddressScope($case, 'city');

        return $this->pushService->sendToTokens(
            tokens: $tokens,
            title: 'City Gau Emergency Alert',
            body: 'A medium severity case has been reported in ' . ($case->city ?: 'your city') . '.',
            data: $this->casePayload($case, 'city'),
            imageUrl: null,
            sound: 'gau_alert_medium',
            androidChannelId: 'emergency_medium_alerts'
        );
    }

    private function sendDistrictAlert(EmergencyCase $case): array
    {
        $tokens = $this->getTokensByAddressScope($case, 'district');

        return $this->pushService->sendToTokens(
            tokens: $tokens,
            title: 'High Gau Emergency Alert',
            body: 'High priority emergency reported in ' . ($case->district ?: $case->city ?: 'your area') . '. Immediate support may be required.',
            data: $this->casePayload($case, 'district'),
            imageUrl: null,
            sound: 'gau_alert_high',
            androidChannelId: 'emergency_high_alerts'
        );
    }

    private function getTokensByAddressScope(EmergencyCase $case, string $scope): array
    {
        try {
            $tokens = DeviceToken::query()
                ->active()
                ->withFcmToken()
                ->whereHas('user', function (Builder $userQuery) use ($case, $scope) {
                    $userQuery
                        ->where('status', 'active')

                        // Reporter already receives confirmation notification.
                        ->when($case->reporter_id, function ($q) use ($case) {
                            $q->where('id', '!=', $case->reporter_id);
                        })

                        ->whereHas('addresses', function (Builder $addressQuery) use ($case, $scope) {
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
                ->pluck('fcm_token')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            Log::info('Emergency severity alert tokens fetched', [
                'case_id'     => $case->id,
                'case_uid'    => $case->case_uid,
                'reporter_id' => $case->reporter_id,
                'scope'       => $scope,
                'severity'    => $case->severity,
                'token_count' => count($tokens),
                'area_name'   => $case->area_name,
                'city'        => $case->city,
                'district'    => $case->district,
                'pincode'     => $case->pincode,
                'latitude'    => $case->latitude,
                'longitude'   => $case->longitude,
            ]);

            return $tokens;
        } catch (\Throwable $e) {
            Log::error('Severity wise token fetch failed', [
                'case_id' => $case->id,
                'scope'   => $scope,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return [];
        }
    }

    private function notDeletedAddress(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNull('status')
              ->orWhere('status', '!=', 'deleted');
        });
    }

    private function applyAreaFilter(Builder $query, EmergencyCase $case): void
    {
        $query->where(function ($q) use ($case) {
            $this->orWhereNormalizedEquals($q, 'area_name', $case->area_name);

            if (!empty($case->pincode)) {
                $q->orWhere('pincode', $case->pincode);
            }

            // Fallback: exact same city.
            $this->orWhereNormalizedEquals($q, 'city', $case->city);

            // Best fallback: same lat/lng area within 20 KM.
            $this->orWhereWithinKm($q, $case, 20);
        });
    }

    private function applyCityFilter(Builder $query, EmergencyCase $case): void
    {
        $query->where(function ($q) use ($case) {
            $this->orWhereNormalizedEquals($q, 'city', $case->city);

            if (!empty($case->pincode)) {
                $q->orWhere('pincode', $case->pincode);
            }

            // Medium severity fallback: users within 35 KM.
            $this->orWhereWithinKm($q, $case, 35);
        });
    }

    private function applyDistrictFilter(Builder $query, EmergencyCase $case): void
    {
        $query->where(function ($q) use ($case) {
            $this->orWhereNormalizedEquals($q, 'district', $case->district);

            // Fallback if district value is different or missing.
            $this->orWhereNormalizedEquals($q, 'city', $case->city);

            // High severity fallback: users within 75 KM.
            $this->orWhereWithinKm($q, $case, 75);
        });
    }

    private function orWhereNormalizedEquals($query, string $column, ?string $value): void
    {
        $value = $this->cleanText($value);

        if ($value === null) {
            return;
        }

        $query->orWhereRaw("LOWER(TRIM($column)) = ?", [$value]);
    }

    private function orWhereWithinKm($query, EmergencyCase $case, float $radiusKm): void
    {
        if ($case->latitude === null || $case->longitude === null) {
            return;
        }

        $lat = (float) $case->latitude;
        $lng = (float) $case->longitude;

        if ($lat == 0.0 && $lng == 0.0) {
            return;
        }

        $query->orWhere(function ($distanceQuery) use ($lat, $lng, $radiusKm) {
            $distanceQuery
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereRaw(
                    '(6371 * ACOS(LEAST(1, GREATEST(-1,
                        COS(RADIANS(?)) *
                        COS(RADIANS(latitude)) *
                        COS(RADIANS(longitude) - RADIANS(?)) +
                        SIN(RADIANS(?)) *
                        SIN(RADIANS(latitude))
                    )))) <= ?',
                    [$lat, $lng, $lat, $radiusKm]
                );
        });
    }

    private function cleanText(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return Str::lower($value);
    }

    private function casePayload(EmergencyCase $case, string $alertScope): array
    {
        return [
            'type'        => 'emergency_case_alert',
            'case_id'     => (string) $case->id,
            'case_uid'    => (string) $case->case_uid,
            'status'      => (string) $case->status,
            'case_type'   => (string) $case->case_type,
            'severity'    => (string) $case->severity,
            'alert_scope' => (string) $alertScope,
            'screen'      => 'EmergencyCaseDetails',
            'latitude'    => (string) ($case->latitude ?? ''),
            'longitude'   => (string) ($case->longitude ?? ''),
        ];
    }

    public function sendCaseAcceptedSeverityWiseAlert(EmergencyCase $case, ?User $acceptedByUser = null): array
    {
        $severity = strtolower($case->severity ?? 'medium');

        $responderName = $acceptedByUser?->name ?? 'A responder';

        if (in_array($severity, ['high', 'critical'])) {
            $tokens = $this->getTokensByAddressScope($case, 'district');

            return $this->pushService->sendToTokens(
                tokens: $tokens,
                title: 'High Alert Case Accepted',
                body: $responderName . ' accepted case ' . $case->case_uid . ' and is going to the spot.',
                data: $this->acceptedCasePayload($case, $acceptedByUser, 'district'),
                imageUrl: null,
                sound: 'gau_alert_high',
                androidChannelId: 'emergency_high_alerts'
            );
        }

        if ($severity === 'medium') {
            $tokens = $this->getTokensByAddressScope($case, 'city');

            return $this->pushService->sendToTokens(
                tokens: $tokens,
                title: 'City Case Accepted',
                body: $responderName . ' accepted case ' . $case->case_uid . ' and is going to the spot.',
                data: $this->acceptedCasePayload($case, $acceptedByUser, 'city'),
                imageUrl: null,
                sound: 'gau_alert_medium',
                androidChannelId: 'emergency_medium_alerts'
            );
        }

        $tokens = $this->getTokensByAddressScope($case, 'area');

        return $this->pushService->sendToTokens(
            tokens: $tokens,
            title: 'Nearby Case Accepted',
            body: $responderName . ' accepted case ' . $case->case_uid . ' and is going to the spot.',
            data: $this->acceptedCasePayload($case, $acceptedByUser, 'area'),
            imageUrl: null,
            sound: 'gau_alert_low',
            androidChannelId: 'emergency_low_alerts'
        );
    }

    private function acceptedCasePayload(EmergencyCase $case, ?User $acceptedByUser, string $alertScope): array
    {
        return [
            'type'                  => 'emergency_case_accepted_alert',
            'case_id'               => (string) $case->id,
            'case_uid'              => (string) $case->case_uid,
            'status'                => (string) $case->status,
            'case_type'             => (string) $case->case_type,
            'severity'              => (string) $case->severity,
            'alert_scope'           => (string) $alertScope,
            'accepted_by_user_id'   => (string) ($acceptedByUser?->id ?? ''),
            'accepted_by_user_name' => (string) ($acceptedByUser?->name ?? ''),
            'screen'                => 'EmergencyCaseDetails',
            'latitude'              => (string) ($case->latitude ?? ''),
            'longitude'             => (string) ($case->longitude ?? ''),
        ];
    }
}