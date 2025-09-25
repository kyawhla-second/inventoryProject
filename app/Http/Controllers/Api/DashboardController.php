<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function overview()
    {
        $data = [
            'totals' => [
                'products' => Product::count(),
                'categories' => Category::count(),
                'raw_materials' => RawMaterial::count(),
                'suppliers' => Supplier::count(),
            ],
            'inventory_alerts' => [
                'low_stock_products' => Product::whereColumn('quantity', '<=', 'min_quantity')->count(),
                'low_stock_raw_materials' => RawMaterial::whereColumn('current_stock', '<=', 'min_stock_level')->count(),
                'out_of_stock_products' => Product::where('quantity', 0)->count(),
            ],
            'recent_activity' => [
                'recent_sales' => Sale::with(['customer'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function($sale) {
                        return [
                            'id' => $sale->id,
                            'customer' => $sale->customer->name ?? 'Walk-in Customer',
                            'total' => $sale->total_amount,
                            'date' => $sale->created_at->format('Y-m-d H:i:s'),
                        ];
                    }),
                'recent_purchases' => Purchase::with(['supplier'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function($purchase) {
                        return [
                            'id' => $purchase->id,
                            'supplier' => $purchase->supplier->name ?? 'Unknown Supplier',
                            'total' => $purchase->total_amount,
                            'date' => $purchase->created_at->format('Y-m-d H:i:s'),
                        ];
                    }),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function salesStats(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        
        $startDate = match($period) {
            'day' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $sales = Sale::where('created_at', '>=', $startDate)->get();
        
        $stats = [
            'total_sales' => $sales->sum('total_amount'),
            'total_orders' => $sales->count(),
            'average_order_value' => $sales->count() > 0 ? $sales->sum('total_amount') / $sales->count() : 0,
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function inventoryValue()
    {
        $productValue = Product::selectRaw('SUM(quantity * cost) as total_value')->first()->total_value ?? 0;
        $rawMaterialValue = RawMaterial::selectRaw('SUM(current_stock * cost_per_unit) as total_value')->first()->total_value ?? 0;
        
        $data = [
            'product_inventory_value' => (float) $productValue,
            'raw_material_inventory_value' => (float) $rawMaterialValue,
            'total_inventory_value' => (float) ($productValue + $rawMaterialValue),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}