<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\User;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Get first admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }

        // Get some products and raw materials
        $products = Product::take(3)->get();
        $rawMaterials = RawMaterial::take(5)->get();

        if ($products->count() > 0 && $rawMaterials->count() > 0 && $admin) {
            foreach ($products as $index => $product) {
                $recipe = Recipe::create([
                    'product_id' => $product->id,
                    'name' => $product->name . ' Standard Recipe',
                    'description' => 'Standard production recipe for ' . $product->name,
                    'batch_size' => 10,
                    'unit' => 'pcs',
                    'yield_percentage' => 95.0,
                    'preparation_time' => 30,
                    'production_time' => 120,
                    'instructions' => "1. Prepare all materials\n2. Mix according to specifications\n3. Process for required time\n4. Quality check\n5. Package",
                    'is_active' => true,
                    'version' => '1.0',
                    'created_by' => $admin->id,
                ]);

                // Add 2-3 recipe items per recipe
                $materialsToUse = $rawMaterials->random(min(3, $rawMaterials->count()));
                
                foreach ($materialsToUse as $materialIndex => $material) {
                    RecipeItem::create([
                        'recipe_id' => $recipe->id,
                        'raw_material_id' => $material->id,
                        'quantity_required' => rand(1, 5) + (rand(0, 999) / 1000), // Random quantity between 1-6
                        'unit' => $material->unit,
                        'cost_per_unit' => $material->cost_per_unit,
                        'waste_percentage' => rand(0, 10), // 0-10% waste
                        'notes' => 'Standard requirement for ' . $material->name,
                        'sequence_order' => $materialIndex + 1,
                    ]);
                }
            }

            $this->command->info('Created ' . $products->count() . ' sample recipes with recipe items.');
        } else {
            $this->command->warn('Skipping recipe seeder - insufficient data (need products, raw materials, and admin user).');
        }
    }
}