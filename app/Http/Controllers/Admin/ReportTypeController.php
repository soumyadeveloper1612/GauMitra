<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReportTypeController extends Controller
{
    public function index()
    {
        $reportTypes = ReportType::notDeleted()
            ->ordered()
            ->get();

        return view('admin.report-cases.report-type-manage', compact('reportTypes'));
    }

    public function create()
    {
        return view('admin.report-cases.report-type-create');
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

        $slug = $this->generateUniqueSlug($request->name);

        ReportType::create([
            'name'        => $validated['name'],
            'slug'        => $slug,
            'icon_class'  => $validated['icon_class'] ?? null,
            'color_code'  => $validated['color_code'] ?? '#d97706',
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('admin.report-types.index')
            ->with('success', 'Report type created successfully.');
    }

    public function update(Request $request, $id)
    {
        $reportType = ReportType::findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'icon_class'  => ['nullable', 'string', 'max:100'],
            'color_code'  => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'status'      => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $reportType->update([
            'name'        => $validated['name'],
            'slug'        => $this->generateUniqueSlug($validated['name'], $reportType->id),
            'icon_class'  => $validated['icon_class'] ?? null,
            'color_code'  => $validated['color_code'] ?? '#d97706',
            'description' => $validated['description'] ?? null,
            'sort_order'  => $validated['sort_order'] ?? 0,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('admin.report-types.index')
            ->with('success', 'Report type updated successfully.');
    }

    public function destroy($id)
    {
        $reportType = ReportType::findOrFail($id);

        $reportType->update([
            'status' => 'deleted',
        ]);

        return redirect()
            ->route('admin.report-types.index')
            ->with('success', 'Report type deleted successfully.');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            ReportType::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}