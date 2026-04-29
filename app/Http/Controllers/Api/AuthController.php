<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\LoginOtp;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $mobile = $request->mobile;

            $existingUser = User::where('mobile', $mobile)->first();

            if ($existingUser && $existingUser->status !== 'active') {
                return response()->json([
                    'status'  => false,
                    'message' => 'User exists but is inactive.',
                    'data'    => [
                        'mobile'      => $mobile,
                        'user_exists' => true,
                        'is_new_user' => false,
                    ],
                ], 403);
            }

            // Development OTP: last 4 digits of mobile.
            $otp = substr($mobile, -4);

            DB::transaction(function () use ($mobile, $existingUser, $otp, $request) {
                $loginOtp = LoginOtp::where('mobile', $mobile)
                    ->where('purpose', 'login')
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();

                $payload = [
                    'user_id'     => $existingUser?->id,
                    'mobile'      => $mobile,
                    'purpose'     => 'login',
                    'otp_hash'    => Hash::make($otp),
                    'expires_at'  => now()->addMinutes(10),
                    'verified_at' => null,
                    'is_used'     => false,
                    'attempts'    => 0,
                    'ip_address'  => $request->ip(),
                    'user_agent'  => $request->userAgent(),
                ];

                if ($loginOtp) {
                    $loginOtp->update($payload);
                } else {
                    LoginOtp::create($payload);
                }
            });

            return response()->json([
                'status'  => true,
                'message' => 'OTP sent successfully.',
                'data'    => [
                    'mobile'      => $mobile,
                    'user_exists' => (bool) $existingUser,
                    'is_new_user' => !$existingUser,
                    'otp'         => $otp, // Remove in production.
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('SEND OTP ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while sending OTP.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
{
    $validator = Validator::make($request->all(), [
        'mobile'    => 'required|digits:10',
        'otp'       => 'required|digits:4',
        'name'      => 'nullable|string|max:255',
        'platform'  => 'required|string|in:android,ios,web',
        'device_id' => 'required|string|max:191',
        'fcm_token' => 'nullable|string|max:5000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        $response = DB::transaction(function () use ($request) {
            $mobile   = $request->mobile;
            $otp      = $request->otp;
            $platform = $request->platform;
            $deviceId = $request->device_id;
            $fcmToken = $request->filled('fcm_token') ? $request->fcm_token : null;

            $user = User::where('mobile', $mobile)
                ->lockForUpdate()
                ->first();

            if ($user && $user->status !== 'active') {
                return response()->json([
                    'status'  => false,
                    'message' => 'User exists but is inactive.',
                    'data'    => [
                        'mobile'      => $mobile,
                        'user_exists' => true,
                        'is_new_user' => false,
                    ],
                ], 403);
            }

            $loginOtp = LoginOtp::where('mobile', $mobile)
                ->where('purpose', 'login')
                ->where('is_used', false)
                ->whereNull('verified_at')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (!$loginOtp) {
                return response()->json([
                    'status'  => false,
                    'message' => 'OTP not found or already used. Please resend OTP.',
                ], 422);
            }

            if ($loginOtp->expires_at && now()->greaterThan($loginOtp->expires_at)) {
                $loginOtp->update([
                    'is_used' => true,
                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'OTP expired. Please resend OTP.',
                ], 422);
            }

            if (!Hash::check($otp, $loginOtp->otp_hash)) {
                $loginOtp->increment('attempts');

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid OTP.',
                ], 422);
            }

            $isNewUser = false;

            if (!$user) {
                $user = User::create([
                    'name'               => $request->filled('name') ? $request->name : 'User',
                    'mobile'             => $mobile,
                    'password'           => Hash::make(Str::random(32)),
                    'status'             => 'active',
                    'mobile_verified_at' => now(),
                    'last_login_at'      => now(),
                ]);

                $isNewUser = true;
                $message = 'New user registered and login successful.';
            } else {
                $updateData = [
                    'mobile_verified_at' => now(),
                    'last_login_at'      => now(),
                ];

                if ($request->filled('name')) {
                    $updateData['name'] = $request->name;
                }

                $user->update($updateData);

                $message = 'Login successful.';
            }

            $loginOtp->update([
                'user_id'     => $user->id,
                'platform'    => $platform,
                'device_id'   => $deviceId,
                'verified_at' => now(),
                'is_used'     => true,
                'attempts'    => 0,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            if (!empty($fcmToken)) {
                DeviceToken::where('fcm_token', $fcmToken)
                    ->where('user_id', '!=', $user->id)
                    ->update([
                        'is_active' => 0,
                    ]);
            }

            $deviceTokenPayload = [
                'is_active'    => 1,
                'last_used_at' => now(),
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
            ];

            if (!empty($fcmToken)) {
                $deviceTokenPayload['fcm_token'] = $fcmToken;
            }

            $deviceToken = DeviceToken::updateOrCreate(
                [
                    'user_id'   => $user->id,
                    'platform'  => $platform,
                    'device_id' => $deviceId,
                ],
                $deviceTokenPayload
            );

            $token = $user->createToken('user-auth-token')->plainTextToken;

            $addressQuery = UserAddress::where('user_id', $user->id);

            if (Schema::hasColumn('user_addresses', 'status')) {
                $addressQuery->where('status', '!=', 'deleted');
            }

            $addressExists = $addressQuery->exists();

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
        });

        return $response;
    } catch (\Throwable $e) {
        Log::error('VERIFY OTP ERROR', [
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ]);

        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong while verifying OTP.',
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}

    public function logout(Request $request)
    {
        try {
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
                    'is_active' => 0,
                ]);

                $request->user()->currentAccessToken()?->delete();
            }

            return response()->json([
                'status'  => true,
                'message' => 'Logout successful.',
            ]);
        } catch (\Throwable $e) {
            Log::error('LOGOUT ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while logout.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}