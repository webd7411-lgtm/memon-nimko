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
        Schema::table('distributor_customer_ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('distributor_customer_ledgers', 'account_id')) {
                $table->unsignedBigInteger('account_id')->nullable()->after('distributor_id')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributor_customer_ledgers', function (Blueprint $table) {
            if (Schema::hasColumn('distributor_customer_ledgers', 'account_id')) {
                $table->dropColumn('account_id');
            }
        });
    }
};
