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
        Schema::table('expense_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('expense_vouchers', 'is_stock_adjustment')) {
                $table->boolean('is_stock_adjustment')->default(false)->after('total_amount');
            }
            if (!Schema::hasColumn('expense_vouchers', 'stock_adjustment_id')) {
                $table->unsignedBigInteger('stock_adjustment_id')->nullable()->after('is_stock_adjustment');
            }
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_adjustments', 'expense_voucher_id')) {
                $table->unsignedBigInteger('expense_voucher_id')->nullable()->after('warehouse_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('expense_vouchers', 'stock_adjustment_id')) {
                $table->dropColumn('stock_adjustment_id');
            }
            if (Schema::hasColumn('expense_vouchers', 'is_stock_adjustment')) {
                $table->dropColumn('is_stock_adjustment');
            }
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            if (Schema::hasColumn('stock_adjustments', 'expense_voucher_id')) {
                $table->dropColumn('expense_voucher_id');
            }
        });
    }
};
