<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'platform' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $deviceToken = DeviceToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id' => auth()->id(),
                'platform' => $request->platform,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Device token saved successfully',
            'data' => $deviceToken,
        ]);
    }
}