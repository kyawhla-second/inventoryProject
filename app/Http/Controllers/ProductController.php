<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $products = $query->paginate(5);

        return view('products.index', compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        $categories = Category::all();
        $rawMaterials = RawMaterial::all();
        return view('products.create', compact('categories', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255|unique:products',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'raw_materials' => 'nullable|array',
            'raw_materials.*.raw_material_id' => 'required_with:raw_materials|exists:raw_materials,id',
            'raw_materials.*.quantity_required' => 'required_with:raw_materials|numeric|min:0.001',
            'raw_materials.*.unit' => 'required_with:raw_materials|string|max:50',
            'raw_materials.*.cost_per_unit' => 'nullable|numeric|min:0',
            'raw_materials.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
            'raw_materials.*.is_primary' => 'boolean',
            'raw_materials.*.notes' => 'nullable|string|max:500',
        ]);

        $input = $request->all();

        if ($image = $request->file('image')) {
            $profileImage = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images', $profileImage);
            // store relative path for later retrieval
            $input['image'] = 'storage/images/' . $profileImage;
        }

        // Create the product
        $product = Product::create($input);

        // Handle raw material relationships if provided
        if ($request->has('raw_materials') && is_array($request->raw_materials)) {
            $this->attachRawMaterials($product, $request->raw_materials);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $rawMaterials = RawMaterial::all();
        $product->load('rawMaterials');
        return view('products.edit', compact('product', 'categories', 'rawMaterials'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'raw_materials' => 'nullable|array',
            'raw_materials.*.raw_material_id' => 'required_with:raw_materials|exists:raw_materials,id',
            'raw_materials.*.quantity_required' => 'required_with:raw_materials|numeric|min:0.001',
            'raw_materials.*.unit' => 'required_with:raw_materials|string|max:50',
            'raw_materials.*.cost_per_unit' => 'nullable|numeric|min:0',
            'raw_materials.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
            'raw_materials.*.is_primary' => 'boolean',
            'raw_materials.*.notes' => 'nullable|string|max:500',
        ]);

        $input = $request->all();

        if ($image = $request->file('image')) {
            $profileImage = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/images', $profileImage);
            $input['image'] = 'storage/images/' . $profileImage;
        } else {
            unset($input['image']);
        }

        $product->update($input);

        // Handle raw material relationships if provided
        if ($request->has('raw_materials') && is_array($request->raw_materials)) {
            // Remove existing relationships and add new ones
            $product->rawMaterials()->detach();
            $this->attachRawMaterials($product, $request->raw_materials);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Attach raw materials to a product
     */
    private function attachRawMaterials(Product $product, array $rawMaterialsData)
    {
        DB::transaction(function () use ($product, $rawMaterialsData) {
            foreach ($rawMaterialsData as $index => $materialData) {
                if (!empty($materialData['raw_material_id']) && !empty($materialData['quantity_required'])) {
                    $rawMaterial = RawMaterial::find($materialData['raw_material_id']);
                    
                    if ($rawMaterial) {
                        $product->rawMaterials()->attach($materialData['raw_material_id'], [
                            'quantity_required' => $materialData['quantity_required'],
                            'unit' => $materialData['unit'],
                            'cost_per_unit' => $materialData['cost_per_unit'] ?? $rawMaterial->cost_per_unit,
                            'waste_percentage' => $materialData['waste_percentage'] ?? 0,
                            'notes' => $materialData['notes'] ?? null,
                            'is_primary' => $materialData['is_primary'] ?? false,
                            'sequence_order' => $index + 1,
                        ]);
                    }
                }
            }
        });
    }
}
