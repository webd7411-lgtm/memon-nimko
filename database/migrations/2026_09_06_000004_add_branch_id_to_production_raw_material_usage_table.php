<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('production_raw_material_usage') && !Schema::hasColumn('production_raw_material_usage', 'branch_id')) {
            Schema::table('production_raw_material_usage', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('total_cost')->constrained('branches')->onDelete('set null');
            });

            $defaultBranchId = DB::table('branches')->value('id') ?? 1;
            DB::table('production_raw_material_usage')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('production_raw_material_usage')) {
            Schema::table('production_raw_material_usage', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
