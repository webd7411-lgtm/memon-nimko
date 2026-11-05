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
        if (Schema::hasTable('distributor_claims')) {
            Schema::table('distributor_claims', function (Blueprint $table) {
                if (!Schema::hasColumn('distributor_claims', 'from_date')) {
                    $table->date('from_date')->nullable()->after('claim_date');
                }
                if (!Schema::hasColumn('distributor_claims', 'to_date')) {
                    $table->date('to_date')->nullable()->after('from_date');
                }
            });
        }

        if (Schema::hasTable('distributor_sales')) {
            Schema::table('distributor_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('distributor_sales', 'claim_id')) {
                    $table->unsignedBigInteger('claim_id')->nullable()->index()->after('account_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_claims')) {
            Schema::table('distributor_claims', function (Blueprint $table) {
                if (Schema::hasColumn('distributor_claims', 'to_date')) {
                    $table->dropColumn('to_date');
                }
                if (Schema::hasColumn('distributor_claims', 'from_date')) {
                    $table->dropColumn('from_date');
                }
            });
        }

        if (Schema::hasTable('distributor_sales')) {
            Schema::table('distributor_sales', function (Blueprint $table) {
                if (Schema::hasColumn('distributor_sales', 'claim_id')) {
                    $table->dropColumn('claim_id');
                }
            });
        }
    }
};
