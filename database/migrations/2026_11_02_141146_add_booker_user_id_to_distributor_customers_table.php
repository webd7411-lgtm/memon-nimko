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
        if (Schema::hasTable('distributor_customers') && !Schema::hasColumn('distributor_customers', 'booker_user_id')) {
            Schema::table('distributor_customers', function (Blueprint $table) {
                $table->unsignedBigInteger('booker_user_id')->nullable()->after('distributor_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributor_customers') && Schema::hasColumn('distributor_customers', 'booker_user_id')) {
            Schema::table('distributor_customers', function (Blueprint $table) {
                $table->dropColumn('booker_user_id');
            });
        }
    }
};
