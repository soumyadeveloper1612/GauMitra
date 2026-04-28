<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnimalType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AnimalTypeController extends Controller
{
    public function index()
    {
        $animalTypes = AnimalType::notDeleted()
            ->ordered()
            ->get();

        return view('admin.animal-types.index', compact('animalTypes'));
    }

    public function create()
    {
        return view('admin.animal-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'icon_class'  => ['nullable', 'string', 'max:100'],
            'color_code'  => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'status'      => ['required', Rule::in(['active', 'inactive'])],
        ]);

        AnimalType::create([
            'name'        => $validated['name'],
            'slug'        => $this->generateUniqueSlug($validated['name']),
            'icon_class'  => $validated['icon_class'] ?? null,
            'color_code'  => $validated['color_code'] ?? '#b45309',
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('admin.animal-types.index')
            ->with('success', 'Animal type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $animalType = AnimalType::findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'icon_class'  => ['nullable', 'string', 'max:100'],
            'color_code'  => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'status'      => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $animalType->update([
            'name'        => $validated['name'],
            'slug'        => $this->generateUniqueSlug($validated['name'], $animalType->id),
            'icon_class'  => $validated['icon_class'] ?? null,
            'color_code'  => $validated['color_code'] ?? '#b45309',
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('admin.animal-types.index')
            ->with('success', 'Animal type updated successfully.');
    }

    public function destroy($id)
    {
        $animalType = AnimalType::findOrFail($id);

        $animalType->update([
            'status' => 'deleted',
        ]);

        return redirect()
            ->route('admin.animal-types.index')
            ->with('success', 'Animal type deleted successfully.');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            AnimalType::where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}