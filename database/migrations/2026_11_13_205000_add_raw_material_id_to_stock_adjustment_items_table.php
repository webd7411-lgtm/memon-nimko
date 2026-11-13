<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_adjustment_items')) {
            // Make product_id nullable using raw SQL to ensure smooth MySQL compatibility
            DB::statement('ALTER TABLE stock_adjustment_items MODIFY product_id BIGINT UNSIGNED NULL');

            Schema::table('stock_adjustment_items', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_adjustment_items', 'raw_material_id')) {
                    $table->unsignedBigInteger('raw_material_id')->nullable()->after('product_id');
                    $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_adjustment_items')) {
            Schema::table('stock_adjustment_items', function (Blueprint $table) {
                if (Schema::hasColumn('stock_adjustment_items', 'raw_material_id')) {
                    $table->dropForeign(['raw_material_id']);
                    $table->dropColumn('raw_material_id');
                }
            });

            DB::statement('ALTER TABLE stock_adjustment_items MODIFY product_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
