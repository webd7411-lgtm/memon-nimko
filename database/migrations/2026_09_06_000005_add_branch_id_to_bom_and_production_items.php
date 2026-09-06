<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('product_raw_material_bom') && !Schema::hasColumn('product_raw_material_bom', 'branch_id')) {
            Schema::table('product_raw_material_bom', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('product_id')->constrained('branches')->onDelete('set null');
            });

            $defaultBranchId = DB::table('branches')->value('id') ?? 1;
            DB::table('product_raw_material_bom')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        }

        if (Schema::hasTable('production_entry_items') && !Schema::hasColumn('production_entry_items', 'branch_id')) {
            Schema::table('production_entry_items', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('variant_id')->constrained('branches')->onDelete('set null');
            });

            $defaultBranchId = DB::table('branches')->value('id') ?? 1;
            DB::table('production_entry_items')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('product_raw_material_bom')) {
            Schema::table('product_raw_material_bom', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
        if (Schema::hasTable('production_entry_items')) {
            Schema::table('production_entry_items', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
