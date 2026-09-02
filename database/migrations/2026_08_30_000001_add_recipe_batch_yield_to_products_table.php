<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'recipe_batch_yield')) {
                $table->decimal('recipe_batch_yield', 12, 4)->default(1)->after('unit_type')->comment('Batch yield count for recipe BOM calculation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'recipe_batch_yield')) {
                $table->dropColumn('recipe_batch_yield');
            }
        });
    }
};
