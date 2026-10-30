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
        // 1. Add approved_discount to distributor_customers table
        if (Schema::hasTable('distributor_customers') && !Schema::hasColumn('distributor_customers', 'approved_discount')) {
            Schema::table('distributor_customers', function (Blueprint $table) {
                $table->decimal('approved_discount', 5, 2)->default(0.00)->after('opening_balance');
            });
        }

        // 2. Add booker_user_id to distributor_sales table
        if (Schema::hasTable('distributor_sales') && !Schema::hasColumn('distributor_sales', 'booker_user_id')) {
            Schema::table('distributor_sales', function (Blueprint $table) {
                $table->unsignedBigInteger('booker_user_id')->nullable()->after('distributor_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_customers') && Schema::hasColumn('distributor_customers', 'approved_discount')) {
            Schema::table('distributor_customers', function (Blueprint $table) {
                $table->dropColumn('approved_discount');
            });
        }

        if (Schema::hasTable('distributor_sales') && Schema::hasColumn('distributor_sales', 'booker_user_id')) {
            Schema::table('distributor_sales', function (Blueprint $table) {
                $table->dropColumn('booker_user_id');
            });
        }
    }
};
