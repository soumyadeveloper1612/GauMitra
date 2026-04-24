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

        /*
         * OTP will be last 4 digits of mobile number.
         * Example: 9876543210 => 3210
         */
        $otp = substr($mobile, -4);

        $user = User::where('mobile', $mobile)->first();

        /*
         * Purpose removed.
         * Platform and device_id will be updated during verifyOtp().
         *
         * Using latest OTP row for same mobile to avoid duplicate issue.
         */
        $loginOtp = LoginOtp::where('mobile', $mobile)
            ->latest('id')
            ->first();

        $otpData = [
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
        ];

        if ($loginOtp) {
            $loginOtp->update($otpData);
        } else {
            LoginOtp::create(array_merge([
                'mobile' => $mobile,
            ], $otpData));
        }

        /*
         * Send OTP by SMS provider here if needed.
         * Since OTP is last 4 digits, SMS can send $otp.
         */

        return response()->json([
            'status'  => true,
            'message' => 'OTP sent successfully',

            // Remove this in production if needed
            'otp'     => $otp,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile'    => 'required|digits:10',
            'otp'       => 'required|digits:4',
            'platform'  => 'required|string|in:android,ios,web',
            'device_id' => 'required|string|max:255',

            // Optional Firebase token
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $mobile   = $request->mobile;
        $platform = $request->platform;
        $deviceId = $request->device_id;

        return DB::transaction(function () use ($request, $mobile, $platform, $deviceId) {
            /*
             * Purpose removed.
             * Verify by mobile only.
             */
            $loginOtp = LoginOtp::where('mobile', $mobile)
                ->latest('id')
                ->lockForUpdate()
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

            /*
             * OTP is mobile number last 4 digits.
             * Still checking with hashed otp stored in login_otps table.
             */
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
                    'name'               => 'User',
                    'status'             => 'active',
                    'mobile_verified_at' => now(),
                    'last_login_at'      => now(),
                ]
            );

            $user->update([
                'mobile_verified_at' => $user->mobile_verified_at ?? now(),
                'last_login_at'      => now(),
            ]);

            /*
             * Update OTP row after verification.
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