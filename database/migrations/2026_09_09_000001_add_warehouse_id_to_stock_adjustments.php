<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_adjustments')) {
            Schema::table('stock_adjustments', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_adjustments', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('created_by')->constrained('branches')->nullOnDelete();
                }
                if (!Schema::hasColumn('stock_adjustments', 'warehouse_id')) {
                    $afterCol = Schema::hasColumn('stock_adjustments', 'branch_id') ? 'branch_id' : 'created_by';
                    $table->foreignId('warehouse_id')->nullable()->after($afterCol)->constrained('warehouses')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_adjustments')) {
            Schema::table('stock_adjustments', function (Blueprint $table) {
                if (Schema::hasColumn('stock_adjustments', 'warehouse_id')) {
                    $table->dropForeign(['warehouse_id']);
                    $table->dropColumn('warehouse_id');
                }
                if (Schema::hasColumn('stock_adjustments', 'branch_id')) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                }
            });
        }
    }
};
