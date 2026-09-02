<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE product_raw_material_bom ADD INDEX idx_bom_product_id (product_id)");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE product_raw_material_bom DROP INDEX product_raw_material_bom_product_id_raw_material_id_unique");
        } catch (\Exception $e) {
            // Ignore if already dropped
        }
    }

    public function down(): void
    {
    }
};
