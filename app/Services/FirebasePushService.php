<?php

namespace App\Services;

use App\Models\LoginOtp;
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
        $tokens = LoginOtp::where('user_id', $user->id)
            ->whereNotNull('verified_at')
            ->where('is_used', true)
            ->whereNotNull('device_id')
            ->latest('verified_at')
            ->pluck('device_id')
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

        $data = collect($data)->map(function ($value) {
            if (is_array($value) || is_object($value)) {
                return json_encode($value);
            }

            return (string) $value;
        })->toArray();

        $successCount = 0;
        $failureCount = 0;
        $results = [];

        foreach ($tokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(Notification::create($title, $body))
                    ->withData($data);

                $this->messaging->send($message);

                $successCount++;

                $results[] = [
                    'token'  => $token,
                    'status' => 'sent',
                ];
            } catch (\Throwable $e) {
                $failureCount++;

                Log::warning('FCM send failed', [
                    'token' => $token,
                    'error' => $e->getMessage(),
                ]);

                $msg = strtolower($e->getMessage());

                if (
                    str_contains($msg, 'not found') ||
                    str_contains($msg, 'invalid') ||
                    str_contains($msg, 'registration token')
                ) {
                    LoginOtp::where('device_id', $token)->update([
                        'device_id' => null,
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