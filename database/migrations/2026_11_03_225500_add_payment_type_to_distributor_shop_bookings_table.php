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
        if (Schema::hasTable('distributor_shop_bookings') && !Schema::hasColumn('distributor_shop_bookings', 'payment_type')) {
            Schema::table('distributor_shop_bookings', function (Blueprint $table) {
                $table->string('payment_type', 20)->default('cash')->after('booking_date')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_shop_bookings') && Schema::hasColumn('distributor_shop_bookings', 'payment_type')) {
            Schema::table('distributor_shop_bookings', function (Blueprint $table) {
                $table->dropColumn('payment_type');
            });
        }
    }
};
