<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimalTreatmentGuide;
use Illuminate\Http\Request;

class AnimalTreatmentGuideController extends Controller
{
    public function index(Request $request)
    {
        $query = AnimalTreatmentGuide::query();

        if ($request->filled('animal_type')) {
            $query->where('animal_type', $request->animal_type);
        }

        if ($request->filled('case_type')) {
            $query->where('case_type', $request->case_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('condition_name', 'like', "%{$search}%")
                  ->orWhere('animal_type', 'like', "%{$search}%")
                  ->orWhere('case_type', 'like', "%{$search}%")
                  ->orWhere('medicines', 'like', "%{$search}%");
            });
        }

        $guides = $query->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'     => AnimalTreatmentGuide::count(),
            'active'    => AnimalTreatmentGuide::where('status', 'active')->count(),
            'inactive'  => AnimalTreatmentGuide::where('status', 'inactive')->count(),
            'emergency' => AnimalTreatmentGuide::where('priority', 'emergency')->count(),
        ];

        return view('admin.animal-treatment-guides.index', compact('guides', 'stats'));
    }

    public function create()
    {
        return view('admin.animal-treatment-guides.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'animal_type'       => 'required|string|max:100',
            'case_type'         => 'required|string|max:100',
            'condition_name'    => 'required|string|max:255',
            'symptoms'          => 'nullable|string',
            'first_aid_steps'   => 'nullable|string',
            'medicines'         => 'nullable|string',
            'dosage'            => 'nullable|string|max:1000',
            'treatment_steps'   => 'nullable|string',
            'recovery_steps'    => 'nullable|string',
            'precautions'       => 'nullable|string',
            'vet_contact_note'  => 'nullable|string|max:1000',
            'priority'          => 'required|in:normal,emergency',
            'status'            => 'required|in:active,inactive',
            'sort_order'        => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = session('admin_id');
        $validated['sort_order'] = $request->sort_order ?? 0;

        AnimalTreatmentGuide::create($validated);

        return redirect()
            ->route('admin.animal-treatment-guides.index')
            ->with('success', 'Treatment guide added successfully.');
    }

    public function update(Request $request, $id)
    {
        $guide = AnimalTreatmentGuide::findOrFail($id);

        $validated = $request->validate([
            'animal_type'       => 'required|string|max:100',
            'case_type'         => 'required|string|max:100',
            'condition_name'    => 'required|string|max:255',
            'symptoms'          => 'nullable|string',
            'first_aid_steps'   => 'nullable|string',
            'medicines'         => 'nullable|string',
            'dosage'            => 'nullable|string|max:1000',
            'treatment_steps'   => 'nullable|string',
            'recovery_steps'    => 'nullable|string',
            'precautions'       => 'nullable|string',
            'vet_contact_note'  => 'nullable|string|max:1000',
            'priority'          => 'required|in:normal,emergency',
            'status'            => 'required|in:active,inactive',
            'sort_order'        => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $request->sort_order ?? 0;

        $guide->update($validated);

        return redirect()
            ->route('admin.animal-treatment-guides.index')
            ->with('success', 'Treatment guide updated successfully.');
    }

    public function destroy($id)
    {
        $guide = AnimalTreatmentGuide::findOrFail($id);
        $guide->delete();

        return redirect()
            ->route('admin.animal-treatment-guides.index')
            ->with('success', 'Treatment guide deleted successfully.');
    }
}