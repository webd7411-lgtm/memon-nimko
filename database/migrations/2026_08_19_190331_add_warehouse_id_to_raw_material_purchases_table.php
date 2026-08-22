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
        if (!Schema::hasColumn('raw_material_purchases', 'warehouse_id')) {
            Schema::table('raw_material_purchases', function (Blueprint $table) {
                $table->foreignId('warehouse_id')->nullable()->after('vendor_name')->constrained('warehouses')->nullOnDelete();
            });
        }
        if (!Schema::hasColumn('raw_material_stocks', 'warehouse_id')) {
            Schema::table('raw_material_stocks', function (Blueprint $table) {
                $table->foreignId('warehouse_id')->nullable()->after('raw_material_id')->constrained('warehouses')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('raw_material_purchases', 'warehouse_id')) {
            Schema::table('raw_material_purchases', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            });
        }
        if (Schema::hasColumn('raw_material_stocks', 'warehouse_id')) {
            Schema::table('raw_material_stocks', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            });
        }
    }
};
