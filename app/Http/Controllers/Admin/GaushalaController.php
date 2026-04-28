<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gaushala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GaushalaController extends Controller
{
    public function index()
    {
        $gaushalas = Gaushala::with('members')
            ->latest()
            ->paginate(10);

        return view('admin.gaushalas.manage-gaushala', compact('gaushalas'));
    }

    public function create()
    {
        return view('admin.gaushalas.create-gaushala');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gaushala_name'          => 'required|string|max:255',
            'owner_manager_name'     => 'required|string|max:255',
            'mobile_number'          => 'required|digits:10',
            'alternate_number'       => 'nullable|digits:10|different:mobile_number',
            'full_address'           => 'required|string',
            'district'               => 'required|string|max:150',
            'state'                  => 'required|string|max:150',

            'total_capacity'         => 'required|integer|min:0',
            'available_capacity'     => 'required|integer|min:0|lte:total_capacity',

            'rescue_vehicle'         => 'nullable|boolean',
            'doctor'                 => 'nullable|boolean',
            'food_support'           => 'nullable|boolean',
            'temporary_shelter'      => 'nullable|boolean',

            'gaushala_photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'registration_proof'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',

            'working_hours'          => 'nullable|string|max:255',
            'emergency_availability' => 'required|in:yes,no',
            'status'                 => 'nullable|in:active,inactive',

            'members'                => 'required|array|min:1',
            'members.*.name'         => 'required|string|max:150',
            'members.*.phone'        => 'required|digits:10',
        ], [
            'gaushala_name.required'          => 'Gaushala name is required.',
            'owner_manager_name.required'     => 'Owner / Manager name is required.',
            'mobile_number.required'          => 'Mobile number is required.',
            'mobile_number.digits'            => 'Mobile number must be 10 digits.',
            'alternate_number.digits'         => 'Alternate number must be 10 digits.',
            'alternate_number.different'      => 'Alternate number must be different from mobile number.',
            'full_address.required'           => 'Full address is required.',
            'district.required'               => 'District is required.',
            'state.required'                  => 'State is required.',
            'total_capacity.required'         => 'Total capacity is required.',
            'available_capacity.required'     => 'Available capacity is required.',
            'available_capacity.lte'          => 'Available capacity must be less than or equal to total capacity.',
            'emergency_availability.required' => 'Emergency availability is required.',

            'members.required'                => 'At least one Gaushala member is required.',
            'members.array'                   => 'Gaushala members format is invalid.',
            'members.min'                     => 'At least one Gaushala member is required.',
            'members.*.name.required'         => 'Every member name is required.',
            'members.*.phone.required'        => 'Every member phone number is required.',
            'members.*.phone.digits'          => 'Every member phone number must be 10 digits.',
        ]);

        DB::beginTransaction();

        try {
            $photoPath = null;
            $proofPath = null;

            if ($request->hasFile('gaushala_photo')) {
                $photoPath = $request->file('gaushala_photo')->store('gaushalas/photos', 'public');
            }

            if ($request->hasFile('registration_proof')) {
                $proofPath = $request->file('registration_proof')->store('gaushalas/proofs', 'public');
            }

            $gaushala = Gaushala::create([
                'gaushala_name'          => $validated['gaushala_name'],
                'owner_manager_name'     => $validated['owner_manager_name'],
                'mobile_number'          => $validated['mobile_number'],
                'alternate_number'       => $validated['alternate_number'] ?? null,
                'full_address'           => $validated['full_address'],
                'district'               => $validated['district'],
                'state'                  => $validated['state'],

                'total_capacity'         => $validated['total_capacity'],
                'available_capacity'     => $validated['available_capacity'],

                'rescue_vehicle'         => $request->boolean('rescue_vehicle'),
                'doctor'                 => $request->boolean('doctor'),
                'food_support'           => $request->boolean('food_support'),
                'temporary_shelter'      => $request->boolean('temporary_shelter'),

                'gaushala_photo'         => $photoPath,
                'registration_proof'     => $proofPath,

                'working_hours'          => $validated['working_hours'] ?? null,
                'emergency_availability' => $validated['emergency_availability'],
                'status'                 => $validated['status'] ?? 'active',
            ]);

            foreach ($validated['members'] as $member) {
                $gaushala->members()->create([
                    'member_name'  => $member['name'],
                    'member_phone' => $member['phone'],
                    'status'       => 'active',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.gaushalas.index')
                ->with('success', 'Gaushala registered successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gaushala registration failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            $message = 'Gaushala registration failed.';

            if (config('app.debug')) {
                $message .= ' Error: ' . $e->getMessage();
            } else {
                $message .= ' Please contact administrator.';
            }

            return back()
                ->withInput()
                ->with('error', $message);
        }
    }

    public function show($id)
    {
        $gaushala = Gaushala::with('members')->findOrFail($id);

        return view('admin.gaushalas.show', compact('gaushala'));
    }
}