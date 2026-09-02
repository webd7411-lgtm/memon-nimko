<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_raw_material_bom', function (Blueprint $table) {
            $table->unsignedBigInteger('raw_material_id')->nullable()->change();
            if (!Schema::hasColumn('product_raw_material_bom', 'ingredient_product_id')) {
                $table->foreignId('ingredient_product_id')->nullable()->after('raw_material_id')->constrained('products')->cascadeOnDelete();
            }
        });

        Schema::table('production_raw_material_usage', function (Blueprint $table) {
            $table->unsignedBigInteger('raw_material_id')->nullable()->change();
            if (!Schema::hasColumn('production_raw_material_usage', 'ingredient_product_id')) {
                $table->foreignId('ingredient_product_id')->nullable()->after('raw_material_id')->constrained('products')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_raw_material_bom', function (Blueprint $table) {
            if (Schema::hasColumn('product_raw_material_bom', 'ingredient_product_id')) {
                $table->dropForeign(['ingredient_product_id']);
                $table->dropColumn('ingredient_product_id');
            }
        });

        Schema::table('production_raw_material_usage', function (Blueprint $table) {
            if (Schema::hasColumn('production_raw_material_usage', 'ingredient_product_id')) {
                $table->dropForeign(['ingredient_product_id']);
                $table->dropColumn('ingredient_product_id');
            }
        });
    }
};
