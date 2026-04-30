<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebasePushService
{
    public function __construct(
        private readonly Messaging $messaging
    ) {
    }

    public function sendEmergencyAlertToTokens(
        array $tokens,
        string $title,
        string $body,
        array $data = []
    ): array {
        $tokens = array_values(array_unique(array_filter($tokens)));

        if (empty($tokens)) {
            return [
                'success' => false,
                'success_count' => 0,
                'failure_count' => 0,
                'message' => 'No FCM tokens found.',
            ];
        }

        $channelId = env('GAU_MITRA_ALERT_CHANNEL_ID', 'gau_mitra_emergency_alerts');
        $androidSound = env('GAU_MITRA_ALERT_ANDROID_SOUND', 'gau_alert');
        $iosSound = env('GAU_MITRA_ALERT_IOS_SOUND', 'gau_alert.caf');

        $data = array_merge($data, [
            'click_action' => 'EMERGENCY_CASE_ALERT',
            'channel_id'  => $channelId,
            'sound'       => $androidSound,
        ]);

        /**
         * FCM data values must be string values.
         */
        $data = collect($data)
            ->map(fn ($value) => is_scalar($value) ? (string) $value : json_encode($value))
            ->toArray();

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data)
            ->withAndroidConfig(AndroidConfig::fromArray([
                'priority' => 'high',
                'notification' => [
                    'title'      => $title,
                    'body'       => $body,
                    'channel_id' => $channelId,
                    'sound'      => $androidSound,
                    'priority'   => 'high',
                    'default_vibrate_timings' => true,
                    'default_light_settings'  => true,
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
                        'sound' => $iosSound,
                    ],
                ],
            ]));

        $successCount = 0;
        $failureCount = 0;
        $failedTokens = [];

        foreach (array_chunk($tokens, 500) as $tokenChunk) {
            try {
                $report = $this->messaging->sendMulticast($message, $tokenChunk);

                $successCount += $report->successes()->count();
                $failureCount += $report->failures()->count();

                if ($report->hasFailures()) {
                    foreach ($report->failures()->getItems() as $failure) {
                        $failedTokens[] = [
                            'token' => $failure->target()->value(),
                            'error' => $failure->error()->getMessage(),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                $failureCount += count($tokenChunk);

                Log::error('Emergency FCM multicast failed', [
                    'error' => $e->getMessage(),
                ]);

                foreach ($tokenChunk as $token) {
                    $failedTokens[] = [
                        'token' => $token,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        return [
            'success' => $successCount > 0,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'failed_tokens' => $failedTokens,
        ];
    }
}