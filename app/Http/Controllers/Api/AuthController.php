<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    /**
     * Send OTP
     * Temporary logic: OTP = last 4 digits of mobile number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
        ], [
            'mobile.required' => 'Mobile number is required',
            'mobile.digits'   => 'Mobile number must be 10 digits',
        ]);

        $user = User::where('mobile', $request->mobile)
            ->where('status', 'active')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or inactive',
            ], 404);
        }

        $otp = substr($request->mobile, -4);

        // Mark old OTPs as used
        LoginOtp::where('mobile', $request->mobile)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // Save new OTP record
        $loginOtp = LoginOtp::create([
            'user_id'    => $user->id,
            'mobile'     => $request->mobile,
            'otp'        => $otp,
            'is_used'    => false,
            'expires_at' => now()->addMinutes(5),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'mobile' => $request->mobile,
                'otp' => $otp, // only for testing purpose
                'expires_at' => $loginOtp->expires_at,
            ]
        ]);
    }

    /**
     * Verify OTP and login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:10',
            'otp'    => 'required|digits:4',
        ], [
            'mobile.required' => 'Mobile number is required',
            'mobile.digits'   => 'Mobile number must be 10 digits',
            'otp.required'    => 'OTP is required',
            'otp.digits'      => 'OTP must be 4 digits',
        ]);

        $user = User::where('mobile', $request->mobile)
            ->where('status', 'active')
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or inactive',
            ], 404);
        }

        $loginOtp = LoginOtp::where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$loginOtp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP',
            ], 401);
        }

        if ($loginOtp->expires_at && now()->gt($loginOtp->expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired',
            ], 401);
        }

        // mark OTP as used
        $loginOtp->update([
            'is_used' => true,
        ]);

        // update user fields
        $user->update([
            'mobile_verified_at' => $user->mobile_verified_at ?? now(),
            'last_login_at'      => now(),
        ]);

        // create sanctum token
        $token = $user->createToken('mobile-login-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful',
        ]);
    }
}