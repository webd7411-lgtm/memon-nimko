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
        // 1. Add distributor_id to accounts
        if (Schema::hasTable('accounts') && !Schema::hasColumn('accounts', 'distributor_id')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->unsignedBigInteger('distributor_id')->nullable()->after('head_id')->index();
                $table->string('account_number')->nullable()->after('title');
                $table->string('bank_name')->nullable()->after('account_number');
                $table->string('branch_name')->nullable()->after('bank_name');
            });

            try {
                // Drop unique on account_code so multiple distributors can use their own custom codes without collision
                Schema::table('accounts', function (Blueprint $table) {
                    $table->dropUnique('accounts_account_code_unique');
                });
            } catch (\Throwable $e) {
                // Ignore if index has different name or already dropped
            }
        }

        // 2. Add account_id to distributor_sales
        if (Schema::hasTable('distributor_sales') && !Schema::hasColumn('distributor_sales', 'account_id')) {
            Schema::table('distributor_sales', function (Blueprint $table) {
                $table->unsignedBigInteger('account_id')->nullable()->after('payment_type')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_sales') && Schema::hasColumn('distributor_sales', 'account_id')) {
            Schema::table('distributor_sales', function (Blueprint $table) {
                $table->dropColumn('account_id');
            });
        }

        if (Schema::hasTable('accounts') && Schema::hasColumn('accounts', 'distributor_id')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn(['distributor_id', 'account_number', 'bank_name', 'branch_name']);
            });
        }
    }
};
