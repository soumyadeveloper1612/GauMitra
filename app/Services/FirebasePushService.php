<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

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
        ?string $imageUrl = null,
        ?string $platform = null,
        string $sound = 'gau_alert',
        string $androidChannelId = 'gau_mitra_emergency_alerts'
    ): array {
        $tokens = $user->deviceTokens()
            ->active()
            ->withFcmToken()
            ->platform($platform)
            ->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        if (empty($tokens)) {
            return [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'No active FCM token found for this user.',
                'first_error'   => null,
            ];
        }

        return $this->sendToTokens(
            tokens: $tokens,
            title: $title,
            body: $body,
            data: $data,
            imageUrl: $imageUrl,
            sound: $sound,
            androidChannelId: $androidChannelId
        );
    }

    public function sendToTokens(
        array $tokens,
        string $title,
        string $body,
        array $data = [],
        ?string $imageUrl = null,
        string $sound = 'gau_alert',
        string $androidChannelId = 'gau_mitra_emergency_alerts'
    ): array {
        $tokens = array_values(array_unique(array_filter($tokens)));

        if (empty($tokens)) {
            return [
                'success_count' => 0,
                'failure_count' => 0,
                'results'       => [],
                'message'       => 'No FCM tokens found.',
                'first_error'   => null,
            ];
        }

        $data = collect($data)
            ->map(fn ($value) => is_scalar($value) ? (string) $value : json_encode($value))
            ->toArray();

        $data['click_action'] = $data['click_action'] ?? 'FLUTTER_NOTIFICATION_CLICK';
        $data['sound'] = $sound;
        $data['channel_id'] = $androidChannelId;

        $notification = Notification::create($title, $body);

        if ($imageUrl) {
            $notification = $notification->withImageUrl($imageUrl);
        }

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($data)
            ->withAndroidConfig(AndroidConfig::fromArray([
                'priority' => 'high',
                'notification' => [
                    'title'      => $title,
                    'body'       => $body,
                    'channel_id' => $androidChannelId,
                    'sound'      => $sound,
                    'priority'   => 'high',
                ],
            ]))
            ->withApnsConfig(ApnsConfig::fromArray([
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'sound' => $sound . '.caf',
                    ],
                ],
            ]));

        $successCount = 0;
        $failureCount = 0;
        $results = [];
        $firstError = null;

        foreach (array_chunk($tokens, 500) as $chunk) {
            try {
                $report = $this->messaging->sendMulticast($message, $chunk);

                $successCount += $report->successes()->count();
                $failureCount += $report->failures()->count();

                foreach ($report->successes()->getItems() as $success) {
                    $results[] = [
                        'token'  => method_exists($success->target(), 'value') ? $success->target()->value() : null,
                        'status' => 'sent',
                        'error'  => null,
                    ];
                }

                foreach ($report->failures()->getItems() as $failure) {
                    $error = $failure->error()->getMessage();

                    if (!$firstError) {
                        $firstError = $error;
                    }

                    $results[] = [
                        'token'  => method_exists($failure->target(), 'value') ? $failure->target()->value() : null,
                        'status' => 'failed',
                        'error'  => $error,
                    ];
                }
            } catch (\Throwable $e) {
                $failureCount += count($chunk);

                if (!$firstError) {
                    $firstError = $e->getMessage();
                }

                Log::error('Firebase push failed', [
                    'error' => $e->getMessage(),
                ]);

                foreach ($chunk as $token) {
                    $results[] = [
                        'token'  => $token,
                        'status' => 'failed',
                        'error'  => $e->getMessage(),
                    ];
                }
            }
        }

        return [
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'results'       => $results,
            'message'       => $successCount > 0 ? 'Notification sent successfully.' : 'Notification failed.',
            'first_error'   => $firstError,
        ];
    }
}