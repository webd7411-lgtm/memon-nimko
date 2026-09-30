<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('customer_payments')) {
            Schema::table('customer_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('customer_payments', 'cash')) {
                    $table->decimal('cash', 12, 2)->default(0)->after('amount');
                }
                if (!Schema::hasColumn('customer_payments', 'card')) {
                    $table->decimal('card', 12, 2)->default(0)->after('cash');
                }
                if (!Schema::hasColumn('customer_payments', 'card_account_id')) {
                    $table->unsignedBigInteger('card_account_id')->nullable()->after('card');
                    $table->foreign('card_account_id')->references('id')->on('accounts')->onDelete('set null');
                }
            });
        }

        if (Schema::hasTable('vendor_payments')) {
            Schema::table('vendor_payments', function (Blueprint $table) {
                if (!Schema::hasColumn('vendor_payments', 'cash')) {
                    $table->decimal('cash', 12, 2)->default(0)->after('amount');
                }
                if (!Schema::hasColumn('vendor_payments', 'card')) {
                    $table->decimal('card', 12, 2)->default(0)->after('cash');
                }
                if (!Schema::hasColumn('vendor_payments', 'card_account_id')) {
                    $table->unsignedBigInteger('card_account_id')->nullable()->after('card');
                    $table->foreign('card_account_id')->references('id')->on('accounts')->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customer_payments')) {
            Schema::table('customer_payments', function (Blueprint $table) {
                if (Schema::hasColumn('customer_payments', 'card_account_id')) {
                    $table->dropForeign(['card_account_id']);
                    $table->dropColumn('card_account_id');
                }
                if (Schema::hasColumn('customer_payments', 'card')) {
                    $table->dropColumn('card');
                }
                if (Schema::hasColumn('customer_payments', 'cash')) {
                    $table->dropColumn('cash');
                }
            });
        }

        if (Schema::hasTable('vendor_payments')) {
            Schema::table('vendor_payments', function (Blueprint $table) {
                if (Schema::hasColumn('vendor_payments', 'card_account_id')) {
                    $table->dropForeign(['card_account_id']);
                    $table->dropColumn('card_account_id');
                }
                if (Schema::hasColumn('vendor_payments', 'card')) {
                    $table->dropColumn('card');
                }
                if (Schema::hasColumn('vendor_payments', 'cash')) {
                    $table->dropColumn('cash');
                }
            });
        }
    }
};
