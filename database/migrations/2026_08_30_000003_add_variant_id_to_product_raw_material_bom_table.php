<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_raw_material_bom', function (Blueprint $table) {
            if (!Schema::hasColumn('product_raw_material_bom', 'variant_id')) {
                $table->foreignId('variant_id')->nullable()->after('product_id')->constrained('product_variants')->cascadeOnDelete();
            }
        });

        // Drop old unique constraint if it exists, to allow raw materials per variant
        try {
            Schema::table('product_raw_material_bom', function (Blueprint $table) {
                $table->dropUnique(['product_id', 'raw_material_id']);
            });
        } catch (\Exception $e) {
            // Ignore if index did not exist or already dropped
        }
    }

    public function down(): void
    {
        Schema::table('product_raw_material_bom', function (Blueprint $table) {
            if (Schema::hasColumn('product_raw_material_bom', 'variant_id')) {
                $table->dropForeign(['variant_id']);
                $table->dropColumn('variant_id');
            }
        });
    }
};
