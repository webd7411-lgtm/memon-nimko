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
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'trade_offer')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->text('trade_offer')->nullable()->after('per_discount');
            });
        }

        if (Schema::hasTable('sales_returns') && !Schema::hasColumn('sales_returns', 'trade_offer')) {
            Schema::table('sales_returns', function (Blueprint $table) {
                $table->text('trade_offer')->nullable()->after('per_discount');
            });
        }

        if (Schema::hasTable('product_bookings') && !Schema::hasColumn('product_bookings', 'trade_offer')) {
            Schema::table('product_bookings', function (Blueprint $table) {
                $table->text('trade_offer')->nullable()->after('per_discount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'trade_offer')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('trade_offer');
            });
        }

        if (Schema::hasTable('sales_returns') && Schema::hasColumn('sales_returns', 'trade_offer')) {
            Schema::table('sales_returns', function (Blueprint $table) {
                $table->dropColumn('trade_offer');
            });
        }

        if (Schema::hasTable('product_bookings') && Schema::hasColumn('product_bookings', 'trade_offer')) {
            Schema::table('product_bookings', function (Blueprint $table) {
                $table->dropColumn('trade_offer');
            });
        }
    }
};
