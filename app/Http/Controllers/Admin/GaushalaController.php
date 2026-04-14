<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gaushala;
use Illuminate\Http\Request;

class GaushalaController extends Controller
{
    public function index()
    {
        $gaushalas = Gaushala::latest()->paginate(10);

        return view('admin.gaushalas.manage-gaushala', compact('gaushalas'));
    }

    public function create()
    {
        return view('admin.gaushalas.create-gaushala');
    }

    public function store(Request $request)
    {
        $request->validate([
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

            'latitude'               => 'nullable|numeric|between:-90,90',
            'longitude'              => 'nullable|numeric|between:-180,180',
            'status'                 => 'nullable|in:active,inactive',
        ], [
            'gaushala_name.required'      => 'Gaushala name is required.',
            'owner_manager_name.required' => 'Owner / Manager name is required.',
            'mobile_number.required'      => 'Mobile number is required.',
            'mobile_number.digits'        => 'Mobile number must be 10 digits.',
            'alternate_number.digits'     => 'Alternate number must be 10 digits.',
            'alternate_number.different'  => 'Alternate number must be different from mobile number.',
            'full_address.required'       => 'Full address is required.',
            'district.required'           => 'District is required.',
            'state.required'              => 'State is required.',
            'total_capacity.required'     => 'Total capacity is required.',
            'available_capacity.required' => 'Available capacity is required.',
            'available_capacity.lte'      => 'Available capacity must be less than or equal to total capacity.',
            'emergency_availability.required' => 'Emergency availability is required.',
        ]);

        $photoPath = null;
        $proofPath = null;

        if ($request->hasFile('gaushala_photo')) {
            $photoPath = $request->file('gaushala_photo')->store('gaushalas/photos', 'public');
        }

        if ($request->hasFile('registration_proof')) {
            $proofPath = $request->file('registration_proof')->store('gaushalas/proofs', 'public');
        }

        Gaushala::create([
            'gaushala_name'          => $request->gaushala_name,
            'owner_manager_name'     => $request->owner_manager_name,
            'mobile_number'          => $request->mobile_number,
            'alternate_number'       => $request->alternate_number,
            'full_address'           => $request->full_address,
            'district'               => $request->district,
            'state'                  => $request->state,

            'total_capacity'         => $request->total_capacity,
            'available_capacity'     => $request->available_capacity,

            'rescue_vehicle'         => $request->boolean('rescue_vehicle'),
            'doctor'                 => $request->boolean('doctor'),
            'food_support'           => $request->boolean('food_support'),
            'temporary_shelter'      => $request->boolean('temporary_shelter'),

            'gaushala_photo'         => $photoPath,
            'registration_proof'     => $proofPath,

            'working_hours'          => $request->working_hours,
            'emergency_availability' => $request->emergency_availability,

            'latitude'               => $request->latitude,
            'longitude'              => $request->longitude,
            'status'                 => $request->status ?? 'active',
        ]);

        return redirect()
            ->route('admin.gaushalas.index')
            ->with('success', 'Gaushala registered successfully.');
    }

    public function show($id)
    {
        $gaushala = Gaushala::findOrFail($id);

        return view('admin.gaushalas.show', compact('gaushala'));
    }
}