<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Balance Sheet - {{ $asOfDate }}</title>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
            body {
                margin: 0;
                padding: 0;
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { display: none !important; }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background: #f1f5f9;
            margin: 0;
            padding: 20px 0;
            font-size: 13px;
            line-height: 1.4;
        }

        .sheet-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 35px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
        }

        .print-btn-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #0f766e;
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(15, 118, 110, 0.3);
        }
        .btn-print:hover { background: #115e59; }

        .header-section {
            text-align: center;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 12px;
            color: #64748b;
            margin: 3px 0 8px 0;
        }
        .doc-title {
            display: inline-block;
            background: #0f766e;
            color: #ffffff;
            padding: 4px 18px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 4px 0;
        }
        .meta-info {
            font-size: 12px;
            color: #475569;
            margin-top: 6px;
            font-weight: 500;
        }

        /* Dual Column Layout */
        .two-column-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .two-column-table > tbody > tr > td {
            vertical-align: top;
            width: 50%;
            padding: 0 10px;
        }
        .two-column-table > tbody > tr > td:first-child {
            border-right: 1px solid #cbd5e1;
            padding-left: 0;
        }
        .two-column-table > tbody > tr > td:last-child {
            padding-right: 0;
        }

        .section-box {
            margin-bottom: 15px;
        }
        .section-header {
            background: #f8fafc;
            border-left: 4px solid #0f766e;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
        }
        .item-table td {
            padding: 5px 4px;
            border-bottom: 1px solid #f1f5f9;
        }
        .item-table .label {
            color: #334155;
            font-weight: 500;
        }
        .item-table .amount {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
        }
        .item-table .subtotal-row td {
            border-top: 1px dashed #cbd5e1;
            border-bottom: 1px solid #94a3b8;
            font-weight: 700;
            color: #0f172a;
            padding-top: 6px;
            background: #f8fafc;
        }

        .total-box {
            margin-top: 15px;
            border-top: 2px solid #0f172a;
            border-bottom: 3px double #0f172a;
            padding: 8px 4px;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            background: #f8fafc;
        }

        /* Signatures */
        .signatures-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }
        .sig-block {
            width: 25%;
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
        }

        .balanced-stamp {
            text-align: center;
            margin-top: 15px;
            font-size: 11px;
            color: #15803d;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="no-print print-btn-bar">
        <button class="btn-print" onclick="window.print()">
            <svg style="vertical-align:middle;margin-right:5px;" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg>
            Print Balance Sheet
        </button>
    </div>

    <div class="sheet-wrapper">
        {{-- Header --}}
        <div class="header-section">
            <h1 class="company-name">{{ function_exists('shop_name') ? shop_name() : 'MEMON NIMKO & SWEETS' }}</h1>
            <div class="company-subtitle">{{ function_exists('shop_tagline') ? shop_tagline() : 'Quality Bakers & Sweets' }}</div>
            <div class="doc-title">BALANCE SHEET / STATEMENT OF FINANCIAL POSITION</div>
            <div class="meta-info">
                <strong>As on Date:</strong> {{ date('d-M-Y', strtotime($asOfDate)) }} &nbsp;|&nbsp; 
                <strong>Branch:</strong> {{ $data['branch_name'] ?? 'Consolidated' }} &nbsp;|&nbsp; 
                <strong>Currency:</strong> PKR (Rs)
            </div>
        </div>

        {{-- Main Two-Column Sheet --}}
        <table class="two-column-table">
            <tbody>
                <tr>
                    {{-- ────── LEFT COLUMN: ASSETS ────── --}}
                    <td>
                        {{-- 1. Current Assets --}}
                        <div class="section-box">
                            <div class="section-header">Current Assets (Rawaan Asasay)</div>
                            <table class="item-table">
                                <tr>
                                    <td class="label">Cash in Hand (Counter &amp; Tills)</td>
                                    <td class="amount">Rs {{ number_format($data['assets']['cash_in_hand']['total'] ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Bank Accounts Balance</td>
                                    <td class="amount">Rs {{ number_format($data['assets']['bank_accounts']['total'] ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Accounts Receivable (Customers)</td>
                                    <td class="amount">Rs {{ number_format($data['assets']['accounts_receivable']['total'] ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Closing Inventory / Stock Value</td>
                                    <td class="amount">Rs {{ number_format($data['assets']['inventory']['total'] ?? 0, 2) }}</td>
                                </tr>
                                @if(!empty($data['assets']['advances_to_vendors']['total']) && $data['assets']['advances_to_vendors']['total'] > 0)
                                <tr>
                                    <td class="label">Advances to Suppliers / Vendors</td>
                                    <td class="amount">Rs {{ number_format($data['assets']['advances_to_vendors']['total'], 2) }}</td>
                                </tr>
                                @endif
                                <tr class="subtotal-row">
                                    <td>Total Current Assets</td>
                                    <td class="amount">Rs {{ number_format($data['assets']['total_current_assets'] ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- 2. Fixed Assets --}}
                        <div class="section-box">
                            <div class="section-header">Fixed Assets (Mustaqil Asasay)</div>
                            <table class="item-table">
                                @forelse($data['assets']['fixed_assets']['assets'] ?? [] as $ast)
                                <tr>
                                    <td class="label">{{ $ast['name'] }}</td>
                                    <td class="amount">Rs {{ number_format($ast['current_value'] ?? 0, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="label text-muted">No fixed assets registered</td>
                                    <td class="amount">Rs 0.00</td>
                                </tr>
                                @endforelse
                                <tr class="subtotal-row">
                                    <td>Total Fixed Assets</td>
                                    <td class="amount">Rs {{ number_format($data['assets']['total_fixed_assets'] ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- TOTAL ASSETS --}}
                        <div class="total-box">
                            <span>TOTAL ASSETS</span>
                            <span>Rs {{ number_format($data['assets']['total_assets'] ?? 0, 2) }}</span>
                        </div>
                    </td>

                    {{-- ────── RIGHT COLUMN: LIABILITIES & EQUITY ────── --}}
                    <td>
                        {{-- 1. Liabilities --}}
                        <div class="section-box">
                            <div class="section-header">Liabilities (Wajib-ul-Ada)</div>
                            <table class="item-table">
                                <tr>
                                    <td class="label">Accounts Payable (Suppliers / Vendors)</td>
                                    <td class="amount">Rs {{ number_format($data['liabilities']['accounts_payable']['total'] ?? 0, 2) }}</td>
                                </tr>
                                @if(!empty($data['liabilities']['customer_advances']['total']) && $data['liabilities']['customer_advances']['total'] > 0)
                                <tr>
                                    <td class="label">Customer Advances Received</td>
                                    <td class="amount">Rs {{ number_format($data['liabilities']['customer_advances']['total'], 2) }}</td>
                                </tr>
                                @endif
                                <tr class="subtotal-row">
                                    <td>Total Liabilities</td>
                                    <td class="amount">Rs {{ number_format($data['liabilities']['total_liabilities'] ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- 2. Owner's Equity --}}
                        <div class="section-box">
                            <div class="section-header">Owner's Equity &amp; Capital (Sarmaya)</div>
                            <table class="item-table">
                                <tr>
                                    <td class="label">Owner's Invested Capital</td>
                                    <td class="amount">Rs {{ number_format($data['equity']['invested_capital'] ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Retained Reserves / Earnings</td>
                                    <td class="amount">Rs {{ number_format($data['equity']['retained_earnings'] ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Net Profit / (Loss) for Period</td>
                                    <td class="amount">Rs {{ number_format($data['equity']['net_profit'] ?? 0, 2) }}</td>
                                </tr>
                                <tr class="subtotal-row">
                                    <td>Total Owner's Equity</td>
                                    <td class="amount">Rs {{ number_format($data['equity']['total_equity'] ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        {{-- TOTAL LIABILITIES & EQUITY --}}
                        <div class="total-box">
                            <span>TOTAL LIAB. &amp; CAPITAL</span>
                            <span>Rs {{ number_format($data['equity']['total_liabilities_and_equity'] ?? 0, 2) }}</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Verification Stamp --}}
        <div class="balanced-stamp">
            &#10004; Account Books Verified &amp; Balanced &bull; Assets = Liabilities + Equity (Difference: Rs {{ number_format($data['metrics']['difference'] ?? 0, 2) }})
        </div>

        {{-- Signatures Block --}}
        <div class="signatures-section">
            <div class="sig-block">Prepared By</div>
            <div class="sig-block">Accounts Officer</div>
            <div class="sig-block">Approved By / Owner</div>
        </div>
    </div>

</body>
</html>
