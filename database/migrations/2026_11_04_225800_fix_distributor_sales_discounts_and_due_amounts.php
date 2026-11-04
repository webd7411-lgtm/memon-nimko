<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\DistributorSale;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sales = DistributorSale::with('items')->get();
        foreach ($sales as $sale) {
            if (!$sale->items || $sale->items->count() === 0) continue;

            $computedGross = 0;
            $computedDisc = 0;
            foreach ($sale->items as $it) {
                $computedGross += ((float)$it->qty * (float)$it->unit_price);
                $computedDisc += (float)($it->discount ?? 0);
            }

            if ($computedDisc <= 0) continue;

            $trueNet = max(0, $computedGross - $computedDisc);

            $needsFix = false;
            if (abs((float)$sale->grand_total - $computedGross) < 0.05) {
                $needsFix = true;
            } elseif (abs((float)$sale->discount - (2 * $computedDisc)) < 0.05) {
                $needsFix = true;
            }

            if ($needsFix) {
                $newDue = max(0, $trueNet - (float)$sale->paid_amount);

                // Update sale record
                DB::table('distributor_sales')->where('id', $sale->id)->update([
                    'subtotal'    => $computedGross,
                    'discount'    => $computedDisc,
                    'grand_total' => $trueNet,
                    'due_amount'  => $newDue,
                ]);

                // Update ledger debit entry
                DB::table('distributor_customer_ledgers')
                    ->where('reference_no', $sale->invoice_no)
                    ->where('type', 'sale')
                    ->update([
                        'debit' => $trueNet,
                    ]);

                // Recalculate customer balance
                $custBalance = DB::table('distributor_customer_ledgers')
                    ->where('distributor_customer_id', $sale->distributor_customer_id)
                    ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as bal')
                    ->value('bal');

                if ($custBalance !== null) {
                    DB::table('distributor_customers')
                        ->where('id', $sale->distributor_customer_id)
                        ->update(['current_balance' => (float)$custBalance]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
