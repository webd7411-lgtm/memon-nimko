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
        if (Schema::hasTable('distributor_sale_items') && !Schema::hasColumn('distributor_sale_items', 'trade_offer')) {
            Schema::table('distributor_sale_items', function (Blueprint $table) {
                $table->decimal('trade_offer', 15, 2)->default(0.00)->after('qty');
            });
        }

        if (Schema::hasTable('distributor_shop_booking_items') && !Schema::hasColumn('distributor_shop_booking_items', 'trade_offer')) {
            Schema::table('distributor_shop_booking_items', function (Blueprint $table) {
                $table->decimal('trade_offer', 15, 2)->default(0.00)->after('qty');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_sale_items') && Schema::hasColumn('distributor_sale_items', 'trade_offer')) {
            Schema::table('distributor_sale_items', function (Blueprint $table) {
                $table->dropColumn('trade_offer');
            });
        }

        if (Schema::hasTable('distributor_shop_booking_items') && Schema::hasColumn('distributor_shop_booking_items', 'trade_offer')) {
            Schema::table('distributor_shop_booking_items', function (Blueprint $table) {
                $table->dropColumn('trade_offer');
            });
        }
    }
};
