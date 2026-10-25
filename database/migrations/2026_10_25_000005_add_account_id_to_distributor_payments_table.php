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
        if (Schema::hasTable('distributor_payments')) {
            Schema::table('distributor_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('distributor_payments', 'account_id')) {
                    $table->unsignedBigInteger('account_id')->nullable()->after('payment_method')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_payments')) {
            Schema::table('distributor_payments', function (Blueprint $table) {
                if (Schema::hasColumn('distributor_payments', 'account_id')) {
                    $table->dropColumn('account_id');
                }
            });
        }
    }
};
