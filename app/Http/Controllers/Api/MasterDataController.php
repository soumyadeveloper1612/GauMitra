<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CowCondition;
use App\Models\ReportType;
use App\Models\AnimalType;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function reportTypes()
    {
        $reportTypes = ReportType::active()
            ->ordered()
            ->get()
            ->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'name'        => $item->name,
                    'slug'        => $item->slug,
                    'icon_class'  => $item->icon_class,
                    'color_code'  => $item->color_code,
                    'description' => $item->description,
                ];
            });

        return response()->json([
            'status'  => true,
            'message' => 'Report types fetched successfully.',
            'data'    => $reportTypes,
        ]);
    }

    public function cowConditions(Request $request)
    {
        $query = CowCondition::with('reportType')
            ->active()
            ->ordered();

        if ($request->filled('report_type_id')) {
            $query->where('report_type_id', $request->report_type_id);
        }

        if ($request->filled('severity_level')) {
            $query->where('severity_level', $request->severity_level);
        }

        $cowConditions = $query->get()->map(function ($item) {
            return [
                'id'              => $item->id,
                'report_type_id'  => $item->report_type_id,
                'report_type'     => $item->reportType?->name,
                'name'            => $item->name,
                'slug'            => $item->slug,
                'severity_level'  => $item->severity_level,
                'icon_class'      => $item->icon_class,
                'color_code'      => $item->color_code,
                'symptoms'        => $item->symptoms,
                'first_aid_steps' => $item->first_aid_steps,
                'description'     => $item->description,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Cow conditions fetched successfully.',
            'data'    => $cowConditions,
        ]);
    }

    public function reportOptions()
    {
        $reportTypes = ReportType::with(['activeCowConditions'])
            ->active()
            ->ordered()
            ->get()
            ->map(function ($reportType) {
                return [
                    'id'          => $reportType->id,
                    'name'        => $reportType->name,
                    'slug'        => $reportType->slug,
                    'icon_class'  => $reportType->icon_class,
                    'color_code'  => $reportType->color_code,
                    'description' => $reportType->description,
                    'cow_conditions' => $reportType->activeCowConditions->map(function ($condition) {
                        return [
                            'id'              => $condition->id,
                            'name'            => $condition->name,
                            'slug'            => $condition->slug,
                            'severity_level'  => $condition->severity_level,
                            'icon_class'      => $condition->icon_class,
                            'color_code'      => $condition->color_code,
                            'symptoms'        => $condition->symptoms,
                            'first_aid_steps' => $condition->first_aid_steps,
                            'description'     => $condition->description,
                        ];
                    }),
                ];
            });

        return response()->json([
            'status'  => true,
            'message' => 'Report options fetched successfully.',
            'data'    => $reportTypes,
        ]);
    }

    public function animalTypes()
{
    $animalTypes = AnimalType::active()
        ->ordered()
        ->get()
        ->map(function ($item) {
            return [
                'id'          => $item->id,
                'name'        => $item->name,
                'slug'        => $item->slug,
                'icon_class'  => $item->icon_class,
                'color_code'  => $item->color_code,
                'description' => $item->description,
            ];
        });

    return response()->json([
        'status'  => true,
        'message' => 'Animal types fetched successfully.',
        'data'    => $animalTypes,
    ]);
}
}