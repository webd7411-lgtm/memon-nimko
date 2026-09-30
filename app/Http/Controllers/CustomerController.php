<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\CustomerPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $query = Customer::with('branch')->latest();
        if (!is_all_branches()) {
            $query->where('branch_id', active_branch_id());
        }
        $customers = $query->get();

        $closingBalances = DB::table('customer_ledgers')
            ->select('customer_id', 'closing_balance')
            ->orderBy('id', 'desc')
            ->get()
            ->keyBy('customer_id');

        foreach ($customers as $customer) {
            $customer->closing_balance = $closingBalances[$customer->id]->closing_balance ?? 0;
        }

        $totalClosingBalance = $customers->sum('closing_balance');

        return view('admin_panel.customers.index', compact('customers', 'totalClosingBalance'));
    }

    public function toggleStatus($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        return redirect()->back()->with('success', 'Customer status updated.');
    }

    // Add this in CustomerController
    public function getCustomerLedger($id)
    {
        $ledger = CustomerLedger::where('customer_id', $id)->latest()->first();
        return response()->json([
            'closing_balance' => $ledger->closing_balance ?? 0
        ]);
    }


    public function markInactive($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = 'inactive';
        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Customer marked as inactive.');
    }

    public function inactiveCustomers()
    {
        $query = Customer::with('branch')->where('status', 'inactive')->latest();
        if (!is_all_branches()) {
            $query->where('branch_id', active_branch_id());
        }
        $customers = $query->get();
        return view('admin_panel.customers.inactive', compact('customers'));
    }

    public function create()
    {
        $latestId = 'CUST-' . str_pad(Customer::max('id') + 1, 4, '0', STR_PAD_LEFT);
        $branches = \App\Models\Branch::orderBy('name')->get();
        $activeBranchId = active_branch_id();
        return view('admin_panel.customers.create', compact('latestId', 'branches', 'activeBranchId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'      => 'required|unique:customers',
            'customer_name'    => 'required|string|max:255',
            'customer_type'    => 'nullable|string',
            'customer_category' => 'nullable|string',
            'mobile'           => 'nullable|string|max:20',
            'address'          => 'nullable|string',
            'opening_balance'  => 'nullable|numeric|min:0',
            'branch_id'        => 'nullable|exists:branches,id',
        ]);

        if (empty($data['branch_id'])) {
            $data['branch_id'] = active_branch_id();
        }

        // Handle checkbox (unchecked = not submitted)
        $data['credit_allowed'] = $request->has('credit_allowed') ? 1 : 0;

        // Customer create
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $customer = Customer::create($data);

        // Ledger entry if opening balance exists
        $opening = $data['opening_balance'] ?? 0;

        if ($opening > 0) {
            CustomerLedger::create([
                'customer_id'      => $customer->id,
                'admin_or_user_id' => Auth::id(),
                'previous_balance' => 0,
                'opening_balance'  => $opening,
                'closing_balance'  => $opening,
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }


    public function edit($id)
    {
        $customer = Customer::with('branch')->findOrFail($id);
        $branches = \App\Models\Branch::orderBy('name')->get();
        return view('admin_panel.customers.edit', compact('customer', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Validation
        $data = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_type'    => 'nullable|string',
            'customer_category' => 'nullable|string',
            'mobile'           => 'nullable|string|max:20',
            'address'          => 'nullable|string',
            'opening_balance'  => 'nullable|numeric|min:0',
            'branch_id'        => 'nullable|exists:branches,id',
        ]);

        if (empty($data['branch_id']) && empty($customer->branch_id)) {
            $data['branch_id'] = active_branch_id();
        }

        // Handle checkbox (not submitted when unchecked)
        $data['credit_allowed'] = $request->has('credit_allowed') ? 1 : 0;

        // Update customer basic info
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $customer->update($data);

        // Update ledger if opening balance is changed
        if (isset($data['opening_balance'])) {

            $ledger = CustomerLedger::where('customer_id', $customer->id)
                ->orderBy('created_at', 'asc') // first ledger entry
                ->first();

            if ($ledger) {
                $old_opening = $ledger->opening_balance;
                $difference = $data['opening_balance'] - $old_opening;

                // Update first ledger opening and closing balance
                $ledger->opening_balance = $data['opening_balance'];
                $ledger->closing_balance += $difference; // preserve all previous transactions
                $ledger->save();

                // Update all subsequent ledgers
                $subsequentLedgers = CustomerLedger::where('customer_id', $customer->id)
                    ->where('id', '>', $ledger->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                foreach ($subsequentLedgers as $sub) {
                    $sub->previous_balance += $difference;
                    $sub->closing_balance += $difference;
                    $sub->save();
                }
            } else if ($data['opening_balance'] > 0) {
                // No ledger exists, create first entry
                CustomerLedger::create([
                    'customer_id'      => $customer->id,
                    'admin_or_user_id' => Auth::id(),
                    'previous_balance' => 0,
                    'opening_balance'  => $data['opening_balance'],
                    'closing_balance'  => $data['opening_balance'],
                ]);
            }
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }


    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }


    // Customer Ledger View
    public function customer_ledger()
    {
        if (Auth::check()) {
            $query = CustomerLedger::with(['customer', 'customer.branch'])->latest();
            if (!is_all_branches()) {
                $query->whereHas('customer', function($q) {
                    $q->where('branch_id', active_branch_id());
                });
            }
            $CustomerLedgers = $query->get();
            return view('admin_panel.customers.customer_ledger', compact('CustomerLedgers'));
        } else {
            return redirect()->back();
        }
    }

    // View all customer payments
    public function customer_payments()
    {
        $query = CustomerPayment::with(['customer', 'customer.branch', 'cardAccount'])->orderByDesc('id');
        if (!is_all_branches()) {
            $query->where('branch_id', active_branch_id());
        }
        $payments = $query->get();

        $customerQuery = Customer::where('status', '!=', 'inactive');
        if (!is_all_branches()) {
            $customerQuery->where('branch_id', active_branch_id());
        }
        $customers = $customerQuery->get();

        $bankAccounts = \App\Models\Account::where('status', 1)->with('head')->orderBy('title')->get();

        return view('admin_panel.customers.customer_payments', compact('payments', 'customers', 'bankAccounts'));
    }

    public function store_customer_payment(Request $request)
    {
        $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'amount'          => 'required|numeric|min:0.01',
            'adjustment_type' => 'required|in:plus,minus',
            'payment_mode'    => 'nullable|in:cash,card,split',
            'payment_method'  => 'nullable|string',
            'cash'            => 'nullable|numeric|min:0',
            'card'            => 'nullable|numeric|min:0',
            'card_account_id' => 'nullable|exists:accounts,id',
            'payment_date'    => 'required|date',
            'note'            => 'nullable|string',
        ]);

        $mode = $request->payment_mode ?? 'cash';
        $totalAmount = (float) $request->amount;
        $cashAmount = 0;
        $cardAmount = 0;
        $cardAccountId = null;
        $paymentMethod = $request->payment_method;

        if ($mode === 'cash') {
            $cashAmount = $totalAmount;
            $cardAmount = 0;
            $cardAccountId = null;
            $paymentMethod = $paymentMethod ?: 'Cash';
        } elseif ($mode === 'card') {
            $cashAmount = 0;
            $cardAmount = $totalAmount;
            $cardAccountId = $request->card_account_id ?: null;
            $paymentMethod = $paymentMethod ?: 'Card';
        } elseif ($mode === 'split') {
            $cashAmount = (float) ($request->cash ?? 0);
            $cardAmount = (float) ($request->card ?? 0);
            $cardAccountId = $request->card_account_id ?: null;
            if (($cashAmount + $cardAmount) > 0) {
                $totalAmount = $cashAmount + $cardAmount;
            }
            $paymentMethod = $paymentMethod ?: 'Split (Cash + Card)';
        } else {
            $cashAmount = $totalAmount;
            $paymentMethod = $paymentMethod ?: 'Cash';
        }

        $userId = Auth::id();

        // Last received number
        $lastPayment = CustomerPayment::latest('id')->first();

        if ($lastPayment && $lastPayment->received_no) {
            $lastNumber = (int) str_replace('REC-', '', $lastPayment->received_no);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $receivedNo = 'REC-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Save payment
        CustomerPayment::create([
            'received_no'     => $receivedNo,
            'customer_id'     => $request->customer_id,
            'admin_or_user_id'=> $userId,
            'branch_id'       => active_branch_id(),
            'amount'          => $totalAmount,
            'cash'            => $cashAmount,
            'card'            => $cardAmount,
            'card_account_id' => $cardAccountId,
            'payment_method'  => $paymentMethod,
            'payment_date'    => $request->payment_date,
            'note'            => $request->note,
        ]);

        // Ledger update
        $ledger = CustomerLedger::where('customer_id', $request->customer_id)->latest()->first();

        if ($ledger) {
            $newBalance = $request->adjustment_type === 'plus'
                ? $ledger->closing_balance + $totalAmount
                : $ledger->closing_balance - $totalAmount;

            $ledger->update([
                'previous_balance' => $ledger->closing_balance,
                'closing_balance'  => $newBalance,
            ]);
        }

        return back()->with('success', 'Payment adjusted and ledger updated.');
    }

    public function customer_payment_receipt($id)
    {
        $payment = CustomerPayment::with(['customer', 'cardAccount'])->findOrFail($id);
        return view('admin_panel.customers.customer_payment_receipt', compact('payment'));
    }


    // Edit customer payment
    public function edit_customer_payment($id)
    {
        $payment = CustomerPayment::with(['customer', 'cardAccount'])->findOrFail($id);

        $customerQuery = Customer::where('status', '!=', 'inactive');
        if (!is_all_branches()) {
            $branchId = $payment->branch_id ?? active_branch_id();
            $customerQuery->where(function($q) use ($branchId, $payment) {
                $q->where('branch_id', $branchId)
                  ->orWhere('id', $payment->customer_id);
            });
        }
        $customers = $customerQuery->get();

        $bankAccounts = \App\Models\Account::where('status', 1)->with('head')->orderBy('title')->get();

        // Get current ledger balance
        $ledger = CustomerLedger::where('customer_id', $payment->customer_id)->latest()->first();
        $current_balance = $ledger ? $ledger->closing_balance : 0;

        // Calculate original balance (before this payment was made)
        $original_balance = $current_balance + $payment->amount;

        $adjustment_type = 'minus';

        return view('admin_panel.customers.edit_customer_payment', compact('payment', 'customers', 'original_balance', 'adjustment_type', 'bankAccounts'));
    }

    // Update customer payment
    public function update_customer_payment(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'payment_date'    => 'required|date',
            'amount'          => 'required|numeric|min:0.01',
            'payment_mode'    => 'nullable|in:cash,card,split',
            'payment_method'  => 'nullable|string',
            'cash'            => 'nullable|numeric|min:0',
            'card'            => 'nullable|numeric|min:0',
            'card_account_id' => 'nullable|exists:accounts,id',
            'note'            => 'nullable|string',
            'adjustment_type' => 'required|in:plus,minus',
        ]);

        $payment = CustomerPayment::findOrFail($id);

        $mode = $request->payment_mode ?? ($payment->card > 0 && $payment->cash > 0 ? 'split' : ($payment->card > 0 ? 'card' : 'cash'));
        $totalAmount = (float) $validated['amount'];
        $cashAmount = 0;
        $cardAmount = 0;
        $cardAccountId = null;
        $paymentMethod = $validated['payment_method'] ?? null;

        if ($mode === 'cash') {
            $cashAmount = $totalAmount;
            $cardAmount = 0;
            $cardAccountId = null;
            $paymentMethod = $paymentMethod ?: 'Cash';
        } elseif ($mode === 'card') {
            $cashAmount = 0;
            $cardAmount = $totalAmount;
            $cardAccountId = $request->card_account_id ?: null;
            $paymentMethod = $paymentMethod ?: 'Card';
        } elseif ($mode === 'split') {
            $cashAmount = (float) ($request->cash ?? 0);
            $cardAmount = (float) ($request->card ?? 0);
            $cardAccountId = $request->card_account_id ?: null;
            if (($cashAmount + $cardAmount) > 0) {
                $totalAmount = $cashAmount + $cardAmount;
            }
            $paymentMethod = $paymentMethod ?: 'Split (Cash + Card)';
        } else {
            $cashAmount = $totalAmount;
            $paymentMethod = $paymentMethod ?: 'Cash';
        }

        // Get current ledger
        $ledger = CustomerLedger::where('customer_id', $payment->customer_id)->latest()->first();

        if ($ledger) {
            $current_balance = $ledger->closing_balance;
            $original_balance = $current_balance + $payment->amount;
            $new_balance = $original_balance +
                ($validated['adjustment_type'] === 'minus' ? -1 : 1) * $totalAmount;

            $ledger->closing_balance = $new_balance;
            $ledger->save();
        }

        // Update payment record
        $payment->update([
            'customer_id'     => $validated['customer_id'],
            'payment_date'    => $validated['payment_date'],
            'amount'          => $totalAmount,
            'cash'            => $cashAmount,
            'card'            => $cardAmount,
            'card_account_id' => $cardAccountId,
            'payment_method'  => $paymentMethod,
            'note'            => $validated['note'],
        ]);

        return redirect()->route('customer.payments')->with('success', 'Customer payment updated successfully.');
    }

    public function destroy_payment($id)
    {
        $payment = CustomerPayment::findOrFail($id);

        $customerId = $payment->customer_id;
        $amount     = $payment->amount;

        // Latest ledger record for that customer
        $ledger = CustomerLedger::where('customer_id', $customerId)
            ->orderBy('id', 'desc')
            ->first();
        if ($ledger) {
            $ledger->closing_balance += $amount;
            $ledger->save();
        }

        // Delete the payment entry
        $payment->delete();

        return redirect()->back()->with('success', 'Payment deleted and customer ledger updated successfully.');
    }


    public function getByType(Request $request)
    {
        $type = $request->get('type');

        $query = Customer::where('customer_type', $type)->where('status', '!=', 'inactive');
        if (!is_all_branches()) {
            $query->where('branch_id', active_branch_id());
        }

        $customers = $query->get(['id', 'customer_name']);

        return response()->json(['customers' => $customers]);
    }

    /**
     * AJAX: Return credit_allowed status for a customer (used by POS)
     */
    public function getCreditInfo($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['credit_allowed' => false]);
        }
        return response()->json([
            'credit_allowed' => (bool)$customer->credit_allowed,
            'customer_name'  => $customer->customer_name,
        ]);
    }
}
