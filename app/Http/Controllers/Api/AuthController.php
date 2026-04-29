<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
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

        // Testing OTP: last 4 digits of mobile number
        $otp = substr($mobile, -4);

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

            /*
             * Your current app sends Firebase token in device_id.
             * So keep device_id required.
             */
            'device_id' => 'required|string|max:1000',

            /*
             * New recommended field.
             * If app sends fcm_token, we use it.
             * If not, we fallback to device_id.
             */
            'fcm_token' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $mobile   = $request->mobile;
        $otp      = $request->otp;
        $platform = $request->platform;

        /*
         * Important:
         * Current mobile app stores FCM token in device_id.
         * So this fixes fcm_token NULL issue.
         */
        $deviceId = $request->device_id;
        $fcmToken = $request->filled('fcm_token')
            ? $request->fcm_token
            : $request->device_id;

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

            if ($request->filled('name') && empty($user->name)) {
                $updateData['name'] = $request->name;
            }

            $user->update($updateData);

            $message = 'Login successful';
        }

        /*
         * Update login_otps row.
         */
        $loginOtp->update([
            'user_id'     => $user->id,
            'platform'    => $platform,
            'device_id'   => $deviceId,
            'verified_at' => now(),
            'is_used'     => true,
        ]);

        /*
         * Deactivate old duplicate token rows for other users/devices.
         * This prevents one FCM token being active in multiple rows.
         */
        DeviceToken::where('fcm_token', $fcmToken)
            ->where('user_id', '!=', $user->id)
            ->update([
                'is_active' => false,
            ]);

        /*
         * Store/update device_tokens table.
         *
         * Main fix:
         * fcm_token will now be saved.
         */
        if ($request->filled('platform') && $request->filled('device_id')) {
            DeviceToken::updateOrCreate(
                [
                    'user_id'   => $user->id,
                    'platform'  => $request->platform,
                    'device_id' => $request->device_id,
                ],
                [
                    'fcm_token'    => $request->fcm_token,
                    'is_active'    => true,
                    'last_used_at' => now(),
                    'ip_address'   => $request->ip(),
                    'user_agent'   => $request->userAgent(),
                ]
            );
        }

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
                'platform'       => $platform,
                'device_id'      => $deviceId,
                'fcm_token'      => $fcmToken,
                'device_token'   => $deviceToken,
                'user_exists'    => !$isNewUser,
                'is_new_user'    => $isNewUser,
                'address_exists' => $addressExists,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $deviceId = $request->input('device_id');
            $fcmToken = $request->input('fcm_token');

            $query = DeviceToken::where('user_id', $user->id);

            if (!empty($fcmToken)) {
                $query->where('fcm_token', $fcmToken);
            } elseif (!empty($deviceId)) {
                $query->where('device_id', $deviceId);
            }

            $query->update([
                'is_active' => false,
            ]);

            $request->user()->currentAccessToken()?->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Logout successful',
        ]);
    }

}