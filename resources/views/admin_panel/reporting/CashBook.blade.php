@extends('admin_panel.layout.app')
@section('content')
<style>
    .cashbook-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        overflow: hidden;
        margin: 20px 0;
        border: none;
    }
    .cashbook-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        color: #fff;
        padding: 18px 24px;
    }
    .cashbook-body { padding: 24px; }

    .summary-card {
        background: #f8f9fc;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #e9ecef;
        height: 100%;
    }
    .summary-card .summary-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 4px;
    }
    .summary-card .summary-value {
        font-size: 24px;
        font-weight: 900;
        color: #1a1a2e;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }
    .summary-card .summary-sub {
        font-size: 13px;
        color: #6c757d;
        margin-top: 2px;
    }
    .summary-card.bg-gradient-cash { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); }
    .summary-card.bg-gradient-card { background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%); }
    .summary-card.bg-gradient-change { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); }
    .summary-card.bg-gradient-sale { background: linear-gradient(135deg, #e8daef 0%, #d2b4de 100%); }

    .summary-card.bg-gradient-recovery { background: linear-gradient(135deg, #d5f4e6 0%, #b7e4c7 100%); }
    .summary-card.bg-gradient-vendor { background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%); }
    .summary-card.bg-gradient-expense { background: linear-gradient(135deg, #ffe0b2 0%, #ffcc80 100%); }

    .balance-hero {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 12px;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
        margin-bottom: 24px;
    }
    .balance-hero .balance-label {
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
    }
    .balance-hero .balance-amount {
        font-size: 32px;
        font-weight: 900;
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }
    .balance-hero .balance-amount.positive { color: #4ade80; }
    .balance-hero .balance-amount.negative { color: #f87171; }

    .cash-table {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        overflow: hidden;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .cash-table thead th {
        background: #1a1a2e;
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 14px;
        border-bottom: 2px solid #0f3460;
    }
    .cash-table tbody td {
        padding: 10px 14px;
        vertical-align: middle;
        font-size: 14px;
        border-bottom: 1px solid #eee;
    }
    .cash-table tbody tr:last-child td { border-bottom: none; }
    .cash-table .sep-col {
        width: 4px;
        background: #1a1a2e;
        padding: 0 !important;
        border: none !important;
    }
    .entry-title { font-weight: 700; color: #1a1a2e; }
    .entry-ref { font-size: 12px; color: #6c757d; display: block; margin-top: 2px; }
    .entry-amount { font-weight: 800; text-align: right; font-size: 15px; }
    .entry-amount.credit { color: #dc3545; }

    .total-row td {
        background: #f1f3f5;
        font-weight: 800;
        font-size: 15px;
        border-top: 2px solid #1a1a2e !important;
        padding: 12px 14px;
    }
    .grand-row td {
        background: #1a1a2e;
        color: #fff;
        font-weight: 800;
        font-size: 15px;
        padding: 12px 14px;
    }

    .method-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .method-badge.cash { background: #d4edda; color: #155724; }
    .method-badge.card { background: #cce5ff; color: #004085; }
    .method-badge.account { background: #fff3cd; color: #856404; }
    .method-badge.credit { background: #f8d7da; color: #721c24; }
    .method-badge.bank { background: #d1ecf1; color: #0c5460; }

    .section-divider {
        position: relative;
        text-align: center;
        margin: 20px 0;
    }
    .section-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(to right, transparent, #1a1a2e, transparent);
    }
    .section-divider span {
        position: relative;
        background: #fff;
        padding: 0 16px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 14px;
        color: #1a1a2e;
        letter-spacing: 1px;
    }

    .method-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 8px;
    }

    /* ═══════ HEADER FILTERS (mobile-first) ═══════ */
    .cb-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
        width: 100%;
    }
    .cb-fgroup { display: flex; flex-direction: column; gap: 5px; flex: 1 1 150px; min-width: 130px; }
    .cb-fgroup label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: rgba(255, 255, 255, .6);
        margin: 0;
    }
    .cb-input {
        background: rgba(255, 255, 255, .15);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 9px;
        padding: .45rem .7rem;
        font-size: .82rem;
        font-weight: 600;
        color-scheme: dark;
        outline: none;
        width: 100%;
        transition: all .2s ease;
    }
    .cb-input:focus { border-color: rgba(255, 255, 255, .55); background: rgba(255, 255, 255, .22); }
    .cb-input::-webkit-calendar-picker-indicator { cursor: pointer; }

    /* Desktop: fixed layout = table NEVER overflows / no horizontal scroll */
    .cashbook-card .table-responsive { overflow-x: hidden; }
    .cash-table { table-layout: fixed; width: 100%; }
    .cash-table thead th, .cash-table tbody td { white-space: normal !important; overflow-wrap: anywhere; word-break: normal; }

    /* ═══════ MOBILE / TABLET PREMIUM CARDS ═══════ */
    .cb-cards { display: none; }

    .cb-card {
        background: #fff;
        border: 1.5px solid #e9ecef;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
        overflow: hidden;
        margin-bottom: .8rem;
    }
    .cb-row { display: flex; }
    .cb-col { flex: 1 1 50%; min-width: 0; padding: .7rem .8rem; }
    .cb-col + .cb-col { border-left: 1px dashed #e9ecef; }

    .cb-col-hd {
        display: flex;
        align-items: center;
        gap: .4rem;
        font-size: .66rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding-bottom: .45rem;
        margin-bottom: .45rem;
        border-bottom: 1px solid #f1f3f5;
    }
    .cb-col-rec .cb-col-hd { color: #155724; }
    .cb-col-pay .cb-col-hd { color: #dc3545; }
    .cb-col-hd i { font-size: .75rem; }
    .cb-col-hd .cb-amt { margin-left: auto; font-size: .8rem; font-weight: 800; }
    .cb-col-pay .cb-amt { color: #dc3545; }
    .cb-col-rec .cb-amt { color: #155724; }

    .cb-title { font-size: .84rem; font-weight: 700; color: #1a1a2e; line-height: 1.3; word-break: normal; overflow-wrap: anywhere; }
    .cb-ref { font-size: .72rem; color: #6c757d; margin-top: 2px; word-break: normal; overflow-wrap: anywhere; }
    .cb-empty { font-size: .72rem; color: #adb5bd; font-style: italic; }

    .cb-totals {
        display: flex;
        border-radius: 14px;
        background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
        color: #fff;
        overflow: hidden;
    }
    .cb-total { flex: 1 1 50%; min-width: 0; padding: .8rem .9rem; }
    .cb-total + .cb-total { border-left: 1px solid rgba(255, 255, 255, .15); }
    .cb-total span { display: block; font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; opacity: .75; margin-bottom: .2rem; }
    .cb-total b { font-size: 1rem; font-weight: 800; word-break: normal; overflow-wrap: anywhere; }
    .cb-total-rec b { color: #4ade80; }
    .cb-total-pay b { color: #f87171; }

    @media (max-width: 768px) {
        .cashbook-body { padding: 14px; }
        .balance-hero { flex-direction: column; gap: 12px; text-align: center; }
        .balance-hero .balance-amount { font-size: 24px; }
        .summary-card .summary-value { font-size: 18px; }
        .method-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 991.98px) {
        .cashbook-card .table-responsive { display: none; }
        .cb-cards { display: block; }
    }

    @media (max-width: 575.98px) {
        .cashbook-header { padding: 14px 14px; }
        .cashbook-header h4 { font-size: 1.05rem; }
        .cb-fgroup { flex-basis: 100%; min-width: 0; }
        .cb-row { flex-direction: column; }
        .cb-col + .cb-col { border-left: none; border-top: 1px dashed #e9ecef; }
        .balance-hero { padding: 16px; }
        .summary-card { padding: 12px 14px; }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">
            <div class="cashbook-card">
                <div class="cashbook-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h4 class="m-0 fw-bold"><i class="fa fa-book me-2"></i>DAILY CASH BOOK</h4>
                        <form method="GET" action="{{ route('cashbook') }}" id="dateFilterForm" class="cb-filters">
                            <div class="cb-fgroup">
                                <label>From</label>
                                <input type="date" name="start_date" class="cb-input"
                                       value="{{ $startDate ?? now()->subDays(30)->format('Y-m-d') }}"
                                       onchange="document.getElementById('dateFilterForm').submit()">
                            </div>
                            <div class="cb-fgroup">
                                <label>View</label>
                                <input type="date" name="date" class="cb-input"
                                       value="{{ $selectedDate ?? date('Y-m-d') }}"
                                       onchange="document.getElementById('dateFilterForm').submit()">
                            </div>
                            <div class="cb-fgroup">
                                <label>Time From</label>
                                <input type="time" name="start_time" class="cb-input"
                                       value="{{ $startTime ?? '00:00' }}"
                                       onchange="document.getElementById('dateFilterForm').submit()">
                            </div>
                            <div class="cb-fgroup">
                                <label>Time To</label>
                                <input type="time" name="end_time" class="cb-input"
                                       value="{{ $endTime ?? '23:59' }}"
                                       onchange="document.getElementById('dateFilterForm').submit()">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="cashbook-body">
                    {{-- BALANCE HERO --}}
                    <div class="balance-hero">
                        <div class="text-center">
                            <div class="balance-label">Opening Balance</div>
                            <div class="balance-amount {{ $openingBalance >= 0 ? 'positive' : 'negative' }}">
                                {{ number_format($openingBalance, 0) }}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="balance-label">Closing Balance</div>
                            <div class="balance-amount {{ $closingBalance >= 0 ? 'positive' : 'negative' }}">
                                {{ number_format($closingBalance, 0) }}
                            </div>
                        </div>
                    </div>

                    {{-- SALES BREAKDOWN --}}
                    <div class="section-divider"><span>Sales Breakdown</span></div>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="summary-card bg-gradient-cash">
                                <div class="summary-label">Cash Sales</div>
                                <div class="summary-value">{{ number_format($totalSaleCash, 0) }}</div>
                                <div class="summary-sub">{{ $saleCount }} invoice(s)</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="summary-card bg-gradient-card">
                                <div class="summary-label">Card Sales</div>
                                <div class="summary-value">{{ number_format($totalSaleCard, 0) }}</div>
                                <div class="summary-sub">via card terminal</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="summary-card bg-gradient-change">
                                <div class="summary-label">Change Returned</div>
                                <div class="summary-value">{{ number_format($totalChange, 0) }}</div>
                                <div class="summary-sub">deducted from cash</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="summary-card bg-gradient-sale">
                                <div class="summary-label">Net Sales</div>
                                <div class="summary-value">{{ number_format($totalSaleNet, 0) }}</div>
                                <div class="summary-sub">total_net = cash + card - change</div>
                            </div>
                        </div>
                    </div>

                    {{-- RECOVERY & PAYMENT METHODS --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="summary-card bg-gradient-recovery" style="height:auto;">
                                <div class="summary-label">Customer Recoveries</div>
                                <div class="summary-value mb-2">{{ number_format($totalRecoveries, 0) }}</div>
                                @if(count($recoveryByMethod))
                                    <div class="method-grid">
                                        @foreach($recoveryByMethod as $method => $amt)
                                            <div>
                                                <span class="method-badge {{ strtolower($method) }}">{{ $method }}</span>
                                                <span style="font-weight:700;font-size:14px;float:right;">{{ number_format($amt, 0) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="summary-sub">No recoveries today</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-card bg-gradient-vendor" style="height:auto;">
                                <div class="summary-label">Vendor Payments</div>
                                <div class="summary-value mb-2">{{ number_format($totalVendorPayments, 0) }}</div>
                                @if(count($vendorPayByMethod))
                                    <div class="method-grid">
                                        @foreach($vendorPayByMethod as $method => $amt)
                                            <div>
                                                <span class="method-badge {{ strtolower($method) }}">{{ $method }}</span>
                                                <span style="font-weight:700;font-size:14px;float:right;">{{ number_format($amt, 0) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="summary-sub">No vendor payments today</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- TRANSACTION TABLE --}}
                    <div class="section-divider"><span>Transactions</span></div>
                    <div class="table-responsive">
                        <table class="cash-table">
                            <thead>
                                <tr>
                                    <th width="38%">Receipts</th>
                                    <th width="12%">Amount</th>
                                    <th class="sep-col"></th>
                                    <th width="38%">Payments</th>
                                    <th width="12%">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 0; $i < $maxRows; $i++)
                                    <tr>
                                        <td>
                                            @if(isset($receipts[$i]))
                                                <span class="entry-title">{{ $receipts[$i]['title'] }}</span>
                                                <span class="entry-ref">{{ $receipts[$i]['ref'] }}</span>
                                            @endif
                                        </td>
                                        <td class="entry-amount">{{ isset($receipts[$i]) ? number_format($receipts[$i]['amount'],0) : '' }}</td>
                                        <td class="sep-col"></td>
                                        <td>
                                            @if(isset($payments[$i]))
                                                <span class="entry-title">{{ $payments[$i]['title'] }}</span>
                                                <span class="entry-ref">{{ $payments[$i]['ref'] }}</span>
                                            @endif
                                        </td>
                                        <td class="entry-amount credit">{{ isset($payments[$i]) ? number_format($payments[$i]['amount'],0) : '' }}</td>
                                    </tr>
                                @endfor

                                {{-- Totals --}}
                                <tr class="total-row">
                                    <td>Total Receipts</td>
                                    <td class="text-end">{{ number_format($totalReceipts, 0) }}</td>
                                    <td class="sep-col"></td>
                                    <td>Total Payments</td>
                                    <td class="text-end">{{ number_format($totalPayments, 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE / TABLET PREMIUM CARDS --}}
                    <div class="cb-cards">
                        @for($i = 0; $i < $maxRows; $i++)
                        <div class="cb-card">
                            <div class="cb-row">
                                <div class="cb-col cb-col-rec">
                                    <div class="cb-col-hd"><i class="fa fa-arrow-down"></i> Receipt
                                        <span class="cb-amt">{{ isset($receipts[$i]) ? number_format($receipts[$i]['amount'],0) : '' }}</span>
                                    </div>
                                    @if(isset($receipts[$i]))
                                        <div class="cb-title">{{ $receipts[$i]['title'] }}</div>
                                        <div class="cb-ref">{{ $receipts[$i]['ref'] }}</div>
                                    @else
                                        <div class="cb-empty">No receipt</div>
                                    @endif
                                </div>
                                <div class="cb-col cb-col-pay">
                                    <div class="cb-col-hd"><i class="fa fa-arrow-up"></i> Payment
                                        <span class="cb-amt">{{ isset($payments[$i]) ? number_format($payments[$i]['amount'],0) : '' }}</span>
                                    </div>
                                    @if(isset($payments[$i]))
                                        <div class="cb-title">{{ $payments[$i]['title'] }}</div>
                                        <div class="cb-ref">{{ $payments[$i]['ref'] }}</div>
                                    @else
                                        <div class="cb-empty">No payment</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endfor

                        <div class="cb-totals">
                            <div class="cb-total cb-total-rec">
                                <span>Total Receipts</span>
                                <b>{{ number_format($totalReceipts, 0) }}</b>
                            </div>
                            <div class="cb-total cb-total-pay">
                                <span>Total Payments</span>
                                <b>{{ number_format($totalPayments, 0) }}</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection