# RESET ALL STOCK — COMPLETE AUDIT REPORT
## Reset Feature aur Item Stock Report Ka Relationship
### Date: 10 September 2026

---

## 1. RESET ALL STOCK EXACTLY KYA KARTA HAI?

### Implementation: `ProductController.php` Line 983-1027

Reset All Stock yeh 5 cheezein karta hai:

```
Step 1: stocks table → qty = 0, reserved_qty = 0 (sirf active branch)
Step 2: warehouse_stocks table → quantity = 0 (sirf active branch)
Step 3: product_variants table → stock_qty = 0 (SAARI branches — BUG!)
Step 4: products table → initial_stock = 0 (SAARI products — BUG!)
Step 5: storage/stock_reset_timestamp.txt → current timestamp save karta hai
```

### Key Points:
- **Transaction history PRESERVED hai** — sales, purchases, ledgers delete nahi hote
- **Sirf current stock ZERO hota hai**
- **Ek timestamp file save hota hai** — `storage/app/stock_reset_timestamp.txt`
- **No database record** — sirf text file hai, koi `stock_closing` ya `stock_reset` table nahi hai

---

## 2. RESET TIMESTAMP Kahan STORE HOTA HAI?

**File:** `storage/app/stock_reset_timestamp.txt`
**Content:** `2026-09-09 23:07:53`

### Current Status:
- Reset **HO CHUKA HAI** — Sep 9, 23:07:53
- File exists: **YES**
- Timestamp: `2026-09-09 23:07:53`

### ⚠️ Architectural Issue:
- File **global hai**, branch-specific nahi
- Agar Branch 2 reset kare toh Branch 1 ka timestamp overwrite ho jayega
- Koi database record nahi — file delete ho gayi toh reset ka pata nahi chalega

---

## 3. ITEM STOCK REPORT RESET KO KAISE HANDLE KARTA HAI?

### `ReportingController.php` Line 39-43:
```php
$resetTime = null;
if (Storage::exists('stock_reset_timestamp.txt')) {
    $resetTime = trim(Storage::get('stock_reset_timestamp.txt'));
}
```

### Har Transaction Query Mein Filter:
```php
if ($resetTime) {
    $query->where('created_at', '>=', $resetTime);
}
```

**Yeh filter IN-DATE transactions AND after-period transactions DONO pe lagta hai.**

### Kya Yeh Sahi Hai?
**PARTIAL YES** — Reset ke baad ke transactions count hote hain. ❌ Lekin purane transactions completely ignore ho jaate hain jo reporting period mein aate hain.

---

## 4. REAL DATA: PRODUCT 227 RESET FLOW

### Timeline:
```
Sep 1-9 (23:07:53): PURANE TRANSACTIONS (before reset)
Sep 9 (23:07:53):   RESET → stocks.qty = 0
Sep 9 (23:08+):     NAYE TRANSACTIONS (after reset)
Sep 10:              More transactions
```

### BEFORE RESET (Excluded from Report):
| Transaction | Amount |
|---|---|
| Purchases | 230 |
| Sales | 615 |
| Production | 65 |
| Purchase Returns | 30 |
| Adjustments | Various |
| Transfers | Various |

### AFTER RESET (Included in Report):
| Transaction | Amount | Created |
|---|---|---|
| Purchase (ID:56) | 20 | Sep 10, 19:19 |
| Purchase Return (PRID:4) | 8 | Sep 10, 19:24 |
| Sale (ID:26414) | 10 | Sep 9, 23:08 |
| Sale (ID:26415) | 10 | Sep 9, 23:28 |
| Sale Return (SRID:423) | 5 | Sep 9, 23:09 |
| Sale Return (SRID:424) | 4 | Sep 9, 23:28 |
| Adj Increase (ADJ:21) | 40 | Sep 10, 19:32 |
| Transfer Out (ID:13) | 15 | Sep 10, 20:10 |
| **NET** | **+26** | |

### Post-Reset Calculation:
```
Starting: stocks.qty = 0 (after reset)
+ Purchases: 20
+ Production: 0
- Purchase Returns: 8
- Sales: 20
+ Sale Returns: 9
+ Adj Increase: 40
- Adj Decrease: 0
+ Transfer In: 0
- Transfer Out: 15
= Expected Closing: 26
```

