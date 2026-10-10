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
        if (Schema::hasTable('sales')) {
            DB::statement("ALTER TABLE `sales` MODIFY `reference` TEXT NULL");
            DB::statement("ALTER TABLE `sales` MODIFY `brand` TEXT NULL");
            DB::statement("ALTER TABLE `sales` MODIFY `unit` TEXT NULL");
            DB::statement("ALTER TABLE `sales` MODIFY `color` TEXT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for down
    }
};
