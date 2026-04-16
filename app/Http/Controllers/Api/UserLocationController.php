<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserLocationController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'full_address' => 'nullable|string|max:500',
            'district' => 'nullable|string|max:150',
            'state' => 'nullable|string|max:150',
            'is_available' => 'nullable|boolean',
            'notification_enabled' => 'nullable|boolean',
            'radius_preference_km' => 'nullable|numeric|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $location = UserLocation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'full_address' => $request->full_address,
                'district' => $request->district,
                'state' => $request->state,
                'is_available' => $request->boolean('is_available', true),
                'notification_enabled' => $request->boolean('notification_enabled', true),
                'radius_preference_km' => $request->radius_preference_km ?? 20,
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'User location updated successfully',
            'data' => $location,
            'auth_user_id' => $user->id,
        ]);
    }
}