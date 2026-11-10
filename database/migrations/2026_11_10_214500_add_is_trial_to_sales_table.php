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
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'is_trial')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->boolean('is_trial')->default(0)->index()->after('sale_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'is_trial')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('is_trial');
            });
        }
    }
};
