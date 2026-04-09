<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'digits:10'],
        ]);

        $user = User::where('mobile', $request->mobile)
            ->where('status', 'active')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or inactive.',
            ], 404);
        }

        $lastOtp = LoginOtp::where('mobile', $request->mobile)
            ->where('purpose', 'login')
            ->latest()
            ->first();

        if ($lastOtp && $lastOtp->created_at->gt(now()->subSeconds(60))) {
            return response()->json([
                'status' => false,
                'message' => 'Please wait 60 seconds before requesting another OTP.',
            ], 429);
        }

        LoginOtp::where('mobile', $request->mobile)
            ->where('purpose', 'login')
            ->where('is_used', false)
            ->update(['is_used' => true]);

        $otp = (string) random_int(100000, 999999);

        $otpRow = LoginOtp::create([
            'user_id' => $user->id,
            'mobile' => $request->mobile,
            'purpose' => 'login',
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // SMS gateway integration here
        // Example: MSG91 / Twilio / Fast2SMS
        // send SMS to $request->mobile with $otp

        $response = [
            'status' => true,
            'message' => 'OTP sent successfully.',
            'expires_in_seconds' => 300,
        ];

        // Only for local/testing
        if (app()->environment('local')) {
            $response['otp'] = $otp;
        }

        return response()->json($response);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'digits:10'],
            'otp' => ['required', 'digits:6'],
        ]);

        $otpRow = LoginOtp::where('mobile', $request->mobile)
            ->where('purpose', 'login')
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otpRow) {
            return response()->json([
                'status' => false,
                'message' => 'OTP not found.',
            ], 404);
        }

        if ($otpRow->expires_at->isPast()) {
            $otpRow->update(['is_used' => true]);

            return response()->json([
                'status' => false,
                'message' => 'OTP expired.',
            ], 422);
        }

        if ($otpRow->attempts >= 5) {
            $otpRow->update(['is_used' => true]);

            return response()->json([
                'status' => false,
                'message' => 'Maximum OTP attempts exceeded.',
            ], 429);
        }

        if (!Hash::check($request->otp, $otpRow->otp_hash)) {
            $otpRow->increment('attempts');

            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP.',
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)
            ->where('status', 'active')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or inactive.',
            ], 404);
        }

        $otpRow->update([
            'verified_at' => now(),
            'is_used' => true,
        ]);

        if (!$user->mobile_verified_at) {
            $user->mobile_verified_at = now();
        }

        $user->last_login_at = now();
        $user->save();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'OTP verified. Login successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function passwordLogin(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'digits:10'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('mobile', $request->mobile)
            ->where('status', 'active')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or inactive.',
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Invalid mobile number or password.'],
            ]);
        }

        $user->last_login_at = now();
        $user->save();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Password login successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password set successfully.',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}