### Actual stocks.qty = 32
### Difference: 32 - 26 = **+6 units unaccounted**

**Yeh 6 units kahan se aaye?** Possible causes:
1. Sale returns jo stock mein add hue lekin report ki query se miss hue
2. Sale controller jo warehouse stock se deduct kar raha hai shop stock ki jagah
3. Koi aur transaction jo track mein nahi hai

---

## 5. REPORT KA CALCULATION — STEP BY STEP

### Report Settings:
- Period: Sep 1-10
- ResetTime: Sep 9, 23:07:53
- Current stocks.qty: 32

### Step 1: closingStock Formula (Line 538/643):
```php
$closingStock = $balance - $purchAft - $prodAft - $sRetAft + $soldAft + $prAft - $pReturn - $adjIncAft + $adjDecAft - $transferInAft + $transferAft;
```

**Variables for Product 227:**
| Variable | Value | Source |
|---|---|---|
| $balance | 32 | stocks.qty |
| $purchAft | 0 | Purchases after Sep 10 AND after reset = 0 |
| $prodAft | 0 | Production after Sep 10 AND after reset = 0 |
| $sRetAft | 0 | Sale returns after Sep 10 AND after reset = 0 |
| $soldAft | 0 | Sales after Sep 10 AND after reset = 0 |
| $prAft | 0 | Purchase returns after Sep 10 AND after reset = 0 |
| **$pReturn** | **8** | **WITHIN-period purchase return (BUG: shouldn't be here)** |
| $adjIncAft | 0 | Adjustments after Sep 10 AND after reset = 0 |
| $adjDecAft | 0 | Same |
| $transferInAft | 0 | Same |
| $transferAft | 0 | Same |

### closingStock = 32 - 0 - 0 - 0 + 0 + 0 - 8 - 0 + 0 - 0 + 0 = **24** ❌
### Sahi closingStock = **32** ✅

**BUG: `- $pReturn` (8) ghalat tarike se closingStock mein se subtract ho raha hai.**

---

### Step 2: openingStock Formula (Line 539/644):
```php
$openingStock = $closingStock - $purchased - $produced - $sReturn - $transferInQty - $adjInc + $sold + $pReturn + $transferQty + $adjDec;
```

**Variables (post-reset only):**
| Variable | Value |
|---|---|
| $purchased | 20 |
| $produced | 0 |
| $pReturn | 8 |
| $sold | 20 |
| $sReturn | 9 |
| $adjInc | 40 |
| $adjDec | 0 |
| $transferQty | 15 |
| $transferInQty | 0 |

### With Bug:
```
openingStock = 24 - 20 - 0 - 9 - 0 - 40 + 20 + 8 + 15 + 0
openingStock = 24 - 69 + 43
openingStock = -2 ❌
```

### Without Bug (sahi closingStock = 32):
```
openingStock = 32 - 20 - 0 - 9 - 0 - 40 + 20 + 8 + 15 + 0
openingStock = 32 - 69 + 43
openingStock = 6 ❌ (expected 0)
```

### What It Should Be:
```
Initial Stock = 0 (reset happened during period)
Balance = 32 (stocks.qty)
```

**PROBLEM: Report 0 ki jagah -2 ya 6 dikha raha hai. Reset boundary correctly handle nahi ho raha.**

---

## 6. KYA OLD TRANSACTIONS DOBARA COUNT HO RAHI HAIN?

### ❌ DOUBLE COUNTING NAHI HAI (resetTime filter ki wajah se)

Report sirf `created_at >= resetTime` wale transactions count karta hai. Pre-reset transactions (230 purchases, 615 sales, etc.) report mein nahi aa rahe.

**Example:**
- PurchaseID:55 (Sep 9, 19:22, Qty:15) — **BEFORE reset** — ❌ Report mein nahi
- PurchaseID:56 (Sep 10, 19:19, Qty:20) — **AFTER reset** — ✅ Report mein hai

**Yeh SAHI hai.** ✅

---

## 7. PROBLEM: REPORT RESET BOUNDARY NAHI SAMAJhta

### Current Behavior:
```
Report Period: Sep 1-10
Reset Time: Sep 9, 23:07:53

Report shows:
  Initial = -2 (WRONG — should be 0)
  Balance = 24 (WRONG — should be 32)
```

### Expected Behavior:
```
Report Period: Sep 1-10
Reset Time: Sep 9, 23:07:53

Report should show:
  Initial = 0 (reset happened during period)
  Purchases = 20 (post-reset only)
  Sales = 20 (post-reset only)
  Balance = 32 (stocks.qty — post-reset cumulative)
```

### Why This Happens:
1. Report uses backward calculation from stocks.qty
2. stocks.qty = 32 (post-reset cumulative, CORRECT)
3. But closingStock formula has `- $pReturn` bug → closingStock = 24
4. openingStock = 24 - post_reset_transactions = -2
5. Report doesn't know that a reset happened → doesn't set Initial = 0

---

## 8. COMPLETE FLOW DIAGRAM

```
┌─────────────────────────────────────────────────┐
│              RESET ALL STOCK                     │
│  stocks.qty → 0 for all products                 │
│  timestamp → 2026-09-09 23:07:53                │
└─────────────────────┬───────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────┐
│         POST-RESET TRANSACTIONS                  │
│  Purchase: +20                                   │
│  Sales: -20                                      │
│  Sale Return: +9                                 │
│  Purchase Return: -8                             │
│  Adj Increase: +40                               │
│  Transfer Out: -15                               │
│  Net: +26                                        │
└─────────────────────┬───────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────┐
│         stocks.qty = 0 + 26 = 26                │
│         ACTUAL: 32 (difference of 6)             │
└─────────────────────┬───────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────┐
│         ITEM STOCK REPORT                        │
│  1. Reads stocks.qty = 32                        │
│  2. Reverses after-period transactions (0)       │
│  3. closingStock = 32 - 0 - 8 (pReturn bug)     │
│  4. openingStock = 24 - post_reset_txns = -2     │
│  5. Shows Initial = -2 ❌ (should be 0)          │
│  6. Shows Balance = 24 ❌ (should be 32)         │
└─────────────────────────────────────────────────┘
```

---

## 9. ROOT CAUSES (3 SEPARATE ISSUES)

### Issue #1: `- $pReturn` Bug in closingStock Formula
**File:** `ReportingController.php` Lines 538, 643
**Problem:** Within-period $pReturn ghalat tarike se closingStock mein se subtract ho raha hai
**Impact:** Balance column -8 kam, Initial column bhi galat
**Fix:** `- $pReturn` hatao

### Issue #2: Report Reset Boundary awareness
**File:** `ReportingController.php` Lines 538-539, 643-644
**Problem:** Report nahi jaanta ke reset hua hai. Backward calculation se fake Initial Stock aa raha hai
**Impact:** Initial Stock galat aa raha hai
**Fix:** Agar resetTime hai aur report period reset ke baad start hota hai toh Initial = 0

### Issue #3: stocks.qty Difference of 6
**Problem:** Expected 26, Actual 32. 6 units kahan se aaye?
**Possible Causes:**
- Sale returns jo stock mein add hue
- Sale controller warehouse stock se deduct kar raha hai
- Koi aur tracking issue

---

## 10. ARCHITECTURAL ISSUES WITH RESET FEATURE

### Issue A: Timestamp File is Global (Not Branch-Specific)
- `stock_reset_timestamp.txt` sirf ek file hai
- Agar Branch 2 reset kare toh Branch 1 ka timestamp overwrite ho jayega
- **Fix:** Branch-wise timestamp store karo ya database table banao

### Issue B: product_variants Reset is Global
```php
DB::table('product_variants')->update(['stock_qty' => 0]);
// ❌ SAARI branches ka stock reset ho raha hai!
```
- **Fix:** Branch-wise filter lagao

### Issue C: products.initial_stock Reset is Global
```php
DB::table('products')->update(['initial_stock' => 0]);
// ❌ SAARI products ka initial_stock reset ho raha hai!
```
- **Fix:** Branch-wise filter lagao ya sirf active branch ki products reset karo

### Issue D: No Database Record of Reset
- Sirf text file hai — no audit trail
- Agar file delete ho gayi toh reset ka pata nahi chalega
- **Fix:** `stock_resets` table banao with: id, branch_id, reset_at, performed_by

### Issue E: No Stock Closing Statement
- Reset sirf qty=0 karta hai
- Koi "closing statement" nahi banta jo dikhaye ke reset pe kitna stock tha
- **Fix:** Reset se pehle `stock_closing` record banao with: product_id, branch_id, closing_qty, reset_at

---

## 11. EXPECTED BEHAVIOR AFTER RESET

### Scenario:
```
1. Reset at Sep 9, 23:07:53 → stocks.qty = 0
2. Purchase: 50 (Sep 10)
3. Sale: 20 (Sep 10)
4. Sale Return: 5 (Sep 10)
```

### Expected Report (Period Sep 10-15):
```
Initial Stock: 0 (reset boundary)
Purchased: 50
Sold: 20
Sale Return: 5
Balance: 35 (0 + 50 - 20 + 5)
```

### Current Report (With Bugs):
```
Initial Stock: -35 ❌ (backward calculation from stocks.qty)
Purchased: 50
Sold: 20
Sale Return: 5
Balance: 0 ❌ (closingStock formula bug)
```

---

## 12. RECOMMENDED SAFE FIX

### Fix #1: Remove `- $pReturn` from closingStock (CRITICAL)
**File:** `ReportingController.php` Lines 538, 643
**Action:** `- $pReturn` hatao
**Impact:** Balance column sahi hoga

### Fix #2: Add Reset Awareness to Report (IMPORTANT)
**File:** `ReportingController.php` Lines 538-539, 643-644
**Logic:**
```php
// Agar reset hua hai aur start_date reset ke baad hai
// Toh openingStock = 0 (kyunki naya cycle start hua)
if ($resetTime && $startDate >= substr($resetTime, 0, 10)) {
    $openingStock = 0;
    $closingStock = $balance; // stocks.qty is the truth
} else {
    // Normal backward calculation
    $closingStock = $balance - $purchAft - ... ; // (without $pReturn bug)
    $openingStock = $closingStock - $purchased - ... ;
}
```

### Fix #3: Create stock_resets Table (ARCHITECTURAL)
```sql
CREATE TABLE stock_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    reset_at DATETIME NOT NULL,
    performed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Fix #4: Branch-Specific Timestamp (ARCHITECTURAL)
```php
// Instead of global file:
Storage::put("stock_reset_timestamp_{$branchId}.txt", now()->toDateTimeString());
// Or use database table
```

### Fix #5: Branch-Specific Variant/Product Reset (BUG FIX)
```php
// Current (WRONG):
DB::table('product_variants')->update(['stock_qty' => 0]);

// Fixed:
DB::table('product_variants')
    ->whereIn('product_id', function($q) use ($branchId) {
        $q->select('product_id')->from('stocks')->where('branch_id', $branchId);
    })
    ->update(['stock_qty' => 0]);
```

---

## 13. SUMMARY

| # | Finding | Status |
|---|---|---|
| 1 | Reset All Stock sirf qty=0 karta hai, history preserved | ✅ Correct |
| 2 | Reset timestamp file store hota hai | ✅ Correct |
| 3 | Report resetTime filter lagata hai — pre-reset transactions excluded | ✅ Correct |
| 4 | **ClosingStock formula mein `- $pReturn` bug hai** | ❌ BUG |
| 5 | **Report reset boundary nahi samajhta** — fake Initial Stock | ❌ BUG |
| 6 | **Koi double counting nahi hai** | ✅ Correct |
| 7 | **Reset timestamp global hai, branch-specific nahi** | ⚠️ Issue |
| 8 | **product_variants/products reset global hai** | ⚠️ Issue |
| 9 | **Koi database record nahi hai reset ka** | ⚠️ Issue |
| 10 | **stocks.qty difference of 6** — needs investigation | ⚠️ Issue |

---

**AUDIT END.**
