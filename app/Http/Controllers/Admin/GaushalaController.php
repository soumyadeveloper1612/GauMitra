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
        return view('admin.gaushalas.index', compact('gaushalas'));
    }

    public function create()
    {
        return view('admin.gaushalas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'gaushala_name'       => 'required|string|max:255',
            'owner_manager_name'  => 'required|string|max:255',
            'mobile_number'       => 'required|digits:10',
            'alternate_number'    => 'nullable|digits:10|different:mobile_number',
            'full_address'        => 'required|string',
            'district'            => 'required|string|max:150',
            'state'               => 'required|string|max:150',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'status'              => 'nullable|in:active,inactive',
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
        ]);

        Gaushala::create([
            'gaushala_name'      => $request->gaushala_name,
            'owner_manager_name' => $request->owner_manager_name,
            'mobile_number'      => $request->mobile_number,
            'alternate_number'   => $request->alternate_number,
            'full_address'       => $request->full_address,
            'district'           => $request->district,
            'state'              => $request->state,
            'latitude'           => $request->latitude,
            'longitude'          => $request->longitude,
            'status'             => $request->status ?? 'active',
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