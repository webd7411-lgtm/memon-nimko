<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $defaultBranchId;

    public function up(): void
    {
        $this->defaultBranchId = \Illuminate\Support\Facades\DB::table('branches')->value('id') ?? 1;

        Schema::table('raw_materials', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->default($this->defaultBranchId)->constrained('branches')->onDelete('set null')->after('id');
        });

        \Illuminate\Support\Facades\DB::table('raw_materials')->whereNull('branch_id')->update(['branch_id' => $this->defaultBranchId]);
    }

    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
