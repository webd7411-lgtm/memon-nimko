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
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'cash_account_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->unsignedBigInteger('cash_account_id')->nullable()->after('cash');
            });
        }

        if (Schema::hasTable('sales_returns') && !Schema::hasColumn('sales_returns', 'cash_account_id')) {
            Schema::table('sales_returns', function (Blueprint $table) {
                $table->unsignedBigInteger('cash_account_id')->nullable()->after('cash');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'cash_account_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('cash_account_id');
            });
        }

        if (Schema::hasTable('sales_returns') && Schema::hasColumn('sales_returns', 'cash_account_id')) {
            Schema::table('sales_returns', function (Blueprint $table) {
                $table->dropColumn('cash_account_id');
            });
        }
    }
};
