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
        Schema::create('staff_daily_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('charge_date');
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('hours_worked', 5, 2)->default(8.00);
            $table->decimal('overtime_hours', 5, 2)->default(0.00);
            $table->decimal('overtime_rate', 10, 2)->nullable();
            $table->decimal('total_charge', 10, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamps();
            
            $table->unique(['user_id', 'charge_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_daily_charges');
    }
};
