@extends('admin_panel.layout.app')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.drec-container {
    padding-left: 20px;
    padding-right: 20px;
    padding-top: 12px;
    padding-bottom: 40px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* KPI Cards */
.drec-kpi {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #eef2f6;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 100%;
}
.drec-kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
}

/* Distributor Master Card */
.dist-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
    margin-bottom: 24px;
    overflow: hidden;
    transition: all 0.2s ease;
}
.dist-card:hover {
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.09);
}

.dist-header {
    background: linear-gradient(135deg, #090d16 0%, #1e293b 60%, #0f766e 100%);
    color: #ffffff;
    padding: 16px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.booker-section {
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    margin: 14px 18px;
    padding: 14px 16px;
}

.booker-title-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    padding-bottom: 10px;
    border-bottom: 2px solid #cbd5e1;
    margin-bottom: 12px;
}

.shop-table th {
    background: #f1f5f9;
    font-size: 0.76rem;
    text-transform: uppercase;
    font-weight: 700;
    color: #475569;
    letter-spacing: 0.4px;
}

.shop-table td {
    font-size: 0.85rem;
    vertical-align: middle;
}

/* Quick Ledger Modal & Badges */
.ledger-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 50rem;
    font-size: 0.74rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
}
.ledger-pill:hover {
    transform: translateY(-1px);
}

/* ═══════ MOBILE RESPONSIVE ═══════ */
@media (max-width: 991.98px) {
    html, body { overflow-x: hidden !important; }
    .drec-container { padding: 0.6rem 0.65rem 1.25rem !important; overflow-x: hidden !important; max-width: 100% !important; }
    .drec-container .d-flex.justify-content-between {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.6rem !important;
    }
    .drec-container .d-flex.align-items-center.gap-2:last-child {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.45rem;
        width: 100%;
    }
    .drec-container .btn.rounded-pill {
        width: 100%;
        min-height: 34px !important;
        font-size: 0.76rem !important;
        justify-content: center;
        display: inline-flex;
        align-items: center;
    }
    .drec-filter-card { border-radius: 12px !important; margin-bottom: 0.75rem !important; }
    .drec-filter-card .row.g-3 {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.45rem 0.55rem !important;
        margin: 0 !important;
    }
    .drec-filter-card .row.g-3 > [class*="col-"] {
        width: 100% !important;
        padding: 0 !important;
    }
    .drec-filter-card .row.g-3 > [class*="col-"]:nth-child(3):not(:last-child),
    .drec-filter-card .row.g-3 > [class*="col-"]:last-child {
        grid-column: 1 / -1 !important;
    }
    .drec-filter-card .form-control,
    .drec-filter-card .form-select {
        min-height: 36px !important;
        font-size: 0.8rem !important;
        border-radius: 8px;
    }

    .kpi-summary-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 0.5rem !important;
        margin-bottom: 0.75rem !important;
    }
    .kpi-card {
        padding: 0.7rem 0.8rem !important;
        border-radius: 12px !important;
    }
    .kpi-val { font-size: 1.1rem !important; }
    .kpi-label { font-size: 0.62rem !important; }

    .dist-card { border-radius: 12px !important; margin-bottom: 0.75rem !important; }
    .dist-header { padding: 0.75rem 0.85rem !important; flex-wrap: wrap !important; gap: 0.45rem; }
    .dist-header h5 { font-size: 0.95rem !important; }
    .booker-pill-card { padding: 0.7rem 0.8rem !important; border-radius: 10px !important; }

    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    .shop-table {
        min-width: 540px;
        font-size: 0.76rem !important;
    }
}

