<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FcmService
{
    public function sendToMany(array $tokens, string $title, string $body, array $data = []): array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));

        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No device tokens found'];
        }

        // Replace this part with actual Firebase / OneSignal integration
        Log::info('Emergency push notification', [
            'tokens' => $tokens,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        return [
            'success' => true,
            'message' => 'Notification dispatched',
            'count' => count($tokens),
        ];
    }
}