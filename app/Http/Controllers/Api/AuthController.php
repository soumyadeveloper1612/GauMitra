<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'  => 'required|digits:10',
            'purpose' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $mobile  = $request->mobile;
        $purpose = $request->purpose ?? 'login';

        $otp = random_int(100000, 999999);

        $user = User::where('mobile', $mobile)->first();

        /*
        * Same mobile + same purpose = update same row.
        * Platform and device_id will be updated during verifyOtp().
        */
        LoginOtp::updateOrCreate(
            [
                'mobile'  => $mobile,
                'purpose' => $purpose,
            ],
            [
                'user_id'     => $user?->id,
                'platform'    => null,
                'device_id'   => null,
                'otp_hash'    => Hash::make($otp),
                'expires_at'  => now()->addMinutes(5),
                'verified_at' => null,
                'is_used'     => false,
                'attempts'    => 0,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]
        );

        /*
        * Send OTP by SMS provider here.
        */

        return response()->json([
            'status'  => true,
            'message' => 'OTP sent successfully',

            // Remove this in production
            'otp'     => $otp,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'     => 'required|digits:10',
            'otp'        => 'required|digits:6',
            'platform'   => 'required|string|in:android,ios,web',
            'device_id'  => 'required|string|max:255',

            // Optional Firebase token
            'fcm_token'  => 'nullable|string',
            'purpose'    => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $mobile = $request->mobile;
        $platform = $request->platform;
        $deviceId = $request->device_id;
        $purpose = $request->purpose ?? 'login';

        return DB::transaction(function () use ($request, $mobile, $platform, $deviceId, $purpose) {
            $loginOtp = LoginOtp::where('mobile', $mobile)
                ->where('platform', $platform)
                ->where('purpose', $purpose)
                ->first();

            if (!$loginOtp) {
                return response()->json([
                    'status'  => false,
                    'message' => 'OTP record not found. Please request OTP again.',
                ], 404);
            }

            if ($loginOtp->is_used) {
                return response()->json([
                    'status'  => false,
                    'message' => 'OTP already used. Please request a new OTP.',
                ], 400);
            }

            if ($loginOtp->expires_at && now()->greaterThan($loginOtp->expires_at)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'OTP expired. Please request a new OTP.',
                ], 400);
            }

            if ($loginOtp->attempts >= 5) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Too many wrong attempts. Please request a new OTP.',
                ], 429);
            }

            if (!Hash::check($request->otp, $loginOtp->otp_hash)) {
                $loginOtp->increment('attempts');

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid OTP',
                ], 400);
            }

            $user = User::firstOrCreate(
                ['mobile' => $mobile],
                [
                    'name' => 'User',
                    'status' => 'active',
                    'mobile_verified_at' => now(),
                    'last_login_at' => now(),
                ]
            );

            $user->update([
                'mobile_verified_at' => $user->mobile_verified_at ?? now(),
                'last_login_at' => now(),
            ]);

            /*
             * Update same OTP row after verification.
             */
            $loginOtp->update([
                'user_id'     => $user->id,
                'platform'    => $platform,
                'device_id'   => $deviceId,
                'verified_at' => now(),
                'is_used'     => true,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            /*
             * Update device token every login.
             * Same platform + same device_id = update same row.
             */
            DeviceToken::updateOrCreate(
                [
                    'platform'  => $platform,
                    'device_id' => $deviceId,
                ],
                [
                    'user_id'      => $user->id,
                    'fcm_token'    => $request->fcm_token,
                    'is_active'    => true,
                    'last_used_at' => now(),
                    'ip_address'   => $request->ip(),
                    'user_agent'   => $request->userAgent(),
                ]
            );

            $token = $user->createToken('user-login-token')->plainTextToken;

            return response()->json([
                'status'  => true,
                'message' => 'OTP verified successfully',
                'token'   => $token,
                'user'    => [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'mobile' => $user->mobile,
                    'status' => $user->status,
                ],
            ]);
        });
    }
    
}