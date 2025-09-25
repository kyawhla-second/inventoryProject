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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->constrained()->onDelete('set null')->after('total_amount');
            $table->string('voucher_code')->nullable()->after('voucher_id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('voucher_code');
            // subtotal already exists, total_amount will be subtotal - discount_amount + tax
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['voucher_id', 'voucher_code', 'discount_amount']);
        });
    }
};