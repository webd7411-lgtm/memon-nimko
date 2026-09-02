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
        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'to_branch_id')) {
                $table->unsignedBigInteger('to_branch_id')->nullable()->after('to_warehouse_id');
            }
            if (!Schema::hasColumn('stock_transfers', 'status')) {
                $table->string('status')->default('completed')->after('admin_notified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transfers', 'to_branch_id')) {
                $table->dropColumn('to_branch_id');
            }
            if (Schema::hasColumn('stock_transfers', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
