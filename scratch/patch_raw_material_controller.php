<?php
$filePath = 'app/Http/Controllers/RawMaterialController.php';
$content = file_get_contents($filePath);

// 1. Eager load vendor and paymentAccount in purchasesQuery
$targetWith = "RawMaterialPurchase::with(['items.rawMaterial', 'creator', 'warehouse', 'branch', 'returns.items'])";
$replaceWith = "RawMaterialPurchase::with(['items.rawMaterial', 'creator', 'warehouse', 'branch', 'returns.items', 'vendor', 'paymentAccount'])";
$content = str_replace($targetWith, $replaceWith, $content);

// 2. Pass paymentAccounts to view in index()
$targetCompact = "return view('admin_panel.raw_material.index', compact(\r\n            'materials', 'purchases', 'purchaseReturns', 'vendors', 'warehouses', 'productsWithBom', 'warehouseStocks',\r\n            'startDate', 'startTime', 'endDate', 'endTime'\r\n        ));";
if (strpos($content, $targetCompact) === false) {
    $targetCompact = str_replace("\r\n", "\n", $targetCompact);
}

$replaceCompact = "\$paymentAccounts = \\App\\Models\\Account::whereNull('distributor_id')->where('status', 1)->with('head')->orderBy('title')->get();\r\n\r\n        return view('admin_panel.raw_material.index', compact(\r\n            'materials', 'purchases', 'purchaseReturns', 'vendors', 'warehouses', 'productsWithBom', 'warehouseStocks',\r\n            'startDate', 'startTime', 'endDate', 'endTime', 'paymentAccounts'\r\n        ));";

$content = str_replace($targetCompact, $replaceCompact, $content);

// 3. Update storePurchase with payment handling
$targetStore = "            if (\$purchaseId) {\r\n" .
"                \$purchase = RawMaterialPurchase::find(\$purchaseId);\r\n" .
"                if (!\$purchase) {\r\n" .
"                    return response()->json(['error' => 'Purchase not found'], 404);\r\n" .
"                }\r\n" .
"                // Delete old items to replace with new\r\n" .
"                RawMaterialPurchaseItem::where('purchase_id', \$purchase->id)->delete();\r\n\r\n" .
"                \$purchase->date = \$request->date;\r\n" .
"                \$purchase->vendor_id = \$vendor ? \$vendor->id : null;\r\n" .
"                \$purchase->vendor_name = \$vendor ? \$vendor->name : \$request->vendor_name;\r\n" .
"                \$purchase->warehouse_id = \$request->warehouse_id ?: null;\r\n" .
"                \$purchase->total_cost = \$totalCost;\r\n" .
"                \$purchase->notes = \$request->notes;\r\n" .
"                \$purchase->save();\r\n\r\n" .
"                \$successMsg = 'Raw Material Purchase Updated Successfully';\r\n" .
"            } else {\r\n" .
"                \$purchase = RawMaterialPurchase::create([\r\n" .
"                    'date' => \$request->date,\r\n" .
"                    'invoice_no' => 'RMP-' . date('Ymd-His'),\r\n" .
"                    'vendor_id' => \$vendor ? \$vendor->id : null,\r\n" .
"                    'vendor_name' => \$vendor ? \$vendor->name : \$request->vendor_name,\r\n" .
"                    'warehouse_id' => \$request->warehouse_id ?: null,\r\n" .
"                    'total_cost' => \$totalCost,\r\n" .
"                    'notes' => \$request->notes,\r\n" .
"                    'created_by' => Auth::id(),\r\n" .
"                    'branch_id' => \$branchId,\r\n" .
"                ]);\r\n" .
"                \$successMsg = 'Raw Material Purchase Saved Successfully';\r\n" .
"            }\r\n\r\n" .
"            // 🔹 Update Vendor Ledger closing balance\r\n" .
"            if (\$vendor) {\r\n" .
"                \$ledger = VendorLedger::firstOrNew(['vendor_id' => \$vendor->id]);\r\n" .
"                if (!\$ledger->exists) {\r\n" .
"                    \$ledger->admin_or_user_id = Auth::id();\r\n" .
"                    \$ledger->opening_balance = \$vendor->opening_balance ?? 0;\r\n" .
"                    \$ledger->closing_balance = \$vendor->opening_balance ?? 0;\r\n" .
"                    \$ledger->previous_balance = 0;\r\n" .
"                }\r\n" .
"                \$ledger->previous_balance = \$ledger->closing_balance;\r\n" .
"                \$ledger->closing_balance += \$totalCost;\r\n" .
"                \$ledger->save();\r\n" .
"            }";

if (strpos($content, $targetStore) === false) {
    $targetStore = str_replace("\r\n", "\n", $targetStore);
}

