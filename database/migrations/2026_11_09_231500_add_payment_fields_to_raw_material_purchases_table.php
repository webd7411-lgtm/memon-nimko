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
        Schema::table('raw_material_purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('raw_material_purchases', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('total_cost');
            }
            if (!Schema::hasColumn('raw_material_purchases', 'due_amount')) {
                $table->decimal('due_amount', 15, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('raw_material_purchases', 'payment_method')) {
                $table->string('payment_method', 100)->nullable()->after('due_amount');
            }
            if (!Schema::hasColumn('raw_material_purchases', 'cash_account_id')) {
                $table->unsignedBigInteger('cash_account_id')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_material_purchases', function (Blueprint $table) {
            $cols = ['paid_amount', 'due_amount', 'payment_method', 'cash_account_id'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('raw_material_purchases', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
