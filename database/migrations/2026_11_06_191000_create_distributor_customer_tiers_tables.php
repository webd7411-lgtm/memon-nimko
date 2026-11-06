<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create distributor_customer_tiers table
        if (!Schema::hasTable('distributor_customer_tiers')) {
            Schema::create('distributor_customer_tiers', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->decimal('discount_percentage', 5, 2)->default(0.00);
                $table->decimal('trade_offer_buy_qty', 10, 2)->default(0.00);
                $table->decimal('trade_offer_free_qty', 10, 2)->default(0.00);
                $table->string('status', 20)->default('active');
                $table->text('description')->nullable();
                $table->timestamps();
            });

            // Seed standard initial tiers
            DB::table('distributor_customer_tiers')->insert([
                [
                    'name'                => 'Retailer (Standard)',
                    'discount_percentage' => 0.00,
                    'trade_offer_buy_qty' => 0.00,
                    'trade_offer_free_qty'=> 0.00,
                    'status'              => 'active',
                    'description'         => 'Standard retail pricing with 0% discount and standard rate.',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ],
                [
                    'name'                => 'Wholesale (Bulk Buyer)',
                    'discount_percentage' => 5.00,
                    'trade_offer_buy_qty' => 12.00,
                    'trade_offer_free_qty'=> 1.00,
                    'status'              => 'active',
                    'description'         => 'Wholesale tier: 5% flat discount and Buy 12 get 1 Free Scheme.',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ],
                [
                    'name'                => 'Super Store / Mart',
                    'discount_percentage' => 3.00,
                    'trade_offer_buy_qty' => 24.00,
                    'trade_offer_free_qty'=> 2.00,
                    'status'              => 'active',
                    'description'         => 'Super store / Mart: 3% discount and Buy 24 get 2 Free Scheme.',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ],
                [
                    'name'                => 'Key Account / VIP',
                    'discount_percentage' => 4.00,
                    'trade_offer_buy_qty' => 12.00,
                    'trade_offer_free_qty'=> 1.00,
                    'status'              => 'active',
                    'description'         => 'Key Account: 4% discount and Buy 12 get 1 Free Scheme.',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ],
            ]);
        }

        // 2. Add tier and trade offer columns to distributor_customers table
        if (Schema::hasTable('distributor_customers')) {
            Schema::table('distributor_customers', function (Blueprint $table) {
                if (!Schema::hasColumn('distributor_customers', 'tier_id')) {
                    $table->unsignedBigInteger('tier_id')->nullable()->after('booker_user_id')->index();
                }
                if (!Schema::hasColumn('distributor_customers', 'trade_offer_buy_qty')) {
                    $table->decimal('trade_offer_buy_qty', 10, 2)->default(0.00)->after('approved_discount');
                }
                if (!Schema::hasColumn('distributor_customers', 'trade_offer_free_qty')) {
                    $table->decimal('trade_offer_free_qty', 10, 2)->default(0.00)->after('trade_offer_buy_qty');
                }
            });

            // Set existing customers to default Retailer tier if tier_id is null
            $retailTier = DB::table('distributor_customer_tiers')->where('name', 'like', '%Retailer%')->first();
            if ($retailTier) {
                DB::table('distributor_customers')->whereNull('tier_id')->update([
                    'tier_id' => $retailTier->id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_customers')) {
            Schema::table('distributor_customers', function (Blueprint $table) {
                if (Schema::hasColumn('distributor_customers', 'trade_offer_free_qty')) {
                    $table->dropColumn('trade_offer_free_qty');
                }
                if (Schema::hasColumn('distributor_customers', 'trade_offer_buy_qty')) {
                    $table->dropColumn('trade_offer_buy_qty');
                }
                if (Schema::hasColumn('distributor_customers', 'tier_id')) {
                    $table->dropColumn('tier_id');
                }
            });
        }

        if (Schema::hasTable('distributor_customer_tiers')) {
            Schema::dropIfExists('distributor_customer_tiers');
        }
    }
};
