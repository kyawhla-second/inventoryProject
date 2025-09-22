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
        Schema::create('raw_material_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('quantity_used', 10, 3);
            $table->decimal('cost_per_unit', 10, 2);
            $table->decimal('total_cost', 15, 2);
            $table->date('usage_date');
            $table->string('usage_type')->default('production'); // production, waste, adjustment, etc.
            $table->text('notes')->nullable();
            $table->string('batch_number')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['usage_date', 'usage_type']);
            $table->index(['raw_material_id', 'usage_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_usages');
    }
};
