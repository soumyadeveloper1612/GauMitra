<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnimalCondition;
use App\Models\AnimalType;
use App\Models\EmergencyCase;
use App\Models\ReportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MasterDataController extends Controller
{
    public function animalTypes()
    {
        try {
            $animalTypes = AnimalType::query()
                ->where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
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
                })
                ->values();

            return response()->json([
                'status'  => true,
                'message' => 'Animal types fetched successfully.',
                'data'    => $animalTypes,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Animal types API error', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while fetching animal types.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function reportTypes()
    {
        try {
            $reportTypes = ReportType::query()
                ->where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
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
                })
                ->values();

            return response()->json([
                'status'  => true,
                'message' => 'Report types fetched successfully.',
                'data'    => $reportTypes,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Report types API error', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while fetching report types.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function animalConditions(Request $request)
    {
        try {
            $query = AnimalCondition::query()
                ->with([
                    'reportType' => function ($q) {
                        $q->select('id', 'name', 'slug', 'icon_class', 'color_code');
                    }
                ])
                ->where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc');

            if ($request->filled('report_type_id')) {
                $query->where('report_type_id', $request->report_type_id);
            }

            if ($request->filled('severity_level')) {
                $query->where('severity_level', $request->severity_level);
            }

            $animalConditions = $query->get()
                ->map(function ($item) {
                    return [
                        'id'              => $item->id,
                        'report_type_id'  => $item->report_type_id,
                        'report_type'     => optional($item->reportType)->name,
                        'name'            => $item->name,
                        'slug'            => $item->slug,
                        'severity_level'  => $item->severity_level,
                        'icon_class'      => $item->icon_class,
                        'color_code'      => $item->color_code,
                        'symptoms'        => $item->symptoms,
                        'first_aid_steps' => $item->first_aid_steps,
                        'description'     => $item->description,
                    ];
                })
                ->values();

            return response()->json([
                'status'  => true,
                'message' => 'Animal conditions fetched successfully.',
                'data'    => $animalConditions,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Animal conditions API error', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'query' => $request->all(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while fetching animal conditions.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function emergencyCaseOptions()
    {
        try {
            $animalTypes = AnimalType::query()
                ->where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
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
                })
                ->values();

            $reportTypes = ReportType::query()
                ->with([
                    'activeAnimalConditions' => function ($q) {
                        $q->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
                    }
                ])
                ->where('status', 'active')
                ->orderBy('sort_order', 'asc')
                ->orderBy('name', 'asc')
                ->get()
                ->map(function ($reportType) {
                    return [
                        'id'          => $reportType->id,
                        'name'        => $reportType->name,
                        'slug'        => $reportType->slug,
                        'icon_class'  => $reportType->icon_class,
                        'color_code'  => $reportType->color_code,
                        'description' => $reportType->description,

                        'animal_conditions' => $reportType->activeAnimalConditions
                            ->map(function ($condition) {
                                return [
                                    'id'              => $condition->id,
                                    'report_type_id'  => $condition->report_type_id,
                                    'name'            => $condition->name,
                                    'slug'            => $condition->slug,
                                    'severity_level'  => $condition->severity_level,
                                    'icon_class'      => $condition->icon_class,
                                    'color_code'      => $condition->color_code,
                                    'symptoms'        => $condition->symptoms,
                                    'first_aid_steps' => $condition->first_aid_steps,
                                    'description'     => $condition->description,
                                ];
                            })
                            ->values(),
                    ];
                })
                ->values();

            $severityLevels = defined(EmergencyCase::class . '::SEVERITIES')
                ? EmergencyCase::SEVERITIES
                : [
                    'low',
                    'medium',
                    'high',
                    'critical',
                ];

            return response()->json([
                'status'  => true,
                'message' => 'Emergency case options fetched successfully.',
                'data'    => [
                    'animal_types'    => $animalTypes,
                    'report_types'    => $reportTypes,
                    'severity_levels' => $severityLevels,
                ],
            ], 200);

        } catch (Throwable $e) {
            Log::error('Emergency case options API error', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong while fetching emergency case options.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}