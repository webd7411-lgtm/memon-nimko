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
        // Ensure at least one default branch exists
        if (Schema::hasTable('branches')) {
            $branchCount = DB::table('branches')->count();
            if ($branchCount === 0) {
                // Find first user or set user_id to 1
                $firstUserId = DB::table('users')->value('id') ?? 1;
                DB::table('branches')->insert([
                    'id' => 1,
                    'name' => 'Main Branch',
                    'address' => 'Head Office',
                    'number' => '00000000000',
                    'user_id' => $firstUserId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $defaultBranchId = DB::table('branches')->value('id') ?? 1;

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'branch_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null')->after('id');
            });
        }

        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'branch_id')) {
            Schema::table('sales', function (Blueprint $table) use ($defaultBranchId) {
                $table->foreignId('branch_id')->nullable()->default($defaultBranchId)->constrained('branches')->onDelete('set null')->after('id');
            });
        }

        if (Schema::hasTable('expense_vouchers') && !Schema::hasColumn('expense_vouchers', 'branch_id')) {
            Schema::table('expense_vouchers', function (Blueprint $table) use ($defaultBranchId) {
                $table->foreignId('branch_id')->nullable()->default($defaultBranchId)->constrained('branches')->onDelete('set null')->after('id');
            });
        }

        if (Schema::hasTable('customer_payments') && !Schema::hasColumn('customer_payments', 'branch_id')) {
            Schema::table('customer_payments', function (Blueprint $table) use ($defaultBranchId) {
                $table->foreignId('branch_id')->nullable()->default($defaultBranchId)->constrained('branches')->onDelete('set null')->after('id');
            });
        }

        if (Schema::hasTable('production_entries') && !Schema::hasColumn('production_entries', 'branch_id')) {
            Schema::table('production_entries', function (Blueprint $table) use ($defaultBranchId) {
                $table->foreignId('branch_id')->nullable()->default($defaultBranchId)->constrained('branches')->onDelete('set null')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'branch_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'branch_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('expense_vouchers') && Schema::hasColumn('expense_vouchers', 'branch_id')) {
            Schema::table('expense_vouchers', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('customer_payments') && Schema::hasColumn('customer_payments', 'branch_id')) {
            Schema::table('customer_payments', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
