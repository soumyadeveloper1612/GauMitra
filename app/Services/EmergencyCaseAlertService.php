<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\EmergencyCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;

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
            body: 'A low severity case has been reported near ' . ($case->area_name ?: $case->city) . '. Please check if you can help.',
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
            return DeviceToken::query()
                ->active()
                ->withFcmToken()
                ->whereHas('user', function (Builder $userQuery) use ($case, $scope) {
                    $userQuery
                        ->where('status', 'active')
                        ->when($case->reporter_id, function ($q) use ($case) {
                            $q->where('id', '!=', $case->reporter_id);
                        })
                        ->whereHas('addresses', function (Builder $addressQuery) use ($case, $scope) {
                            $addressQuery->where('status', '!=', 'deleted');

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
        } catch (\Throwable $e) {
            Log::error('Severity wise token fetch failed', [
                'case_id' => $case->id,
                'scope'   => $scope,
                'error'   => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function applyAreaFilter(Builder $query, EmergencyCase $case): void
    {
        $query->where(function ($q) use ($case) {
            if (!empty($case->area_name)) {
                $q->orWhereRaw('LOWER(TRIM(area_name)) = ?', [
                    Str::lower(trim($case->area_name)),
                ]);
            }

            if (!empty($case->pincode)) {
                $q->orWhere('pincode', $case->pincode);
            }

            if (!empty($case->city)) {
                $q->orWhereRaw('LOWER(TRIM(city)) = ?', [
                    Str::lower(trim($case->city)),
                ]);
            }
        });

        if (!empty($case->district)) {
            $query->whereRaw('LOWER(TRIM(district)) = ?', [
                Str::lower(trim($case->district)),
            ]);
        }
    }

    private function applyCityFilter(Builder $query, EmergencyCase $case): void
    {
        if (!empty($case->city)) {
            $query->whereRaw('LOWER(TRIM(city)) = ?', [
                Str::lower(trim($case->city)),
            ]);
        }

        if (!empty($case->district)) {
            $query->whereRaw('LOWER(TRIM(district)) = ?', [
                Str::lower(trim($case->district)),
            ]);
        }
    }

    private function applyDistrictFilter(Builder $query, EmergencyCase $case): void
    {
        if (!empty($case->district)) {
            $query->whereRaw('LOWER(TRIM(district)) = ?', [
                Str::lower(trim($case->district)),
            ]);
            return;
        }

        if (!empty($case->city)) {
            $query->whereRaw('LOWER(TRIM(city)) = ?', [
                Str::lower(trim($case->city)),
            ]);
        }
    }

    private function casePayload(EmergencyCase $case, string $alertScope): array
    {
        return [
            'type'        => 'emergency_case_alert',
            'case_id'     => $case->id,
            'case_uid'    => $case->case_uid,
            'status'      => $case->status,
            'case_type'   => $case->case_type,
            'severity'    => $case->severity,
            'alert_scope' => $alertScope,
            'screen'      => 'EmergencyCaseDetails',
            'latitude'    => $case->latitude,
            'longitude'   => $case->longitude,
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
            'type'                   => 'emergency_case_accepted_alert',
            'case_id'                => (string) $case->id,
            'case_uid'               => (string) $case->case_uid,
            'status'                 => (string) $case->status,
            'case_type'              => (string) $case->case_type,
            'severity'               => (string) $case->severity,
            'alert_scope'            => (string) $alertScope,
            'accepted_by_user_id'    => (string) ($acceptedByUser?->id ?? ''),
            'accepted_by_user_name'  => (string) ($acceptedByUser?->name ?? ''),
            'screen'                 => 'EmergencyCaseDetails',
            'latitude'               => (string) ($case->latitude ?? ''),
            'longitude'              => (string) ($case->longitude ?? ''),
        ];
    }

}