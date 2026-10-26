@extends('admin_panel.layout.app')

@section('content')
<style>
    .dist-module-container {
        padding-left: 18px !important;
        padding-right: 18px !important;
        padding-top: 10px !important;
        padding-bottom: 30px !important;
    }
    @media (min-width: 992px) {
        .dist-module-container {
            padding-left: 28px !important;
            padding-right: 28px !important;
            padding-top: 16px !important;
        }
    }
    .dist-kpi-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #eef2f6;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 14px 18px !important;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .hover-underline:hover {
        text-decoration: underline !important;
    }

    /* Print Styles */
    @media print {
        header, .navbar, .dist-nav-container, .btn, .card-header form, .alert, .modal, .no-print {
            display: none !important;
        }
        body, .container-fluid, .dist-module-container {
            padding: 0 !important;
            margin: 0 !important;
            background: #ffffff !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
        .print-header {
            display: block !important;
        }
        .table {
            font-size: 11pt !important;
        }
    }
    .print-header {
        display: none;
    }
</style>

<div class="container-fluid dist-module-container">
    <!-- Horizontal Navigation Tabs -->
    <div class="no-print">
        @include('admin_panel.distributors.partials.navigation_tabs')
    </div>

    <!-- Print Only Header -->
    <div class="print-header mb-4 text-center border-bottom pb-3">
        <h3 class="fw-bold mb-1">MEMON NIMKO & FOODS</h3>
        <p class="mb-1 text-muted">Retail Customer Statement of Account / Ledger</p>
        <div class="row text-start mt-3">
            <div class="col-6">
                <strong>Retailer:</strong> {{ $customer->shop_name }}<br>
                <strong>Proprietor:</strong> {{ $customer->owner_name ?: 'N/A' }}<br>
                <strong>Phone:</strong> {{ $customer->phone ?: '—' }}<br>
                <strong>Area / Market:</strong> {{ $customer->area ?: '—' }}
            </div>
            <div class="col-6 text-end">
                <strong>Distributor:</strong> {{ $distributor->name ?? 'N/A' }} ({{ $distributor->code ?? '' }})<br>
                <strong>Distributor Phone:</strong> {{ $distributor->phone ?? '—' }}<br>
                <strong>Generated Date:</strong> {{ date('d M, Y h:i A') }}
            </div>
        </div>
    </div>

    <!-- Top Action Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom no-print">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.distributors.customers') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1">
                    <i class="fas fa-arrow-left me-1"></i> Back to Retailers
                </a>
                <span class="text-muted">•</span>
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.72rem;">
                    Retail Account Ledger
                </span>
            </div>
            <h4 class="mb-0 mt-2 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-store" style="color: #0f766e;"></i>
                {{ $customer->shop_name }}
            </h4>
            <div class="text-muted small mt-0.5">
                <span><i class="fas fa-user me-1"></i> Owner: <strong>{{ $customer->owner_name ?: 'N/A' }}</strong></span>
                <span class="mx-2">•</span>
                <span><i class="fas fa-phone me-1"></i> <strong>{{ $customer->phone ?: '—' }}</strong></span>
                <span class="mx-2">•</span>
                <span><i class="fas fa-map-marker-alt me-1"></i> {{ $customer->area ?: 'General Market' }}</span>
                <span class="mx-2">•</span>
                <span><i class="fas fa-truck-loading me-1"></i> Assigned: <strong>{{ $distributor->name ?? 'N/A' }}</strong> ({{ $distributor->code ?? '' }})</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Statement
            </button>
            <button type="button" class="btn btn-sm text-white rounded-pill px-3 py-1.5 fw-bold shadow-sm"
                    style="background: linear-gradient(135deg, #0f766e, #0d9488); border: 1px solid #14b8a6;"
                    data-bs-toggle="modal" data-bs-target="#receiveCustomerPaymentModal">
                <i class="fas fa-hand-holding-usd me-1"></i> + Record Payment
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-3 no-print" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-3 no-print" role="alert">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Balance Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #0f766e !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Billed / Goods Sold (Debit)</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.35rem;">Rs. {{ number_format($totalDebit, 2) }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(15, 118, 110, 0.1); color: #0f766e; font-size: 1.15rem;">
                    <i class="fas fa-cart-arrow-down"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #10b981 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Payments Collected (Credit)</span>
                    <h3 class="fw-bold mb-0 text-success mt-1" style="font-size: 1.35rem;">Rs. {{ number_format($totalCredit, 2) }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.15rem;">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #ef4444 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Current Outstanding Balance (Due)</span>
                    <h3 class="fw-bold mb-0 {{ $customer->current_balance > 0 ? 'text-danger' : 'text-success' }} mt-1" style="font-size: 1.35rem;">
                        Rs. {{ number_format($customer->current_balance, 2) }}
                    </h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 1.15rem;">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.distributors.customers.ledger', $customer->id) }}" class="row g-2 align-items-center">
                <div class="col-12 col-sm-4 col-md-3">
                    <label class="form-label small text-muted mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-sm-4 col-md-3">
                    <label class="form-label small text-muted mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-12 col-sm-4 col-md-3 d-flex align-items-end gap-1 mt-auto">
                    <button type="submit" class="btn btn-sm text-white rounded-pill px-3 shadow-sm w-100" style="background: #0f766e;">
                        <i class="fas fa-filter me-1"></i> Filter Entries
                    </button>
                    @if(request('start_date') || request('end_date'))
                    <a href="{{ route('admin.distributors.customers.ledger', $customer->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" title="Reset Dates">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
                <div class="col-12 col-md-3 text-md-end mt-auto text-muted small">
                    <span>Total Entries: <strong>{{ count($entries) }}</strong></span>
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list-alt me-2 text-teal" style="color: #0f766e;"></i> Account Ledger Statement
            </h6>
            @if(request('start_date') || request('end_date'))
            <span class="badge bg-light text-dark border">
                Period: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M, Y') : 'Start' }} to {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M, Y') : 'Today' }}
            </span>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Type</th>
                        <th>Invoice / Ref #</th>
                        <th>Description / Transaction Details</th>
                        <th class="text-end">Sale (Debit)</th>
                        <th class="text-end">Payment (Credit)</th>
                        <th class="text-end pe-4">Running Balance</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @php
                        $running = (float)($openingBalanceBeforeFilter ?? 0);
                    @endphp

                    {{-- If filtered by start date and there was a prior balance, show opening row --}}
                    @if(request('start_date') && $openingBalanceBeforeFilter != 0)
                    <tr class="bg-light fw-bold">
                        <td class="ps-4 text-muted">{{ \Carbon\Carbon::parse(request('start_date'))->format('d M, Y') }}</td>
                        <td><span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5">Opening</span></td>
                        <td class="text-muted">—</td>
                        <td>Opening Balance (Brought Forward prior to {{ \Carbon\Carbon::parse(request('start_date'))->format('d M, Y') }})</td>
                        <td class="text-end">—</td>
                        <td class="text-end">—</td>
                        <td class="text-end pe-4 text-dark">Rs. {{ number_format($running, 2) }}</td>
                    </tr>
                    @endif

                    @forelse($entries as $entry)
                    @php
                        $running += ((float)$entry->debit - (float)$entry->credit);
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($entry->date)->format('d M, Y') }}</span>
                        </td>
                        <td>
                            @if($entry->type === 'sale')
                            <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-0.5">
                                <i class="fas fa-shopping-cart me-1"></i> Sale
                            </span>
                            @elseif($entry->type === 'payment_received')
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-0.5">
                                <i class="fas fa-money-bill-wave me-1"></i> Payment
                            </span>
                            @elseif($entry->type === 'opening')
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5">
                                <i class="fas fa-flag me-1"></i> Opening
                            </span>
                            @elseif($entry->type === 'return')
                            <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-0.5">
                                <i class="fas fa-undo me-1"></i> Return
                            </span>
                            @else
                            <span class="badge bg-light text-secondary border px-2 py-0.5">
                                {{ ucfirst($entry->type) }}
                            </span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($entry->reference_no))
                                @if(isset($invoices[$entry->reference_no]))
                                <span class="font-monospace fw-bold text-primary">
                                    {{ $entry->reference_no }}
                                </span>
                                @else
                                <span class="font-monospace text-muted small">
                                    {{ $entry->reference_no }}
                                </span>
                                @endif
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">{{ $entry->description }}</div>
                        </td>
                        <td class="text-end">
                            @if($entry->debit > 0)
                            <span class="fw-bold text-danger">Rs. {{ number_format($entry->debit, 2) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($entry->credit > 0)
                            <span class="fw-bold text-success">Rs. {{ number_format($entry->credit, 2) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <span class="fw-bold {{ $running > 0 ? 'text-danger' : 'text-success' }}">
                                Rs. {{ number_format($running, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Transactions on Record</p>
                            <span class="small">Sales and payments recorded for this shop will appear here automatically.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($entries) > 0)
                <tfoot style="background: #f1f5f9; font-size: 0.92rem; font-weight: 700;">
                    <tr>
                        <td colspan="4" class="ps-4 text-uppercase">Total Period Summary</td>
                        <td class="text-end text-danger">Rs. {{ number_format($totalDebit, 2) }}</td>
                        <td class="text-end text-success">Rs. {{ number_format($totalCredit, 2) }}</td>
                        <td class="text-end pe-4 {{ $running > 0 ? 'text-danger' : 'text-success' }}">
                            Rs. {{ number_format($running, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL: RECORD PAYMENT FROM RETAILER (Compact No-Scroll) -->
<!-- ============================================== -->
<div class="modal fade no-print" id="receiveCustomerPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 680px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.distributors.customers.payment.store', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header py-2 px-3 text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-white fs-6"></i>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" style="font-size: 0.95rem;">Receive Direct Customer Payment</h6>
                            <span class="text-white-50" style="font-size: 0.7rem;">Deposit into bank, clear customer due & update distributor ledger</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <!-- Compact Info Banner -->
                    <div class="p-2 px-3 mb-2 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $customer->shop_name }}</span>
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5" style="font-size: 0.68rem;">
                                {{ $distributor->name ?? 'Distributor' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-muted small me-1">Current Due:</span>
                            <span class="fw-bold text-danger" style="font-size: 0.95rem;">Rs. {{ number_format($customer->current_balance, 2) }}</span>
                        </div>
                    </div>

                    <div class="row g-2">
                        <!-- Left Column -->
                        <div class="col-12 col-md-6">
                            <!-- Receiving Account -->
                            <div class="mb-2">
                                <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.76rem;">
                                    <i class="fas fa-university text-teal me-1" style="color: #0f766e;"></i> Receiving Bank / Cash Account *
                                </label>
                                <select name="account_id" class="form-select form-select-sm" required style="font-size: 0.8rem;">
                                    <option value="">-- Select Bank / Cash Account --</option>
                                    @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $acc->title }} ({{ $acc->account_code }}) [Bal: Rs. {{ number_format($acc->opening_balance, 2) }}]
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Amount Received -->
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold text-dark mb-0" style="font-size: 0.76rem;">Amount Collected (Rs.) *</label>
                                    @if($customer->current_balance > 0)
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" style="font-size: 0.72rem; color: #0f766e;"
                                            onclick="document.getElementById('ledgerPayAmount').value = '{{ $customer->current_balance }}'">
                                        <i class="fas fa-bolt text-warning me-0.5"></i> Settle Full (Rs. {{ number_format($customer->current_balance, 2) }})
                                    </button>
                                    @endif
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text fw-bold">Rs.</span>
                                    <input type="number" step="0.01" min="0.01" name="amount" id="ledgerPayAmount" class="form-control fw-bold" placeholder="0.00" required>
                                </div>
                            </div>

                            <!-- Payment Date & Method -->
                            <div class="row g-1.5 mb-1">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Date *</label>
                                    <input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required style="font-size: 0.8rem;">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Method</label>
                                    <select name="payment_method" class="form-select form-select-sm" style="font-size: 0.8rem;">
                                        <option value="cash">Cash Counter</option>
                                        <option value="bank_transfer">Bank Transfer / Online</option>
                                        <option value="raast">Raast / QR</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="pay_order">Pay Order</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-12 col-md-6">
                            <input type="hidden" name="adjust_distributor" value="1">

                            <!-- Ref & Slip Image -->
                            <div class="row g-1.5 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Trx Ref #</label>
                                    <input type="text" name="transaction_reference" class="form-control form-control-sm" placeholder="e.g. Raast ID, IBFT Ref" style="font-size: 0.8rem;">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Slip / Proof</label>
                                    <input type="file" name="slip_image" class="form-control form-control-sm" accept="image/*" style="font-size: 0.75rem;">
                                </div>
                            </div>

                            <!-- Notes / Description -->
                            <div class="mb-2">
                                <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.76rem;">Notes / Description</label>
                                <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Optional notes, recovery details..." style="font-size: 0.8rem;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small" style="font-size: 0.72rem;">* Bank balance & customer ledger will be instantly credited</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal" style="font-size: 0.78rem;">Cancel</button>
                        <button type="submit" class="btn btn-sm text-white rounded-pill px-3.5 fw-semibold shadow-sm" style="background: #0f766e; font-size: 0.78rem;">
                            <i class="fas fa-check me-1"></i> Receive & Credit to Bank
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
