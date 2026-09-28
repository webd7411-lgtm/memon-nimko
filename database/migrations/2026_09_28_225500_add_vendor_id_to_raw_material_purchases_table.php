<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('raw_material_purchases') && !Schema::hasColumn('raw_material_purchases', 'vendor_id')) {
            Schema::table('raw_material_purchases', function (Blueprint $table) {
                $table->foreignId('vendor_id')->nullable()->after('invoice_no')->constrained('vendors')->nullOnDelete();
            });
        }

        // Map existing purchases to vendor_id by vendor_name
        DB::statement("UPDATE raw_material_purchases r JOIN vendors v ON r.vendor_name = v.name SET r.vendor_id = v.id WHERE r.vendor_id IS NULL");

        // Update Usman Bhai's ledger closing balance for RMP-20260928-221811
        $usman = DB::table('vendors')->where('name', 'USMAN BHAI')->first();
        if ($usman) {
            $ledger = DB::table('vendor_ledgers')->where('vendor_id', $usman->id)->first();
            if ($ledger) {
                DB::table('vendor_ledgers')->where('id', $ledger->id)->update([
                    'previous_balance' => $ledger->closing_balance,
                    'closing_balance' => $ledger->closing_balance + 4000,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('raw_material_purchases') && Schema::hasColumn('raw_material_purchases', 'vendor_id')) {
            Schema::table('raw_material_purchases', function (Blueprint $table) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            });
        }
    }
};
