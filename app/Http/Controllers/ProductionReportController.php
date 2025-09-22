<?php

namespace App\Http\Controllers;

use App\Models\ProductionPlan;
use App\Models\ProductionPlanItem;
use App\Models\RawMaterialUsage;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionReportController extends Controller
{
    public function index()
    {
        return view('production-reports.index');
    }

    public function varianceAnalysis(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id',
            'production_plan_id' => 'nullable|exists:production_plans,id',
        ]);

        $query = ProductionPlanItem::with(['product', 'recipe', 'productionPlan'])
            ->whereHas('productionPlan', function ($q) use ($request) {
                $q->whereBetween('actual_start_date', [$request->start_date, $request->end_date])
                  ->where('status', 'completed');
            });

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('production_plan_id')) {
            $query->where('production_plan_id', $request->production_plan_id);
        }

        $planItems = $query->get();

        $varianceData = $planItems->map(function ($item) {
            return [
                'production_plan' => $item->productionPlan->plan_number,
                'product' => $item->product->name,
                'planned_quantity' => $item->planned_quantity,
                'actual_quantity' => $item->actual_quantity,
                'quantity_variance' => $item->actual_quantity - $item->planned_quantity,
                'quantity_variance_percentage' => $item->getVariancePercentage(),
                'estimated_cost' => $item->estimated_material_cost,
                'actual_cost' => $item->actual_material_cost,
                'cost_variance' => $item->getCostVariance(),
                'cost_variance_percentage' => $item->getCostVariancePercentage(),
                'efficiency' => $item->getEfficiencyPercentage(),
                'planned_start' => $item->planned_start_date,
                'actual_start' => $item->actual_start_date,
                'planned_end' => $item->planned_end_date,
                'actual_end' => $item->actual_end_date,
            ];
        });

        $summary = [
            'total_items' => $varianceData->count(),
            'total_planned_quantity' => $varianceData->sum('planned_quantity'),
            'total_actual_quantity' => $varianceData->sum('actual_quantity'),
            'total_estimated_cost' => $varianceData->sum('estimated_cost'),
            'total_actual_cost' => $varianceData->sum('actual_cost'),
            'average_efficiency' => $varianceData->avg('efficiency'),
            'items_over_budget' => $varianceData->where('cost_variance', '>', 0)->count(),
            'items_under_budget' => $varianceData->where('cost_variance', '<', 0)->count(),
        ];

        $products = Product::all();
        $productionPlans = ProductionPlan::where('status', 'completed')->get();

        return view('production-reports.variance-analysis', compact(
            'varianceData', 'summary', 'products', 'productionPlans', 'request'
        ));
    }

    public function materialEfficiency(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'raw_material_id' => 'nullable|exists:raw_materials,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        // Get material usage data
        $usageQuery = RawMaterialUsage::with(['rawMaterial', 'product'])
            ->whereBetween('usage_date', [$request->start_date, $request->end_date]);

        if ($request->filled('raw_material_id')) {
            $usageQuery->where('raw_material_id', $request->raw_material_id);
        }

        if ($request->filled('product_id')) {
            $usageQuery->where('product_id', $request->product_id);
        }

        $usages = $usageQuery->get();

        // Group by material and calculate efficiency metrics
        $efficiencyData = $usages->groupBy('raw_material_id')->map(function ($materialUsages, $materialId) {
            $rawMaterial = $materialUsages->first()->rawMaterial;
            
            $totalUsage = $materialUsages->sum('quantity_used');
            $productionUsage = $materialUsages->where('usage_type', 'production')->sum('quantity_used');
            $wasteUsage = $materialUsages->where('usage_type', 'waste')->sum('quantity_used');
            $testingUsage = $materialUsages->where('usage_type', 'testing')->sum('quantity_used');
            $adjustmentUsage = $materialUsages->where('usage_type', 'adjustment')->sum('quantity_used');
            $otherUsage = $totalUsage - $productionUsage - $wasteUsage - $testingUsage - $adjustmentUsage;

            $wastePercentage = $totalUsage > 0 ? ($wasteUsage / $totalUsage) * 100 : 0;
            $efficiencyPercentage = $totalUsage > 0 ? ($productionUsage / $totalUsage) * 100 : 0;

            return [
                'raw_material' => $rawMaterial->name,
                'unit' => $rawMaterial->unit,
                'total_usage' => $totalUsage,
                'production_usage' => $productionUsage,
                'waste_usage' => $wasteUsage,
                'testing_usage' => $testingUsage,
                'adjustment_usage' => $adjustmentUsage,
                'other_usage' => $otherUsage,
                'waste_percentage' => $wastePercentage,
                'efficiency_percentage' => $efficiencyPercentage,
                'total_cost' => $materialUsages->sum('total_cost'),
                'waste_cost' => $materialUsages->where('usage_type', 'waste')->sum('total_cost'),
                'usage_count' => $materialUsages->count(),
            ];
        })->values();

        $summary = [
            'total_materials' => $efficiencyData->count(),
            'average_efficiency' => $efficiencyData->avg('efficiency_percentage'),
            'average_waste' => $efficiencyData->avg('waste_percentage'),
            'total_cost' => $efficiencyData->sum('total_cost'),
            'total_waste_cost' => $efficiencyData->sum('waste_cost'),
            'highest_waste_material' => $efficiencyData->sortByDesc('waste_percentage')->first(),
            'most_efficient_material' => $efficiencyData->sortByDesc('efficiency_percentage')->first(),
        ];

        $rawMaterials = RawMaterial::all();
        $products = Product::all();

        return view('production-reports.material-efficiency', compact(
            'efficiencyData', 'summary', 'rawMaterials', 'products', 'request'
        ));
    }

    public function productionSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id',
        ]);

        // Get production data
        $query = ProductionPlanItem::with(['product', 'productionPlan'])
            ->whereHas('productionPlan', function ($q) use ($request) {
                $q->whereBetween('actual_start_date', [$request->start_date, $request->end_date]);
            });

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $planItems = $query->get();

        // Group by product
        $productionData = $planItems->groupBy('product_id')->map(function ($items, $productId) {
            $product = $items->first()->product;
            
            return [
                'product' => $product->name,
                'total_planned' => $items->sum('planned_quantity'),
                'total_actual' => $items->sum('actual_quantity'),
                'total_estimated_cost' => $items->sum('estimated_material_cost'),
                'total_actual_cost' => $items->sum('actual_material_cost'),
                'production_runs' => $items->count(),
                'completed_runs' => $items->where('status', 'completed')->count(),
                'average_efficiency' => $items->avg(function ($item) {
                    return $item->getEfficiencyPercentage();
                }),
                'cost_variance' => $items->sum('actual_material_cost') - $items->sum('estimated_material_cost'),
            ];
        })->values();

        // Get material usage by product
        $materialUsageQuery = RawMaterialUsage::with(['rawMaterial', 'product'])
            ->whereBetween('usage_date', [$request->start_date, $request->end_date])
            ->where('usage_type', 'production');

        if ($request->filled('product_id')) {
            $materialUsageQuery->where('product_id', $request->product_id);
        }

        $materialUsages = $materialUsageQuery->get();

        $materialData = $materialUsages->groupBy('product_id')->map(function ($usages, $productId) {
            $product = $usages->first()->product;
            
            return [
                'product' => $product ? $product->name : 'Unknown',
                'materials_used' => $usages->groupBy('raw_material_id')->count(),
                'total_material_cost' => $usages->sum('total_cost'),
                'total_quantity_used' => $usages->sum('quantity_used'),
            ];
        })->values();

        $summary = [
            'total_products' => $productionData->count(),
            'total_production_runs' => $productionData->sum('production_runs'),
            'total_completed_runs' => $productionData->sum('completed_runs'),
            'total_planned_quantity' => $productionData->sum('total_planned'),
            'total_actual_quantity' => $productionData->sum('total_actual'),
            'total_estimated_cost' => $productionData->sum('total_estimated_cost'),
            'total_actual_cost' => $productionData->sum('total_actual_cost'),
            'overall_efficiency' => $productionData->avg('average_efficiency'),
            'total_cost_variance' => $productionData->sum('cost_variance'),
        ];

        $products = Product::all();

        return view('production-reports.production-summary', compact(
            'productionData', 'materialData', 'summary', 'products', 'request'
        ));
    }

    public function costAnalysis(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id',
        ]);

        // Get cost breakdown by product
        $query = ProductionPlanItem::with(['product', 'recipe'])
            ->whereHas('productionPlan', function ($q) use ($request) {
                $q->whereBetween('actual_start_date', [$request->start_date, $request->end_date])
                  ->where('status', 'completed');
            });

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $planItems = $query->get();

        $costData = $planItems->groupBy('product_id')->map(function ($items, $productId) {
            $product = $items->first()->product;
            
            $totalActualQuantity = $items->sum('actual_quantity');
            $totalActualCost = $items->sum('actual_material_cost');
            
            return [
                'product' => $product->name,
                'total_quantity' => $totalActualQuantity,
                'total_cost' => $totalActualCost,
                'cost_per_unit' => $totalActualQuantity > 0 ? $totalActualCost / $totalActualQuantity : 0,
                'production_runs' => $items->count(),
                'average_batch_size' => $items->avg('actual_quantity'),
                'min_cost_per_unit' => $items->min(function ($item) {
                    return $item->actual_quantity > 0 ? $item->actual_material_cost / $item->actual_quantity : 0;
                }),
                'max_cost_per_unit' => $items->max(function ($item) {
                    return $item->actual_quantity > 0 ? $item->actual_material_cost / $item->actual_quantity : 0;
                }),
            ];
        })->values();

        // Get material cost breakdown
        $materialCosts = RawMaterialUsage::with(['rawMaterial', 'product'])
            ->whereBetween('usage_date', [$request->start_date, $request->end_date])
            ->where('usage_type', 'production');

        if ($request->filled('product_id')) {
            $materialCosts->where('product_id', $request->product_id);
        }

        $materialCostData = $materialCosts->get()
            ->groupBy('raw_material_id')
            ->map(function ($usages, $materialId) {
                $material = $usages->first()->rawMaterial;
                
                return [
                    'material' => $material->name,
                    'total_quantity' => $usages->sum('quantity_used'),
                    'total_cost' => $usages->sum('total_cost'),
                    'average_cost_per_unit' => $usages->avg('cost_per_unit'),
                    'usage_count' => $usages->count(),
                    'unit' => $material->unit,
                ];
            })
            ->sortByDesc('total_cost')
            ->values();

        $summary = [
            'total_production_cost' => $costData->sum('total_cost'),
            'average_cost_per_unit' => $costData->avg('cost_per_unit'),
            'total_quantity_produced' => $costData->sum('total_quantity'),
            'most_expensive_product' => $costData->sortByDesc('cost_per_unit')->first(),
            'most_cost_effective_product' => $costData->sortBy('cost_per_unit')->first(),
            'top_material_cost' => $materialCostData->first(),
        ];

        $products = Product::all();

        return view('production-reports.cost-analysis', compact(
            'costData', 'materialCostData', 'summary', 'products', 'request'
        ));
    }

    public function exportVarianceAnalysis(Request $request)
    {
        // This would export the variance analysis data to Excel/CSV
        // Implementation depends on your preferred export library (e.g., Laravel Excel)
        
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    public function exportMaterialEfficiency(Request $request)
    {
        // This would export the material efficiency data to Excel/CSV
        
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}