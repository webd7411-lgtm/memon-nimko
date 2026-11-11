<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHead;
use App\Models\AccountLedger;
use App\Models\ExpenseVoucher;
use Illuminate\Http\Request;

class AccountsHeadController extends Controller
{
    public function index()
    {
        $accounts = Account::whereNull('distributor_id')->with('head')->get();
        $heads = AccountHead::all();
        return view('admin_panel.chart_of_accounts', compact('accounts', 'heads'));
    }

    /**
     * Account Ledger / Statement Page with chronological running balance.
     */
    public function ledger(Request $request, $id = null)
    {
        $accounts = Account::whereNull('distributor_id')
            ->with('head')
            ->orderByRaw('CASE WHEN head_id IN (1, 2) THEN 0 ELSE 1 END')
            ->orderBy('title')
            ->get();

        $targetId = $id ?? $request->get('account_id');
        if ($targetId) {
            $account = Account::whereNull('distributor_id')->with('head')->findOrFail($targetId);
        } else {
            $account = $accounts->firstWhere('account_code', 'ACC-CASH-01') ?? $accounts->first();
        }

        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $flow      = $request->get('flow', 'all');
        $search    = trim((string) $request->get('search', ''));

        $entries = collect();
        $periodOpeningBalance = 0.0;
        $totalIn = 0.0;
        $totalOut = 0.0;
        $periodClosingBalance = $account ? (float) ($account->opening_balance ?? 0) : 0.0;

        if ($account) {
            if ($startDate) {
                $lastBefore = AccountLedger::where('account_id', $account->id)
                    ->where('transaction_date', '<', $startDate . ' 00:00:00')
                    ->orderBy('transaction_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                $periodOpeningBalance = $lastBefore ? (float) $lastBefore->balance : 0.0;
            }

            $baseQ = AccountLedger::with('user')
                ->where('account_id', $account->id);

            if ($startDate) {
                $baseQ->where('transaction_date', '>=', $startDate . ' 00:00:00');
            }
            if ($endDate) {
                $baseQ->where('transaction_date', '<=', $endDate . ' 23:59:59');
            }

            $totalIn  = (float) (clone $baseQ)->sum('credit');
            $totalOut = (float) (clone $baseQ)->sum('debit');
            $periodClosingBalance = round($periodOpeningBalance + $totalIn - $totalOut, 2);

            if ($flow === 'in') {
                $baseQ->where('credit', '>', 0);
            } elseif ($flow === 'out') {
                $baseQ->where('debit', '>', 0);
            }

            if ($search !== '') {
                $baseQ->where(function ($q) use ($search) {
                    $q->where('reference_type', 'like', "%{$search}%")
                      ->orWhere('reference_no', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $entries = $baseQ->orderBy('transaction_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        }

        return view('admin_panel.account_ledger', compact(
            'accounts',
            'account',
            'entries',
            'startDate',
            'endDate',
            'flow',
            'search',
            'periodOpeningBalance',
            'totalIn',
            'totalOut',
            'periodClosingBalance'
        ));
    }

    public function storeHead(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $head = AccountHead::create([
            'name' => $request->name,
            'type' => $request->type ?? 'Expense',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense Head added successfully.',
                'head'    => $head,
            ]);
        }

        return redirect()->back()->with('success', 'Head added successfully.');
    }

    public function updateHead(Request $request, $id)
    {
        $head = AccountHead::findOrFail($id);
        $request->validate([
            'name'   => 'required|string|max:100',
            'status' => 'nullable|in:0,1',
        ]);

        $head->name = $request->name;
        if ($request->has('status')) {
            $head->status = $request->status;
        }
        $head->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense Head updated successfully.',
                'head'    => $head,
            ]);
        }

        return redirect()->back()->with('success', 'Expense Head updated successfully.');
    }

    public function destroyHead(Request $request, $id)
    {
        $head = AccountHead::findOrFail($id);

        // Core system heads check
        if (in_array(strtolower(trim($head->name)), ['bank accounts', 'cash in hand', 'bank', 'cash'])) {
            $msg = 'Core account heads cannot be deleted.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Check usage in expense vouchers or accounts
        $usedInVouchers = ExpenseVoucher::where('type', $head->id)->exists();
        $usedInAccounts = Account::where('head_id', $head->id)->exists();

        if ($usedInVouchers || $usedInAccounts) {
            $details = [];
            if ($usedInVouchers) $details[] = 'expense vouchers';
            if ($usedInAccounts) $details[] = 'chart of accounts';
            $msg = 'Cannot delete Expense Head because it is currently linked to ' . implode(' and ', $details) . '. Please delete or reassign them first.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $head->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense Head deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Expense Head deleted successfully.');
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'head_id' => 'required|exists:account_heads,id',
            'account_code' => 'required',
            'title' => 'required|string|max:150',
            'opening_balance' => 'nullable|numeric',
        ]);

        $status = $request->status === 'on' ? 1 : 0;

        // 🔥 UPDATE
        if ($request->account_id) {
            $account = Account::findOrFail($request->account_id);

            $account->setLedgerMeta([
                'reference_type' => 'Manual Balance Edit',
                'reference_no'   => $request->account_code,
                'reference_id'   => $account->id,
                'description'    => 'Balance manually updated from Chart of Accounts (' . $request->title . ')',
            ]);
            $account->update([
                'head_id'         => $request->head_id,
                'account_code'    => $request->account_code,
                'title'           => $request->title,
                'opening_balance' => $request->opening_balance ?? 0,
                'status'          => $status,
            ]);

            return redirect()->back()->with('success', 'Account updated successfully.');
        }

        // 🔥 CREATE
        Account::create([
            'distributor_id'  => null,
            'head_id'         => $request->head_id,
            'account_code'    => $request->account_code,
            'title'           => $request->title,
            'opening_balance' => $request->opening_balance ?? 0,
            'status'          => $status,
        ]);

        return redirect()->back()->with('success', 'Account added successfully.');
    }

    public function destroy($id)
    {
        $account = Account::findOrFail($id);

        // 🔒 Check: account used in expense vouchers
        $usedInExpense = ExpenseVoucher::where('party_id', $account->id)
            ->orWhereJsonContains('row_account_id', (string) $account->id)
            ->exists();

        if ($usedInExpense) {
            return redirect()->back()->with(
                'error',
                'You cannot delete this account because expenses have already been recorded against it.'
            );
        }

        $account->delete();

        return redirect()->back()->with('success', 'Account deleted successfully.');
    }

    /**
     * Re-sync / credit any card/bank sales payments into their accounts.
     */
    public function syncSalesPayments()
    {
        $sales = \App\Models\Sale::whereNotNull('card_account_id')
            ->where('card', '>', 0)
            ->where(function($q) {
                $q->whereNull('is_account_credited')->orWhere('is_account_credited', 0);
            })
            ->get();

        $count = 0;
        $totalAmount = 0;

        foreach ($sales as $sale) {
            $account = Account::find($sale->card_account_id);
            if ($account) {
                $amount = (float) $sale->card;
                $account->setLedgerMeta([
                    'branch_id'        => $sale->branch_id ?? null,
                    'transaction_date' => $sale->created_at ?? now(),
                    'reference_type'   => 'Sale Payment Sync',
                    'reference_no'     => $sale->invoice_no,
                    'reference_id'     => $sale->id,
                    'description'      => 'Synced Card / Bank payment for Sale #' . $sale->invoice_no,
                ]);
                $account->opening_balance = (float) $account->opening_balance + $amount;
                $account->save();

                $sale->is_account_credited = 1;
                $sale->save();

                $count++;
                $totalAmount += $amount;

                // Sync distributor_payments if matched
                if (!empty($sale->invoice_no)) {
                    \App\Models\DistributorPayment::where('transaction_reference', $sale->invoice_no)
                        ->whereNull('account_id')
                        ->update(['account_id' => $sale->card_account_id]);
                }
            }
        }

        if ($count > 0) {
            return redirect()->back()->with('success', "Sync complete! Successfully credited {$count} sale invoice(s) totaling PKR " . number_format($totalAmount, 2) . " into their respective Chart of Accounts.");
        } else {
            return redirect()->back()->with('info', 'All card / bank sale transactions are already synchronized with your Chart of Accounts.');
        }
    }
}
