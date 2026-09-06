<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('raw_materials') && Schema::hasColumn('raw_materials', 'branch_id')) {
            Schema::table('raw_materials', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('raw_materials') && !Schema::hasColumn('raw_materials', 'branch_id')) {
            Schema::table('raw_materials', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->default(1)->constrained('branches')->onDelete('set null')->after('id');
            });
            DB::table('raw_materials')->whereNull('branch_id')->update(['branch_id' => 1]);
        }
    }
};
