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
        Schema::create('recipe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_required', 10, 3);
            $table->string('unit');
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->decimal('waste_percentage', 5, 2)->default(0.00); // Expected waste %
            $table->text('notes')->nullable();
            $table->integer('sequence_order')->default(1);
            $table->timestamps();
            
            $table->index(['recipe_id', 'sequence_order']);
            $table->unique(['recipe_id', 'raw_material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_items');
    }
};
