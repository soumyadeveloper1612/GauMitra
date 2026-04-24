<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserAddressController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $addresses = UserAddress::where('user_id', $user->id)
                ->where('status', '!=', 'deleted')
                ->latest()
                ->get();

            return response()->json([
                'status'  => true,
                'message' => 'Address list fetched successfully.',
                'data'    => $addresses,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get user address list error', [
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

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'full_address'     => 'required|string',
                'street'           => 'nullable|string|max:255',
                'village'          => 'nullable|string|max:150',
                'police_station'   => 'nullable|string|max:150',
                'city'             => 'nullable|string|max:150',
                'district'         => 'nullable|string|max:150',
                'state'            => 'nullable|string|max:150',
                'pincode'          => 'nullable|string|max:20',
                'area_name'        => 'nullable|string|max:150',
                'latitude'         => 'required|numeric|between:-90,90',
                'longitude'        => 'required|numeric|between:-180,180',
                'google_place_id'  => 'nullable|string|max:255',
                'plus_code'        => 'nullable|string|max:100',
            ], [
                'full_address.required' => 'Full address is required.',
                'latitude.required'     => 'Latitude is required.',
                'longitude.required'    => 'Longitude is required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $address = UserAddress::create([
                'user_id'         => $user->id,
                'full_address'    => $request->full_address,
                'street'          => $request->street,
                'village'         => $request->village,
                'police_station'  => $request->police_station,
                'city'            => $request->city,
                'district'        => $request->district,
                'state'           => $request->state,
                'pincode'         => $request->pincode,
                'area_name'       => $request->area_name,
                'latitude'        => $request->latitude,
                'longitude'       => $request->longitude,
                'google_place_id' => $request->google_place_id,
                'plus_code'       => $request->plus_code,
                'status'          => 'active',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Address saved successfully.',
                'data'    => $address,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Save user address error', [
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

    public function show($id)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $address = UserAddress::where('id', $id)
                ->where('user_id', $user->id)
                ->where('status', '!=', 'deleted')
                ->first();

            if (!$address) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Address not found.',
                ], 404);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Address details fetched successfully.',
                'data'    => $address,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Show user address error', [
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

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $address = UserAddress::where('id', $id)
                ->where('user_id', $user->id)
                ->where('status', '!=', 'deleted')
                ->first();

            if (!$address) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Address not found.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'full_address'     => 'required|string',
                'street'           => 'nullable|string|max:255',
                'village'          => 'nullable|string|max:150',
                'police_station'   => 'nullable|string|max:150',
                'city'             => 'nullable|string|max:150',
                'district'         => 'nullable|string|max:150',
                'state'            => 'nullable|string|max:150',
                'pincode'          => 'nullable|string|max:20',
                'area_name'        => 'nullable|string|max:150',
                'latitude'         => 'required|numeric|between:-90,90',
                'longitude'        => 'required|numeric|between:-180,180',
                'google_place_id'  => 'nullable|string|max:255',
                'plus_code'        => 'nullable|string|max:100',
            ], [
                'full_address.required' => 'Full address is required.',
                'latitude.required'     => 'Latitude is required.',
                'longitude.required'    => 'Longitude is required.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $address->update([
                'full_address'    => $request->full_address,
                'street'          => $request->street,
                'village'         => $request->village,
                'police_station'  => $request->police_station,
                'city'            => $request->city,
                'district'        => $request->district,
                'state'           => $request->state,
                'pincode'         => $request->pincode,
                'area_name'       => $request->area_name,
                'latitude'        => $request->latitude,
                'longitude'       => $request->longitude,
                'google_place_id' => $request->google_place_id,
                'plus_code'       => $request->plus_code,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Address updated successfully.',
                'data'    => $address,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Update user address error', [
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

    public function destroy($id)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $address = UserAddress::where('id', $id)
                ->where('user_id', $user->id)
                ->where('status', '!=', 'deleted')
                ->first();

            if (!$address) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Address not found.',
                ], 404);
            }

            $address->update([
                'status' => 'deleted',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Address deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Delete user address error', [
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