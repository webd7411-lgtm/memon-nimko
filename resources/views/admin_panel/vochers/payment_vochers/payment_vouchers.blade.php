@extends('admin_panel.layout.app')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --pvf-font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --pvf-bg: #f8fafc;
    --pvf-surface: #ffffff;
    --pvf-border: #e2e8f0;
    --pvf-primary: #dc2626;
    --pvf-primary-dark: #b91c1c;
    --pvf-primary-light: #fef2f2;
    --pvf-text-main: #0f172a;
    --pvf-text-muted: #64748b;
    --pvf-radius: 16px;
    --pvf-radius-sm: 10px;
    --pvf-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
    --pvf-shadow-lg: 0 12px 32px -4px rgba(15, 23, 42, 0.12);
}

.pvf-page * {
    font-family: var(--pvf-font);
}

.pvf-page {
    background-color: var(--pvf-bg);
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* ══════════ HERO BANNER ══════════ */
.pvf-hero {
    position: relative;
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #881337 100%);
    border-radius: var(--pvf-radius);
    padding: 1.5rem 1.85rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 20px 35px -10px rgba(136, 19, 55, 0.35);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    overflow: hidden;
}

.pvf-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(circle at 15% 90%, rgba(244, 63, 94, 0.3) 0%, transparent 60%),
        radial-gradient(circle at 85% 15%, rgba(245, 158, 11, 0.25) 0%, transparent 55%);
    pointer-events: none;
}

.pvf-hero > * {
    position: relative;
    z-index: 1;
}

.pvf-hero-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.pvf-hero-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 1.5rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.pvf-hero-title h2 {
    font-size: 1.55rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 0.25rem 0;
    letter-spacing: -0.02em;
}

.pvf-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 30px;
    padding: 0.25rem 0.85rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #fecdd3;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.pvf-hero-actions {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-wrap: wrap;
}

.pvf-btn-glass {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    padding: 0.55rem 1.15rem;
    border-radius: var(--pvf-radius-sm);
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.2s;
    text-decoration: none;
    backdrop-filter: blur(6px);
}
.pvf-btn-glass:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-1px);
}

/* ══════════ FORM CARD ══════════ */
.pvf-card {
    background: var(--pvf-surface);
    border: 1px solid var(--pvf-border);
    border-radius: var(--pvf-radius);
    box-shadow: var(--pvf-shadow);
    padding: 1.75rem;
    margin-bottom: 1.5rem;
}

.pvf-section-title {
    font-size: 0.92rem;
    font-weight: 800;
    color: #881337;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.65rem;
    border-bottom: 1.5px solid #fff1f2;
}

.pvf-label {
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    margin-bottom: 0.35rem;
    display: block;
}

.pvf-input {
    border: 1.5px solid #e2e8f0;
    border-radius: var(--pvf-radius-sm);
    padding: 0.55rem 0.85rem;
    font-size: 0.88rem;
    color: var(--pvf-text-main);
    background: #ffffff;
    transition: all 0.2s ease;
    width: 100%;
}
.pvf-input:focus {
    border-color: var(--pvf-primary);
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
    outline: none;
}
.pvf-input[readonly] {
    background: #f8fafc;
    color: #334155;
    cursor: not-allowed;
}

/* ══════════ TABLE STYLING ══════════ */
.pvf-table-container {
    border: 1px solid var(--pvf-border);
    border-radius: var(--pvf-radius-sm);
    overflow: hidden;
    margin-bottom: 1.5rem;
    background: #ffffff;
}

table.pvf-table {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0;
}

table.pvf-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.85rem 0.95rem;
    border-bottom: 1.5px solid #e2e8f0;
    border-top: none;
}

