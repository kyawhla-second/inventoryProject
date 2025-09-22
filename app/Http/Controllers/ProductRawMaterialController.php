<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRawMaterialController extends Controller
{
    public function index(Product $product)
    {
        $product->load(['rawMaterials', 'category']);
        $availableRawMaterials = RawMaterial::whereNotIn('id', $product->rawMaterials->pluck('id'))->get();
        
        return view('products.raw-materials.index', compact('product', 'availableRawMaterials'));
    }

    public function create(Product $product)
    {
        // Get raw materials that are not already added to this product
        $rawMaterials = RawMaterial::whereNotIn('id', $product->rawMaterials->pluck('id'))->get();
        return view('products.raw-materials.create', compact('product', 'rawMaterials'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'raw_materials' => 'required|array|min:1',
            'raw_materials.*.raw_material_id' => [
                'required',
                'exists:raw_materials,id',
                function ($attribute, $value, $fail) use ($product) {
                    if ($product->rawMaterials()->where('raw_material_id', $value)->exists()) {
                        $rawMaterial = RawMaterial::find($value);
                        $fail("The raw material '{$rawMaterial->name}' is already added to this product.");
                    }
                },
            ],
            'raw_materials.*.quantity_required' => 'required|numeric|min:0.001',
            'raw_materials.*.unit' => 'required|string|max:50',
            'raw_materials.*.cost_per_unit' => 'nullable|numeric|min:0',
            'raw_materials.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
            'raw_materials.*.is_primary' => 'boolean',
            'raw_materials.*.notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                foreach ($request->raw_materials as $index => $materialData) {
                    if (!empty($materialData['raw_material_id']) && !empty($materialData['quantity_required'])) {
                        $rawMaterial = RawMaterial::find($materialData['raw_material_id']);
                        
                        // Check if relationship already exists
                        if (!$product->rawMaterials()->where('raw_material_id', $materialData['raw_material_id'])->exists()) {
                            $product->rawMaterials()->attach($materialData['raw_material_id'], [
                                'quantity_required' => $materialData['quantity_required'],
                                'unit' => $materialData['unit'],
                                'cost_per_unit' => $materialData['cost_per_unit'] ?? $rawMaterial->cost_per_unit,
                                'waste_percentage' => $materialData['waste_percentage'] ?? 0,
                                'notes' => $materialData['notes'],
                                'is_primary' => $materialData['is_primary'] ?? false,
                                'sequence_order' => $index + 1,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('products.raw-materials.index', $product)
                ->with('success', 'Raw materials added to product successfully.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate entry or other database errors
            if ($e->errorInfo[1] == 1062) { // Duplicate entry error
                return redirect()->back()
                    ->with('error', 'One or more raw materials are already added to this product.')
                    ->withInput();
            }
            
            return redirect()->back()
                ->with('error', 'An error occurred while adding raw materials: ' . $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Product $product, RawMaterial $rawMaterial)
    {
        $pivotData = $product->rawMaterials()->where('raw_material_id', $rawMaterial->id)->first();
        
        if (!$pivotData) {
            return redirect()->route('products.raw-materials.index', $product)
                ->with('error', 'Raw material not found for this product.');
        }

        return view('products.raw-materials.edit', compact('product', 'rawMaterial', 'pivotData'));
    }

    public function update(Request $request, Product $product, RawMaterial $rawMaterial)
    {
        $request->validate([
            'quantity_required' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'waste_percentage' => 'nullable|numeric|min:0|max:100',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        $product->rawMaterials()->updateExistingPivot($rawMaterial->id, [
            'quantity_required' => $request->quantity_required,
            'unit' => $request->unit,
            'cost_per_unit' => $request->cost_per_unit ?? $rawMaterial->cost_per_unit,
            'waste_percentage' => $request->waste_percentage ?? 0,
            'notes' => $request->notes,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return redirect()->route('products.raw-materials.index', $product)
            ->with('success', 'Raw material relationship updated successfully.');
    }

    public function destroy(Product $product, RawMaterial $rawMaterial)
    {
        $product->rawMaterials()->detach($rawMaterial->id);

        return redirect()->route('products.raw-materials.index', $product)
            ->with('success', 'Raw material removed from product successfully.');
    }

    public function calculateCost(Product $product, Request $request)
    {
        $quantity = $request->get('quantity', 1);
        $requirements = $product->calculateRequiredRawMaterials($quantity);
        
        return response()->json([
            'quantity' => $quantity,
            'total_cost' => $requirements->sum('total_cost'),
            'cost_per_unit' => $quantity > 0 ? $requirements->sum('total_cost') / $quantity : 0,
            'requirements' => $requirements,
        ]);
    }

    public function bulkAdd(Request $request, Product $product)
    {
        $request->validate([
            'raw_material_ids' => 'required|array|min:1',
            'raw_material_ids.*' => 'exists:raw_materials,id',
            'default_quantity' => 'required|numeric|min:0.001',
            'default_unit' => 'required|string|max:50',
            'default_waste_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $addedCount = 0;
            
            DB::transaction(function () use ($request, $product, &$addedCount) {
                foreach ($request->raw_material_ids as $index => $rawMaterialId) {
                    $rawMaterial = RawMaterial::find($rawMaterialId);
                    
                    if (!$product->rawMaterials()->where('raw_material_id', $rawMaterialId)->exists()) {
                        $product->rawMaterials()->attach($rawMaterialId, [
                            'quantity_required' => $request->default_quantity,
                            'unit' => $request->default_unit,
                            'cost_per_unit' => $rawMaterial->cost_per_unit,
                            'waste_percentage' => $request->default_waste_percentage ?? 0,
                            'notes' => 'Added via bulk operation',
                            'is_primary' => false,
                            'sequence_order' => $product->rawMaterials()->count() + $index + 1,
                        ]);
                        $addedCount++;
                    }
                }
            });

            if ($addedCount > 0) {
                return redirect()->route('products.raw-materials.index', $product)
                    ->with('success', "Successfully added {$addedCount} raw materials to the product.");
            } else {
                return redirect()->route('products.raw-materials.index', $product)
                    ->with('info', 'All selected raw materials were already added to this product.');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred during bulk add: ' . $e->getMessage())
                ->withInput();
        }
    }
}