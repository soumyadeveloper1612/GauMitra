<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $mobile = $request->mobile;

        $existingUser = User::where('mobile', $mobile)->first();

        if ($existingUser && $existingUser->status !== 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'User exists but is inactive',
                'data'    => [
                    'mobile'      => $mobile,
                    'user_exists' => true,
                    'is_new_user' => false,
                ],
            ], 403);
        }

        $isNewUser = !$existingUser;

        // OTP is mobile number last 4 digits
        $otp = substr($mobile, -4);

        // Expire previous unused OTPs
        LoginOtp::where('mobile', $mobile)
            ->where('is_used', false)
            ->whereNull('verified_at')
            ->update([
                'is_used' => true,
            ]);

        LoginOtp::create([
            'user_id'     => $existingUser?->id,
            'mobile'      => $mobile,
            'otp_hash'    => Hash::make($otp),
            'expires_at'  => now()->addMinutes(10),
            'verified_at' => null,
            'is_used'     => false,
            'attempts'    => 0,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'OTP sent successfully',
            'data'    => [
                'mobile'      => $mobile,
                'user_exists' => (bool) $existingUser,
                'is_new_user' => $isNewUser,

                // Testing purpose only
                'otp'         => $otp,
            ],
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'    => 'required|digits:10',
            'otp'       => 'required|digits:4',
            'name'      => 'nullable|string|max:255',
            'platform'  => 'required|string|in:android,ios,web',
            'device_id' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $mobile = $request->mobile;
        $otp    = $request->otp;

        $user = User::where('mobile', $mobile)->first();
        $isNewUser = false;

        if ($user && $user->status !== 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'User exists but is inactive',
                'data'    => [
                    'mobile'      => $mobile,
                    'user_exists' => true,
                    'is_new_user' => false,
                ],
            ], 403);
        }

        $loginOtp = LoginOtp::where('mobile', $mobile)
            ->where('is_used', false)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (!$loginOtp) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP not found or already used',
            ], 404);
        }

        if ($loginOtp->expires_at && now()->greaterThan($loginOtp->expires_at)) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP expired',
            ], 422);
        }

        if (!Hash::check($otp, $loginOtp->otp_hash)) {
            $loginOtp->increment('attempts');

            return response()->json([
                'status'  => false,
                'message' => 'Invalid OTP',
            ], 422);
        }

        if (!$user) {
            $user = User::create([
                'name'               => $request->filled('name') ? $request->name : null,
                'mobile'             => $mobile,
                'status'             => 'active',
                'mobile_verified_at' => now(),
                'last_login_at'      => now(),
            ]);

            $isNewUser = true;
            $message = 'New user registered and login successful';
        } else {
            $updateData = [
                'mobile_verified_at' => now(),
                'last_login_at'      => now(),
            ];

            // If old user name is empty, update name from verify OTP request
            if ($request->filled('name') && empty($user->name)) {
                $updateData['name'] = $request->name;
            }

            $user->update($updateData);

            $message = 'Login successful';
        }

        // Save only platform and device_id during verify OTP.
        // No FCM token save logic here.
        $loginOtp->update([
            'user_id'     => $user->id,
            'platform'    => $request->platform,
            'device_id'   => $request->device_id,
            'verified_at' => now(),
            'is_used'     => true,
        ]);

        $token = $user->createToken('user-auth-token')->plainTextToken;

        $addressExists = UserAddress::where('user_id', $user->id)
            ->where('status', '!=', 'deleted')
            ->exists();

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => [
                'token'          => $token,
                'user'           => $user->fresh(),
                'mobile'         => $mobile,
                'platform'       => $request->platform,
                'device_id'      => $request->device_id,
                'user_exists'    => !$isNewUser,
                'is_new_user'    => $isNewUser,
                'address_exists' => $addressExists,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout successfully',
        ]);
    }
}