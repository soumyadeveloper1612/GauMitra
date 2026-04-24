<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $mobile = $request->mobile;

        $user = User::firstOrCreate(
            ['mobile' => $mobile],
            [
                'name' => 'User ' . substr($mobile, -4),
                'status' => 'active',
            ]
        );

        $otp = rand(100000, 999999);

        LoginOtp::where('mobile', $mobile)
            ->where('purpose', 'login')
            ->where('is_used', false)
            ->update([
                'is_used' => true,
            ]);

        LoginOtp::create([
            'user_id'    => $user->id,
            'mobile'     => $mobile,
            'purpose'    => 'login',
            'otp_hash'   => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'is_used'    => false,
            'attempts'   => 0,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'mobile' => $mobile,

                // Remove this in production.
                'otp' => $otp,
            ],
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'    => 'required|digits:10',
            'otp'       => 'required|digits:4',
            'platform'  => 'required|string|in:android,ios,web',
            'device_id' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $otpRecord = LoginOtp::where('mobile', $request->mobile)
            ->where('purpose', 'login')
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => 'OTP not found. Please request a new OTP.',
            ], 404);
        }

        if ($otpRecord->expires_at && now()->greaterThan($otpRecord->expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired. Please request a new OTP.',
            ], 410);
        }

        if ((int) $otpRecord->attempts >= 5) {
            return response()->json([
                'status' => false,
                'message' => 'Too many wrong attempts. Please request a new OTP.',
            ], 429);
        }

        if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
            $otpRecord->increment('attempts');

            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP.',
            ], 401);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            $user = User::create([
                'mobile' => $request->mobile,
                'name'   => 'User ' . substr($request->mobile, -4),
                'status' => 'active',
            ]);
        }

        $otpRecord->update([
            'user_id'     => $user->id,
            'platform'    => $request->platform,
            'device_id'   => $request->device_id,
            'verified_at' => now(),
            'is_used'     => true,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'device' => [
                    'platform' => $request->platform,
                    'device_id' => $request->device_id,
                ],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()?->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}