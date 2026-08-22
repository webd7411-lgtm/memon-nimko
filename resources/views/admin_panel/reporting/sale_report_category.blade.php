@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
  --rp-bg: #f8fafc;
  --rp-surface: #ffffff;
  --rp-border: #e2e8f0;
  --rp-border-lt: #f1f5f9;
  --rp-text: #0f172a;
  --rp-text-sec: #475569;
  --rp-text-muted: #64748b;
  --rp-primary: #7c3aed;
  --rp-radius: 16px;
  --rp-radius-sm: 10px;
  --rp-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  --rp-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
  --rp-font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.rp-cat * {
  font-family: var(--rp-font);
}

.rp-cat {
  background-color: var(--rp-bg);
  min-height: 100vh;
  padding-bottom: 3rem;
}

/* ═══════ HERO HEADER ═══════ */
.rp-hdr {
  position: relative;
  background: linear-gradient(135deg, #2e1065 0%, #4c1d95 50%, #6d28d9 100%);
  border-radius: var(--rp-radius);
  padding: 1.5rem 1.75rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 20px 30px -10px rgba(46, 16, 101, 0.3);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  overflow: hidden;
}

.rp-hdr::before {
  content: '';
  position: absolute;
  inset: 0;
  background: 
    radial-gradient(circle at 10% 90%, rgba(124, 58, 237, 0.3) 0%, transparent 60%),
    radial-gradient(circle at 90% 10%, rgba(236, 72, 153, 0.2) 0%, transparent 50%);
  pointer-events: none;
}

.rp-hdr > * {
  position: relative;
  z-index: 1;
}

.rp-hdr-title {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}

.rp-hdr-title h2 {
  font-size: 1.45rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0;
  letter-spacing: -0.02em;
}

.rp-hdr-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c4b5fd;
  font-size: 1.4rem;
}

.rp-hdr-badge {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 30px;
  padding: 0.3rem 0.9rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #ddd6fe;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.rp-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.15rem;
  border-radius: var(--rp-radius-sm);
  font-size: 0.85rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.rp-btn-primary {
  background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);
}
.rp-btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(124, 58, 237, 0.45);
}

.rp-btn-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: #ffffff !important;
  box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
}
.rp-btn-danger:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(239, 68, 68, 0.45);
}

.rp-btn-glass {
  background: rgba(255, 255, 255, 0.12);
  color: #ffffff !important;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}
.rp-btn-glass:hover {
  background: rgba(255, 255, 255, 0.22);
  transform: translateY(-1px);
}

/* ═══════ CARD & CONTROLS ═══════ */
.rp-card {
  background: var(--rp-surface);
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius);
  box-shadow: var(--rp-shadow);
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.rp-card-body { padding: 1.35rem; }

.rp-label {
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: var(--rp-text-sec);
  margin-bottom: 0.35rem;
  display: block;
}

.rp-input, .rp-select {
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius-sm);
  padding: 0.55rem 0.85rem;
  font-size: 0.88rem;
  color: var(--rp-text);
  background: #ffffff;
  transition: all 0.2s ease;
  width: 100%;
}
.rp-input:focus, .rp-select:focus {
  border-color: var(--rp-primary);
  box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
  outline: none;
}

.rp-chk input { accent-color: var(--rp-primary); width: 16px; height: 16px; }
.rp-chk .form-check-label { font-size: 0.85rem; font-weight: 600; color: var(--rp-text); }

/* ═══════ DATA TABLE ═══════ */
.rp-table-card {
  background: var(--rp-surface);
  border: 1px solid var(--rp-border);
  border-radius: var(--rp-radius);
  box-shadow: var(--rp-shadow-lg);
  overflow: hidden;
}

.rp-table-wrapper {
  overflow-x: auto;
  padding: 0 1rem 1rem 1rem;
  -webkit-overflow-scrolling: touch;
}

.rp-table {
  width: 100% !important;
  margin: 0 !important;
  border-collapse: separate;
  border-spacing: 0;
}

.rp-table thead th {
  background: #f8fafc;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--rp-text-muted);
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--rp-border);
  border-top: none;
  white-space: nowrap;
}

