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
        if (!Schema::hasTable('distributor_sales_returns')) {
            Schema::create('distributor_sales_returns', function (Blueprint $table) {
                $table->id();
                $table->string('return_no')->unique();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->unsignedBigInteger('distributor_sale_id')->nullable()->index();
                $table->unsignedBigInteger('distributor_customer_id')->index();
                $table->unsignedBigInteger('booker_user_id')->nullable()->index();
                $table->unsignedBigInteger('account_id')->nullable()->index();
                $table->date('return_date');
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->decimal('tax_or_deduction', 15, 2)->default(0.00);
                $table->decimal('refund_amount', 15, 2)->default(0.00);      // Cash refunded to customer
                $table->decimal('net_return_amount', 15, 2)->default(0.00);  // Total return value
                $table->decimal('credit_adjusted', 15, 2)->default(0.00);   // Amount deducted from customer outstanding balance
                $table->string('refund_mode', 20)->default('credit');       // credit, cash
                $table->text('reason')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('distributor_sales_return_items')) {
            Schema::create('distributor_sales_return_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_sales_return_id')->index();
                $table->unsignedBigInteger('distributor_sale_item_id')->nullable()->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->decimal('qty', 15, 2)->default(0.00);
                $table->decimal('unit_price', 15, 2)->default(0.00);
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->text('item_condition')->nullable(); // restockable, damaged
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_sales_return_items');
        Schema::dropIfExists('distributor_sales_returns');
    }
};
