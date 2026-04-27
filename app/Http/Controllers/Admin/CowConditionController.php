<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CowCondition;
use App\Models\ReportType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CowConditionController extends Controller
{
    public function index()
    {
        $cowConditions = CowCondition::with('reportType')
            ->notDeleted()
            ->ordered()
            ->get();

        $reportTypes = ReportType::active()
            ->ordered()
            ->get();

        return view('admin.report-cases.cow-conditions-manage', compact('cowConditions', 'reportTypes'));
    }

    public function create()
    {
        $reportTypes = ReportType::active()
            ->ordered()
            ->get();

        return view('admin.report-cases.cow-conditions-create', compact('reportTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type_id'  => ['nullable', 'exists:report_types,id'],
            'name'            => ['required', 'string', 'max:150'],
            'severity_level'  => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'icon_class'      => ['nullable', 'string', 'max:100'],
            'color_code'      => ['nullable', 'string', 'max:20'],
            'symptoms'        => ['nullable', 'string'],
            'first_aid_steps' => ['nullable', 'string'],
            'description'     => ['nullable', 'string'],
            'sort_order'      => ['nullable', 'integer', 'min:0'],
            'status'          => ['required', Rule::in(['active', 'inactive'])],
        ]);

        CowCondition::create([
            'report_type_id'  => $validated['report_type_id'] ?? null,
            'name'            => $validated['name'],
            'slug'            => $this->generateUniqueSlug($validated['name']),
            'severity_level'  => $validated['severity_level'],
            'icon_class'      => $validated['icon_class'] ?? null,
            'color_code'      => $validated['color_code'] ?? '#b45309',
            'symptoms'        => $validated['symptoms'] ?? null,
            'first_aid_steps' => $validated['first_aid_steps'] ?? null,
            'description'     => $validated['description'] ?? null,
            'sort_order'      => $validated['sort_order'] ?? 0,
            'status'          => $validated['status'],
        ]);

        return redirect()
            ->route('admin.cow-conditions.index')
            ->with('success', 'Cow condition created successfully.');
    }

    public function update(Request $request, $id)
    {
        $cowCondition = CowCondition::findOrFail($id);

        $validated = $request->validate([
            'report_type_id'  => ['nullable', 'exists:report_types,id'],
            'name'            => ['required', 'string', 'max:150'],
            'severity_level'  => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'icon_class'      => ['nullable', 'string', 'max:100'],
            'color_code'      => ['nullable', 'string', 'max:20'],
            'symptoms'        => ['nullable', 'string'],
            'first_aid_steps' => ['nullable', 'string'],
            'description'     => ['nullable', 'string'],
            'sort_order'      => ['nullable', 'integer', 'min:0'],
            'status'          => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $cowCondition->update([
            'report_type_id'  => $validated['report_type_id'] ?? null,
            'name'            => $validated['name'],
            'slug'            => $this->generateUniqueSlug($validated['name'], $cowCondition->id),
            'severity_level'  => $validated['severity_level'],
            'icon_class'      => $validated['icon_class'] ?? null,
            'color_code'      => $validated['color_code'] ?? '#b45309',
            'symptoms'        => $validated['symptoms'] ?? null,
            'first_aid_steps' => $validated['first_aid_steps'] ?? null,
            'description'     => $validated['description'] ?? null,
            'sort_order'      => $validated['sort_order'] ?? 0,
            'status'          => $validated['status'],
        ]);

        return redirect()
            ->route('admin.cow-conditions.index')
            ->with('success', 'Cow condition updated successfully.');
    }

    public function destroy($id)
    {
        $cowCondition = CowCondition::findOrFail($id);

        $cowCondition->update([
            'status' => 'deleted',
        ]);

        return redirect()
            ->route('admin.cow-conditions.index')
            ->with('success', 'Cow condition deleted successfully.');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            CowCondition::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}