@media print {
    .no-print, header, .navbar, .dist-nav-container, .btn, .drec-filter-card {
        display: none !important;
    }
    body, .drec-container {
        padding: 0 !important;
        background: #fff !important;
    }
    .dist-card {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
        break-inside: avoid;
        margin-bottom: 15px !important;
    }
    .dist-header {
        background: #334155 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>

<div class="container-fluid drec-container">
    <!-- Horizontal Navigation Tabs -->
    @include('admin_panel.distributors.partials.navigation_tabs')

    <!-- Top Page Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 mt-1 no-print">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill text-white px-3 py-1" style="background: #0f766e; font-size: 0.72rem;">
                    Multi-Tier Ledger Audit
                </span>
                <span class="text-muted small">• Complete Receivable Chain</span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-sitemap text-teal" style="color: #0f766e;"></i>
                Distribution Recovery & Full Chain Outstanding Ledger
            </h4>
            <span class="text-muted small">
                Audit who owes whom: Company &rarr; Distributor Outstanding, Distributor &rarr; Order Booker Field Recovery, and Order Booker &rarr; Retail Shop Debts.
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 shadow-sm small fw-semibold" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Recovery Report
            </button>
            <button type="button" class="btn btn-teal text-white rounded-pill px-3 py-1.5 shadow-sm small fw-semibold" style="background: #0f766e;" onclick="loadRecoveryData()">
                <i class="fas fa-sync-alt me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <!-- 1. Company -> Distributor Receivable -->
        <div class="col-12 col-md-4">
            <div class="drec-kpi" style="border-left: 5px solid #0f766e !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        1. Company Receivable (From Distributors)
                    </span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" id="kpiCompanyDistDue" style="font-size: 1.45rem;">
                        Rs. 0.00
                    </h3>
                    <span class="text-muted small" id="kpiDistCount">0 Active Distributors</span>
                </div>
                <div class="drec-kpi-icon" style="background: rgba(15, 118, 110, 0.1); color: #0f766e;">
                    <i class="fas fa-building-columns"></i>
                </div>
            </div>
        </div>

        <!-- 2. Distributor -> Retail Market Due -->
        <div class="col-12 col-md-4">
            <div class="drec-kpi" style="border-left: 5px solid #ef4444 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        2. Market Outstanding Due (From Retailers)
                    </span>
                    <h3 class="fw-bold mb-0 text-danger mt-1" id="kpiMarketRetailDue" style="font-size: 1.45rem;">
                        Rs. 0.00
                    </h3>
                    <span class="text-muted small" id="kpiShopCount">Across 0 Retail Shops</span>
                </div>
                <div class="drec-kpi-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
            </div>
        </div>

        <!-- 3. Field Order Bookers Network -->
        <div class="col-12 col-md-4">
            <div class="drec-kpi" style="border-left: 5px solid #3b82f6 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        3. Field Recovery Network
                    </span>
                    <h3 class="fw-bold mb-0 text-primary mt-1" id="kpiBookersCount" style="font-size: 1.45rem;">
                        0 Bookers
                    </h3>
                    <span class="text-muted small">Managing Order Visits & Payments</span>
                </div>
                <div class="drec-kpi-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fas fa-users-gear"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 drec-filter-card">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <!-- Search -->
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                        <input type="text" id="filterSearch" class="form-control border-start-0" placeholder="Search retail shop, owner, phone or area..." onkeyup="debounceFilter()">
                    </div>
                </div>

                <!-- Distributor Select -->
                <div class="col-12 col-sm-6 col-md-4">
                    <select id="filterDistributor" class="form-select form-select-sm" onchange="loadRecoveryData()">
                        <option value="all">-- All Distributors --</option>
                        @foreach($distributors as $d)
                        <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->code }} - {{ ucfirst($d->type ?? 'distributor') }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Order Booker Select -->
                <div class="col-12 col-sm-6 col-md-3">
                    <select id="filterBooker" class="form-select form-select-sm" onchange="loadRecoveryData()">
                        <option value="all">-- All Order Bookers --</option>
                        @foreach($bookers as $bkr)
                        <option value="{{ $bkr->id }}">{{ $bkr->name }} ({{ $bkr->email }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-12 col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-light border w-100" onclick="resetFilters()" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Container -->
    <div id="recoveryReportWrapper">
        <div class="text-center py-5 text-muted" id="loadingState">
            <div class="spinner-border text-teal mb-3" style="color: #0f766e;" role="status"></div>
            <p class="mb-0 fw-semibold">Loading Multi-Tier Recovery & Ledger Chain...</p>
        </div>

        <div id="reportDataContainer" style="display: none;">
            <!-- Injected by JavaScript -->
        </div>

        <div id="emptyState" class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted" style="display: none;">
            <i class="fas fa-store-slash fa-3x mb-3 text-secondary opacity-50"></i>
            <h5 class="fw-bold mb-1">No Distribution Records Found</h5>
            <p class="small text-muted mb-0">No distributor or retail customer matched the active filters.</p>
        </div>
    </div>
</div>

<script>
let debounceTimer;
function debounceFilter() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        loadRecoveryData();
    }, 350);
}

