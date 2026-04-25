<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Throwable;

class FirebasePushService
{
    public function __construct(
        protected Messaging $messaging
    ) {
    }

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
            ->active()
            ->withNotificationToken();

        if (!empty($platform)) {
            $query->platform($platform);
        }

        if (!empty($deviceId)) {
            $query->device($deviceId);
        }

        $tokens = $query
            ->latest('last_used_at')
            ->get()
            ->map(fn ($device) => $device->notification_token)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($tokens)) {
            return [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'No active Firebase token found in device_tokens table.',
                'first_error'   => null,
            ];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

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

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));

        if (empty($tokens)) {
            return [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'No Firebase tokens found.',
                'first_error'   => null,
            ];
        }

        /*
         * Firebase data payload must contain string values only.
         */
        $stringData = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                $stringData[$key] = '';
            } elseif (is_scalar($value)) {
                $stringData[$key] = (string) $value;
            } else {
                $stringData[$key] = json_encode($value);
            }
        }

        $successCount = 0;
        $failureCount = 0;
        $results = [];
        $firstError = null;

        foreach ($tokens as $token) {
            try {
                /*
                 * Important:
                 * Do not use withNotification() / withData().
                 * Your installed Kreait version is throwing undefined method error.
                 */
                $message = CloudMessage::fromArray([
                    'token' => $token,

                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],

                    'data' => $stringData,

                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound'      => 'default',
                            'channel_id' => 'default',
                        ],
                    ],

                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ]);

                $this->messaging->send($message);

                $successCount++;

                DeviceToken::where(function ($q) use ($token) {
                    $q->where('fcm_token', $token)
                        ->orWhere('device_id', $token);
                })->update([
                    'is_active'    => true,
                    'last_used_at' => now(),
                ]);

                $results[] = [
                    'token'   => $token,
                    'status'  => 'sent',
                    'message' => 'Notification sent successfully',
                ];
            } catch (Throwable $e) {
                $failureCount++;

                $errorMessage = $e->getMessage();

                if ($firstError === null) {
                    $firstError = $errorMessage;
                }

                Log::warning('FCM send failed', [
                    'token' => $token,
                    'error' => $errorMessage,
                ]);

                if ($this->isInvalidFirebaseTokenError($errorMessage)) {
                    DeviceToken::where(function ($q) use ($token) {
                        $q->where('fcm_token', $token)
                            ->orWhere('device_id', $token);
                    })->update([
                        'is_active' => false,
                    ]);
                }

                $results[] = [
                    'token'   => $token,
                    'status'  => 'failed',
                    'message' => $errorMessage,
                ];
            }
        }

        $message = $successCount > 0
            ? 'Notification process completed.'
            : ($firstError ?: 'Firebase notification failed.');

        return [
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'results'       => $results,
            'message'       => $message,
            'first_error'   => $firstError,
        ];
    }

    private function isInvalidFirebaseTokenError(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'not found') ||
            str_contains($message, 'invalid') ||
            str_contains($message, 'registration token') ||
            str_contains($message, 'requested entity was not found') ||
            str_contains($message, 'unregistered') ||
            str_contains($message, 'not a valid fcm registration token');
    }
}