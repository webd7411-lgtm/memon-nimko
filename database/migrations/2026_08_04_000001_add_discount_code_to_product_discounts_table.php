<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ensure discount_code column exists on product_discounts.
     * (Previously added in 2026_03_08_052346 but missing from some environments.)
     */
    public function up(): void
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('product_discounts', 'discount_code')) {
                $table->string('discount_code', 20)->nullable()->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_discounts', function (Blueprint $table) {
            if (Schema::hasColumn('product_discounts', 'discount_code')) {
                $table->dropColumn('discount_code');
            }
        });
    }
};