function resetFilters() {
    document.getElementById('filterSearch').value = '';
    document.getElementById('filterDistributor').value = 'all';
    document.getElementById('filterBooker').value = 'all';
    loadRecoveryData();
}

function formatRs(num) {
    const val = parseFloat(num) || 0;
    return 'Rs. ' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function loadRecoveryData() {
    const search = document.getElementById('filterSearch').value;
    const distributorId = document.getElementById('filterDistributor').value;
    const bookerId = document.getElementById('filterBooker').value;

    document.getElementById('loadingState').style.display = 'block';
    document.getElementById('reportDataContainer').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';

    const params = new URLSearchParams({
        search: search,
        distributor_id: distributorId,
        booker_user_id: bookerId
    });

    fetch("{{ route('report.distribution_recovery_ledger.fetch') }}?" + params.toString())
        .then(res => res.json())
        .then(data => {
            document.getElementById('loadingState').style.display = 'none';

            if (!data.status || !data.distributors || data.distributors.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
                updateKPIs(data.summary || {});
                return;
            }

            updateKPIs(data.summary);
            renderHierarchy(data.distributors);
            document.getElementById('reportDataContainer').style.display = 'block';
        })
        .catch(err => {
            console.error('Error fetching recovery data:', err);
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
        });
}

function updateKPIs(s) {
    document.getElementById('kpiCompanyDistDue').innerText = formatRs(s.company_receivable_from_distributors || 0);
    document.getElementById('kpiMarketRetailDue').innerText = formatRs(s.market_receivable_from_retailers || 0);
    document.getElementById('kpiBookersCount').innerText = (s.total_bookers_count || 0) + ' Bookers';
    document.getElementById('kpiDistCount').innerText = (s.total_distributors_count || 0) + ' Distributors';
    document.getElementById('kpiShopCount').innerText = 'Across ' + (s.total_retail_shops_count || 0) + ' Retail Shops';
}

function renderHierarchy(distributors) {
    const container = document.getElementById('reportDataContainer');
    container.innerHTML = '';

    distributors.forEach(d => {
        let bookersHtml = '';

        if (!d.bookers || d.bookers.length === 0) {
            bookersHtml = `
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-user-slash fa-2x mb-2 text-secondary opacity-50"></i>
                    <p class="mb-0 small">No Order Bookers or Retail Customers mapped under this distributor yet.</p>
                </div>
            `;
        } else {
            d.bookers.forEach(b => {
                let shopsRows = '';
                if (!b.customers || b.customers.length === 0) {
                    shopsRows = `
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted small">
                                No retail shops mapped to this order booker yet.
                            </td>
                        </tr>
                    `;
                } else {
                    b.customers.forEach(c => {
                        const balanceClass = c.current_balance > 0 ? 'text-danger fw-bold' : (c.current_balance < 0 ? 'text-info fw-bold' : 'text-success fw-bold');
                        
                        // Recent transactions mini ledger preview
                        let recentTxHtml = '';
                        if (c.recent_ledgers && c.recent_ledgers.length > 0) {
                            recentTxHtml = c.recent_ledgers.map(tx => `
                                <div class="small d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                    <span>${tx.date} (${tx.type}):</span>
                                    <span>${tx.debit > 0 ? '+Rs. ' + tx.debit.toFixed(2) : '-Rs. ' + tx.credit.toFixed(2)}</span>
                                </div>
                            `).join('');
                        } else {
                            recentTxHtml = '<span class="text-muted small" style="font-size: 0.7rem;">No transactions recorded yet</span>';
                        }

                        shopsRows += `
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">${c.shop_name}</div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">
                                        Prop: <strong>${c.owner_name}</strong> • Phone: ${c.phone}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="fas fa-map-marker-alt me-1 text-muted"></i> ${c.area}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-teal-subtle text-teal border px-2 py-0.5" style="background: rgba(15, 118, 110, 0.1); color: #0f766e; font-size: 0.72rem;">
                                        ${c.approved_discount}% Max
                                    </span>
                                </td>
                                <td style="max-width: 240px;">
                                    <div class="p-1.5 rounded-2 bg-light border">
                                        ${recentTxHtml}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="${balanceClass}" style="font-size: 0.95rem;">
                                        ${formatRs(c.current_balance)}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="${c.ledger_url}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" title="Open Full Customer Ledger">
                                        <i class="fas fa-file-invoice-dollar me-1"></i> Ledger
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                }

                bookersHtml += `
                    <div class="booker-section">
                        <div class="booker-title-bar">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                                     style="width: 32px; height: 32px; background: #0f766e; font-size: 0.85rem;">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">${b.name}</h6>
                                    <span class="text-muted small" style="font-size: 0.72rem;">${b.email || 'Order Booker'}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-light text-dark border px-2.5 py-1">
                                    <i class="fas fa-store me-1"></i> ${b.shops_count} Retail Shops
                                </span>
                                <div class="text-end">
                                    <span class="text-muted small d-block" style="font-size: 0.7rem; font-weight: 600;">BOOKER'S MARKET RECEIVABLE:</span>
                                    <span class="fw-bold ${b.total_market_due > 0 ? 'text-danger' : 'text-success'}" style="font-size: 1rem;">
                                        ${formatRs(b.total_market_due)}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Retail Shops Table under this Booker -->
                        <div class="table-responsive rounded-3 border bg-white">
                            <table class="table table-hover align-middle mb-0 shop-table">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Retail Shop Name & Prop</th>
                                        <th>Market Area / Route</th>
                                        <th>Discount Limit</th>
                                        <th>Recent Activity Preview</th>
                                        <th class="text-end">Outstanding Udhaar</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${shopsRows}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            });
        }

        // Recent Distributor Company Ledger Preview
        let distLedgerPreviewHtml = '';
        if (d.recent_ledgers && d.recent_ledgers.length > 0) {
            distLedgerPreviewHtml = d.recent_ledgers.map(tx => `
                <span class="badge bg-dark-subtle text-white border border-secondary px-2 py-1 small">
                    ${tx.date} (${tx.type}): ${tx.debit > 0 ? '+Rs.' + tx.debit.toFixed(0) : '-Rs.' + tx.credit.toFixed(0)}
                </span>
            `).join(' ');
        }

        const distCardHtml = `
            <div class="dist-card">
                <!-- Distributor Header -->
                <div class="dist-header">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill bg-light text-dark fw-bold px-2.5 py-1" style="font-size: 0.72rem;">
                                ${d.code}
                            </span>
                            <h5 class="mb-0 fw-bold text-white">${d.name}</h5>
                            <span class="text-white-50 small">• ${d.city}</span>
                        </div>
                        <div class="text-white-50 small mt-1">
                            <span><i class="fas fa-phone me-1"></i> Phone: <strong>${d.phone}</strong></span>
                            <span class="mx-2">•</span>
                            <span><i class="fas fa-users-gear me-1"></i> <strong>${d.bookers_count}</strong> Order Bookers</span>
                            <span class="mx-2">•</span>
                            <span><i class="fas fa-store me-1"></i> <strong>${d.shops_count}</strong> Retail Shops</span>
                        </div>
                    </div>

                    <!-- Distributor Balances & Action -->
                    <div class="d-flex align-items-center gap-3">
                        <!-- Company -> Distributor Due -->
                        <div class="text-end px-3 py-1.5 rounded-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                            <span class="text-white-50 d-block" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">
                                COMPANY RECEIVABLE FROM DISTRIBUTOR:
                            </span>
                            <span class="fw-bold ${d.company_receivable > 0 ? 'text-warning' : 'text-success'}" style="font-size: 1.15rem;">
                                ${formatRs(d.company_receivable)}
                            </span>
                        </div>

                        <!-- Distributor -> Total Market Retail Due -->
                        <div class="text-end px-3 py-1.5 rounded-3" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4);">
                            <span class="text-white-50 d-block" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">
                                TOTAL MARKET UDHAAR (RETAILERS):
                            </span>
                            <span class="fw-bold text-white" style="font-size: 1.15rem;">
                                ${formatRs(d.market_receivable)}
                            </span>
                        </div>

                        <a href="${d.ledger_url}" class="btn btn-sm btn-light rounded-pill px-3 py-1.5 fw-bold shadow-sm" title="View Distributor Company Ledger Statement">
                            <i class="fas fa-receipt me-1 text-teal"></i> Company Ledger
                        </a>
                    </div>
                </div>

                <!-- Bookers & Their Retail Shops -->
                <div class="py-2">
                    ${bookersHtml}
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', distCardHtml);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    loadRecoveryData();
});
</script>
@endsection
