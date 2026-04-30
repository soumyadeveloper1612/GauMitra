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
        ?string $imageUrl = null,
        ?string $platform = null,
        string $sound = 'default',
        string $androidChannelId = 'default'
    ): array {
        $query = DeviceToken::query()
            ->where('user_id', $user->id)
            ->active()
            ->withFcmToken();

        if (!empty($platform)) {
            $query->platform($platform);
        }

        $tokens = $query
            ->orderByDesc('last_used_at')
            ->orderByDesc('id')
            ->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

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
        string $sound = 'default',
        string $androidChannelId = 'default'
    ): array {
        $tokens = array_values(array_unique(array_filter($tokens)));

        $validTokens = [];
        $skippedTokens = [];

        foreach ($tokens as $token) {
            if ($this->looksLikeValidFcmToken($token)) {
                $validTokens[] = $token;
            } else {
                $skippedTokens[] = $token;

                DeviceToken::where('fcm_token', $token)->update([
                    'is_active' => false,
                ]);
            }
        }

        if (empty($validTokens)) {
            return [
                'success_count' => 0,
                'failure_count' => count($skippedTokens),
                'results'       => collect($skippedTokens)->map(function ($token) {
                    return [
                        'token'   => $token,
                        'status'  => 'skipped',
                        'message' => 'Invalid or fake FCM token skipped.',
                    ];
                })->values()->toArray(),
                'message'     => 'No valid Firebase tokens found.',
                'first_error' => null,
            ];
        }

        $stringData = $this->stringifyData($data);

        if (!empty($imageUrl)) {
            $stringData['image_url'] = $imageUrl;
        }

        $androidSound = $sound === 'default'
            ? 'default'
            : pathinfo($sound, PATHINFO_FILENAME);

        $iosSound = $sound === 'default'
            ? 'default'
            : $this->iosSoundName($sound);

        $successCount = 0;
        $failureCount = 0;
        $results = [];
        $firstError = null;

        foreach ($validTokens as $token) {
            try {
                $notification = [
                    'title' => $title,
                    'body'  => $body,
                ];

                if (!empty($imageUrl)) {
                    $notification['image'] = $imageUrl;
                }

                $messageArray = [
                    'token'        => $token,
                    'notification' => $notification,
                    'data'         => array_merge($stringData, [
                        'sound'              => $androidSound,
                        'ios_sound'          => $iosSound,
                        'android_channel_id' => $androidChannelId,
                    ]),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound'      => $androidSound,
                            'channel_id' => $androidChannelId,
                        ],
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                        'payload' => [
                            'aps' => [
                                'sound' => $iosSound,
                            ],
                        ],
                    ],
                ];

                if (!empty($imageUrl)) {
                    $messageArray['android']['notification']['image'] = $imageUrl;
                    $messageArray['apns']['fcm_options'] = [
                        'image' => $imageUrl,
                    ];
                }

                $message = CloudMessage::fromArray($messageArray);

                $this->messaging->send($message);

                $successCount++;

                DeviceToken::where('fcm_token', $token)->update([
                    'is_active'    => true,
                    'last_used_at' => now(),
                ]);

                $results[] = [
                    'token'   => $token,
                    'status'  => 'sent',
                    'message' => 'Notification sent successfully.',
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
                    DeviceToken::where('fcm_token', $token)->update([
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

        foreach ($skippedTokens as $token) {
            $results[] = [
                'token'   => $token,
                'status'  => 'skipped',
                'message' => 'Invalid or fake FCM token skipped.',
            ];
        }

        return [
            'success_count' => $successCount,
            'failure_count' => $failureCount + count($skippedTokens),
            'results'       => $results,
            'message'       => $successCount > 0
                ? 'Notification process completed.'
                : ($firstError ?: 'Firebase notification failed.'),
            'first_error'   => $firstError,
        ];
    }

    private function stringifyData(array $data): array
    {
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

        return $stringData;
    }

    private function iosSoundName(string $sound): string
    {
        if (str_contains($sound, '.')) {
            return $sound;
        }

        return $sound . '.caf';
    }

    private function looksLikeValidFcmToken(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $token = trim($token);

        if (strlen($token) < 80) {
            return false;
        }

        $invalidWords = [
            'device-token',
            'device-id',
            'test-token',
            'firebase-token',
            'paste-real',
            'sample',
            'dummy',
            'null',
        ];

        foreach ($invalidWords as $word) {
            if (str_contains(strtolower($token), $word)) {
                return false;
            }
        }

        return true;
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