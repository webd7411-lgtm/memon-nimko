@extends('admin_panel.layout.app')
@section('content')
@include('admin_panel.partials.mgmt_cards')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .rp-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2rem; }
    .rp-mgmt { --rp-accent: #14b8a6; --rp-accent-2: #0f766e; }
    .rp-mgmt * { font-family: inherit; }

    .rp-hdr {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #134e4a 0%, #14b8a6 100%);
        border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.4rem;
        box-shadow: 0 16px 40px rgba(20, 184, 166, .25);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem;
    }
    .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
    .rp-hdr h2 i { color: #99f6e4; }
    .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .8); font-size: .85rem; font-weight: 500; }

    .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
    .rp-btn-primary { background: linear-gradient(135deg, #14b8a6, #0f766e); color: #fff; box-shadow: 0 6px 18px rgba(20, 184, 166, .35); }
    .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(20, 184, 166, .42); color: #fff; }

    .rp-card { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05); overflow: hidden; }
    .rp-card-body { padding: 1.4rem; }

    /* ── Stats strip ── */
    .rp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: .9rem; margin-bottom: 1.4rem; }
    .rp-stat { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; padding: .9rem 1.1rem; display: flex; align-items: center; gap: .8rem; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 6px 18px rgba(0, 0, 0, .04); }
    .rp-stat-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); color: #fff; box-shadow: 0 4px 12px rgba(20, 184, 166, .3); }
    .rp-stat-tx .t { font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .4px; color: #64748b; }
    .rp-stat-tx .v { font-size: 1.05rem; font-weight: 800; color: #0f172a; }
    .rp-stat-tx .v small { font-size: .72rem; font-weight: 700; color: var(--rp-accent); }

    .rp-mgmt table.rp-table.dataTable { border-collapse: separate; border-spacing: 0; font-size: .8rem; }
    .rp-mgmt .rp-table thead th { background: #0f172a !important; color: #94a3b8 !important; font-size: .64rem; font-weight: 700; text-transform: uppercase; letter-spacing: .45px; padding: .65rem .7rem; border: none; border-bottom: 2px solid #0f172a !important; text-align: left; white-space: nowrap; }
    .rp-mgmt .rp-table thead th:first-child { border-radius: 10px 0 0 0; }
    .rp-mgmt .rp-table thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-mgmt .rp-table tbody td { padding: .55rem .7rem; border: none; border-bottom: 1px solid #f1f4f9 !important; vertical-align: middle; color: #334155; }
    .rp-mgmt .rp-table tbody tr:nth-of-type(odd) td { background: transparent !important; }
    .rp-mgmt .rp-table tbody tr:hover td { background: #fafbfc !important; }
    .rp-mgmt .rp-table tbody tr:last-child td { border-bottom: none !important; }

    .rp-mgmt .rp-table .amt { font-weight: 800; color: var(--rp-accent); white-space: nowrap; }
    .rp-mgmt .rp-table .payno { font-weight: 700; color: #0f172a; white-space: nowrap; }
    .rp-mgmt .rp-table .note-cell { color: #64748b; font-size: .76rem; }

    .rp-mgmt .dataTables_wrapper { padding: .25rem; }
    .rp-mgmt .dataTables_length, .rp-mgmt .dataTables_info, .rp-mgmt .dataTables_filter { font-size: .78rem; font-weight: 600; color: #54657e; }
    .rp-mgmt .dataTables_filter input { border: 1.5px solid #e9edf2; border-radius: 9px; padding: .35rem .6rem; font-size: .8rem; outline: none; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .dataTables_filter input:focus { border-color: var(--rp-accent); box-shadow: 0 0 0 3px rgba(20, 184, 166, .12); }
    .rp-mgmt .dataTables_length select { border: 1.5px solid #e9edf2; border-radius: 8px; padding: .3rem .5rem; font-size: .78rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .page-link { color: #334155; border: none; border-radius: 8px; margin: 0 2px; font-size: .75rem; font-weight: 700; padding: .3rem .6rem; }
    .rp-mgmt .page-item.active .page-link { background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); border: none; color: #fff; }

    .rp-mgmt .btn { border-radius: 8px; font-size: .7rem; font-weight: 700; letter-spacing: .2px; padding: .32rem .6rem; border: none; transition: all .2s ease; }
    .rp-mgmt .btn:hover { transform: translateY(-1px); }
    .rp-mgmt .btn-info { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; }
    .rp-mgmt .btn-primary { background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); color: #fff; }
    .rp-mgmt .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
    .rp-mgmt .btn-secondary { background: #eef2f7; color: #334155; }

    .rp-mgmt .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(2, 6, 23, .28); }
    .rp-mgmt .modal-header { background: linear-gradient(135deg, #134e4a, #14b8a6); border-bottom: none; }
    .rp-mgmt .modal-header .modal-title { color: #fff; font-weight: 800; font-size: 1.02rem; }
    .rp-mgmt .modal-body { padding: 1.3rem 1.4rem; }
    .rp-mgmt .modal-body .form-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .35px; color: #54657e; margin-bottom: .3rem; }
    .rp-mgmt .modal-body .form-control, .rp-mgmt .modal-body .form-select { border: 1.5px solid #e9edf2; border-radius: 10px; padding: .5rem .8rem; font-size: .84rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .modal-body .form-control:focus, .rp-mgmt .modal-body .form-select:focus { border-color: var(--rp-accent); box-shadow: 0 0 0 3px rgba(20, 184, 166, .12); }
    .rp-mgmt .modal-body .fw-words { background: #f8fafc !important; font-weight: 600; color: #334155; }
    .rp-mgmt .modal-footer { border-top: 1px solid #eef2f7; padding: .9rem 1.4rem; }

    .rp-mgmt .rp-chip { display: inline-flex; align-items: center; font-size: .68rem; font-weight: 700; letter-spacing: .3px; padding: .3rem .6rem; border-radius: 7px; }
    .rp-mgmt .rp-chip.bg-teal { background: #ccfbf1 !important; color: #0f766e !important; }
    .rp-mgmt .rp-chip.bg-method { background: #f1f5f9 !important; color: #475569 !important; }

    .rp-mgmt .alert-premium { display: flex; align-items: center; gap: .6rem; background: #ccfbf1; color: #134e4a; border: 1px solid #5eead4; border-radius: 12px; padding: .75rem 1rem; font-size: .85rem; font-weight: 700; margin-bottom: 1.2rem; }
    .rp-mgmt .alert-premium i { font-size: 1.1rem; }

    @media (max-width: 575.98px) {
        .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: flex-start; }
        .rp-hdr h2 { font-size: 1.15rem; }
        .rp-btn { width: 100%; }
    }
</style>
<div class="rp-mgmt">
    <div class="container-fluid px-3 px-md-4 py-3">
        <div class="rp-hdr">
            <div>
                <h2><i class="bi bi-cash-coin"></i> Vendor Payments</h2>
                <p>Record and manage vendor payment transactions with ledger updates</p>
            </div>
            <button type="button" class="rp-btn rp-btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal" onclick="clearPaymentForm()">
                <i class="bi bi-plus-lg"></i> Add Payment
            </button>
        </div>

        @if (session()->has('success'))
        <div class="alert-premium">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
        </div>
        @endif

        {{-- Stats strip --}}
        <div class="rp-stats">
            <div class="rp-stat">
                <span class="rp-stat-ic"><i class="bi bi-receipt"></i></span>
                <div class="rp-stat-tx">
                    <div class="t">Total Payments</div>
                    <div class="v">{{ $payments->count() }}</div>
                </div>
            </div>
            <div class="rp-stat">
                <span class="rp-stat-ic"><i class="bi bi-cash-stack"></i></span>
                <div class="rp-stat-tx">
                    <div class="t">Total Amount</div>
                    <div class="v">{{ number_format($payments->sum('amount'), 2) }}</div>
                </div>
            </div>
            <div class="rp-stat">
                <span class="rp-stat-ic"><i class="bi bi-people"></i></span>
                <div class="rp-stat-tx">
                    <div class="t">Vendors</div>
                    <div class="v">{{ $vendors->count() }}</div>
                </div>
            </div>
        </div>

        <div class="rp-card">
            <div class="rp-card-body p-0">
                <div class="table-responsive">
                    <table id="default-datatable" class="table rp-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">S.No</th>
                                <th>Payment No</th>
                                <th>Vendor</th>
                                <th class="text-end">Amount</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Note</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $key => $pay)
                            <tr>
                                <td class="text-center">
                                    <span class="rp-chip bg-teal">{{ $key + 1 }}</span>
                                </td>
                                <td class="payno">{{ $pay->payment_no ?? 'N/A' }}</td>
                                <td class="fw-bold">{{ $pay->vendor->name ?? 'N/A' }}</td>
                                <td class="amt text-end">{{ number_format($pay->amount, 2) }}</td>
                                <td><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $pay->payment_date }}</td>
                                <td><span class="rp-chip bg-method">{{ $pay->payment_method ?? '—' }}</span></td>
                                <td class="note-cell">{{ $pay->note ?? '—' }}</td>
                                <td class="text-center" style="white-space:nowrap;">
                                    <a href="{{ route('vendor.payments.edit', $pay->id) }}" class="btn btn-info me-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="{{ route('vendor.payment.receipt', $pay->id) }}" target="_blank" class="btn btn-primary me-1" title="Print">
                                        <i class="bi bi-printer"></i> Print
                                    </a>
                                    <a href="{{ route('vendor.payment.delete', $pay->id) }}"
                                        class="btn btn-danger delete-pay-btn"
                                        data-id="{{ $pay->id }}"
                                        data-msg="Are you sure you want to delete this payment? The ledger will be updated."
                                        title="Delete">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Mobile / Tablet premium cards --}}
        <div class="rp-ucards" id="vendorPaymentCards">
            @forelse($payments as $pay)
            <div class="rp-ucard">
                <div class="rp-uc-top">
                    <span class="rp-uc-av"><i class="bi bi-cash-stack"></i></span>
                    <div class="rp-uc-idf">
                        <span class="rp-uc-nm">{{ $pay->payment_no ?? 'N/A' }}</span>
                        <span class="rp-uc-em">{{ $pay->vendor->name ?? 'N/A' }}</span>
                    </div>
                    <span class="rp-uc-ix">{{ $pay->payment_date }}</span>
                </div>
                <div class="rp-uc-bal">
                    <span class="bl">Amount</span>
                    <b>{{ number_format($pay->amount, 2) }}</b>
                </div>
                <div class="rp-uc-info">
                    <span>Method: {{ $pay->payment_method ?? '—' }}</span>
                    <span>Note: {{ $pay->note ?? '—' }}</span>
                </div>
                <div class="rp-uc-acts">
                    <a href="{{ route('vendor.payments.edit', $pay->id) }}" class="btn btn-info">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <a href="{{ route('vendor.payment.receipt', $pay->id) }}" target="_blank" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Print
                    </a>
                    <a href="{{ route('vendor.payment.delete', $pay->id) }}"
                        class="btn btn-danger delete-pay-btn"
                        data-id="{{ $pay->id }}"
                        data-msg="Are you sure you want to delete this payment? The ledger will be updated.">
                        <i class="bi bi-trash"></i> Delete
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4">No payments found</div>
            @endforelse
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal">
        <div class="modal-dialog">
            <form action="{{ route('vendor.payments.store') }}" method="POST">@csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-cash-coin me-1"></i> Add Vendor Payment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-select" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Balance</label>
                            <input type="text" id="vendor_stock" class="form-control" readonly placeholder="Select a vendor">
                        </div>

                        <div class="row g-3 mb-1">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment Date</label>
                                    <input type="date" name="payment_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="adjustment_type" class="form-select" required>
                                        <option value="minus">Minus (Payment)</option>
                                        <option value="plus">Plus (Return / Advance)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount in Words</label>
                            <input type="text" id="amount_in_words" class="form-control fw-words" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <input type="text" name="payment_method" class="form-control" placeholder="e.g. Cash, Bank">
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control" placeholder="Optional note" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Save Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#default-datatable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            order: [[0, 'desc']],
            language: {
                search: "Search Payments:",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        // Delete with SweetAlert
        $(document).on('click', '.delete-pay-btn', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var msg = $(this).data('msg');
            Swal.fire({
                title: 'Confirm Deletion',
                text: msg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        // Load vendor current balance
        $('#vendor_id').on('change', function() {
            var vendorId = $(this).val();
            if (vendorId) {
                $.ajax({
                    url: '/get-vendor-balance/' + vendorId,
                    type: 'GET',
                    success: function(data) {
                        $('#vendor_stock').val(data.closing_balance);
                    },
                    error: function() {
                        $('#vendor_stock').val('0');
                    }
                });
            } else {
                $('#vendor_stock').val('');
            }
        });

        function numberToWords(num) {
            if (!num || num === 0) return 'Zero Rupees Only';

            const a = [
                '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven',
                'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen',
                'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
            ];
            const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty',
                'Sixty', 'Seventy', 'Eighty', 'Ninety'
            ];

            function inWords(n) {
                if (n < 20) return a[n];
                if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? ' ' + a[n % 10] : '');
                if (n < 1000)
                    return a[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' ' + inWords(n % 100) : '');
                if (n < 100000)
                    return inWords(Math.floor(n / 1000)) + ' Thousand' + (n % 1000 ? ' ' + inWords(n % 1000) : '');
                if (n < 10000000)
                    return inWords(Math.floor(n / 100000)) + ' Lakh' + (n % 100000 ? ' ' + inWords(n % 100000) : '');
                return inWords(Math.floor(n / 10000000)) + ' Crore' + (n % 10000000 ? ' ' + inWords(n % 10000000) : '');
            }

            let parts = num.toString().split('.');
            let rupees = parseInt(parts[0]);
            let paisa = parts[1] ? parseInt(parts[1].substring(0, 2)) : 0;

            let words = inWords(rupees) + ' Rupees';

            if (paisa > 0) {
                words += ' and ' + inWords(paisa) + ' Paisa';
            }

            return words + ' Only';
        }

        $(document).on('input', 'input[name="amount"]', function() {
            let val = $(this).val();
            $('#amount_in_words').val(val ? numberToWords(val) : '');
        });

        function clearPaymentForm() {
            $('#paymentModal').find('form')[0].reset ? $('#paymentModal').find('form')[0].reset() : null;
            $('#amount_in_words').val('');
            $('#vendor_stock').val('');
        }
    });
</script>
@endsection