<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginOtp;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|digits:10',
            ], [
                'mobile.required' => 'Mobile number is required',
                'mobile.digits'   => 'Mobile number must be 10 digits',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $existingUser = User::where('mobile', $request->mobile)->first();

            // If user exists but inactive, block login/registration on same mobile
            if ($existingUser && $existingUser->status !== 'active') {
                return response()->json([
                    'status'  => false,
                    'message' => 'User exists but is inactive',
                    'data'    => [
                        'mobile'      => $request->mobile,
                        'user_exists' => true,
                        'is_new_user' => false,
                    ],
                ], 403);
            }

            $isNewUser = $existingUser ? false : true;

            // Demo OTP
            $otp = substr($request->mobile, -4);

            // Mark previous OTPs as used
            LoginOtp::where('mobile', $request->mobile)
                ->where('purpose', 'login')
                ->where('is_used', 0)
                ->update([
                    'is_used' => 1,
                ]);

            // Save OTP
            $loginOtp = LoginOtp::create([
                'user_id'     => $existingUser?->id, // null for new user
                'mobile'      => $request->mobile,
                'purpose'     => 'login',
                'otp_hash'    => $otp, // testing only
                'expires_at'  => now()->addMinutes(5),
                'verified_at' => null,
                'is_used'     => 0,
                'attempts'    => 0,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'OTP sent successfully',
                'data'    => [
                    'mobile'      => $request->mobile,
                    'otp'         => $otp,
                    'expires_at'  => $loginOtp->expires_at,
                    'user_exists' => $existingUser ? true : false,
                    'is_new_user' => $isNewUser,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Server error',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'mobile' => 'required|digits:10',
                'otp'    => 'required|digits:4',
                'name'   => 'nullable|string|max:255',
            ], [
                'mobile.required' => 'Mobile number is required',
                'mobile.digits'   => 'Mobile number must be 10 digits',
                'otp.required'    => 'OTP is required',
                'otp.digits'      => 'OTP must be 4 digits',
                'name.string'     => 'Name must be a string',
                'name.max'        => 'Name may not be greater than 255 characters',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $loginOtp = LoginOtp::where('mobile', $request->mobile)
                ->where('purpose', 'login')
                ->where('otp_hash', $request->otp)
                ->where('is_used', 0)
                ->latest()
                ->first();

            if (!$loginOtp) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid OTP',
                ], 401);
            }

            if (!empty($loginOtp->expires_at) && Carbon::parse($loginOtp->expires_at)->isPast()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'OTP expired',
                ], 401);
            }

            $user = User::where('mobile', $request->mobile)->first();
            $isNewUser = false;

            if ($user && $user->status !== 'active') {
                return response()->json([
                    'status'  => false,
                    'message' => 'User exists but is inactive',
                ], 403);
            }

            // New user flow
            if (!$user) {
                $user = User::create([
                    'name'               => $request->filled('name') ? $request->name : null,
                    'mobile'             => $request->mobile,
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

                // update name for old user if sent and current name is empty
                if ($request->filled('name') && empty($user->name)) {
                    $updateData['name'] = $request->name;
                }

                $user->update($updateData);

                $message = 'Login successful';
            }

            $loginOtp->update([
                'user_id'     => $user->id,
                'is_used'     => 1,
                'verified_at' => now(),
            ]);

            // Address check moved here
            $addressExists = UserAddress::where('user_id', $user->id)->exists();

            $token = $user->createToken('mobile-login-token')->plainTextToken;

            return response()->json([
                'status'         => true,
                'message'        => $message,
                'is_new_user'    => $isNewUser,
                'address_exists' => $addressExists,
                'needs_address'  => !$addressExists,
                'token'          => $token,
                'token_type'     => 'Bearer',
                'user'           => $user->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Server error',
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Logout successful',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Server error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}