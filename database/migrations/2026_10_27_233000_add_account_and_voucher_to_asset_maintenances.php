<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('asset_maintenances')) {
            Schema::table('asset_maintenances', function (Blueprint $table) {
                if (!Schema::hasColumn('asset_maintenances', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable()->after('cost');
                    $table->foreign('account_id')->references('id')->on('accounts')->nullOnDelete();
                }
                if (!Schema::hasColumn('asset_maintenances', 'expense_voucher_id')) {
                    $table->unsignedBigInteger('expense_voucher_id')->nullable()->after('account_id');
                    $table->foreign('expense_voucher_id')->references('id')->on('expense_vouchers')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('asset_maintenances')) {
            Schema::table('asset_maintenances', function (Blueprint $table) {
                if (Schema::hasColumn('asset_maintenances', 'expense_voucher_id')) {
                    $table->dropForeign(['expense_voucher_id']);
                    $table->dropColumn('expense_voucher_id');
                }
                if (Schema::hasColumn('asset_maintenances', 'account_id')) {
                    $table->dropForeign(['account_id']);
                    $table->dropColumn('account_id');
                }
            });
        }
    }
};
