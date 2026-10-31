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
        if (Schema::hasTable('distributor_sale_items')) {
            Schema::table('distributor_sale_items', function (Blueprint $table) {
                if (!Schema::hasColumn('distributor_sale_items', 'discount')) {
                    $table->decimal('discount', 15, 2)->default(0.00)->after('subtotal');
                }
                if (!Schema::hasColumn('distributor_sale_items', 'discount_type')) {
                    $table->string('discount_type', 20)->default('percent')->after('discount');
                }
                if (!Schema::hasColumn('distributor_sale_items', 'discount_val')) {
                    $table->decimal('discount_val', 15, 2)->default(0.00)->after('discount_type');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_sale_items')) {
            Schema::table('distributor_sale_items', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('distributor_sale_items', 'discount')) {
                    $columns[] = 'discount';
                }
                if (Schema::hasColumn('distributor_sale_items', 'discount_type')) {
                    $columns[] = 'discount_type';
                }
                if (Schema::hasColumn('distributor_sale_items', 'discount_val')) {
                    $columns[] = 'discount_val';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
