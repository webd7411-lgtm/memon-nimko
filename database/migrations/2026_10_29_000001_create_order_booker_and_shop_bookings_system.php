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
        // 1. Add distributor_id to users table for Order Bookers
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'distributor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('distributor_id')->nullable()->after('branch_id')->index();
            });
        }

        // 2. Create distributor_shop_bookings table
        if (!Schema::hasTable('distributor_shop_bookings')) {
            Schema::create('distributor_shop_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('booking_no')->unique();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->unsignedBigInteger('booker_user_id')->index();
                $table->unsignedBigInteger('distributor_customer_id')->index(); // the retail shop
                $table->date('booking_date');
                $table->integer('total_items')->default(0);
                $table->decimal('total_qty', 15, 2)->default(0.00);
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->decimal('discount', 15, 2)->default(0.00);
                $table->decimal('grand_total', 15, 2)->default(0.00);
                $table->string('status', 30)->default('booked'); // booked, delivered, cancelled
                $table->unsignedBigInteger('converted_sale_id')->nullable()->index();
                $table->timestamp('converted_at')->nullable();
                $table->unsignedBigInteger('converted_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create distributor_shop_booking_items table
        if (!Schema::hasTable('distributor_shop_booking_items')) {
            Schema::create('distributor_shop_booking_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->decimal('qty', 15, 2)->default(0.00);
                $table->decimal('unit_price', 15, 2)->default(0.00);
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_shop_booking_items');
        Schema::dropIfExists('distributor_shop_bookings');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'distributor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('distributor_id');
            });
        }
    }
};
