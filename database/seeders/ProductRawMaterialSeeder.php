<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\RawMaterial;

class ProductRawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Get some products and raw materials
        $products = Product::take(5)->get();
        $rawMaterials = RawMaterial::all();

        if ($products->count() > 0 && $rawMaterials->count() > 0) {
            foreach ($products as $product) {
                // Add 2-4 random raw materials to each product
                $materialsToAdd = $rawMaterials->random(min(rand(2, 4), $rawMaterials->count()));
                
                foreach ($materialsToAdd as $index => $material) {
                    // Check if relationship doesn't already exist
                    if (!$product->rawMaterials()->where('raw_material_id', $material->id)->exists()) {
                        $product->rawMaterials()->attach($material->id, [
                            'quantity_required' => rand(1, 10) + (rand(0, 999) / 1000), // Random quantity 1-11
                            'unit' => $material->unit,
                            'cost_per_unit' => $material->cost_per_unit,
                            'waste_percentage' => rand(0, 15), // 0-15% waste
                            'notes' => 'Sample relationship for ' . $product->name,
                            'is_primary' => $index === 0, // First material is primary
                            'sequence_order' => $index + 1,
                        ]);
                    }
                }
            }

            $this->command->info('Created sample product-raw material relationships.');
        } else {
            $this->command->warn('Skipping product-raw material seeder - insufficient data.');
        }
    }
}