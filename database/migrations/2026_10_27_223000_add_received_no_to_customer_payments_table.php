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
        if (Schema::hasTable('customer_payments') && !Schema::hasColumn('customer_payments', 'received_no')) {
            Schema::table('customer_payments', function (Blueprint $table) {
                $table->string('received_no')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('customer_payments') && Schema::hasColumn('customer_payments', 'received_no')) {
            Schema::table('customer_payments', function (Blueprint $table) {
                $table->dropColumn('received_no');
            });
        }
    }
};
