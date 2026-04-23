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
            'token'    => 'required|string',
            'platform' => 'required|in:android,ios,web',
        ], [
            'token.required'    => 'Token is required',
            'platform.required' => 'Platform is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $deviceToken = DeviceToken::updateOrCreate(
            ['token' => $request->token],
            [
                'user_id'      => auth()->id(),
                'platform'     => $request->platform,
                'is_active'    => true,
                'last_used_at' => now(),
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Device token saved successfully',
            'data'    => $deviceToken,
        ]);
    }

    public function deactivate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        DeviceToken::where('user_id', auth()->id())
            ->where('token', $request->token)
            ->update([
                'is_active'    => false,
                'last_used_at' => now(),
            ]);

        return response()->json([
            'status'  => true,
            'message' => 'Device token deactivated successfully',
        ]);
    }
}