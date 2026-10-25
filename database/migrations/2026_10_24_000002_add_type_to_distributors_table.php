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
        if (Schema::hasTable('distributors') && !Schema::hasColumn('distributors', 'type')) {
            Schema::table('distributors', function (Blueprint $table) {
                $table->string('type', 30)->default('distributor')->after('code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('distributors') && Schema::hasColumn('distributors', 'type')) {
            Schema::table('distributors', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
