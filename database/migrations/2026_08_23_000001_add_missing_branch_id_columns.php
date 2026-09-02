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
        $defaultBranchId = DB::table('branches')->value('id') ?? 1;

        $tables = [
            'sales_returns',
            'purchase_returns',
            'vendor_payments',
            'receipts_vouchers',
            'payment_vouchers',
            'production_entries',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($defaultBranchId) {
                    $table->foreignId('branch_id')->nullable()->default($defaultBranchId)->constrained('branches')->onDelete('set null')->after('id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'sales_returns',
            'purchase_returns',
            'vendor_payments',
            'receipts_vouchers',
            'payment_vouchers',
            'production_entries',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                });
            }
        }
    }
};
