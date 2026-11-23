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
        if (Schema::hasTable('raw_material_purchase_items')) {
            Schema::table('raw_material_purchase_items', function (Blueprint $table) {
                $table->decimal('price_per_unit', 16, 4)->change();
                $table->decimal('qty', 14, 4)->change();
            });
        }

        if (Schema::hasTable('raw_material_purchase_return_items')) {
            Schema::table('raw_material_purchase_return_items', function (Blueprint $table) {
                $table->decimal('price_per_unit', 16, 4)->change();
                $table->decimal('qty', 14, 4)->change();
            });
        }

        if (Schema::hasTable('raw_materials')) {
            Schema::table('raw_materials', function (Blueprint $table) {
                $table->decimal('initial_price', 16, 4)->default(0)->change();
                $table->decimal('alert_qty', 14, 4)->default(0)->change();
            });
        }

        if (Schema::hasTable('raw_material_stocks')) {
            Schema::table('raw_material_stocks', function (Blueprint $table) {
                $table->decimal('qty', 14, 4)->default(0)->change();
            });
        }

        if (Schema::hasTable('production_raw_material_usage')) {
            Schema::table('production_raw_material_usage', function (Blueprint $table) {
                $table->decimal('cost_per_unit', 16, 4)->default(0)->change();
                $table->decimal('qty_used', 14, 4)->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('raw_material_purchase_items')) {
            Schema::table('raw_material_purchase_items', function (Blueprint $table) {
                $table->decimal('price_per_unit', 12, 2)->change();
                $table->decimal('qty', 12, 2)->change();
            });
        }

        if (Schema::hasTable('raw_material_purchase_return_items')) {
            Schema::table('raw_material_purchase_return_items', function (Blueprint $table) {
                $table->decimal('price_per_unit', 12, 2)->change();
                $table->decimal('qty', 12, 2)->change();
            });
        }

        if (Schema::hasTable('raw_materials')) {
            Schema::table('raw_materials', function (Blueprint $table) {
                $table->decimal('initial_price', 12, 2)->default(0)->change();
                $table->decimal('alert_qty', 12, 2)->default(0)->change();
            });
        }

        if (Schema::hasTable('raw_material_stocks')) {
            Schema::table('raw_material_stocks', function (Blueprint $table) {
                $table->decimal('qty', 12, 2)->default(0)->change();
            });
        }

        if (Schema::hasTable('production_raw_material_usage')) {
            Schema::table('production_raw_material_usage', function (Blueprint $table) {
                $table->decimal('cost_per_unit', 12, 2)->default(0)->change();
                $table->decimal('qty_used', 12, 2)->default(0)->change();
            });
        }
    }
};
