<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\Sale;
use App\Models\DistributorPayment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('sales', 'is_account_credited')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->tinyInteger('is_account_credited')->default(0)->after('card_account_id');
            });
        }

        // Auto-sync existing sales that had card/bank payments into Accounts
        $sales = DB::table('sales')
            ->whereNotNull('card_account_id')
            ->where('card', '>', 0)
            ->where('is_account_credited', 0)
            ->get();

        foreach ($sales as $sale) {
            $account = Account::find($sale->card_account_id);
            if ($account) {
                $account->opening_balance = (float)$account->opening_balance + (float)$sale->card;
                $account->save();

                DB::table('sales')
                    ->where('id', $sale->id)
                    ->update(['is_account_credited' => 1]);
            }

            if (!empty($sale->invoice_no)) {
                DB::table('distributor_payments')
                    ->where('transaction_reference', $sale->invoice_no)
                    ->whereNull('account_id')
                    ->update(['account_id' => $sale->card_account_id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sales', 'is_account_credited')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('is_account_credited');
            });
        }
    }
};
