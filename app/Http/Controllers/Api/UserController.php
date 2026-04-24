<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function profile()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            if ($user->status === 'deleted') {
                return response()->json([
                    'status'  => false,
                    'message' => 'User account has been deleted.',
                ], 403);
            }

            $user->load('latestAddress');

            return response()->json([
                'status'  => true,
                'message' => 'User profile fetched successfully.',
                'data'    => $user,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get user profile error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            if ($user->status === 'deleted') {
                return response()->json([
                    'status'  => false,
                    'message' => 'User account has been deleted.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name'          => 'sometimes|required|string|max:255',
                'mobile'        => [
                    'sometimes',
                    'required',
                    'digits:10',
                    Rule::unique('users', 'mobile')->ignore($user->id),
                ],
                'email'         => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],
                'profile_photo' => 'nullable|string|max:255',
            ], [
                'name.required'   => 'Name is required.',
                'mobile.required' => 'Mobile number is required.',
                'mobile.digits'   => 'Mobile number must be exactly 10 digits.',
                'mobile.unique'   => 'This mobile number is already registered.',
                'email.email'     => 'Please enter a valid email address.',
                'email.unique'    => 'This email is already registered.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $user->update($validator->validated());

            return response()->json([
                'status'  => true,
                'message' => 'User profile updated successfully.',
                'data'    => $user->fresh(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update user profile error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            if ($user->status === 'deleted') {
                return response()->json([
                    'status'  => false,
                    'message' => 'User account already deleted.',
                ], 400);
            }

            $user->update([
                'status' => 'deleted',
            ]);

            /*
             * Optional but recommended:
             * Delete all active Sanctum tokens after account soft delete.
             */
            $user->tokens()->delete();

            return response()->json([
                'status'  => true,
                'message' => 'User account deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete user account error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Server error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}