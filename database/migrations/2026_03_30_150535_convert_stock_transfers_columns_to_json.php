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
        // Drop foreign keys and indexes using raw SQL first
        $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_transfers' AND COLUMN_NAME = 'product_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE stock_transfers DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        $indexes = DB::select("SHOW INDEX FROM stock_transfers WHERE Column_name = 'product_id'");
        $droppedIndexes = [];
        foreach ($indexes as $idx) {
            if (!in_array($idx->Key_name, $droppedIndexes) && $idx->Key_name !== 'PRIMARY') {
                try {
                    DB::statement("ALTER TABLE stock_transfers DROP INDEX `{$idx->Key_name}`");
                    $droppedIndexes[] = $idx->Key_name;
                } catch (\Exception $e) {}
            }
        }

        // Now safely change column types
        DB::statement("ALTER TABLE stock_transfers MODIFY product_id LONGTEXT NULL");
        DB::statement("ALTER TABLE stock_transfers MODIFY quantity LONGTEXT NULL");
    }

    private function getForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        return array_keys($conn->listTableForeignKeys($table));
    }

    private function getIndexes($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        return array_keys($conn->listTableIndexes($table));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('quantity')->change();
        });
    }
};
