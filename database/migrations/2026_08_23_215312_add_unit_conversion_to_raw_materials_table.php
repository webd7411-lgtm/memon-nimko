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
        Schema::table('raw_materials', function (Blueprint $table) {
            if (!Schema::hasColumn('raw_materials', 'consumption_unit')) {
                $table->string('consumption_unit', 50)->nullable()->after('unit');
            }
            if (!Schema::hasColumn('raw_materials', 'conversion_factor')) {
                $table->decimal('conversion_factor', 12, 4)->default(1)->after('consumption_unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            if (Schema::hasColumn('raw_materials', 'consumption_unit')) {
                $table->dropColumn('consumption_unit');
            }
            if (Schema::hasColumn('raw_materials', 'conversion_factor')) {
                $table->dropColumn('conversion_factor');
            }
        });
    }
};
