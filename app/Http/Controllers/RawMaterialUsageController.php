<?php

namespace App\Http\Controllers;

use App\Models\RawMaterialUsage;
use App\Models\RawMaterial;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RawMaterialUsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RawMaterialUsage::with(['rawMaterial', 'product', 'order', 'recordedBy']);

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->forPeriod($request->start_date, $request->end_date);
        }

        // Filter by usage type
        if ($request->filled('usage_type')) {
            $query->byType($request->usage_type);
        }

        // Filter by raw material
        if ($request->filled('raw_material_id')) {
            $query->byRawMaterial($request->raw_material_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->byProduct($request->product_id);
        }

        $usages = $query->orderBy('usage_date', 'desc')->paginate(15);
        
        $rawMaterials = RawMaterial::all();
        $products = Product::all();
        $usageTypes = RawMaterialUsage::getUsageTypes();

        return view('raw-material-usages.index', compact('usages', 'rawMaterials', 'products', 'usageTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $rawMaterials = RawMaterial::where('quantity', '>', 0)->get();
        $products = Product::all();
        $orders = Order::whereIn('status', ['pending', 'processing'])->with('customer')->get();
        $usageTypes = RawMaterialUsage::getUsageTypes();

        // Pre-fill if coming from specific raw material or order
        $selectedRawMaterial = null;
        $selectedOrder = null;

        if ($request->filled('raw_material_id')) {
            $selectedRawMaterial = RawMaterial::find($request->raw_material_id);
        }

        if ($request->filled('order_id')) {
            $selectedOrder = Order::find($request->order_id);
        }

        return view('raw-material-usages.create', compact(
            'rawMaterials', 'products', 'orders', 'usageTypes', 
            'selectedRawMaterial', 'selectedOrder'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'product_id' => 'nullable|exists:products,id',
            'order_id' => 'nullable|exists:orders,id',
            'quantity_used' => 'required|numeric|min:0.001',
            'usage_date' => 'required|date',
            'usage_type' => 'required|string|in:' . implode(',', array_keys(RawMaterialUsage::getUsageTypes())),
            'notes' => 'nullable|string|max:1000',
            'batch_number' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $rawMaterial = RawMaterial::findOrFail($request->raw_material_id);

            // Check if sufficient stock is available
            if ($rawMaterial->quantity < $request->quantity_used) {
                throw new \Exception('Insufficient raw material stock. Available: ' . $rawMaterial->quantity . ' ' . $rawMaterial->unit);
            }

            $usage = new RawMaterialUsage($request->all());
            $usage->cost_per_unit = $rawMaterial->cost_per_unit;
            $usage->calculateTotalCost();
            $usage->recorded_by = Auth::id();
            $usage->save();

            // Update raw material stock
            $usage->updateRawMaterialStock();
        });

        return redirect()->route('raw-material-usages.index')
            ->with('success', 'Raw material usage recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RawMaterialUsage $rawMaterialUsage)
    {
        $rawMaterialUsage->load(['rawMaterial', 'product', 'order.customer', 'recordedBy']);
        return view('raw-material-usages.show', compact('rawMaterialUsage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RawMaterialUsage $rawMaterialUsage)
    {
        $rawMaterials = RawMaterial::all();
        $products = Product::all();
        $orders = Order::whereIn('status', ['pending', 'processing'])->with('customer')->get();
        $usageTypes = RawMaterialUsage::getUsageTypes();

        return view('raw-material-usages.edit', compact('rawMaterialUsage', 'rawMaterials', 'products', 'orders', 'usageTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RawMaterialUsage $rawMaterialUsage)
    {
        $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'product_id' => 'nullable|exists:products,id',
            'order_id' => 'nullable|exists:orders,id',
            'quantity_used' => 'required|numeric|min:0.001',
            'usage_date' => 'required|date',
            'usage_type' => 'required|string|in:' . implode(',', array_keys(RawMaterialUsage::getUsageTypes())),
            'notes' => 'nullable|string|max:1000',
            'batch_number' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $rawMaterialUsage) {
            $oldQuantity = $rawMaterialUsage->quantity_used;
            $oldRawMaterialId = $rawMaterialUsage->raw_material_id;
            
            // Restore old stock
            if ($oldRawMaterialId) {
                $oldRawMaterial = RawMaterial::find($oldRawMaterialId);
                if ($oldRawMaterial) {
                    $oldRawMaterial->increment('quantity', $oldQuantity);
                }
            }

            // Update usage record
            $newRawMaterial = RawMaterial::findOrFail($request->raw_material_id);
            
            // Check if sufficient stock is available
            if ($newRawMaterial->quantity < $request->quantity_used) {
                throw new \Exception('Insufficient raw material stock. Available: ' . $newRawMaterial->quantity . ' ' . $newRawMaterial->unit);
            }

            $rawMaterialUsage->fill($request->all());
            $rawMaterialUsage->cost_per_unit = $newRawMaterial->cost_per_unit;
            $rawMaterialUsage->calculateTotalCost();
            $rawMaterialUsage->save();

            // Update new raw material stock
            $rawMaterialUsage->updateRawMaterialStock();
        });

        return redirect()->route('raw-material-usages.show', $rawMaterialUsage)
            ->with('success', 'Raw material usage updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RawMaterialUsage $rawMaterialUsage)
    {
        DB::transaction(function () use ($rawMaterialUsage) {
            // Restore stock when deleting usage record
            $rawMaterial = $rawMaterialUsage->rawMaterial;
            if ($rawMaterial) {
                $rawMaterial->increment('quantity', $rawMaterialUsage->quantity_used);
            }

            $rawMaterialUsage->delete();
        });

        return redirect()->route('raw-material-usages.index')
            ->with('success', 'Raw material usage record deleted and stock restored.');
    }

    /**
     * Get usage statistics for a specific raw material
     */
    public function getUsageStats(RawMaterial $rawMaterial, Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());

        $stats = [
            'total_usage' => $rawMaterial->getTotalUsageForPeriod($startDate, $endDate),
            'total_cost' => $rawMaterial->getTotalCostForPeriod($startDate, $endDate),
            'usage_by_type' => $rawMaterial->usages()
                ->forPeriod($startDate, $endDate)
                ->selectRaw('usage_type, SUM(quantity_used) as total_quantity, SUM(total_cost) as total_cost')
                ->groupBy('usage_type')
                ->get(),
            'daily_usage' => $rawMaterial->usages()
                ->forPeriod($startDate, $endDate)
                ->selectRaw('DATE(usage_date) as date, SUM(quantity_used) as total_quantity')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk record usage for multiple raw materials
     */
    public function bulkCreate()
    {
        $rawMaterials = RawMaterial::where('quantity', '>', 0)->get();
        $products = Product::all();
        $orders = Order::whereIn('status', ['pending', 'processing'])->with('customer')->get();
        $usageTypes = RawMaterialUsage::getUsageTypes();

        return view('raw-material-usages.bulk-create', compact('rawMaterials', 'products', 'orders', 'usageTypes'));
    }

    /**
     * Store bulk usage records
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'usages' => 'required|array|min:1',
            'usages.*.raw_material_id' => 'required|exists:raw_materials,id',
            'usages.*.quantity_used' => 'required|numeric|min:0.001',
            'usages.*.usage_type' => 'required|string|in:' . implode(',', array_keys(RawMaterialUsage::getUsageTypes())),
            'common_usage_date' => 'required|date',
            'common_product_id' => 'nullable|exists:products,id',
            'common_order_id' => 'nullable|exists:orders,id',
            'common_notes' => 'nullable|string|max:1000',
            'common_batch_number' => 'nullable|string|max:100',
        ]);

        $createdCount = 0;

        DB::transaction(function () use ($request, &$createdCount) {
            foreach ($request->usages as $usageData) {
                if (empty($usageData['raw_material_id']) || empty($usageData['quantity_used'])) {
                    continue;
                }

                $rawMaterial = RawMaterial::findOrFail($usageData['raw_material_id']);

                // Check if sufficient stock is available
                if ($rawMaterial->quantity < $usageData['quantity_used']) {
                    throw new \Exception("Insufficient stock for {$rawMaterial->name}. Available: {$rawMaterial->quantity} {$rawMaterial->unit}");
                }

                $usage = new RawMaterialUsage([
                    'raw_material_id' => $usageData['raw_material_id'],
                    'product_id' => $request->common_product_id,
                    'order_id' => $request->common_order_id,
                    'quantity_used' => $usageData['quantity_used'],
                    'cost_per_unit' => $rawMaterial->cost_per_unit,
                    'usage_date' => $request->common_usage_date,
                    'usage_type' => $usageData['usage_type'],
                    'notes' => $request->common_notes,
                    'batch_number' => $request->common_batch_number,
                    'recorded_by' => Auth::id(),
                ]);

                $usage->calculateTotalCost();
                $usage->save();
                $usage->updateRawMaterialStock();

                $createdCount++;
            }
        });

        return redirect()->route('raw-material-usages.index')
            ->with('success', "Successfully recorded {$createdCount} raw material usage records.");
    }
}
