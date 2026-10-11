<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('production_entries') && !Schema::hasColumn('production_entries', 'warehouse_id')) {
            Schema::table('production_entries', function (Blueprint $table) {
                $table->foreignId('warehouse_id')
                    ->nullable()
                    ->after('source')
                    ->constrained('warehouses')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('production_entries') && Schema::hasColumn('production_entries', 'warehouse_id')) {
            Schema::table('production_entries', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            });
        }
    }
};