$replaceStore = "            \$paidAmount = floatval(\$request->paid_amount ?? 0);\r\n" .
"            \$paymentMethod = \$request->payment_method ?: (\$paidAmount > 0 ? 'Cash' : null);\r\n" .
"            \$cashAccountId = !empty(\$request->cash_account_id) ? (int)\$request->cash_account_id : null;\r\n" .
"            \$dueAmount = max(0, \$totalCost - \$paidAmount);\r\n\r\n" .
"            if (\$purchaseId) {\r\n" .
"                \$purchase = RawMaterialPurchase::find(\$purchaseId);\r\n" .
"                if (!\$purchase) {\r\n" .
"                    return response()->json(['error' => 'Purchase not found'], 404);\r\n" .
"                }\r\n" .
"                RawMaterialPurchaseItem::where('purchase_id', \$purchase->id)->delete();\r\n\r\n" .
"                \$purchase->date = \$request->date;\r\n" .
"                \$purchase->vendor_id = \$vendor ? \$vendor->id : null;\r\n" .
"                \$purchase->vendor_name = \$vendor ? \$vendor->name : \$request->vendor_name;\r\n" .
"                \$purchase->warehouse_id = \$request->warehouse_id ?: null;\r\n" .
"                \$purchase->total_cost = \$totalCost;\r\n" .
"                \$purchase->paid_amount = \$paidAmount;\r\n" .
"                \$purchase->due_amount = \$dueAmount;\r\n" .
"                \$purchase->payment_method = \$paymentMethod;\r\n" .
"                \$purchase->cash_account_id = \$cashAccountId;\r\n" .
"                \$purchase->notes = \$request->notes;\r\n" .
"                \$purchase->save();\r\n\r\n" .
"                \$successMsg = 'Raw Material Purchase Updated Successfully';\r\n" .
"            } else {\r\n" .
"                \$purchase = RawMaterialPurchase::create([\r\n" .
"                    'date' => \$request->date,\r\n" .
"                    'invoice_no' => 'RMP-' . date('Ymd-His'),\r\n" .
"                    'vendor_id' => \$vendor ? \$vendor->id : null,\r\n" .
"                    'vendor_name' => \$vendor ? \$vendor->name : \$request->vendor_name,\r\n" .
"                    'warehouse_id' => \$request->warehouse_id ?: null,\r\n" .
"                    'total_cost' => \$totalCost,\r\n" .
"                    'paid_amount' => \$paidAmount,\r\n" .
"                    'due_amount' => \$dueAmount,\r\n" .
"                    'payment_method' => \$paymentMethod,\r\n" .
"                    'cash_account_id' => \$cashAccountId,\r\n" .
"                    'notes' => \$request->notes,\r\n" .
"                    'created_by' => Auth::id(),\r\n" .
"                    'branch_id' => \$branchId,\r\n" .
"                ]);\r\n" .
"                \$successMsg = 'Raw Material Purchase Saved Successfully';\r\n" .
"            }\r\n\r\n" .
"            // 🔹 Update Vendor Ledger closing balance & record payment\r\n" .
"            if (\$vendor) {\r\n" .
"                \$ledger = VendorLedger::firstOrNew(['vendor_id' => \$vendor->id]);\r\n" .
"                if (!\$ledger->exists) {\r\n" .
"                    \$ledger->admin_or_user_id = Auth::id();\r\n" .
"                    \$ledger->opening_balance = \$vendor->opening_balance ?? 0;\r\n" .
"                    \$ledger->closing_balance = \$vendor->opening_balance ?? 0;\r\n" .
"                    \$ledger->previous_balance = 0;\r\n" .
"                }\r\n" .
"                \$ledger->previous_balance = \$ledger->closing_balance;\r\n" .
"                \$ledger->closing_balance += \$totalCost; // Add purchase cost\r\n\r\n" .
"                if (\$paidAmount > 0) {\r\n" .
"                    \$ledger->closing_balance -= \$paidAmount; // Deduct paid amount\r\n\r\n" .
"                    \$lastPayment = \\App\\Models\\VendorPayment::latest('id')->first();\r\n" .
"                    \$nextNum = 1;\r\n" .
"                    if (\$lastPayment && \$lastPayment->payment_no) {\r\n" .
"                        \$lastNum = (int) str_replace('PAY-', '', \$lastPayment->payment_no);\r\n" .
"                        \$nextNum = \$lastNum + 1;\r\n" .
"                    }\r\n" .
"                    \$paymentNo = 'PAY-' . str_pad(\$nextNum, 3, '0', STR_PAD_LEFT);\r\n\r\n" .
"                    \\App\\Models\\VendorPayment::create([\r\n" .
"                        'payment_no'       => \$paymentNo,\r\n" .
"                        'vendor_id'        => \$vendor->id,\r\n" .
"                        'admin_or_user_id' => Auth::id(),\r\n" .
"                        'branch_id'        => \$branchId ?: active_branch_id(),\r\n" .
"                        'payment_date'     => \$purchase->date,\r\n" .
"                        'amount'           => \$paidAmount,\r\n" .
"                        'cash'             => (\$paymentMethod === 'Cash' || !\$cashAccountId) ? \$paidAmount : 0,\r\n" .
"                        'card'             => (\$paymentMethod !== 'Cash' && \$cashAccountId) ? \$paidAmount : 0,\r\n" .
"                        'card_account_id'  => \$cashAccountId ?: null,\r\n" .
"                        'payment_method'   => \$paymentMethod ?? 'Cash',\r\n" .
"                        'note'             => 'Paid on Raw Material Purchase (' . \$purchase->invoice_no . ')',\r\n" .
"                    ]);\r\n\r\n" .
"                    if (\$cashAccountId) {\r\n" .
"                        \$acc = \\App\\Models\\Account::find(\$cashAccountId);\r\n" .
"                        if (\$acc) {\r\n" .
"                            \$acc->opening_balance = (float)\$acc->opening_balance - \$paidAmount;\r\n" .
"                            \$acc->save();\r\n" .
"                        }\r\n" .
"                    }\r\n" .
"                }\r\n\r\n" .
"                \$ledger->save();\r\n" .
"            }";

$content = str_replace($targetStore, $replaceStore, $content);

file_put_contents($filePath, $content);
echo "Successfully patched RawMaterialController.php\n";
