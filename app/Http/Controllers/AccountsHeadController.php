<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHead;
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

    public function storeHead(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        AccountHead::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Head added successfully.');
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
