<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Throwable;

class FirebasePushService
{
    public function __construct(
        protected Messaging $messaging
    ) {
    }

    /**
     * Send notification to all active devices of a user.
     * Optional filters:
     * - $platform = android / ios / web
     * - $deviceId = specific device_id
     */
    public function sendToUser(
        User $user,
        string $title,
        string $body,
        array $data = [],
        ?string $platform = null,
        ?string $deviceId = null
    ): array {
        $query = DeviceToken::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '');

        if (!empty($platform)) {
            $query->where('platform', $platform);
        }

        if (!empty($deviceId)) {
            $query->where('device_id', $deviceId);
        }

        $tokens = $query
            ->latest('last_used_at')
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            return [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'No active Firebase token found in device_tokens table.',
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send notification to user by platform.
     * Example: android / ios / web
     */
    public function sendToUserPlatform(
        User $user,
        string $platform,
        string $title,
        string $body,
        array $data = []
    ): array {
        return $this->sendToUser(
            user: $user,
            title: $title,
            body: $body,
            data: $data,
            platform: $platform
        );
    }

    /**
     * Send notification to one specific device of user.
     */
    public function sendToUserDevice(
        User $user,
        string $deviceId,
        string $title,
        string $body,
        array $data = [],
        ?string $platform = null
    ): array {
        return $this->sendToUser(
            user: $user,
            title: $title,
            body: $body,
            data: $data,
            platform: $platform,
            deviceId: $deviceId
        );
    }

    /**
     * Send notification to multiple FCM tokens.
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));

        if (empty($tokens)) {
            return [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'No Firebase tokens found.',
            ];
        }

        $stringData = [];

        foreach ($data as $key => $value) {
            $stringData[$key] = is_scalar($value)
                ? (string) $value
                : json_encode($value);
        }

        $successCount = 0;
        $failureCount = 0;
        $results = [];

        foreach ($tokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body))
                    ->withData($stringData);

                $this->messaging->send($message);

                $successCount++;

                $results[] = [
                    'token'   => $token,
                    'status'  => 'sent',
                    'message' => 'Notification sent successfully',
                ];
            } catch (Throwable $e) {
                $failureCount++;

                $results[] = [
                    'token'   => $token,
                    'status'  => 'failed',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'results'       => $results,
            'message'       => 'Notification process completed.',
        ];
    }

    private function isInvalidFirebaseTokenError(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'not found') ||
            str_contains($message, 'invalid') ||
            str_contains($message, 'registration token') ||
            str_contains($message, 'requested entity was not found') ||
            str_contains($message, 'unregistered');
    }
}