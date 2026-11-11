<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('account_ledgers') || !Schema::hasTable('accounts')) {
            return;
        }

        $accounts = DB::table('accounts')->orderBy('id')->get();

        foreach ($accounts as $acc) {
            if (DB::table('account_ledgers')->where('account_id', $acc->id)->exists()) {
                continue;
            }

            $events = [];

            // 1. Sales (Cash & Card)
            if (Schema::hasColumn('sales', 'cash_account_id')) {
                $cashSales = DB::table('sales')
                    ->where('cash_account_id', $acc->id)
                    ->where('cash', '>', 0)
                    ->get();
                foreach ($cashSales as $s) {
                    $netCash = round(max(0, (float) $s->cash - max(0, (float) ($s->change ?? 0))), 2);
                    if ($netCash > 0) {
                        $events[] = [
                            'transaction_date' => $s->created_at ?? now(),
                            'branch_id'        => $s->branch_id ?? null,
                            'reference_type'   => 'Sale',
                            'reference_no'     => $s->invoice_no,
                            'reference_id'     => $s->id,
                            'description'      => 'Cash received on Sale #' . $s->invoice_no,
                            'delta'            => $netCash,
                            'created_by'       => $s->user_id ?? null,
                        ];
                    }
                }
            }

            if (Schema::hasColumn('sales', 'card_account_id')) {
                $cardSales = DB::table('sales')
                    ->where('card_account_id', $acc->id)
                    ->where('card', '>', 0)
                    ->get();
                foreach ($cardSales as $s) {
                    $cardAmt = round((float) $s->card, 2);
                    if ($cardAmt > 0) {
                        $events[] = [
                            'transaction_date' => $s->created_at ?? now(),
                            'branch_id'        => $s->branch_id ?? null,
                            'reference_type'   => 'Sale (Bank/Card)',
                            'reference_no'     => $s->invoice_no,
                            'reference_id'     => $s->id,
                            'description'      => 'Card / Bank received on Sale #' . $s->invoice_no,
                            'delta'            => $cardAmt,
                            'created_by'       => $s->user_id ?? null,
                        ];
                    }
                }
            }

            // 2. Sales Returns
            if (Schema::hasColumn('sales_returns', 'cash_account_id')) {
                $returns = DB::table('sales_returns')
                    ->where('cash_account_id', $acc->id)
                    ->where('cash', '>', 0)
                    ->get();
                foreach ($returns as $r) {
                    $retCash = round((float) $r->cash, 2);
                    if ($retCash > 0) {
                        $events[] = [
                            'transaction_date' => $r->created_at ?? now(),
                            'branch_id'        => $r->branch_id ?? null,
                            'reference_type'   => 'Sale Return',
                            'reference_no'     => 'RET-' . $r->id,
                            'reference_id'     => $r->id,
                            'description'      => 'Refund paid on Sale Return #' . $r->id,
                            'delta'            => -$retCash,
                            'created_by'       => $r->user_id ?? null,
                        ];
                    }
                }
            }

            // 3. Customer Payments (Card/Bank excluding Paid on Sale & Receipt Vouchers)
            if (Schema::hasColumn('customer_payments', 'card_account_id')) {
                $custPays = DB::table('customer_payments')
                    ->where('card_account_id', $acc->id)
                    ->where('card', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('note')->orWhere('note', 'not like', '%Paid on Sale%');
                    })
                    ->get();
                foreach ($custPays as $cp) {
                    $amt = round((float) $cp->card, 2);
                    if ($amt > 0) {
                        $events[] = [
                            'transaction_date' => $cp->created_at ?? ($cp->payment_date . ' 00:00:00'),
                            'branch_id'        => $cp->branch_id ?? null,
                            'reference_type'   => 'Customer Recovery',
                            'reference_no'     => $cp->received_no,
                            'reference_id'     => $cp->id,
                            'description'      => 'Customer recovery (' . ($cp->payment_method ?? 'Card') . ')',
                            'delta'            => $amt,
                            'created_by'       => $cp->admin_or_user_id ?? null,
                        ];
                    }
                }
            }

            // 4. Vendor Payments (Card/Bank excluding Paid on Purchase & Payment Vouchers)
            if (Schema::hasColumn('vendor_payments', 'card_account_id')) {
                $vendPays = DB::table('vendor_payments')
                    ->where('card_account_id', $acc->id)
                    ->where('card', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('note')
                          ->orWhere(function ($s) {
                              $s->where('note', 'not like', '%Paid on Purchase%')
                                ->where('note', 'not like', 'Payment Voucher #%');
                          });
                    })
                    ->get();
                foreach ($vendPays as $vp) {
                    $amt = round((float) $vp->card, 2);
                    if ($amt > 0) {
                        $events[] = [
                            'transaction_date' => $vp->created_at ?? ($vp->payment_date . ' 00:00:00'),
                            'branch_id'        => $vp->branch_id ?? null,
                            'reference_type'   => 'Vendor Payment',
                            'reference_no'     => $vp->payment_no,
                            'reference_id'     => $vp->id,
                            'description'      => 'Vendor payment (' . ($vp->payment_method ?? 'Card') . ')',
                            'delta'            => -$amt,
                            'created_by'       => $vp->admin_or_user_id ?? null,
                        ];
                    }
                }
            }

            // 5. Distributor Payments (excluding Payment on Sale Invoice & Receipt Voucher)
            if (Schema::hasTable('distributor_payments')) {
                $distPays = DB::table('distributor_payments')
                    ->where('account_id', $acc->id)
                    ->where('status', 'approved')
                    ->where('payment_method', '!=', 'receipt_voucher')
                    ->where(function ($q) {
                        $q->whereNull('notes')->orWhere('notes', 'not like', '%Payment on Sale Invoice%');
                    })
                    ->get();
                foreach ($distPays as $dp) {
                    $amt = round((float) $dp->amount, 2);
                    if ($amt > 0) {
                        $events[] = [
                            'transaction_date' => $dp->created_at ?? ($dp->payment_date . ' 00:00:00'),
                            'branch_id'        => null,
                            'reference_type'   => 'Distributor Payment',
                            'reference_no'     => $dp->payment_no,
                            'reference_id'     => $dp->id,
                            'description'      => $dp->notes ?: 'Distributor payment #' . $dp->payment_no,
                            'delta'            => $amt,
                            'created_by'       => $dp->created_by ?? null,
                        ];
                    }
                }
            }

            // 6. Payment Vouchers
            foreach (DB::table('payment_vouchers')->get() as $pv) {
                $rowAccs = json_decode($pv->row_account_id ?? '[]', true);
                $rowAmts = json_decode($pv->amount ?? '[]', true);
                if (is_array($rowAccs)) {
                    foreach ($rowAccs as $idx => $aId) {
                        if ((int) $aId === (int) $acc->id) {
                            $amt = round((float) ($rowAmts[$idx] ?? 0), 2);
                            if ($amt > 0) {
                                $events[] = [
                                    'transaction_date' => $pv->created_at ?? ($pv->receipt_date . ' 00:00:00'),
                                    'branch_id'        => $pv->branch_id ?? null,
                                    'reference_type'   => 'Payment Voucher',
                                    'reference_no'     => $pv->pvid,
                                    'reference_id'     => $pv->id,
                                    'description'      => 'Payment Voucher #' . $pv->pvid . ' (' . ucfirst((string) $pv->type) . ')',
                                    'delta'            => -$amt,
                                    'created_by'       => null,
                                ];
                            }
                        }
                    }
                }
                if (is_numeric($pv->type) && (int) $pv->party_id === (int) $acc->id) {
                    $amt = round((float) $pv->total_amount, 2);
                    if ($amt > 0) {
                        $events[] = [
                            'transaction_date' => $pv->created_at ?? ($pv->receipt_date . ' 00:00:00'),
                            'branch_id'        => $pv->branch_id ?? null,
                            'reference_type'   => 'Payment Voucher',
                            'reference_no'     => $pv->pvid,
                            'reference_id'     => $pv->id,
                            'description'      => 'Transfer / Receipt via Payment Voucher #' . $pv->pvid,
                            'delta'            => $amt,
                            'created_by'       => null,
                        ];
                    }
                }
            }

            // 7. Receipt Vouchers
            foreach (DB::table('receipts_vouchers')->get() as $rv) {
                $rowAccs = json_decode($rv->row_account_id ?? '[]', true);
                $rowAmts = json_decode($rv->amount ?? '[]', true);
                if (is_array($rowAccs)) {
                    foreach ($rowAccs as $idx => $aId) {
                        if ((int) $aId === (int) $acc->id) {
                            $amt = round((float) ($rowAmts[$idx] ?? 0), 2);
                            if ($amt > 0) {
                                $events[] = [
                                    'transaction_date' => $rv->created_at ?? ($rv->receipt_date . ' 00:00:00'),
                                    'branch_id'        => $rv->branch_id ?? null,
                                    'reference_type'   => 'Receipt Voucher',
                                    'reference_no'     => $rv->rvid,
                                    'reference_id'     => $rv->id,
                                    'description'      => 'Receipt Voucher #' . $rv->rvid . ' (' . ucfirst((string) $rv->type) . ')',
                                    'delta'            => $amt,
                                    'created_by'       => null,
                                ];
                            }
                        }
                    }
                }
                if (is_numeric($rv->type) && (int) $rv->party_id === (int) $acc->id) {
                    $amt = round((float) $rv->total_amount, 2);
                    if ($amt > 0) {
                        $events[] = [
                            'transaction_date' => $rv->created_at ?? ($rv->receipt_date . ' 00:00:00'),
                            'branch_id'        => $rv->branch_id ?? null,
                            'reference_type'   => 'Receipt Voucher',
                            'reference_no'     => $rv->rvid,
                            'reference_id'     => $rv->id,
                            'description'      => 'Transfer / Deduction via Receipt Voucher #' . $rv->rvid,
                            'delta'            => -$amt,
                            'created_by'       => null,
                        ];
                    }
                }
            }

            // 8. Expense Vouchers (paying account deductions, asset maintenances, and claim/expense tracking)
            foreach (DB::table('expense_vouchers')->get() as $ev) {
                $remArr = json_decode($ev->remarks ?? '[]', true);
                $remStr = is_array($remArr) ? implode(', ', $remArr) : (string) ($ev->remarks ?? '');

                $rowAccs = json_decode($ev->row_account_id ?? '[]', true);
                $rowAmts = json_decode($ev->amount ?? '[]', true);
                if (is_array($rowAccs) && count($rowAccs) > 0) {
                    foreach ($rowAccs as $idx => $aId) {
                        if ((int) $aId === (int) $acc->id) {
                            $amt = round((float) ($rowAmts[$idx] ?? 0), 2);
                            if ($amt > 0) {
                                $events[] = [
                                    'transaction_date' => $ev->created_at ?? ($ev->date . ' 00:00:00'),
                                    'branch_id'        => $ev->branch_id ?? null,
                                    'reference_type'   => 'Expense Voucher',
                                    'reference_no'     => $ev->evid,
                                    'reference_id'     => $ev->id,
                                    'description'      => 'Expense #' . $ev->evid . ($remStr ? ': ' . $remStr : ''),
                                    'delta'            => -$amt,
                                    'created_by'       => $ev->user_id ?? null,
                                ];
                            }
                        }
                    }
                } elseif ((int) $ev->party_id === (int) $acc->id && str_contains($remStr, 'Asset Maintenance:')) {
                    $amt = round((float) $ev->total_amount, 2);
                    if ($amt > 0) {
                        $events[] = [
                            'transaction_date' => $ev->created_at ?? ($ev->date . ' 00:00:00'),
                            'branch_id'        => $ev->branch_id ?? null,
                            'reference_type'   => 'Asset Maintenance',
                            'reference_no'     => $ev->evid,
                            'reference_id'     => $ev->id,
                            'description'      => $remStr,
                            'delta'            => -$amt,
                            'created_by'       => $ev->user_id ?? null,
                        ];
                    }
                }

                // Expense-head tracking account (e.g., ACC-CLAIM-TRADE-01 under head_id = 4, excluding Cash/Bank heads 1,2)
                if (!in_array((int) $acc->head_id, [1, 2], true) && (int) $acc->head_id === (int) $ev->type && (int) $ev->party_id === (int) $acc->id && !str_contains($remStr, 'Asset Maintenance:')) {
                    $amt = round((float) $ev->total_amount, 2);
                    if ($amt > 0) {
                        $events[] = [
                            'transaction_date' => $ev->created_at ?? ($ev->date . ' 00:00:00'),
                            'branch_id'        => $ev->branch_id ?? null,
                            'reference_type'   => 'Expense / Claim Tracked',
                            'reference_no'     => $ev->evid,
                            'reference_id'     => $ev->id,
                            'description'      => $remStr ?: ('Expense #' . $ev->evid),
                            'delta'            => $amt,
                            'created_by'       => $ev->user_id ?? null,
                        ];
                    }
                }
            }

            // Sort chronologically
            usort($events, function ($a, $b) {
                return strcmp((string) $a['transaction_date'], (string) $b['transaction_date']);
            });

            $netEvents = array_sum(array_column($events, 'delta'));
            $currentBalance = round((float) ($acc->opening_balance ?? 0), 2);
            $initialBalance = round($currentBalance - $netEvents, 2);

            $running = 0.0;
            $now = now();

            $openingDate = $acc->created_at ?? $now;
            if (!empty($events)) {
                $firstEventTs = strtotime((string) $events[0]['transaction_date']);
                if ($firstEventTs !== false && strtotime((string) $openingDate) >= $firstEventTs) {
                    $openingDate = date('Y-m-d H:i:s', $firstEventTs - 1);
                }
            }

            if (abs($initialBalance) >= 0.005 || (count($events) === 0 && abs($currentBalance) >= 0.005)) {
                $initAmt = $initialBalance;
                DB::table('account_ledgers')->insert([
                    'account_id'       => $acc->id,
                    'branch_id'        => null,
                    'transaction_date' => $openingDate,
                    'reference_type'   => 'Account Opening',
                    'reference_no'     => $acc->account_code,
                    'reference_id'     => $acc->id,
                    'description'      => 'Opening / Prior Balance',
                    'previous_balance' => 0,
                    'credit'           => $initAmt > 0 ? $initAmt : 0,
                    'debit'            => $initAmt < 0 ? abs($initAmt) : 0,
                    'balance'          => $initAmt,
                    'created_by'       => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
                $running = $initAmt;
            }

            foreach ($events as $ev) {
                $prev = round($running, 2);
                $delta = round((float) $ev['delta'], 2);
                $running = round($prev + $delta, 2);

                DB::table('account_ledgers')->insert([
                    'account_id'       => $acc->id,
                    'branch_id'        => $ev['branch_id'],
                    'transaction_date' => $ev['transaction_date'],
                    'reference_type'   => $ev['reference_type'],
                    'reference_no'     => $ev['reference_no'],
                    'reference_id'     => $ev['reference_id'],
                    'description'      => $ev['description'],
                    'previous_balance' => $prev,
                    'credit'           => $delta > 0 ? $delta : 0,
                    'debit'            => $delta < 0 ? abs($delta) : 0,
                    'balance'          => $running,
                    'created_by'       => $ev['created_by'],
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Do not drop or truncate user-generated ledger records automatically.
    }
};
