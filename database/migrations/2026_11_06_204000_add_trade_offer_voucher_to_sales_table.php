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
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'trade_offer_amount')) {
                    $table->decimal('trade_offer_amount', 15, 2)->default(0.00)->after('trade_offer');
                }
                if (!Schema::hasColumn('sales', 'trade_offer_voucher_id')) {
                    $table->unsignedBigInteger('trade_offer_voucher_id')->nullable()->index()->after('trade_offer_amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (Schema::hasColumn('sales', 'trade_offer_voucher_id')) {
                    $table->dropColumn('trade_offer_voucher_id');
                }
                if (Schema::hasColumn('sales', 'trade_offer_amount')) {
                    $table->dropColumn('trade_offer_amount');
                }
            });
        }
    }
};
