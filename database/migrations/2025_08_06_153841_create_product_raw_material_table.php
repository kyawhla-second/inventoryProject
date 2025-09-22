<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_raw_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_required', 10, 3); // How much raw material is needed
            $table->string('unit'); // Unit of measurement (kg, liters, pieces, etc.)
            $table->decimal('cost_per_unit', 10, 2)->nullable(); // Optional: override cost per unit
            $table->decimal('waste_percentage', 5, 2)->default(0.00); // Expected waste percentage
            $table->text('notes')->nullable(); // Additional notes
            $table->boolean('is_primary')->default(false); // Is this a primary ingredient?
            $table->integer('sequence_order')->default(1); // Order in which materials are used
            $table->timestamps();
            
            // Ensure unique combination of product and raw material
            $table->unique(['product_id', 'raw_material_id']);
            $table->index(['product_id', 'sequence_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_raw_material');
    }
};
