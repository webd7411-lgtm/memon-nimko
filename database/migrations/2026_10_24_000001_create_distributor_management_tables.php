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
        // 1. Distributors Profile Table
        if (!Schema::hasTable('distributors')) {
            Schema::create('distributors', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('phone')->nullable();
                $table->string('cnic')->nullable();
                $table->string('city')->nullable();
                $table->string('area')->nullable();
                $table->text('address')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0.00);
                $table->decimal('current_balance', 15, 2)->default(0.00);
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }

        // 2. Distributor Orders (Purchase Order / Indent Request to Super Admin)
        if (!Schema::hasTable('distributor_orders')) {
            Schema::create('distributor_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_no')->unique();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->date('order_date');
                $table->integer('total_items')->default(0);
                $table->decimal('total_qty', 15, 2)->default(0.00);
                $table->decimal('total_amount', 15, 2)->default(0.00);
                $table->string('status', 30)->default('pending'); // pending, approved, dispatched, delivered, rejected
                $table->unsignedBigInteger('dispatched_warehouse_id')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->text('admin_remarks')->nullable();
                $table->timestamps();
            });
        }

        // 3. Distributor Order Items
        if (!Schema::hasTable('distributor_order_items')) {
            Schema::create('distributor_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_order_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->decimal('requested_qty', 15, 2)->default(0.00);
                $table->decimal('approved_qty', 15, 2)->default(0.00);
                $table->decimal('unit_price', 15, 2)->default(0.00);
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 4. Distributor Stocks (Van / Physical stock in distributor custody)
        if (!Schema::hasTable('distributor_stocks')) {
            Schema::create('distributor_stocks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->decimal('qty', 15, 2)->default(0.00);
                $table->decimal('avg_price', 15, 2)->default(0.00);
                $table->timestamps();

                $table->unique(['distributor_id', 'product_id', 'variant_id'], 'dist_prod_var_unique');
            });
        }

        // 5. Distributor Customers (Retailers / Shops)
        if (!Schema::hasTable('distributor_customers')) {
            Schema::create('distributor_customers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->string('shop_name');
                $table->string('owner_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('area')->nullable();
                $table->text('address')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0.00);
                $table->decimal('current_balance', 15, 2)->default(0.00);
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }

        // 6. Distributor Sales (Secondary Sales to Retailers)
        if (!Schema::hasTable('distributor_sales')) {
            Schema::create('distributor_sales', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_no')->unique();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->unsignedBigInteger('distributor_customer_id')->index();
                $table->date('sale_date');
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->decimal('discount', 15, 2)->default(0.00);
                $table->decimal('grand_total', 15, 2)->default(0.00);
                $table->decimal('paid_amount', 15, 2)->default(0.00);
                $table->decimal('due_amount', 15, 2)->default(0.00);
                $table->string('payment_type', 20)->default('cash'); // cash, credit, partial
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 7. Distributor Sale Items
        if (!Schema::hasTable('distributor_sale_items')) {
            Schema::create('distributor_sale_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_sale_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->decimal('qty', 15, 2)->default(0.00);
                $table->decimal('unit_price', 15, 2)->default(0.00);
                $table->decimal('subtotal', 15, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 8. Distributor Ledgers (Company <-> Distributor Account Statement)
        if (!Schema::hasTable('distributor_ledgers')) {
            Schema::create('distributor_ledgers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->date('date');
                $table->string('type', 30); // dispatch, payment_received, return, adjustment, opening
                $table->string('reference_no')->nullable();
                $table->text('description')->nullable();
                $table->decimal('debit', 15, 2)->default(0.00);  // Increases balance (Mall delivered)
                $table->decimal('credit', 15, 2)->default(0.00); // Decreases balance (Payment received)
                $table->decimal('balance', 15, 2)->default(0.00);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 9. Distributor Customer Ledgers (Distributor <-> Retailer Account Statement)
        if (!Schema::hasTable('distributor_customer_ledgers')) {
            Schema::create('distributor_customer_ledgers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('distributor_customer_id')->index();
                $table->unsignedBigInteger('distributor_id')->index();
                $table->date('date');
                $table->string('type', 30); // sale, payment_received, return, adjustment, opening
                $table->string('reference_no')->nullable();
                $table->text('description')->nullable();
                $table->decimal('debit', 15, 2)->default(0.00);
                $table->decimal('credit', 15, 2)->default(0.00);
                $table->decimal('balance', 15, 2)->default(0.00);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_customer_ledgers');
        Schema::dropIfExists('distributor_ledgers');
        Schema::dropIfExists('distributor_sale_items');
        Schema::dropIfExists('distributor_sales');
        Schema::dropIfExists('distributor_customers');
        Schema::dropIfExists('distributor_stocks');
        Schema::dropIfExists('distributor_order_items');
        Schema::dropIfExists('distributor_orders');
        Schema::dropIfExists('distributors');
    }
};
