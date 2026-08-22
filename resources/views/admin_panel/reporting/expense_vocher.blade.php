@extends('admin_panel.layout.app')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .rp-exp { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2.5rem; }
    .rp-exp * { font-family: inherit; }

    .rp-hdr {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #881337 0%, #e11d48 100%);
        border-radius: 16px;
        padding: 1.4rem 1.8rem;
        margin-bottom: 1.4rem;
        box-shadow: 0 16px 40px rgba(136, 19, 55, .2);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .8rem;
    }
    .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
    .rp-hdr h2 i { color: #fda4af; }
    .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .72); font-size: .85rem; font-weight: 500; }

    .rp-card {
        background: #fff;
        border: 1px solid #e9edf2;
        border-radius: 14px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05);
        margin-bottom: 1.2rem;
        overflow: hidden;
    }
    .rp-card-body { padding: 1.4rem; }

    .rp-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #54657e; margin-bottom: .35rem; display: block; }
    .rp-input {
        border: 1.5px solid #e9edf2;
        border-radius: 10px;
        padding: .5rem .8rem;
        font-size: .85rem;
        font-weight: 600;
        color: #0b1a33;
        background: #fff;
        width: 100%;
        outline: none;
        transition: all .2s ease;
        height: auto;
    }
    .rp-input:focus { border-color: #e11d48; box-shadow: 0 0 0 3px rgba(225, 29, 72, .1); }

    .rp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        border: none;
        border-radius: 10px;
        padding: .55rem 1.1rem;
        font-size: .83rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .22s ease;
        min-height: 42px;
        text-decoration: none;
    }
    .rp-btn-primary { background: linear-gradient(135deg, #e11d48, #be123c); color: #fff; box-shadow: 0 6px 18px rgba(225, 29, 72, .28); width: 100%; }
    .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(225, 29, 72, .34); color: #fff; }

    /* Select2 premium */
    .rp-exp .select2-container--default .select2-selection--multiple {
        border: 1.5px solid #e9edf2;
        border-radius: 10px;
        min-height: 42px;
    }
    .rp-exp .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #e11d48;
        box-shadow: 0 0 0 3px rgba(225, 29, 72, .1);
    }
    .rp-exp .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: linear-gradient(135deg, #e11d48, #be123c) !important;
        border: none;
        border-radius: 7px;
        color: #fff !important;
        font-size: .75rem;
        font-weight: 600;
        padding: 2px 24px 2px 10px !important;
    }
    .rp-exp .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff !important;
        border: none;
    }

    /* Result table */
    .rp-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: .8rem; }
    .rp-table thead th {
        background: #0f172a;
        font-size: .66rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #94a3b8;
        padding: .65rem .7rem;
        border-bottom: 2px solid #0f172a;
        text-align: left;
        white-space: nowrap;
    }
    .rp-table thead th:first-child { border-radius: 10px 0 0 0; }
    .rp-table thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-table tbody td { padding: .6rem .7rem; border-bottom: 1px solid #f1f4f9; vertical-align: middle; color: #1e293b; }
    .rp-table tbody tr:hover td { background: #fafbfc; }
    .rp-table tbody tr:last-child td { border-bottom: none; }
    .rp-table tfoot td, .rp-table tfoot th {
        background: #fff1f2 !important;
        color: #881337;
        font-size: .82rem;
        font-weight: 800;
        padding: .7rem;
    }

    /* Desktop: fixed layout = table NEVER overflows / no horizontal scroll */
    .rp-exp .table-responsive { overflow-x: hidden; }
    .rp-table { table-layout: fixed; width: 100% !important; }
    .rp-table thead th { white-space: normal !important; overflow-wrap: break-word; }
    .rp-table tbody td { white-space: normal !important; overflow-wrap: break-word; word-break: normal; }

    /* ═══════ MOBILE / TABLET PREMIUM CARDS ═══════ */
    .expc-cards { display: none; }

    .expc-card {
        background: #fff; border: 1.5px solid #e2e8f0; border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05); margin-bottom: .85rem; overflow: hidden;
    }
    .expc-head {
        display: flex; align-items: center; justify-content: space-between; gap: .6rem;
        padding: .8rem .9rem .7rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e9edf2;
    }
    .expc-head-l { display: flex; flex-direction: column; min-width: 0; gap: 3px; }
    .expc-vno { font-size: .95rem; font-weight: 800; color: #0f172a; letter-spacing: -.2px; word-break: normal; overflow-wrap: break-word; line-height: 1.25; }
    .expc-vno i { color: #e11d48; font-size: .85rem; margin-right: 5px; }
    .expc-sub { display: flex; align-items: center; gap: 5px; font-size: .73rem; color: #64748b; font-weight: 600; word-break: normal; overflow-wrap: break-word; }
    .expc-sub i { color: #94a3b8; font-size: .8rem; }
    .expc-id {
        flex: 0 0 auto; font-size: .74rem; font-weight: 800; color: #be123c;
        background: #fff1f2; border: 1px solid #fecdd3; border-radius: 20px;
        padding: .24rem .65rem; white-space: nowrap;
    }

    .expc-meta { display: flex; flex-wrap: wrap; gap: .4rem; padding: .6rem .9rem; }
    .expc-meta span {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: .72rem; color: #475569; font-weight: 600;
        background: #f8fafc; border: 1px solid #eef2f7; border-radius: 8px;
        padding: .28rem .6rem; word-break: normal; overflow-wrap: break-word; line-height: 1.3;
    }
    .expc-meta span i { color: #e11d48; font-size: .76rem; }

    .expc-remarks {
        display: flex; align-items: flex-start; gap: 8px;
        margin: 0 .9rem .7rem; padding: .55rem .7rem;
        background: #fff5f5; border: 1px solid #ffe4e6; border-radius: 10px;
        font-size: .78rem; color: #9f1239; font-weight: 600;
        word-break: normal; overflow-wrap: break-word; line-height: 1.4;
    }
    .expc-remarks i { color: #f43f5e; margin-top: 2px; }

    .expc-total {
        display: flex; align-items: center; justify-content: space-between; gap: .6rem;
        margin: 0 .9rem .7rem; padding: .6rem .8rem;
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        border: 1px solid #fecdd3; border-radius: 12px;
    }
    .expc-total span { font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: #9f1239; }
    .expc-total b { font-size: 1rem; font-weight: 900; color: #be123c; word-break: normal; overflow-wrap: break-word; text-align: right; }

    .expc-grand { border-color: #fda4af; }
    .expc-grand .expc-head { background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%); border-color: #fecdd3; }
    .expc-grand .expc-vno { color: #9f1239; }
    .expc-grand .expc-total { margin-bottom: 0; border-radius: 0; border: none; border-top: 1px solid #fecdd3; background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%); }
    .expc-grand .expc-total b { font-size: 1.05rem; }

    .expc-empty {
        text-align: center; padding: 2.5rem 1rem; color: #64748b;
        display: flex; flex-direction: column; align-items: center; gap: .4rem;
    }
    .expc-empty i { font-size: 2.2rem; color: #cbd5e1; }

    @media (max-width: 991.98px) {
        .rp-exp { overflow-x: hidden; }
        .rp-exp, .rp-exp .container-fluid { max-width: 100%; }

        .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: stretch; gap: .7rem; }
        .rp-hdr h2 { font-size: 1.12rem; }
        .rp-hdr p { font-size: .8rem; }

        .rp-card-body { padding: 1rem; }
        .rp-card-body .col-md-2, .rp-card-body .col-md-3 { margin-bottom: .5rem; }

        /* Hide table on mobile, render premium cards instead */
        .table-responsive { overflow: visible !important; padding: 0; }
        .rp-table { display: none !important; }
        .expc-cards { display: block; padding: .5rem .6rem 1rem; }
    }

    @media (max-width: 575.98px) {
        .rp-card-body { padding: 1rem; }
        .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: flex-start; }
        .rp-hdr h2 { font-size: 1.15rem; }
        .rp-btn { width: 100%; }
    }
</style>

@section('content')
<div class="rp-exp">
    <div class="container-fluid px-3 px-md-4 py-3">

        <div class="rp-hdr">
            <div>
                <h2><i class="bi bi-receipt"></i> Expense Report</h2>
                <p>Search expenses by account head, account and date range</p>
            </div>
        </div>

        <div class="rp-card">
            <div class="rp-card-body">
                <div class="row g-3 align-items-end">

                    {{-- Account Head --}}
                    <div class="col-md-3">
                        <label class="rp-label">Account Head</label>
                        <select name="account_heads[]"
                            id="account_heads"
                            class="form-control form-control-sm select2"
                            multiple>
                            <option value="all">All</option>
                            @foreach ($accountHeads as $head)
                            <option value="{{ $head->id }}">{{ $head->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Account --}}
                    <div class="col-md-3">
                        <label class="rp-label">Account</label>
                        <select name="accounts[]"
                            id="accounts"
                            class="form-control form-control-sm select2"
                            multiple>
                            @foreach ($accounts as $account)
                            <option value="{{ $account->id }}"
                                data-head="{{ $account->head_id }}">
                                {{ $account->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-2">
                        <label class="rp-label">Start Date</label>
                        <input type="date"
                            name="start_date"
                            class="rp-input"
                            value="{{ date('Y-m-d') }}">
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-2">
                        <label class="rp-label">End Date</label>
                        <input type="date"
                            name="end_date"
                            class="rp-input"
                            value="{{ date('Y-m-d') }}">
                    </div>

                    {{-- Search Button --}}
                    <div class="col-md-2">
                        <button type="submit" class="rp-btn rp-btn-primary btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>

                </div>

            </div>
        </div>

        <div class="rp-card d-none" id="resultCard">
            <div class="rp-card-body p-0">

                <div class="table-responsive">
                    <table class="rp-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Voucher</th>
                                <th>Date</th>
                                <th>Account Head</th>
                                <th>Remarks</th>
                                <th>Account</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="expenseRows"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">Total Expense</th>
                                <th class="text-end" id="grandTotal">0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Mobile / Tablet premium cards --}}
                <div id="expenseCards" class="expc-cards"></div>

            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select option",
            allowClear: true,
            width: '100%'
        });

        // Account Head -> Account filter
        $('#account_heads').on('change', function() {
            const selectedHeads = $(this).val() || [];
            const $accounts = $('#accounts option');

            if (selectedHeads.includes('all')) {
                $accounts.prop('selected', true).show();
                $('#accounts').trigger('change');
                return;
            }

            $accounts.each(function() {
                const headId = $(this).data('head').toString();
                if (selectedHeads.includes(headId)) {
                    $(this).show();
                } else {
                    $(this).prop('selected', false).hide();
                }
            });
            $('#accounts').trigger('change');
        });
    });

    $(document).ready(function() {

        $('.select2').select2({
            width: '100%'
        });

        $('.btn-primary').on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('expense.voucher.ajax') }}",
                type: "GET",
                data: {
                    account_heads: $('#account_heads').val(),
                    accounts: $('#accounts').val(),
                    start_date: $('input[name="start_date"]').val(),
                    end_date: $('input[name="end_date"]').val(),
                },
                beforeSend() {
                    $('#expenseRows').html(
                        `<tr><td colspan="7" class="text-center">Loading...</td></tr>`
                    );
                },
                success(res) {
                    let rows = '';
                    let cards = '';

                    if (res.rows.length === 0) {
                        rows = `<tr>
                        <td colspan="7" class="text-center text-muted">
    No expense found
</td>
                    </tr>`;
                        cards = `<div class="expc-empty">
                            <i class="bi bi-inbox"></i>
                            <span>No expense found</span>
                        </div>`;
                    } else {
                        res.rows.forEach((row, i) => {
                            rows += `
<tr>
    <td>${i + 1}</td>
    <td>${row.evid}</td>
    <td>${row.date}</td>
    <td>${row.head ?? '-'}</td>
    <td>${row.remarks ?? '-'}</td>
    <td>${row.account ?? '-'}</td>
    <td class="text-end">${row.amount}</td>
</tr>`;

                            cards += `
<div class="expc-card">
    <div class="expc-head">
        <div class="expc-head-l">
            <span class="expc-vno"><i class="bi bi-receipt"></i>${row.evid}</span>
            <span class="expc-sub"><i class="bi bi-calendar3"></i>${row.date}</span>
        </div>
        <span class="expc-id">#${i + 1}</span>
    </div>
    <div class="expc-meta">
        <span><i class="bi bi-folder2-open"></i>${row.head ?? '-'}</span>
        <span><i class="bi bi-building"></i>${row.account ?? '-'}</span>
    </div>
    <div class="expc-remarks"><i class="bi bi-chat-left-text"></i><span>${row.remarks ?? '-'}</span></div>
    <div class="expc-total"><span>Amount</span><b>${row.amount}</b></div>
</div>`;
                        });
                    }

                    $('#expenseRows').html(rows);
                    $('#grandTotal').text(res.total);
                    $('#expenseCards').html(cards + `
<div class="expc-card expc-grand">
    <div class="expc-head">
        <div class="expc-head-l">
            <span class="expc-vno"><i class="bi bi-calculator"></i> Total Expense</span>
        </div>
    </div>
    <div class="expc-total"><span>Grand Total</span><b>${res.total}</b></div>
</div>`);
                    $('#resultCard').removeClass('d-none');
                }
            });
        });

    });
</script>
@endsection