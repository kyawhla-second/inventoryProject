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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('batch_size', 10, 3)->default(1); // Standard batch size
            $table->string('unit')->default('pcs'); // Unit for batch size
            $table->decimal('yield_percentage', 5, 2)->default(100.00); // Expected yield %
            $table->integer('preparation_time')->nullable(); // in minutes
            $table->integer('production_time')->nullable(); // in minutes
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('version')->default('1.0');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['product_id', 'is_active']);
            $table->unique(['product_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
