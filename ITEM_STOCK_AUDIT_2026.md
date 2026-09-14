# ITEM STOCK REPORT — COMPLETE AUDIT REPORT
## Date: 12 September 2026
## Auditor: OpenCode AI

---

## 1. EXACT PROBLEM

KG wale items ki Item Stock Report mein:
- **Current Stock (Balance)** wrong aa raha hai
- **Initial Stock** wrong aa raha hai
- KG/Gram conversion mein inconsistency hai
- Purchase Return stock deduction mein KG conversion missing hai
- After-period Sale Returns sahi se match nahi ho rahe

---

## 2. ROOT CAUSE

### A. CLOSING STOCK FORMULA BUG (FIXED ✅)
**Location:** `ReportingController.php` lines 540, 650
**Previous Bug:** `- $pReturn` extra tha closingStock formula mein
**Current Status:** FIXED — formula ab sahi hai:
```php
$closingStock = $balance - $purchAft - $prodAft - $sRetAft + $soldAft + $prAft - $adjIncAft + $adjDecAft - $transferInAft + $transferAft;
```

### B. PURCHASE RETURN KG CONVERSION BUG (EXISTING ❌)
**Location:** `PurchaseController.php` lines 906-913
**Problem:** Purchase return mein KG → Gram conversion missing hai
**Impact:** 20 KG return karne pe stock mein sirf 20 subtract hota hai, 20000 nahi

### C. AFTER-PERIOD SALES RETURNS MATCHING (POTENTIAL ISSUE ⚠️)
**Location:** `ReportingController.php` lines 258-277
**Problem:** `sales_returns.product` column mein product NAME store hota hai (e.g., "ANDA CHANP 1KG (1.000 KG) 1g"), lekin join `products.item_name` se hota hai. Agar exact match nahi hota to returns zero aa sakte hain.

---

## 3. DOUBLE COUNTING CHECK

### ✅ NAHI MILA — Koi double counting nahi hai

| Transaction | Within-Period Variable | After-Period Variable | Double Count? |
|---|---|---|---|
| Purchase | `$purchased` (mapP) | `$purchAft` (mapPAft) | ❌ NAHI |
| Production | `$produced` (mapProd) | `$prodAft` (mapProdAft) | ❌ NAHI |
| Purchase Return | `$pReturn` (mapPR) | `$prAft` (mapPRAft) | ❌ NAHI |
| Sold | `$sold` (soldMap) | `$soldAft` (soldAftMap) | ❌ NAHI |
| Sale Return | `$sReturn` (retMap) | `$sRetAft` (retAftMap) | ❌ NAHI |
| Transfer Out | `$transferQty` (mapTransfer) | `$transferAft` (mapTransferAft) | ❌ NAHI |
| Transfer In | `$transferInQty` (mapTransferIn) | `$transferInAft` (mapTransferInAft) | ❌ NAHI |
| ADJ+ | `$adjInc` (mapAdjInc) | `$adjIncAft` (mapAdjIncAft) | ❌ NAHI |
| ADJ- | `$adjDec` (mapAdjDec) | `$adjDecAft` (mapAdjDecAft) | ❌ NAHI |

**Har transaction sirf EK jagah count hoti hai.**

---

## 4. KG/GRAM ISSUE

### A. STOCK STORAGE (Correct ✅)
- KG products ka stock **grams** mein store hota hai
- `stocks.qty` mein 20 KG = 20000 units store hote hain
- `variant_id` KG products ke liye NULL hota hai

### B. PURCHASE CONTROLLER (Bug ❌)
**Store (line 221):** `$qty * 1000` — sahi hai
**Return (line 906-913):** `$qty` — **GALAT hai**, `* 1000` missing hai

### C. SALE CONTROLLER (Correct ✅)
**Store (line 930-943):** Proper conversion — `size_value * qty * 1000`
**Return (line 1849-1865):** Proper conversion — `size_value * qty * 1000`

### D. REPORT CONVERSION (Correct ✅)
**Lines 588-604:**
```php
if ($is_kg) {
    $purchased = $purchased_kg * 1000;  // KG → grams
    $sold = $rawSold * 1000;            // User qty → grams
    $sReturn = $rawSReturn * 1000;      // User qty → grams
}
```

