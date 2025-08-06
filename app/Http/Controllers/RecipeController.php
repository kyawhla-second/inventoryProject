<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with(['product', 'createdBy']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $recipes = $query->orderBy('created_at', 'desc')->paginate(15);
        $products = Product::all();

        return view('recipes.index', compact('recipes', 'products'));
    }

    public function create(Request $request)
    {
        $products = Product::all();
        $rawMaterials = RawMaterial::all();
        $selectedProduct = null;

        if ($request->filled('product_id')) {
            $selectedProduct = Product::find($request->product_id);
        }

        return view('recipes.create', compact('products', 'rawMaterials', 'selectedProduct'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'batch_size' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
            'yield_percentage' => 'required|numeric|min:0|max:100',
            'preparation_time' => 'nullable|integer|min:0',
            'production_time' => 'nullable|integer|min:0',
            'instructions' => 'nullable|string',
            'version' => 'required|string|max:50',
            'recipe_items' => 'required|array|min:1',
            'recipe_items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'recipe_items.*.quantity_required' => 'required|numeric|min:0.001',
            'recipe_items.*.unit' => 'required|string|max:50',
            'recipe_items.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
            'recipe_items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            // Deactivate existing recipes for this product if this is set as active
            if ($request->is_active) {
                Recipe::where('product_id', $request->product_id)->update(['is_active' => false]);
            }

            $recipe = Recipe::create([
                'product_id' => $request->product_id,
                'name' => $request->name,
                'description' => $request->description,
                'batch_size' => $request->batch_size,
                'unit' => $request->unit,
                'yield_percentage' => $request->yield_percentage,
                'preparation_time' => $request->preparation_time,
                'production_time' => $request->production_time,
                'instructions' => $request->instructions,
                'is_active' => $request->boolean('is_active'),
                'version' => $request->version,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->recipe_items as $index => $item) {
                if (!empty($item['raw_material_id']) && !empty($item['quantity_required'])) {
                    $rawMaterial = RawMaterial::find($item['raw_material_id']);
                    
                    RecipeItem::create([
                        'recipe_id' => $recipe->id,
                        'raw_material_id' => $item['raw_material_id'],
                        'quantity_required' => $item['quantity_required'],
                        'unit' => $item['unit'],
                        'cost_per_unit' => $rawMaterial->cost_per_unit,
                        'waste_percentage' => $item['waste_percentage'] ?? 0,
                        'notes' => $item['notes'],
                        'sequence_order' => $index + 1,
                    ]);
                }
            }
        });

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe created successfully.');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['product', 'recipeItems.rawMaterial', 'createdBy']);
        return view('recipes.show', compact('recipe'));
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load('recipeItems.rawMaterial');
        $products = Product::all();
        $rawMaterials = RawMaterial::all();

        return view('recipes.edit', compact('recipe', 'products', 'rawMaterials'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'batch_size' => 'required|numeric|min:0.001',
            'unit' => 'required|string|max:50',
            'yield_percentage' => 'required|numeric|min:0|max:100',
            'preparation_time' => 'nullable|integer|min:0',
            'production_time' => 'nullable|integer|min:0',
            'instructions' => 'nullable|string',
            'version' => 'required|string|max:50',
            'recipe_items' => 'required|array|min:1',
            'recipe_items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'recipe_items.*.quantity_required' => 'required|numeric|min:0.001',
            'recipe_items.*.unit' => 'required|string|max:50',
            'recipe_items.*.waste_percentage' => 'nullable|numeric|min:0|max:100',
            'recipe_items.*.notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $recipe) {
            // Deactivate existing recipes for this product if this is set as active
            if ($request->is_active && !$recipe->is_active) {
                Recipe::where('product_id', $request->product_id)
                    ->where('id', '!=', $recipe->id)
                    ->update(['is_active' => false]);
            }

            $recipe->update([
                'product_id' => $request->product_id,
                'name' => $request->name,
                'description' => $request->description,
                'batch_size' => $request->batch_size,
                'unit' => $request->unit,
                'yield_percentage' => $request->yield_percentage,
                'preparation_time' => $request->preparation_time,
                'production_time' => $request->production_time,
                'instructions' => $request->instructions,
                'is_active' => $request->boolean('is_active'),
                'version' => $request->version,
            ]);

            // Delete existing recipe items
            $recipe->recipeItems()->delete();

            // Create new recipe items
            foreach ($request->recipe_items as $index => $item) {
                if (!empty($item['raw_material_id']) && !empty($item['quantity_required'])) {
                    $rawMaterial = RawMaterial::find($item['raw_material_id']);
                    
                    RecipeItem::create([
                        'recipe_id' => $recipe->id,
                        'raw_material_id' => $item['raw_material_id'],
                        'quantity_required' => $item['quantity_required'],
                        'unit' => $item['unit'],
                        'cost_per_unit' => $rawMaterial->cost_per_unit,
                        'waste_percentage' => $item['waste_percentage'] ?? 0,
                        'notes' => $item['notes'],
                        'sequence_order' => $index + 1,
                    ]);
                }
            }
        });

        return redirect()->route('recipes.show', $recipe)
            ->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('recipes.index')
            ->with('success', 'Recipe deleted successfully.');
    }

    public function calculateCost(Recipe $recipe, Request $request)
    {
        $quantity = $request->get('quantity', $recipe->batch_size);
        $requirements = $recipe->calculateMaterialRequirements($quantity);
        
        return response()->json([
            'quantity' => $quantity,
            'total_cost' => $requirements->sum('estimated_cost'),
            'cost_per_unit' => $quantity > 0 ? $requirements->sum('estimated_cost') / $quantity : 0,
            'requirements' => $requirements,
        ]);
    }

    public function duplicate(Recipe $recipe)
    {
        $newRecipe = $recipe->replicate();
        $newRecipe->name = $recipe->name . ' (Copy)';
        $newRecipe->version = $recipe->version . '-copy';
        $newRecipe->is_active = false;
        $newRecipe->created_by = Auth::id();
        $newRecipe->save();

        foreach ($recipe->recipeItems as $item) {
            $newItem = $item->replicate();
            $newItem->recipe_id = $newRecipe->id;
            $newItem->save();
        }

        return redirect()->route('recipes.edit', $newRecipe)
            ->with('success', 'Recipe duplicated successfully.');
    }
}
