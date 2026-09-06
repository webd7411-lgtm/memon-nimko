<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('raw_material_stocks') && !Schema::hasColumn('raw_material_stocks', 'branch_id')) {
            Schema::table('raw_material_stocks', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('raw_material_id')->constrained('branches')->onDelete('set null');
            });

            $defaultBranchId = DB::table('branches')->value('id') ?? 1;
            DB::table('raw_material_stocks')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('raw_material_stocks')) {
            Schema::table('raw_material_stocks', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