### E. DISPLAY (Correct ✅)
JavaScript `formatVal()` function (line 773-789) grams ko "Xkg Yg" format mein convert karta hai.

---

## 5. INITIAL STOCK CHECK

### Current Logic (Correct ✅)
**Lines 543-546, 653-656:**
```php
if ($resetTime && $startDate >= substr($resetTime, 0, 10)) {
    $openingStock = 0;
    $closingStock = $balance;
}
```

**Boundary:** Agar report period reset ke baad start hota hai to Initial = 0.
**Normal case:** Backward calculation se Opening Stock nikalta hai.

---

## 6. PURCHASE CHECK

### Within-Period (Correct ✅)
**Lines 73-86:**
```php
$purchasesQuery = DB::table('purchase_items')
    ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
    ->whereNull('purchases.warehouse_id')  // Shop stock only
    ->whereBetween('purchases.purchase_date', [$startDate, $endDate]);
```

### After-Period (Correct ✅)
**Lines 191-205:** Same logic, date `> $endDate`

### KG Conversion (Correct ✅)
**Line 589:** `$purchased = $purchased_kg * 1000;`

---

## 7. PURCHASE RETURN CHECK

### Within-Period (Correct ✅)
**Lines 103-116:** Proper query with branch filter

### After-Period (Correct ✅)
**Lines 222-236:** Proper query with branch filter

### ❌ STOCK DEDUCTION BUG
**Location:** `PurchaseController.php` lines 906-913
**Problem:** Stock deduction mein KG conversion missing hai

**Example:**
- Purchase: 20 KG → Stock mein 20000 grams add hua ✅
- Purchase Return: 20 KG → Stock mein sirf 20 subtract hua ❌
- Expected: 20000 grams subtract hona chahiye

---

## 8. SALE CHECK

### Within-Period (Correct ✅)
**Lines 131-159:** Complex logic but correct:
- Sales table mein comma-separated product IDs aur quantities
- PHP mein explode karke match karte hain
- KG products ke liye variant accumulation (lines 606-638)

### After-Period (Correct ✅)
**Lines 238-257:** Same logic, date `> $endDT`

### KG Conversion (Correct ✅)
**Line 590:** `$sold = $rawSold * 1000;` (base product)
**Line 630:** `$sold += ($soldMap[$vKey] ?? 0) * $mul;` (variants)

---

## 9. SALE RETURN CHECK

### Within-Period (Correct ✅)
**Lines 161-188:**
```php
$allReturnsQuery = DB::table('sales_returns')
    ->leftJoin('products', 'products.item_name', '=', 'sales_returns.product')
    ->whereBetween('sales_returns.created_at', [$startDT, $endDT])
```

### After-Period (Potential Issue ⚠️)
**Lines 258-277:** Same join logic
**Problem:** `sales_returns.product` column mein full product name hota hai (e.g., "ANDA CHANP 1KG (1.000 KG) 1g"), lekin join `products.item_name` se hota hai. Agar exact match nahi hota to returns zero aa sakte hain.

### KG Conversion (Correct ✅)
**Line 591:** `$sReturn = $rawSReturn * 1000;` (base product)
**Line 633:** `$sReturn += ($retMap[$vKey] ?? 0) * $mul;` (variants)

---

## 10. CURRENT STOCK FORMULA

### Existing Formula (Correct ✅)
**Lines 540, 650:**
```
ClosingStock = Balance - PurchAft - ProdAft - SRetAft + SoldAft + PrAft - AdjIncAft + AdjDecAft - TransferInAft + TransferAft
```

### Expected Business Formula
```
Current Stock = Initial + Produced + Purchased + TransferIn + ADJ+ + SaleReturn - PurchaseReturn - TransferOut - ADJ- - Sold
```

### Mathematical Verification
```
Opening + Net Within-Period Changes = Closing

Opening = Closing - (Purchased + Produced + SReturn + TransferIn + AdjInc) + (Sold + PReturn + TransferOut + AdjDec)
```

**Code follows this logic correctly.**

---

