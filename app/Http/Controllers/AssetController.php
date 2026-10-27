<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHead;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetMaintenance;
use App\Models\AssetTransfer;
use App\Models\Branch;
use App\Models\ExpenseVoucher;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

use function active_branch_id;
use function is_all_branches;

class AssetController extends Controller
{
    /**
     * Display a listing of assets with KPI cards and filters.
     */
    public function index(Request $request)
    {
        $branchId = null;
        if (!is_all_branches()) {
            $branchId = active_branch_id();
        }
        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id === 'all' ? null : (int)$request->branch_id;
        }

        $query = Asset::with(['category', 'branch', 'warehouse', 'vendor']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('asset_code', 'like', "%{$s}%")
                  ->orWhere('model_no', 'like', "%{$s}%")
                  ->orWhere('serial_no', 'like', "%{$s}%")
                  ->orWhere('custodian_name', 'like', "%{$s}%");
            });
        }

        $assets = $query->orderBy('id', 'desc')->get();

        // Summary KPI Metrics
        $baseKpiQuery = Asset::query();
        if ($branchId) {
            $baseKpiQuery->where('branch_id', $branchId);
        }
        $allBranchAssets = $baseKpiQuery->get();

        $totalAssets = $allBranchAssets->count();
        $totalCost = $allBranchAssets->sum('purchase_cost');
        $totalCurrentValue = $allBranchAssets->sum(function ($item) {
            return $item->estimated_current_value;
        });

        $machineryCategory = AssetCategory::where('code', 'MCH')->orWhere('name', 'like', '%Machinery%')->first();
        $machineryAssets = $allBranchAssets->filter(function ($a) use ($machineryCategory) {
            return $machineryCategory && $a->category_id == $machineryCategory->id;
        });
        $machineryCost = $machineryAssets->sum('purchase_cost');
        $machineryCount = $machineryAssets->count();

        $furnitureCategory = AssetCategory::where('code', 'FRN')->orWhere('name', 'like', '%Furniture%')->first();
        $furnitureAssets = $allBranchAssets->filter(function ($a) use ($furnitureCategory) {
            return $furnitureCategory && $a->category_id == $furnitureCategory->id;
        });
        $furnitureCost = $furnitureAssets->sum('purchase_cost');
        $furnitureCount = $furnitureAssets->count();

        $inMaintenanceCount = $allBranchAssets->where('status', 'under_maintenance')->count();

        $categories = AssetCategory::where('status', 1)->get();
        $branches = Branch::all();

        return view('admin_panel.assets.index', compact(
            'assets',
            'categories',
            'branches',
            'totalAssets',
            'totalCost',
            'totalCurrentValue',
            'machineryCost',
            'machineryCount',
            'furnitureCost',
            'furnitureCount',
            'inMaintenanceCount',
            'branchId'
        ));
    }

    /**
     * Show form to create new asset.
     */
    public function create()
    {
        $categories = AssetCategory::where('status', 1)->get();
        $branches = Branch::all();
        $warehouses = Warehouse::all();
        $vendors = Vendor::orderBy('name')->get();
        $activeBranch = !is_all_branches() ? active_branch_id() : 1;
        $suggestedCode = Asset::generateAssetCode();

        return view('admin_panel.assets.create', compact(
            'categories',
            'branches',
            'warehouses',
            'vendors',
            'activeBranch',
            'suggestedCode'
        ));
    }

    /**
     * Store a newly created asset.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:200',
            'category_id'       => 'required|exists:asset_categories,id',
            'branch_id'         => 'nullable|exists:branches,id',
            'warehouse_id'      => 'nullable|exists:warehouses,id',
            'vendor_id'         => 'nullable|exists:vendors,id',
            'purchase_date'     => 'nullable|date',
            'purchase_cost'     => 'required|numeric|min:0',
            'salvage_value'     => 'nullable|numeric|min:0',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'useful_life_years' => 'nullable|integer|min:1|max:50',
            'condition'         => 'required|in:new,good,fair,needs_repair,damaged',
            'status'            => 'required|in:active,under_maintenance,idle,disposed,scrapped',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $code = trim($request->asset_code);
        if (empty($code)) {
            $code = Asset::generateAssetCode($request->category_id);
        }

        // Check uniqueness of code
        if (Asset::where('asset_code', $code)->exists()) {
            $code = Asset::generateAssetCode($request->category_id);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'asset_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/assets');
            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $imagePath = 'uploads/assets/' . $filename;
        }

        $cost = (float) $request->purchase_cost;
        $rate = $request->filled('depreciation_rate') ? (float)$request->depreciation_rate : 10.0;

        $asset = Asset::create([
            'asset_code'        => $code,
            'name'              => $request->name,
            'category_id'       => $request->category_id,
            'branch_id'         => $request->branch_id,
            'warehouse_id'      => $request->warehouse_id,
            'location_details'  => $request->location_details,
            'vendor_id'         => $request->vendor_id,
            'purchase_date'     => $request->purchase_date ?: date('Y-m-d'),
            'purchase_cost'     => $cost,
            'current_value'     => $cost,
            'salvage_value'     => $request->salvage_value ?? 0,
            'useful_life_years' => $request->useful_life_years ?? 5,
            'depreciation_rate' => $rate,
            'model_no'          => $request->model_no,
            'serial_no'         => $request->serial_no,
            'warranty_expiry'   => $request->warranty_expiry,
            'custodian_name'    => $request->custodian_name,
            'condition'         => $request->condition,
            'status'            => $request->status,
            'image'             => $imagePath,
            'notes'             => $request->notes,
            'created_by'        => Auth::id(),
        ]);

        return redirect()->route('assets.show', $asset->id)
            ->with('success', "Asset {$asset->name} ({$asset->asset_code}) added successfully!");
    }

    /**
     * Display asset details with timeline/history tabs.
     */
    public function show($id)
    {
        $asset = Asset::with([
            'category',
            'branch',
            'warehouse',
            'vendor',
            'creator',
            'maintenances.creator',
            'maintenances.account.head',
            'maintenances.expenseVoucher',
            'transfers.fromBranch',
            'transfers.toBranch',
            'transfers.user'
        ])->findOrFail($id);

        $branches = Branch::where('id', '!=', $asset->branch_id)->get();
        $paymentAccounts = Account::whereNull('distributor_id')->where('status', 1)->with('head')->get();

        // Barcode preview
        $barcodePng = null;
        try {
            $dns = new DNS1D();
            $barcodePng = 'data:image/png;base64,' . $dns->getBarcodePNG($asset->asset_code, 'C128', 2, 45);
        } catch (\Throwable $e) {}

        return view('admin_panel.assets.show', compact('asset', 'branches', 'paymentAccounts', 'barcodePng'));
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $categories = AssetCategory::where('status', 1)->get();
        $branches = Branch::all();
        $warehouses = Warehouse::all();
        $vendors = Vendor::orderBy('name')->get();

        return view('admin_panel.assets.edit', compact(
            'asset',
            'categories',
            'branches',
            'warehouses',
            'vendors'
        ));
    }

    /**
     * Update asset.
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:200',
            'category_id'       => 'required|exists:asset_categories,id',
            'branch_id'         => 'nullable|exists:branches,id',
            'warehouse_id'      => 'nullable|exists:warehouses,id',
            'vendor_id'         => 'nullable|exists:vendors,id',
            'purchase_date'     => 'nullable|date',
            'purchase_cost'     => 'required|numeric|min:0',
            'salvage_value'     => 'nullable|numeric|min:0',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'useful_life_years' => 'nullable|integer|min:1|max:50',
            'condition'         => 'required|in:new,good,fair,needs_repair,damaged',
            'status'            => 'required|in:active,under_maintenance,idle,disposed,scrapped',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = $asset->image;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'asset_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $destination = public_path('uploads/assets');
            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $imagePath = 'uploads/assets/' . $filename;
        }

        $asset->update([
            'name'              => $request->name,
            'category_id'       => $request->category_id,
            'branch_id'         => $request->branch_id,
            'warehouse_id'      => $request->warehouse_id,
            'location_details'  => $request->location_details,
            'vendor_id'         => $request->vendor_id,
            'purchase_date'     => $request->purchase_date,
            'purchase_cost'     => $request->purchase_cost,
            'salvage_value'     => $request->salvage_value ?? 0,
            'useful_life_years' => $request->useful_life_years ?? 5,
            'depreciation_rate' => $request->depreciation_rate ?? 10.0,
            'model_no'          => $request->model_no,
            'serial_no'         => $request->serial_no,
            'warranty_expiry'   => $request->warranty_expiry,
            'custodian_name'    => $request->custodian_name,
            'condition'         => $request->condition,
            'status'            => $request->status,
            'image'             => $imagePath,
            'notes'             => $request->notes,
        ]);

        return redirect()->route('assets.show', $asset->id)
            ->with('success', "Asset {$asset->name} updated successfully!");
    }

    /**
     * Soft delete asset.
     */
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', "Asset {$asset->name} deleted successfully.");
    }

    /**
     * Record maintenance / service log with optional Cash/Bank deduction.
     */
    public function storeMaintenance(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'maintenance_date'  => 'required|date',
            'service_type'      => 'required|string|max:100',
            'cost'              => 'required|numeric|min:0',
            'account_id'        => 'nullable|exists:accounts,id',
            'performed_by'      => 'nullable|string|max:150',
            'next_service_date' => 'nullable|date',
            'status'            => 'required|in:scheduled,in_progress,completed',
            'description'       => 'nullable|string',
            'parts_replaced'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $cost = (float) $request->cost;
            $voucherId = null;
            $accountId = null;
            $payingAccount = null;

            // Optional automated Expense Voucher & Cash/Bank deduction
            if ($request->filled('account_id') && $cost > 0) {
                $payingAccount = Account::find($request->account_id);
                if ($payingAccount) {
                    $accountId = $payingAccount->id;

                    // Ensure an Expense head exists
                    $expenseHead = AccountHead::firstOrCreate(
                        ['name' => 'Machinery & Asset Maintenance'],
                        ['status' => 1]
                    );

                    // Create automated Expense Voucher
                    $voucher = ExpenseVoucher::create([
                        'evid'         => ExpenseVoucher::generateInvoiceNo(),
                        'user_id'      => Auth::id(),
                        'branch_id'    => $asset->branch_id ?: active_branch_id(),
                        'entry_date'   => now(),
                        'date'         => $request->maintenance_date,
                        'type'         => $expenseHead->id,
                        'party_id'     => $payingAccount->id,
                        'remarks'      => ["Asset Maintenance: {$asset->name} ({$asset->asset_code}) - {$request->service_type}"],
                        'amount'       => [(string)$cost],
                        'total_amount' => $cost,
                    ]);

                    $voucherId = $voucher->id;

                    // Deduct from paying account (Cash in Hand / Bank)
                    $payingAccount->opening_balance -= $cost;
                    $payingAccount->save();
                }
            }

            AssetMaintenance::create([
                'asset_id'           => $asset->id,
                'maintenance_date'   => $request->maintenance_date,
                'service_type'       => $request->service_type,
                'cost'               => $cost,
                'account_id'         => $accountId,
                'expense_voucher_id' => $voucherId,
                'performed_by'       => $request->performed_by,
                'next_service_date'  => $request->next_service_date,
                'description'        => $request->description,
                'parts_replaced'     => $request->parts_replaced,
                'status'             => $request->status,
                'created_by'         => Auth::id(),
            ]);

            // Auto update asset status
            if ($request->status === 'in_progress' || $request->status === 'scheduled') {
                $asset->update(['status' => 'under_maintenance', 'condition' => 'needs_repair']);
            } elseif ($request->status === 'completed' && $asset->status === 'under_maintenance') {
                $asset->update(['status' => 'active', 'condition' => 'good']);
            }

            DB::commit();

            $msg = 'Maintenance record added successfully.';
            if ($voucherId && $payingAccount) {
                $msg .= " Rs. " . number_format($cost, 0) . " deducted from {$payingAccount->title} and Expense Voucher ({$voucher->evid}) generated.";
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save maintenance record: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing maintenance record and synchronize account balances.
     */
    public function updateMaintenance(Request $request, $id)
    {
        $m = AssetMaintenance::findOrFail($id);
        $asset = $m->asset;

        $request->validate([
            'maintenance_date'  => 'required|date',
            'service_type'      => 'required|string|max:100',
            'cost'              => 'required|numeric|min:0',
            'account_id'        => 'nullable|exists:accounts,id',
            'performed_by'      => 'nullable|string|max:150',
            'next_service_date' => 'nullable|date',
            'status'            => 'required|in:scheduled,in_progress,completed',
            'description'       => 'nullable|string',
            'parts_replaced'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $newCost = (float) $request->cost;
            $oldCost = (float) $m->cost;
            $newAccountId = $request->account_id ? (int)$request->account_id : null;
            $oldAccountId = $m->account_id ? (int)$m->account_id : null;
            $voucherId = $m->expense_voucher_id;

            // Handle account balance and voucher changes
            if ($voucherId) {
                $voucher = ExpenseVoucher::find($voucherId);
                // Refund old cost to old account first
                if ($oldAccountId) {
                    $oldAcc = Account::find($oldAccountId);
                    if ($oldAcc) {
                        $oldAcc->opening_balance += $oldCost;
                        $oldAcc->save();
                    }
                }

                // If user selected an account for payment
                if ($newAccountId && $newCost > 0) {
                    $newAcc = Account::find($newAccountId);
                    if ($newAcc) {
                        $newAcc->opening_balance -= $newCost;
                        $newAcc->save();
                    }
                    if ($voucher) {
                        $voucher->update([
                            'date'         => $request->maintenance_date,
                            'party_id'     => $newAccountId,
                            'remarks'      => ["Asset Maintenance: {$asset->name} ({$asset->asset_code}) - {$request->service_type}"],
                            'amount'       => [(string)$newCost],
                            'total_amount' => $newCost,
                        ]);
                    }
                } else {
                    // User deselected account -> remove voucher
                    if ($voucher) {
                        $voucher->delete();
                    }
                    $voucherId = null;
                    $newAccountId = null;
                }
            } else {
                // Previously had no voucher, but now user chose an account
                if ($newAccountId && $newCost > 0) {
                    $newAcc = Account::find($newAccountId);
                    if ($newAcc) {
                        $newAcc->opening_balance -= $newCost;
                        $newAcc->save();

                        $expenseHead = AccountHead::firstOrCreate(
                            ['name' => 'Machinery & Asset Maintenance'],
                            ['status' => 1]
                        );

                        $voucher = ExpenseVoucher::create([
                            'evid'         => ExpenseVoucher::generateInvoiceNo(),
                            'user_id'      => Auth::id(),
                            'branch_id'    => $asset->branch_id ?: active_branch_id(),
                            'entry_date'   => now(),
                            'date'         => $request->maintenance_date,
                            'type'         => $expenseHead->id,
                            'party_id'     => $newAcc->id,
                            'remarks'      => ["Asset Maintenance: {$asset->name} ({$asset->asset_code}) - {$request->service_type}"],
                            'amount'       => [(string)$newCost],
                            'total_amount' => $newCost,
                        ]);

                        $voucherId = $voucher->id;
                    }
                }
            }

            // Update the maintenance entry
            $m->update([
                'maintenance_date'   => $request->maintenance_date,
                'service_type'       => $request->service_type,
                'cost'               => $newCost,
                'account_id'         => $newAccountId,
                'expense_voucher_id' => $voucherId,
                'performed_by'       => $request->performed_by,
                'next_service_date'  => $request->next_service_date,
                'description'        => $request->description,
                'parts_replaced'     => $request->parts_replaced,
                'status'             => $request->status,
            ]);

            // Auto update asset status
            if ($request->status === 'in_progress' || $request->status === 'scheduled') {
                $asset->update(['status' => 'under_maintenance', 'condition' => 'needs_repair']);
            } elseif ($request->status === 'completed' && $asset->status === 'under_maintenance') {
                $asset->update(['status' => 'active', 'condition' => 'good']);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Maintenance record updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update maintenance: ' . $e->getMessage());
        }
    }

    /**
     * Delete maintenance record and reverse payment if deducted.
     */
    public function destroyMaintenance($id)
    {
        $m = AssetMaintenance::findOrFail($id);

        DB::beginTransaction();
        try {
            // Reverse account balance if an expense voucher was created
            if ($m->expense_voucher_id) {
                $voucher = ExpenseVoucher::find($m->expense_voucher_id);
                if ($voucher) {
                    $payingAccount = Account::find($voucher->party_id);
                    if ($payingAccount) {
                        $payingAccount->opening_balance += (float)$m->cost;
                        $payingAccount->save();
                    }
                    $voucher->delete();
                }
            }

            $m->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Maintenance record deleted and payment reversed if applicable.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting record: ' . $e->getMessage());
        }
    }

    /**
     * Transfer asset to another branch.
     */
    public function storeTransfer(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'to_branch_id'   => 'required|exists:branches,id',
            'transfer_date'  => 'required|date',
            'to_location'    => 'nullable|string|max:200',
            'remarks'        => 'nullable|string',
        ]);

        AssetTransfer::create([
            'asset_id'        => $asset->id,
            'from_branch_id'  => $asset->branch_id,
            'to_branch_id'    => $request->to_branch_id,
            'from_location'   => $asset->location_details,
            'to_location'     => $request->to_location,
            'transfer_date'   => $request->transfer_date,
            'transferred_by'  => Auth::id(),
            'remarks'         => $request->remarks,
        ]);

        $asset->update([
            'branch_id'        => $request->to_branch_id,
            'location_details' => $request->to_location ?: $asset->location_details,
        ]);

        return redirect()->back()->with('success', 'Asset transferred successfully.');
    }

    /**
     * Dispose / Scrap asset.
     */
    public function dispose(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'disposal_date'   => 'required|date',
            'disposal_status' => 'required|in:disposed,scrapped',
            'disposal_amount' => 'nullable|numeric|min:0',
            'disposal_reason' => 'required|string|max:500',
        ]);

        $asset->update([
            'status'          => $request->disposal_status,
            'disposal_date'   => $request->disposal_date,
            'disposal_amount' => $request->disposal_amount ?? 0,
            'disposal_reason' => $request->disposal_reason,
            'current_value'   => $request->disposal_amount ?? 0,
        ]);

        return redirect()->back()->with('success', "Asset marked as {$request->disposal_status}.");
    }

    /**
     * Printable barcode / QR label tag.
     */
    public function barcode($id)
    {
        $asset = Asset::with(['category', 'branch'])->findOrFail($id);

        $barcodePng = null;
        $qrPng = null;
        try {
            $dns1d = new DNS1D();
            $barcodePng = 'data:image/png;base64,' . $dns1d->getBarcodePNG($asset->asset_code, 'C128', 2, 40);

            $dns2d = new DNS2D();
            $qrData = "ASSET: {$asset->asset_code}\nNAME: {$asset->name}\nBRANCH: " . ($asset->branch->name ?? 'Main');
            $qrPng = 'data:image/png;base64,' . $dns2d->getBarcodePNG($qrData, 'QRCODE', 4, 4);
        } catch (\Throwable $e) {}

        return view('admin_panel.assets.barcode', compact('asset', 'barcodePng', 'qrPng'));
    }

    /**
     * Asset Register & Valuation Report.
     */
    public function report(Request $request)
    {
        $branchId = null;
        if (!is_all_branches()) {
            $branchId = active_branch_id();
        }
        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id === 'all' ? null : (int)$request->branch_id;
        }

        $query = Asset::with(['category', 'branch', 'vendor']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->orderBy('category_id')->orderBy('name')->get();

        $categories = AssetCategory::where('status', 1)->get();
        $branches = Branch::all();

        return view('admin_panel.assets.report', compact('assets', 'categories', 'branches', 'branchId'));
    }

    /**
     * Get auto-generated code via AJAX for category
     */
    public function getNextCode(Request $request)
    {
        $catId = $request->get('category_id');
        $code = Asset::generateAssetCode($catId);
        return response()->json(['code' => $code]);
    }
}
