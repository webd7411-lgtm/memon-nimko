<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'branch_id')) {
            $defaultBranchId = DB::table('branches')->value('id') ?? 1;

            Schema::table('customers', function (Blueprint $table) use ($defaultBranchId) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->default($defaultBranchId)
                    ->constrained('branches')
                    ->onDelete('set null')
                    ->after('id');
            });

            // Backfill any existing records where branch_id is null
            DB::table('customers')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'branch_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
