<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'card_account_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->foreignId('card_account_id')->nullable()->after('card')->constrained('accounts')->nullOnDelete();
            });
        }

        if (Schema::hasTable('product_bookings') && !Schema::hasColumn('product_bookings', 'card_account_id')) {
            Schema::table('product_bookings', function (Blueprint $table) {
                $table->foreignId('card_account_id')->nullable()->after('card')->constrained('accounts')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'card_account_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropForeign(['card_account_id']);
                $table->dropColumn('card_account_id');
            });
        }

        if (Schema::hasTable('product_bookings') && Schema::hasColumn('product_bookings', 'card_account_id')) {
            Schema::table('product_bookings', function (Blueprint $table) {
                $table->dropForeign(['card_account_id']);
                $table->dropColumn('card_account_id');
            });
        }
    }
};
