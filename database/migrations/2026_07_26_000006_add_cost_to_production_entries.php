<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_entries', function (Blueprint $table) {
            $table->decimal('production_cost', 14, 2)->default(0)->after('notes');
        });

        Schema::create('production_raw_material_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_entry_id')->constrained('production_entries')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->decimal('qty_used', 12, 2);
            $table->decimal('cost_per_unit', 12, 2)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_raw_material_usage');
        Schema::table('production_entries', function (Blueprint $table) {
            $table->dropColumn('production_cost');
        });
    }
};