table.pvf-table tbody td {
    padding: 0.65rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

table.pvf-table tfoot th {
    background: #f8fafc;
    padding: 0.9rem 1rem;
    border-top: 2px solid #e2e8f0;
    font-size: 0.88rem;
}

.pvf-row-btn-del {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #fee2e2;
    color: #dc2626;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    cursor: pointer;
}
.pvf-row-btn-del:hover {
    background: #dc2626;
    color: #ffffff;
    transform: scale(1.05);
}

.pvf-btn-add-row {
    background: #fff1f2;
    border: 1.5px dashed #f43f5e;
    color: #be123c;
    font-size: 0.84rem;
    font-weight: 700;
    padding: 0.6rem 1.25rem;
    border-radius: var(--pvf-radius-sm);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    transition: all 0.2s;
}
.pvf-btn-add-row:hover {
    background: #ffe4e6;
    border-color: #e11d48;
    color: #9f1239;
    transform: translateY(-1px);
}

/* ══════════ TOTAL DISPLAY ══════════ */
.pvf-total-box {
    background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
    border: 1.5px solid #fecdd3;
    border-radius: var(--pvf-radius-sm);
    padding: 0.75rem 1.25rem;
    display: inline-flex;
    align-items: center;
    gap: 0.85rem;
}
.pvf-total-box .total-label {
    font-size: 0.8rem;
    font-weight: 800;
    color: #9f1239;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.pvf-total-box .total-val {
    font-size: 1.45rem;
    font-weight: 800;
    color: #be123c;
    font-family: 'SF Mono', Consolas, Monaco, monospace;
}

/* ══════════ ACTION BUTTONS ══════════ */
.pvf-submit-btn {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 700;
    padding: 0.75rem 2rem;
    border-radius: var(--pvf-radius-sm);
    border: none;
    box-shadow: 0 6px 18px rgba(220, 38, 38, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.pvf-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(220, 38, 38, 0.45);
    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
}

.pvf-cancel-btn {
    background: #ffffff;
    color: #475569;
    font-size: 0.9rem;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: var(--pvf-radius-sm);
    border: 1.5px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none;
    transition: all 0.2s;
}
.pvf-cancel-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}

/* ══════════ RESPONSIVE & MOBILE POLISH ══════════ */
.pvf-table-container {
    border: 1px solid var(--pvf-border);
    border-radius: var(--pvf-radius-sm);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 1.5rem;
    background: #ffffff;
}

table.pvf-table {
    min-width: 980px;
}

@media (max-width: 991.98px) {
    .pvf-hero {
        padding: 1.25rem 1.15rem;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    .pvf-hero-left {
        gap: 0.75rem;
    }
    .pvf-hero-icon {
        width: 44px;
        height: 44px;
        font-size: 1.3rem;
        border-radius: 12px;
    }
    .pvf-hero-title h2 {
        font-size: 1.35rem;
    }
    .pvf-hero-actions {
        width: 100%;
        display: flex;
        gap: 0.5rem;
    }
    .pvf-btn-glass {
        flex: 1;
        justify-content: center;
        text-align: center;
        padding: 0.55rem 0.75rem;
        font-size: 0.8rem;
    }
    .pvf-card {
        padding: 1.2rem;
        border-radius: 14px;
    }
}

@media (max-width: 767.98px) {
    .pvf-input {
        min-height: 42px;
        font-size: 0.95rem;
    }
    .pvf-card {
        padding: 1rem;
    }
    .pvf-bottom-bar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 1.25rem !important;
    }
    .pvf-bottom-bar-left {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.5rem !important;
    }
    .pvf-bottom-bar-left .pvf-btn-add-row {
        width: 100%;
        justify-content: center;
        min-height: 44px;
    }
    .pvf-bottom-bar-right {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.75rem !important;
        width: 100%;
    }
    .pvf-total-box {
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0.85rem 1.15rem;
    }
    .pvf-submit-btn {
        width: 100%;
        justify-content: center;
        min-height: 46px;
        font-size: 1rem;
    }
    .pvf-cancel-btn {
        width: 100%;
        justify-content: center;
        min-height: 44px;
    }
}
</style>

<div class="main-content pvf-page">
    <div class="container-fluid px-3 px-md-4 py-3">

        {{-- ══════════ HERO BANNER ══════════ --}}
        <div class="pvf-hero">
            <div class="pvf-hero-left">
                <div class="pvf-hero-icon">
                    <i class="bi bi-credit-card-2-back-fill"></i>
                </div>
                <div class="pvf-hero-title">
                    <h2>New Payment Voucher</h2>
                    <span class="pvf-hero-badge">
                        <i class="bi bi-arrow-up-right-circle-fill"></i> Outward Cash & Bank Disbursement
                    </span>
                </div>
            </div>

            <div class="pvf-hero-actions">
                <a href="{{ route('all-Payment-vochers') }}" class="pvf-btn-glass">
                    <i class="bi bi-list-ul"></i> All Payment Vouchers
                </a>
                <button type="button" class="pvf-btn-glass" onclick="location.reload();">
                    <i class="bi bi-arrow-clockwise"></i> Reset Form
                </button>
            </div>
        </div>

        {{-- ══════════ ALERTS ══════════ --}}
        @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 border-0 shadow-sm mb-3">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        {{-- ══════════ MAIN FORM CARD ══════════ --}}
        <div class="pvf-card">
            <form action="{{ route('Payment.vochers.store') }}" method="POST" id="paymentVoucherForm">
                @csrf

                {{-- Header Details --}}
                <div class="pvf-section-title">
                    <i class="bi bi-info-circle-fill text-danger"></i> Voucher Primary & Source Details
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="pvf-label">PVID No</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-hash"></i></span>
                            <input type="text" class="pvf-input fw-bold text-danger border-start-0 font-monospace" name="pvid" value="{{ $nextPVID }}" readonly>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="pvf-label">Payment Date</label>
                        <input type="date" name="receipt_date" class="pvf-input" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="pvf-label">Entry Date</label>
                        <input type="date" name="entry_date" class="pvf-input" value="{{ now()->toDateString() }}">
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="pvf-label">Disbursement Account Head</label>
                        <select name="row_account_head[]" class="pvf-input rowAccountHead" required>
                            <option value="">Select Account Head</option>
                            @foreach($AccountHeads as $head)
                            <option value="{{ $head->id }}">{{ $head->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="pvf-label">Disbursement Account (Cash/Bank)</label>
                        <select name="row_account_id[]" class="pvf-input rowAccountSub" required>
                            <option value="">Select Account</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="pvf-label">General Remarks / Notes</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-chat-left-text"></i></span>
                            <input type="text" name="remarks" class="pvf-input border-start-0" id="remarks" placeholder="Enter general remarks for this disbursement...">
                        </div>
                    </div>
                </div>

                {{-- Line Items Table --}}
                <div class="pvf-section-title d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-list-check text-danger"></i> Beneficiary Parties & Amounts
                    </div>
                    <button type="button" class="pvf-btn-add-row" id="btnAddRow">
                        <i class="bi bi-plus-circle-fill"></i> Add Line Row
                    </button>
                </div>

                <div class="d-md-none text-muted small mb-2 d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-right text-danger fw-bold"></i>
                    <span>Table ko left / right swipe karein sub columns ke liye</span>
                </div>

                <div class="pvf-table-container">
                    <table class="table pvf-table align-middle" id="voucherTable">
                        <thead>
                            <tr>
                                <th style="width:20%;">Narration / Detail</th>
                                <th style="width:12%;">Reference#</th>
                                <th style="width:14%;">Party Type</th>
                                <th style="width:20%;">Beneficiary Party</th>
                                <th style="width:12%;">Phone/Code</th>
                                <th style="width:8%;">Discount</th>
                                <th style="width:12%;" class="text-end">Amount (PKR)</th>
                                <th style="width:50px;" class="text-center"><i class="bi bi-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="narration_text[]" class="narrationTextHidden">
                                        <select name="narration_id[]" class="pvf-input narrationSelect">
                                            <option value="">Select / Type New</option>
                                            @foreach($narrations as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="pvf-input narrationInput mt-1" placeholder="Type new narration here" style="display:none;">
                                    </div>
                                </td>
                                <td>
                                    <input name="reference_no[]" type="text" class="pvf-input" placeholder="Ref#">
                                </td>
                                <td>
                                    <select name="vendor_type" class="pvf-input vendorTypeSelect" required>
                                        <option value="">Select Type</option>
                                        @foreach($AccountHeads as $head)
                                        <option value="{{ $head->id }}">{{ $head->name }}</option>
                                        @endforeach
                                        <option value="vendor">Vendor</option>
                                        <option value="customer">Customer</option>
                                        <option value="walkin">Walk-in Customer</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="vendor_id" class="pvf-input vendorIdSelect" required>
                                        <option disabled selected>Select Type First</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="tel" class="pvf-input telInput" readonly placeholder="Code">
                                </td>
                                <td>
                                    <input name="discount_value[]" type="number" step="0.01" class="pvf-input text-end discountValue" value="0">
                                </td>
                                <td>
                                    <input name="amount[]" type="number" step="0.01" class="pvf-input text-end fw-bold text-danger amount" placeholder="0.00" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="pvf-row-btn-del removeRow" title="Delete Row">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Bottom Summary & Submit Bar --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2 pvf-bottom-bar">
                    <div class="d-flex align-items-center gap-2 pvf-bottom-bar-left">
                        <button type="button" class="pvf-btn-add-row" id="btnAddRowBottom">
                            <i class="bi bi-plus-lg"></i> Add Another Row
                        </button>
                        <span class="text-muted small ps-2 d-none d-sm-inline">Tip: Press <kbd class="bg-light text-dark border">Enter</kbd> in amount to quickly add rows</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 pvf-bottom-bar-right">
                        <div class="pvf-total-box">
                            <span class="total-label">Grand Total:</span>
                            <span class="total-val" id="totalDisplay">Rs. 0.00</span>
                            <input type="hidden" name="total_amount" id="totalAmount" value="0">
                        </div>

                        <button type="submit" class="pvf-submit-btn">
                            <i class="bi bi-check2-circle fs-5"></i> Save Payment Voucher
                        </button>

                        <a href="{{ route('all-Payment-vochers') }}" class="pvf-cancel-btn">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                    </div>
                </div>

            </form>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    // Narration toggle for custom text
    $(document).on('change', '.narrationSelect', function() {
        let $row = $(this).closest('td');
        let $input = $row.find('.narrationInput');

        if ($(this).val() === '') {
            $input.show().focus();
        } else {
            $input.hide().val('');
            $row.find('.narrationTextHidden').val('');
        }
    });

    $(document).on('input', '.narrationInput', function() {
        $(this).closest('td').find('.narrationTextHidden').val($(this).val());
    });

    // Party Type change -> fetch parties for that row
    $(document).on('change', '.vendorTypeSelect', function() {
        let type = $(this).val();
        let $row = $(this).closest('tr');
        let $vendorSelect = $row.find('.vendorIdSelect');
        let $telInput = $row.find('.telInput');

        $telInput.val('');
        $vendorSelect.empty().append('<option disabled selected>Loading...</option>');

        if (type === 'vendor' || type === 'customer' || type === 'walkin') {
            $.get('{{ route("party.list") }}?type=' + type, function(data) {
                $vendorSelect.empty().append('<option disabled selected>Select Party</option>');
                data.forEach(function(item) {
                    $vendorSelect.append('<option value="' + item.id + '">' + item.text + '</option>');
                });
            });
        } else if (type) {
            let headId = type;
            $.get('{{ url("get-accounts-by-head") }}/' + headId, function(data) {
                $vendorSelect.empty().append('<option disabled selected>Select Account</option>');
                data.forEach(function(acc) {
                    $vendorSelect.append(
                        '<option value="' + acc.id + '" data-code="' + (acc.account_code || '') + '">' +
                        acc.title + (acc.account_code ? ' (' + acc.account_code + ')' : '') +
                        '</option>'
                    );
                });
            });
        }
    });

    $(document).on('change', '.vendorIdSelect', function() {
        let $selected = $(this).find(':selected');
        let id = $selected.val();
        let $row = $(this).closest('tr');
        let type = $row.find('.vendorTypeSelect').val();
        let $telInput = $row.find('.telInput');

        if (!id) return;

        let accountCode = $selected.data('code');
        if (accountCode) {
            $telInput.val(accountCode);
            return;
        }

        if (type) {
            type = type.toLowerCase();
            $.get('{{ route("customers.show", ["id" => "__ID__"]) }}'.replace('__ID__', id) + '?type=' + type, function(d) {
                $telInput.val(d.mobile || '');
                if (d.remarks && !$('#remarks').val()) {
                    $('#remarks').val(d.remarks);
                }
            });
        }
    });

    // Top Header Account Head -> Sub Accounts
    $(document).on('change', '.rowAccountHead', function() {
        let headId = $(this).val();
        let $subSelect = $('.rowAccountSub');

        if (!headId) {
            $subSelect.html('<option value="">Select Account</option>');
            return;
        }

        $subSelect.html('<option value="">Loading accounts...</option>');
        $.get('{{ url("get-accounts-by-head") }}/' + headId, function(res) {
            let html = '<option value="">Select Account</option>';
            res.forEach(acc => {
                html += `<option value="${acc.id}">${acc.title}</option>`;
            });
            $subSelect.html(html);
        });
    });

    // Total Calculation
    function calculateTotals() {
        let total = 0;
        $('#voucherTable tbody tr').each(function() {
            let amt = parseFloat($(this).find('.amount').val()) || 0;
            total += amt;
        });
        $('#totalAmount').val(total.toFixed(2));
        $('#totalDisplay').text('Rs. ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    $(document).on('input', '.amount', function() {
        calculateTotals();
    });

    // Add Row Helper
    function appendNewRow() {
        let newRow = `
        <tr>
            <td>
                <div class="input-group">
                    <input type="hidden" name="narration_text[]" class="narrationTextHidden">
                    <select name="narration_id[]" class="pvf-input narrationSelect">
                        <option value="">Select / Type New</option>
                        @foreach($narrations as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="pvf-input narrationInput mt-1" placeholder="Type new narration here" style="display:none;">
                </div>
            </td>
            <td><input name="reference_no[]" type="text" class="pvf-input" placeholder="Ref#"></td>
            <td>
                <select name="vendor_type" class="pvf-input vendorTypeSelect" required>
                    <option value="">Select Type</option>
                    @foreach($AccountHeads as $head)
                    <option value="{{ $head->id }}">{{ $head->name }}</option>
                    @endforeach
                    <option value="vendor">Vendor</option>
                    <option value="customer">Customer</option>
                    <option value="walkin">Walk-in Customer</option>
                </select>
            </td>
            <td>
                <select name="vendor_id" class="pvf-input vendorIdSelect" required>
                    <option disabled selected>Select Type First</option>
                </select>
            </td>
            <td><input type="text" name="tel" class="pvf-input telInput" readonly placeholder="Code"></td>
            <td><input name="discount_value[]" type="number" step="0.01" class="pvf-input text-end discountValue" value="0"></td>
            <td><input name="amount[]" type="number" step="0.01" class="pvf-input text-end fw-bold text-danger amount" placeholder="0.00" required></td>
            <td class="text-center">
                <button type="button" class="pvf-row-btn-del removeRow" title="Delete Row"><i class="bi bi-trash3-fill"></i></button>
            </td>
        </tr>`;
        $('#voucherTable tbody').append(newRow);
        $('#voucherTable tbody tr:last .amount').focus();
    }

    $('#btnAddRow, #btnAddRowBottom').on('click', function(e) {
        e.preventDefault();
        appendNewRow();
    });

    // Enter in amount adds new row
    $(document).on('keypress', '.amount', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            appendNewRow();
        }
    });

    // Delete row
    $(document).on('click', '.removeRow', function() {
        if ($('#voucherTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
        } else {
            alert('At least one transaction line is required.');
        }
    });
</script>
@endsection