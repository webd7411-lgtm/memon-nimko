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

        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->default($this->defaultBranchId)->constrained('branches')->onDelete('set null')->after('id');
        });

        // Set existing records to default branch
        \Illuminate\Support\Facades\DB::table('stock_transfers')->whereNull('branch_id')->update(['branch_id' => $this->defaultBranchId]);
    }

    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