## 11. BRANCH/WAREHOUSE CHECK

### Branch Filtering (Correct ✅)
**All queries:** `where('branch_id', active_branch_id())` when `!is_all_branches()`

### Warehouse Filtering (Mostly Correct ⚠️)
- **Purchases:** `whereNull('purchases.warehouse_id')` — Shop stock only ✅
- **Purchase Returns:** `whereNull('purchase_returns.warehouse_id')` — Shop stock only ✅
- **Adjustments:** `whereNull('sa.warehouse_id')` — Shop stock only ✅
- **Stocks:** `whereNull('warehouse_id')` — Shop stock only ✅
- **Sales:** No warehouse filter in query (sales table may not have warehouse_id) ⚠️

---

## 12. DATE/RESET CHECK

### Date Filtering (Correct ✅)
- **Within-period:** `whereBetween(date, [$startDate, $endDate])`
- **After-period:** `where(date, '>', $endDate)`
- **Reset boundary:** `if ($resetTime && $startDate >= substr($resetTime, 0, 10))`

### Reset Logic (Correct ✅)
**Lines 543-546, 653-656:**
```php
if ($resetTime && $startDate >= substr($resetTime, 0, 10)) {
    $openingStock = 0;
    $closingStock = $balance;
}
```

---

## 13. EXACT CODE LOCATIONS

### Bug #1: Purchase Return KG Conversion
- **File:** `app/Http/Controllers/PurchaseController.php`
- **Method:** `storeReturn()`
- **Lines:** 906-913
- **Problem:** `$stock->qty -= $qty;` — `* 1000` missing for KG items

### Bug #2: After-Period Sales Returns Matching
- **File:** `app/Http/Controllers/ReportingController.php`
- **Method:** `fetchItemStock()`
- **Lines:** 258-277
- **Problem:** Product name matching may fail if names don't match exactly

### Bug #3: KG Detection Inconsistency
- **File:** `app/Http/Controllers/PurchaseController.php`
- **Lines:** 217-219
- **Problem:** Uses string matching + unit_type
- **File:** `app/Http/Controllers/SaleController.php`
- **Line:** 925
- **Problem:** Uses only unit_type

---

## 14. RECOMMENDED FIXES

### Fix #1: Purchase Return KG Conversion (CRITICAL)
**File:** `PurchaseController.php` lines 906-913
**Action:** KG items ke liye `* 1000` add karo

**Current:**
```php
$stock->qty -= $qty;
```

**Sahi:**
```php
$productRecord = Product::find($productId);
$isKg = $productRecord->unit_type === 'kg';
$stock->qty -= $isKg ? ($qty * 1000) : $qty;
```

### Fix #2: After-Period Sales Returns Matching (MEDIUM)
**File:** `ReportingController.php` lines 258-277
**Action:** Product name matching improve karo

**Suggestion:** `sales_returns.product` column mein sirf product name store karo, extra info hatao. Ya phir fuzzy matching use karo.

### Fix #3: KG Detection Unification (LOW)
**File:** `PurchaseController.php` lines 217-219
**Action:** Sirf `unit_type === 'kg'` use karo, string matching hatao

---

## FINAL STATUS

| Item | Status |
|---|---|
| Double Counting | ✅ NAHI HAI |
| Purchase Column | ✅ SAHI hai |
| Production Column | ✅ SAHI hai |
| Purchase Return Column | ✅ SAHI hai (report calculation) |
| Purchase Return Stock | ❌ GALAT hai (KG conversion missing) |
| Sold Column | ✅ SAHI hai |
| Sale Return Column | ✅ SAHI hai (within-period) |
| After-Period Sale Returns | ⚠️ Matching issue ho sakta hai |
| Transfer In/Out | ✅ SAHI hai |
| Adjustment Columns | ✅ SAHI hai |
| Balance Column | ✅ SAHI hai (after fix) |
| Initial Stock Column | ✅ SAHI hai (after fix) |
| Reset Boundary | ✅ SAHI hai |
| Branch Filtering | ✅ SAHI hai |
| Warehouse Filtering | ⚠️ Sales mein warehouse filter missing |

---

**AUDIT END — NO CHANGES MADE**