.rp-table tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid var(--rp-border-lt);
  font-size: 0.88rem;
  color: var(--rp-text-sec);
}

.rp-table tbody tr:hover td {
  background-color: #f8fafc;
}

.return-cell {
  max-width: 200px;
  max-height: 90px;
  overflow-y: auto;
  overflow-x: hidden;
  white-space: normal;
  font-size: 0.78rem;
  line-height: 1.4;
  background: #f8fafc;
  border: 1px solid var(--rp-border-lt);
  border-radius: 6px;
  padding: 6px;
  scrollbar-width: thin;
}

.rp-loader { display: none; text-align: center; padding: 1.5rem 0; }
.rp-loader .spinner-border { color: var(--rp-primary); width: 2.2rem; height: 2.2rem; }

/* Desktop: fixed layout = table NEVER overflows / no horizontal scroll */
.rp-table { table-layout: fixed; width: 100% !important; }
.rp-table thead th { white-space: normal !important; overflow-wrap: break-word; }
.rp-table tbody td { white-space: normal !important; overflow-wrap: break-word; word-break: normal; }
.rp-table-wrapper { overflow-x: hidden; }

/* ═══════ MOBILE / TABLET PREMIUM CARDS ═══════ */
.rpc-cards { display: none; }

.rpc-card {
  background: #fff;
  border: 1.5px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
  margin-bottom: .85rem;
  overflow: hidden;
}
.rpc-head {
  display: flex; align-items: center; justify-content: space-between; gap: .6rem;
  padding: .8rem .9rem .7rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-bottom: 1px solid #e9edf2;
}
.rpc-head-l { display: flex; flex-direction: column; min-width: 0; gap: 3px; }
.rpc-inv { font-size: .96rem; font-weight: 800; color: #0f172a; letter-spacing: -.2px; word-break: normal; overflow-wrap: break-word; line-height: 1.25; }
.rpc-sub { display: flex; align-items: center; gap: 5px; font-size: .75rem; color: #64748b; font-weight: 600; word-break: normal; overflow-wrap: break-word; }
.rpc-sub i { color: #94a3b8; font-size: .82rem; }
.rpc-id { flex: 0 0 auto; font-size: .74rem; font-weight: 800; color: #6d28d9; background: #f3e8ff; border: 1px solid #e9d5ff; border-radius: 20px; padding: .24rem .65rem; white-space: nowrap; }

.rpc-meta { display: flex; flex-wrap: wrap; gap: .4rem; padding: .6rem .9rem; }
.rpc-meta span {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: .72rem; color: #475569; font-weight: 600;
  word-break: normal; overflow-wrap: break-word; line-height: 1.3;
  background: #f8fafc; border: 1px solid #eef2f7; border-radius: 8px;
  padding: .28rem .6rem;
}
.rpc-meta span i { color: #7c3aed; font-size: .78rem; }

.rpc-items { margin: .1rem .9rem .7rem; border: 1px solid #e9edf2; border-radius: 12px; overflow: hidden; }
.rpc-items-hd {
  display: flex; align-items: center; justify-content: space-between;
  padding: .5rem .7rem; background: #f8fafc; border-bottom: 1px solid #e9edf2;
  font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #475569;
}
.rpc-items-hd i { color: #7c3aed; font-size: .82rem; }
.rpc-items-hd b { color: #7c3aed; background: #f3e8ff; border-radius: 20px; padding: .03rem .5rem; font-size: .66rem; }
.rpc-item {
  display: flex; flex-wrap: wrap; align-items: center; gap: .3rem .55rem;
  padding: .55rem .7rem; background: #fff; border-bottom: 1px solid #f1f5f9;
}
.rpc-item:last-child { border-bottom: none; }
.rpc-item-nm { flex: 1 1 100%; min-width: 0; font-size: .84rem; font-weight: 600; color: #0f172a; word-break: normal; overflow-wrap: break-word; line-height: 1.4; }
.rpc-item-unit { font-size: .66rem; font-weight: 700; color: #7c3aed; background: #f3e8ff; border-radius: 6px; padding: .12rem .4rem; white-space: nowrap; }
.rpc-item-qty { flex: 0 0 auto; font-size: .72rem; font-weight: 700; color: #475569; background: #f1f5f9; border-radius: 7px; padding: .22rem .55rem; white-space: nowrap; }
.rpc-item-total { flex: 0 0 auto; font-size: .8rem; font-weight: 800; color: #0f172a; white-space: nowrap; margin-left: auto; }

.rpc-net {
  display: flex; align-items: center; justify-content: space-between; gap: .6rem;
  margin: 0 .9rem .55rem; padding: .6rem .8rem;
  background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
  border: 1px solid #ddd6fe; border-radius: 12px;
}
.rpc-net span { font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #5b21b6; }
.rpc-net b { font-size: 1rem; font-weight: 900; color: #6d28d9; word-break: normal; overflow-wrap: break-word; text-align: right; }

.rpc-returns { margin: 0 .9rem .7rem; border: 1px solid #fecaca; border-radius: 12px; overflow: hidden; background: #fef2f2; }
.rpc-returns-hd {
  display: flex; align-items: center; gap: 6px;
  padding: .45rem .7rem; background: #fee2e2; border-bottom: 1px solid #fecaca;
  font-size: .66rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #b91c1c;
}
.rpc-returns-body { padding: .15rem .7rem .4rem; }
.rpc-returns-body .rpc-item { background: transparent; }
.rpc-returns-body hr { margin: .3rem 0; }
.rpc-returns-body small,
.rpc-returns-body span { word-break: normal; overflow-wrap: break-word; }

.rpc-grand { border-color: #c4b5fd; }
.rpc-grand .rpc-net { margin-bottom: 0; border-radius: 0; border: none; border-top: 1px solid #ddd6fe; background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); }
.rpc-grand .rpc-net b { font-size: 1.05rem; }
.rpc-grand .rpc-returns { margin-top: .55rem; }

.rpc-empty {
  text-align: center; padding: 2.5rem 1rem; color: #64748b;
  display: flex; flex-direction: column; align-items: center; gap: .4rem;
}
.rpc-empty i { font-size: 2.2rem; color: #cbd5e1; }

/* ═══════ FULL MOBILE RESPONSIVE (no horizontal scroll) ═══════ */
@media (max-width: 991.98px) {
  .rp-cat { overflow-x: hidden; }
  .rp-cat, .rp-cat .container-fluid { max-width: 100%; }

  .rp-hdr { padding: 1rem; flex-direction: column; align-items: stretch; gap: .8rem; }
  .rp-hdr-title { gap: .7rem; }
  .rp-hdr-title h2 { font-size: 1.12rem; }
  .rp-hdr-icon { width: 42px; height: 42px; font-size: 1.15rem; }
  .rp-hdr .d-flex { width: 100%; }
  .rp-hdr .d-flex .rp-btn { flex: 1 1 auto; justify-content: center; padding: .6rem .45rem; font-size: .78rem; }

  .rp-card-body { padding: 1rem; }

  /* Hide actual table on mobile, render premium cards instead */
  #saleReport { display: none !important; }
  .rpc-cards { display: block; padding: .5rem .6rem 1rem; }
  .rp-table-wrapper { overflow: visible; padding: 0; }
}
</style>

<div class="rp-cat">
  <div class="container-fluid px-3 px-md-4 py-3">

    {{-- ═══════ HERO HEADER ═══════ --}}
    <div class="rp-hdr">
      <div class="rp-hdr-title">
        <div class="rp-hdr-icon">
          <i class="bi bi-grid-1x2"></i>
        </div>
        <div>
          <h2>Category-Wise Sale Report</h2>
          <span class="rp-hdr-badge">Category & Sub-Category Sales Breakdown</span>
        </div>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ url()->previous() }}" class="rp-btn rp-btn-glass">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    {{-- ═══════ FILTER CARD ═══════ --}}
    <div class="rp-card">
      <div class="rp-card-body">
        <form id="SaleFilterForm" class="row g-3 align-items-end">
          <div class="col-12 col-md-2">
            <label class="rp-label"><i class="bi bi-tags me-1 text-primary"></i> Category Name</label>
            <select name="category_id" id="category_id" class="rp-select">
              <option value="">All Categories</option>
              @foreach ($categories as $item)
              <option value="{{ $item->id }}">{{ $item->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="rp-label"><i class="bi bi-tag me-1 text-primary"></i> Sub Category</label>
            <select name="subcategory_id" id="subcategory_id" class="rp-select">
              <option value="">All Sub Categories</option>
            </select>
          </div>

          <div class="col-12 col-md-2">
            <label class="rp-label"><i class="bi bi-calendar-event me-1 text-primary"></i> Start Date & Time</label>
            <input type="datetime-local" name="start_date" id="start_date" class="rp-input" value="{{ date('Y-m-01\T00:00') }}">
          </div>

          <div class="col-12 col-md-2">
            <label class="rp-label"><i class="bi bi-calendar-event-fill me-1 text-primary"></i> End Date & Time</label>
            <input type="datetime-local" name="end_date" id="end_date" class="rp-input" value="{{ date('Y-m-d\T23:59') }}">
          </div>

          <div class="col-12 col-md-2">
            <label class="rp-label"><i class="bi bi-people me-1 text-primary"></i> Customer Type</label>
            <div class="d-flex gap-2 rp-chk pt-1">
              <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="customer_type[]" value="Retailer" id="type_retailer">
                <label class="form-check-label" for="type_retailer">Retailer</label>
              </div>
              <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="customer_type[]" value="Walking Customer" id="type_walking" checked>
                <label class="form-check-label" for="type_walking">Walking</label>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-2 d-flex gap-2">
            <button type="button" id="btnSearch" class="rp-btn rp-btn-primary w-100">
              <i class="bi bi-search me-1"></i> Search
            </button>
            <button type="button" id="btnExportCsv" class="rp-btn rp-btn-danger w-100">
              <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- ═══════ DATA TABLE CARD ═══════ --}}
    <div class="rp-table-card">
      <div class="rp-card-body p-0">
        <div id="loader" class="rp-loader">
          <div class="spinner-border" role="status"></div>
          <p class="mt-2 text-muted small">Fetching category sale analytics...</p>
        </div>

        <div class="rp-table-wrapper">
          <table class="table rp-table align-middle nowrap" id="saleReport" style="width:100%;">
            <thead>
              <tr>
                <th width="4%">#</th>
                <th width="10%">Date</th>
                <th width="10%">Invoice</th>
                <th width="12%">Customer</th>
                <th width="8%">Reference</th>
                <th width="14%">Products</th>
                <th width="10%">Category</th>
                <th width="10%">Sub Category</th>
                <th width="6%">Unit</th>
                <th width="6%">Qty</th>
                <th width="8%">Price</th>
                <th width="8%">Total</th>
                <th width="8%">Net</th>
                <th width="10%">Returns</th>
              </tr>
            </thead>
            <tbody id="saleBody"></tbody>
          </table>
        </div>

        {{-- ═══════ MOBILE / TABLET PREMIUM CARDS (built from same AJAX result) ═══════ --}}
        <div id="categoryCards" class="rpc-cards"></div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
function populateSubcategories(catId) {
    let $sub = $('#subcategory_id');
    $sub.empty();
    $sub.append('<option value="">All Sub Categories</option>');

    if (!catId) return;

    $.ajax({
        url: "{{ route('fetch-subcategories', '') }}/" + catId,
        type: "GET",
        dataType: "json",
        success: function(data) {
            if (Array.isArray(data)) {
                 data.forEach(s => {
                    $sub.append(`<option value="${s.id}">${s.name}</option>`);
                });
            }
        },
        error: function(xhr) {
            console.error("Failed to fetch subcategories:", xhr);
        }
    });
}

$('#category_id').on('change', function() {
    populateSubcategories($(this).val());
});

$(document).on('click', '#btnSearch', function() {
    let start = $('#start_date').val();
    let end = $('#end_date').val();
    let category = $('#category_id').val();
    let subcategory = $('#subcategory_id').val();

    $("#loader").show();
    $.ajax({
        url: "{{ route('report.sale.category.fetch') }}",
        type: "GET",
        data: {
            start_date: start,
            end_date: end,
            category_id: category,
            subcategory_id: subcategory,
            customer_type: $('input[name="customer_type[]"]:checked').map(function(){return $(this).val();}).get(),
            _t: new Date().getTime()
        },
        success: function(res) {
            $("#loader").hide();
            let html = "";
            let cardsHtml = "";

            function esc(str) {
                var d = document.createElement('div');
                d.textContent = str == null ? '' : String(str);
                return d.innerHTML;
            }

            function num(v) {
                if (v === null || v === undefined) return 0;
                if (typeof v === 'number') return v;
                v = String(v).replace(/[^0-9.\-]/g, '');
                const f = parseFloat(v);
                return isNaN(f) ? 0 : f;
            }

            function sumArray(arr) {
                return arr.reduce((a, b) => a + num(b), 0);
            }

            function formatDate(dateString) {
                if (!dateString) return '-';
                const d = new Date(dateString);
                if (isNaN(d)) return dateString;
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = d.getFullYear();
                return `${day}-${month}-${year}`;
            }

            let grandQty = 0,
                grandTotal = 0,
                grandNet = 0,
                grandReturnQty = 0,
                grandReturnAmount = 0;
            
            let grandPiece = 0;
            let grandKG = 0;
            let grandPound = 0;
            let grandMeter = 0;
            let grandYard = 0;

            (res || []).forEach((s, i) => {
                let products = s.product_names ? s.product_names.split('|').map(p => p.trim()).join('<br>') : '-';
                let categories = s.categories ? s.categories.split(',').map(c => c.trim()).join('<br>') : '-';
                let subcategories = s.subcategories ? s.subcategories.split(',').map(c => c.trim()).join('<br>') : '-';

                const qtyArrRaw = (s.filtered_qty || '').toString().trim();
                const qtyArr = qtyArrRaw.length ? qtyArrRaw.split('|').map(x => x.trim()) : [];
                const qtyArrNums = qtyArr.map(num);
                const rowQty = sumArray(qtyArrNums);
                grandQty += rowQty;

                const unitArrRaw = (s.filtered_unit || '').toString().trim();
                const unitArr = unitArrRaw.length ? unitArrRaw.split('|').map(x => x.trim()) : [];
                
                if (unitArr.length === qtyArr.length) {
                    unitArr.forEach((u, idx) => {
                        const q = qtyArrNums[idx];
                        const uLower = u.toLowerCase();
                        if (uLower.includes('piece') || uLower.includes('pcs')) grandPiece += q;
                        else if (uLower.includes('kg')) grandKG += q;
                        else if (uLower.includes('pound')) grandPound += q;
                        else if (uLower.includes('meter')) grandMeter += q;
                        else if (uLower.includes('yard')) grandYard += q;
                    });
                }

                const priceDisplay = (s.filtered_price || '') ?
                    s.filtered_price.toString().split('|').map(p => p.trim()).join('<br>') : '-';
                
                const perTotalRaw = (s.filtered_total || '').toString().trim();
                const perTotalArr = perTotalRaw.length ? perTotalRaw.split('|').map(x => x.trim()) : [];
                const perTotalNums = perTotalArr.map(num);
                const rowTotal = sumArray(perTotalNums);
                grandTotal += rowTotal;

                const rowNet = num(s.filtered_net);
                grandNet += rowNet;

                let returnHtml = '';
                let returnQtyTotal = 0;
                let returnAmountTotal = 0;

                if (s.returns) {
                    if (Array.isArray(s.returns)) {
                        const lines = s.returns.map(r => {
                            const rQty = num(r.qty);
                            const rAmt = num(r.per_total || r.amount || r.total);
                            returnQtyTotal += rQty;
                            returnAmountTotal += rAmt;
                            return `<small class="fw-bold text-dark">${(r.product||'').toString().trim()}</small><br><span class="text-danger">Qty: ${rQty}</span> | <span class="fw-bold text-dark">Rs ${rAmt.toFixed(2)}</span>`;
                        });
                        returnHtml = lines.join('<hr class="my-1 text-muted">');
                    }
                }

                grandReturnQty += returnQtyTotal;
                grandReturnAmount += returnAmountTotal;

                const createdAt = s.created_at || s.date || s.sale_date || s.created || '';

                html += `<tr>
                    <td class="fw-bold">#${i+1}</td>
                    <td class="text-muted small">${formatDate(createdAt)}</td>
                    <td><span class="badge bg-light text-dark border font-monospace">${s.invoice_no??'-'}</span></td>
                    <td class="fw-bold text-dark">${s.customer_name??'-'}</td>
                    <td><span class="badge bg-secondary-subtle text-secondary">${s.reference??'-'}</span></td>
                    <td class="fw-medium">${products}</td>
                    <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">${categories}</span></td>
                    <td><span class="badge bg-info-subtle text-info border border-info-subtle">${subcategories}</span></td>
                    <td>${unitArr.length ? unitArr.join('<br>') : '-'}</td>
                    <td>${qtyArr.length ? qtyArr.map((x, idx) => (num(x) ? num(x).toFixed(2) + ' <small class="text-muted">' + (unitArr[idx]||'') + '</small>' : '0.00')).join('<br>') : '-'}</td>
                    <td>${priceDisplay}</td>
                    <td>${perTotalArr.length ? perTotalArr.map(x=>num(x).toFixed(2)).join('<br>') : '-'}</td>
                    <td class="fw-bold text-success">Rs ${rowNet.toFixed(2)}</td>
                    <td><div class="return-cell">${returnHtml||'-'}</div></td>
                </tr>`;

                let itemRows = '';
                const prodArr = products !== '-' ? products.split('<br>') : [];
                const priceArr = priceDisplay !== '-' ? priceDisplay.split('<br>') : [];
                prodArr.forEach(function (p, idx) {
                    const u = unitArr[idx] || '';
                    const q = qtyArr[idx] !== undefined ? num(qtyArr[idx]).toFixed(2) : '-';
                    const t = perTotalArr[idx] !== undefined ? num(perTotalArr[idx]).toFixed(2) : '';
                    itemRows += '<div class="rpc-item">'
                        + '<span class="rpc-item-nm">' + esc(p) + (u ? ' <span class="rpc-item-unit">' + esc(u) + '</span>' : '') + '</span>'
                        + '<span class="rpc-item-qty">Qty ' + q + '</span>'
                        + '<span class="rpc-item-total">' + (t !== '' ? 'Rs ' + t : '') + '</span>'
                        + '</div>';
                });

                cardsHtml += '<div class="rpc-card">'
                    + '<div class="rpc-head">'
                    + '<div class="rpc-head-l">'
                    + '<span class="rpc-inv">' + esc(s.invoice_no || 'INV-') + '</span>'
                    + '<span class="rpc-sub"><i class="bi bi-person"></i>' + esc(s.customer_name || '-') + '</span>'
                    + '</div>'
                    + '<span class="rpc-id">#' + (i + 1) + '</span>'
                    + '</div>'
                    + '<div class="rpc-meta">'
                    + '<span><i class="bi bi-calendar3"></i>' + esc(formatDate(createdAt)) + '</span>'
                    + '<span><i class="bi bi-tag"></i>' + esc(s.reference || '-') + '</span>'
                    + '<span><i class="bi bi-grid-1x2-fill"></i>' + esc((s.categories || '-').replace(/,/g, ', ')) + '</span>'
                    + '<span><i class="bi bi-grid-fill"></i>' + esc((s.subcategories || '-').replace(/,/g, ', ')) + '</span>'
                    + '</div>'
                    + (itemRows ? '<div class="rpc-items"><div class="rpc-items-hd"><span><i class="bi bi-box-seam"></i>Items</span><b>' + prodArr.length + '</b></div>' + itemRows + '</div>' : '')
                    + '<div class="rpc-net"><span>Net Total</span><b>Rs ' + rowNet.toFixed(2) + '</b></div>'
                    + (returnHtml ? '<div class="rpc-returns"><span class="rpc-returns-hd"><i class="bi bi-arrow-return-left"></i>Returns</span><div class="rpc-returns-body">' + returnHtml + '</div></div>' : '')
                    + '</div>';
            });
            
            html += `<tr class="fw-bold bg-light" id="grandTotalRow">
                <td colspan="8" class="text-end">Grand Total:</td>
                <td class="small">
                    Pieces: ${grandPiece.toFixed(2)}<br>
                    KG: ${grandKG.toFixed(2)}<br>
                    ${grandPound > 0 ? 'Pound: ' + grandPound.toFixed(2) + '<br>' : ''}
                    ${grandMeter > 0 ? 'Meters: ' + grandMeter.toFixed(2) + '<br>' : ''}
                    ${grandYard > 0 ? 'Yards: ' + grandYard.toFixed(2) + '<br>' : ''}
                </td>
                <td class="fs-6">${grandQty.toFixed(2)}</td>
                <td>-</td>
                <td>Rs ${grandTotal.toFixed(2)}</td>
                <td class="text-success fs-6">Rs ${grandNet.toFixed(2)}</td>
                <td class="small">Qty: ${grandReturnQty.toFixed(2)}<br>Return: Rs ${grandReturnAmount.toFixed(2)}</td>
            </tr>`;

            cardsHtml += '<div class="rpc-card rpc-grand">'
                + '<div class="rpc-head">'
                + '<div class="rpc-head-l"><span class="rpc-inv"><i class="bi bi-calculator-fill"></i> Grand Total</span>'
                + '<span class="rpc-sub"><i class="bi bi-collection"></i>' + (res||[]).length + ' Sale(s)</span></div>'
                + '</div>'
                + '<div class="rpc-meta">'
                + '<span><i class="bi bi-box-seam"></i>Pieces: ' + grandPiece.toFixed(2) + '</span>'
                + '<span><i class="bi bi-speedometer"></i>KG: ' + grandKG.toFixed(2) + '</span>'
                + (grandPound > 0 ? '<span><i class="bi bi-weight-hanging"></i>Pound: ' + grandPound.toFixed(2) + '</span>' : '')
                + (grandMeter > 0 ? '<span><i class="bi bi-rulers"></i>Meters: ' + grandMeter.toFixed(2) + '</span>' : '')
                + (grandYard > 0 ? '<span><i class="bi bi-rulers"></i>Yards: ' + grandYard.toFixed(2) + '</span>' : '')
                + '</div>'
                + '<div class="rpc-net"><span>Total Qty</span><b>' + grandQty.toFixed(2) + '</b></div>'
                + '<div class="rpc-net"><span>Total Amount</span><b>Rs ' + grandTotal.toFixed(2) + '</b></div>'
                + '<div class="rpc-net"><span>Net Amount</span><b>Rs ' + grandNet.toFixed(2) + '</b></div>'
                + (grandReturnQty > 0 ? '<div class="rpc-returns"><span class="rpc-returns-hd"><i class="bi bi-arrow-return-left"></i>Returns</span><div class="rpc-returns-body"><div class="rpc-item"><span class="rpc-item-nm">Qty: ' + grandReturnQty.toFixed(2) + '</span><span class="rpc-item-total">Rs ' + grandReturnAmount.toFixed(2) + '</span></div></div></div>' : '')
                + '</div>';

            $('#saleBody').html(html);
            $('#categoryCards').html(cardsHtml);
        },
        error: function() {
            $("#loader").hide();
            alert('Failed to fetch report. Please try again.');
        }
    });
});

$(document).ready(function() {
    $(document).on('click', '#btnExportCsv', function() {
        let csv = [];
        $("#saleReport tr").each(function() {
            let row = [];
            $(this).find('th,td').each(function() {
                let cellHtml = $(this).html();
                let cellText = cellHtml.replace(/<br\s*\/?>/gi, " | ").replace(/&nbsp;/gi, " ").replace(/<[^>]*>/g, "").trim();
                row.push('"' + cellText.replace(/"/g, '""') + '"');
            });
            csv.push(row.join(","));
        });
        let csvString = csv.join("\n");
        let blob = new Blob([csvString], {
            type: 'text/csv;charset=utf-8;'
        });
        let link = document.createElement("a");
        if (link.download !== undefined) {
            let url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "sale_category_report.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            alert('CSV download not supported in this browser.');
        }
    });
});
</script>
@endsection