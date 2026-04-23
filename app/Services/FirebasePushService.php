<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebasePushService
{
    public function __construct(
        protected Messaging $messaging
    ) {
    }

    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        $tokens = $user->deviceTokens()
            ->where('is_active', true)
            ->pluck('token')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = collect($tokens)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            return [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
            ];
        }

        $data = collect($data)
            ->map(fn ($value) => is_scalar($value) ? (string) $value : json_encode($value))
            ->toArray();

        $successCount = 0;
        $failureCount = 0;
        $results = [];

        foreach ($tokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body))
                    ->withData($data);

                $response = $this->messaging->send($message);

                $successCount++;
                $results[] = [
                    'token'   => $token,
                    'status'  => 'sent',
                    'message' => $response,
                ];
            } catch (\Throwable $e) {
                $failureCount++;

                Log::warning('FCM send failed', [
                    'token' => $token,
                    'error' => $e->getMessage(),
                ]);

                if (
                    str_contains(strtolower($e->getMessage()), 'not found') ||
                    str_contains(strtolower($e->getMessage()), 'invalid') ||
                    str_contains(strtolower($e->getMessage()), 'registration token is not a valid fcm registration token')
                ) {
                    DeviceToken::where('token', $token)->update([
                        'is_active'    => false,
                        'last_used_at' => now(),
                    ]);
                }

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
        ];
    }
}