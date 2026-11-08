@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('distributor.dashboard') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-white-50">&bull;</span>
                <a href="{{ route('distributor.customers.index') }}" class="text-white-50 text-decoration-none small">
                    Retail Network
                </a>
                <span class="text-white-50">&bull;</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 118, 110, 0.4); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4;">
                    Collections & Recoveries
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Customer Collections (Date-wise)</h4>
            <p class="mb-0 text-white-50 small">Track and audit cash, cheque, and bank payments collected from retail shops</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Sheet
            </button>
            <button type="button" class="btn btn-sm btn-teal rounded-pill px-3 py-2 shadow-sm fw-bold"
                    data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                <i class="fas fa-hand-holding-usd me-1"></i> + Collect Payment
            </button>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- KPI Metric Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #0f766e !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Filtered Collection</span>
                        <h3 class="fw-bold mb-0 text-teal-dark mt-1" style="color: #0f766e;">Rs. {{ number_format($filteredTotal, 2) }}</h3>
                        <span class="text-muted small">{{ $filteredCount }} receipt(s) in selected range</span>
                    </div>
                    <div class="p-2.5 rounded-circle bg-teal-subtle text-teal">
                        <i class="fas fa-filter fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #14b8a6 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Today's Collection</span>
                        <h3 class="fw-bold mb-0 text-dark mt-1">Rs. {{ number_format($todayTotal, 2) }}</h3>
                        <span class="text-muted small">{{ now()->format('d M, Y') }}</span>
                    </div>
                    <div class="p-2.5 rounded-circle bg-success-subtle text-success">
                        <i class="fas fa-calendar-day fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #3b82f6 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">This Month Collection</span>
                        <h3 class="fw-bold mb-0 text-primary mt-1">Rs. {{ number_format($monthTotal, 2) }}</h3>
                        <span class="text-muted small">{{ now()->format('F Y') }}</span>
                    </div>
                    <div class="p-2.5 rounded-circle bg-primary-subtle text-primary">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #64748b !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">All-Time Collections</span>
                        <h3 class="fw-bold mb-0 text-secondary mt-1">Rs. {{ number_format($allTimeTotal, 2) }}</h3>
                        <span class="text-muted small">Total lifetime recoveries</span>
                    </div>
                    <div class="p-2.5 rounded-circle bg-secondary-subtle text-secondary">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range & Search Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('distributor.customer_payments.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fas fa-calendar-alt me-1 text-teal"></i> Start Date
                        </label>
                        <input type="date" name="start_date" id="startDate" class="form-control rounded-3"
                               value="{{ request('start_date', $startDate) }}">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fas fa-calendar-alt me-1 text-teal"></i> End Date
                        </label>
                        <input type="date" name="end_date" id="endDate" class="form-control rounded-3"
                               value="{{ request('end_date', $endDate) }}">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fas fa-store me-1 text-teal"></i> Retail Customer
                        </label>
                        <select name="customer_id" class="form-select rounded-3">
                            <option value="">-- All Customers / Shops --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->shop_name }} ({{ $c->owner_name ?: 'No owner' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fas fa-university me-1 text-teal"></i> Deposited Account
                        </label>
                        <select name="account_id" class="form-select rounded-3">
                            <option value="">-- All Accounts --</option>
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-semibold text-muted mb-1">
                            <i class="fas fa-search me-1 text-teal"></i> Search Reference / Notes
                        </label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control rounded-start-3"
                                   placeholder="Search by receipt # (REC-), shop name, cheque #, or remarks..."
                                   value="{{ request('search') }}">
                            <button type="submit" class="btn btn-teal px-4 fw-semibold">
                                <i class="fas fa-filter me-1"></i> Apply Filter
                            </button>
                            <a href="{{ route('distributor.customer_payments.index') }}" class="btn btn-outline-secondary px-3">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                    <!-- Quick Date Shortcuts -->
                    <div class="col-12 col-md-4 d-flex align-items-end flex-wrap gap-1">
                        <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-1" onclick="setQuickDate('today')">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-1" onclick="setQuickDate('yesterday')">Yesterday</button>
                        <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-1" onclick="setQuickDate('this_month')">This Month</button>
                        <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-1" onclick="setQuickDate('last_30_days')">Last 30 Days</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-receipt me-2 text-teal"></i> Collection Receipts
                </h5>
                <span class="text-muted small">Showing collections between {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }} and {{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }}</span>
            </div>
            <div class="badge px-3 py-2 rounded-pill fs-6" style="background: rgba(15, 118, 110, 0.1); color: #0f766e; border: 1px solid rgba(20, 184, 166, 0.3);">
                Total: <strong>Rs. {{ number_format($filteredTotal, 2) }}</strong>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-semibold">
                        <tr>
                            <th class="ps-4 py-3">Receipt #</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Retail Shop / Customer</th>
                            <th class="py-3">Account (Deposited)</th>
                            <th class="py-3">Payment Details / Notes</th>
                            <th class="py-3 text-end">Amount (PKR)</th>
                            <th class="py-3 text-end">Cust. Balance</th>
                            <th class="py-3 text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td class="ps-4">
                                <span class="badge rounded-pill px-2.5 py-1" style="background: #f1f5f9; color: #0f766e; border: 1px solid #cbd5e1; font-family: monospace; font-size: 0.85rem;">
                                    {{ $payment->reference_no ?: ('REC-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT)) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($payment->date)->format('d M, Y') }}</div>
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($payment->date)->format('l') }}</span>
                            </td>
                            <td>
                                @if($payment->customer)
                                    <div class="fw-bold text-dark">{{ $payment->customer->shop_name }}</div>
                                    <div class="text-muted small">
                                        {{ $payment->customer->owner_name ?? '' }}
                                        @if($payment->customer->phone)
                                            &bull; <i class="fas fa-phone-alt fa-xs"></i> {{ $payment->customer->phone }}
                                        @endif
                                        @if($payment->customer->area)
                                            &bull; <span class="badge bg-light text-secondary border">{{ $payment->customer->area }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Customer deleted</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->account)
                                    <span class="badge rounded-pill bg-light text-dark border px-2.5 py-1 fw-semibold">
                                        <i class="fas fa-wallet text-teal me-1"></i> {{ $payment->account->title }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-dark small" style="max-width: 280px; word-wrap: break-word;">
                                    {{ $payment->description ?? 'Cash Payment' }}
                                </div>
                                @if($payment->creator)
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-user-edit fa-xs"></i> By: {{ $payment->creator->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold fs-6" style="background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.3);">
                                    Rs. {{ number_format($payment->credit, 2) }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-muted">
                                Rs. {{ number_format($payment->balance, 2) }}
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('distributor.customer_payments.receipt', $payment->id) }}"
                                       target="_blank"
                                       class="btn btn-outline-secondary rounded-pill px-2.5 me-1"
                                       title="Print Thermal Receipt">
                                        <i class="fas fa-print text-primary"></i> Receipt
                                    </a>
                                    @if($payment->customer)
                                    <a href="{{ route('distributor.customers.ledger', $payment->customer->id) }}"
                                       class="btn btn-outline-secondary rounded-pill px-2.5 me-1"
                                       title="View Full Ledger">
                                        <i class="fas fa-book text-teal"></i> Ledger
                                    </a>
                                    @endif
                                    <form action="{{ route('distributor.customer_payments.destroy', $payment->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to reverse this payment of Rs. {{ number_format($payment->credit, 2) }}? Customer balance will be restored.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger rounded-pill px-2" title="Reverse / Delete Payment">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6>No payment collections found in this date range.</h6>
                                <p class="small text-muted mb-3">Adjust the date filters above or collect a new payment from a retail customer.</p>
                                <button type="button" class="btn btn-sm btn-teal rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                                    <i class="fas fa-hand-holding-usd me-1"></i> Collect Payment Now
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($payments->count() > 0)
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="5" class="ps-4 py-3 text-uppercase text-muted">Page Total Collection:</td>
                            <td class="text-end py-3 text-success fs-6">
                                Rs. {{ number_format($payments->sum('credit'), 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        @if($payments->hasPages())
        <div class="card-footer bg-white border-0 py-3 px-4">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Collect Payment Modal (Supports Cash & Cheque) -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('distributor.customer_payments.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-hand-holding-usd me-2"></i> Collect Payment from Retailer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Retail Customer Select -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Select Retail Customer / Shop <span class="text-danger">*</span></label>
                        <select name="distributor_customer_id" class="form-select rounded-3" required id="modalCustomerSelect" onchange="updateCustomerDue()">
                            <option value="">-- Choose Retail Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-balance="{{ $c->current_balance }}">
                                {{ $c->shop_name }} ({{ $c->owner_name ?: 'No owner' }}) — Due: Rs. {{ number_format($c->current_balance, 2) }}
                            </option>
                            @endforeach
                        </select>
                        <div id="customerDueAlert" class="mt-2 p-2 rounded-3 bg-light border small text-muted d-none">
                            Current Outstanding Due: <strong class="text-danger" id="customerDueText">Rs. 0.00</strong>
                        </div>
                    </div>

                    <!-- Payment Date & Amount -->
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control rounded-3" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark">Amount (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" class="form-control rounded-3 fw-bold text-success fs-5" placeholder="0.00" required>
                        </div>
                    </div>

                    <!-- Payment Method (Cash, Cheque, Bank Transfer) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="paymentMethodSelect" class="form-select rounded-3" onchange="toggleChequeFields()">
                            <option value="cash" selected>Cash in Hand</option>
                            <option value="cheque">Cheque</option>
                            <option value="bank_transfer">Bank Transfer / Online</option>
                        </select>
                    </div>

                    <!-- Cheque Details Section (Toggled dynamically) -->
                    <div id="chequeFields" class="p-3 mb-3 rounded-3 bg-light border d-none">
                        <div class="small fw-bold text-teal mb-2"><i class="fas fa-money-check me-1"></i> Cheque Particulars</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Cheque Number</label>
                                <input type="text" name="cheque_no" class="form-control form-control-sm rounded-2" placeholder="e.g. 12345678">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Bank Name</label>
                                <input type="text" name="cheque_bank" class="form-control form-control-sm rounded-2" placeholder="e.g. HBL / Meezan">
                            </div>
                            <div class="col-12 mt-2">
                                <label class="form-label small fw-semibold">Cheque Date</label>
                                <input type="date" name="cheque_date" class="form-control form-control-sm rounded-2" value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Receiving Account -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Receiving Account <span class="text-danger">*</span></label>
                        <select name="account_id" class="form-select rounded-3" required>
                            @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->type }})</option>
                            @endforeach
                        </select>
                        <span class="text-muted small">The account into which this cash/cheque is deposited.</span>
                    </div>

                    <!-- Remarks / Notes -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Remarks / Receipt Note</label>
                        <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Optional notes, recovery person name, etc..."></textarea>
                    </div>

                    <!-- Redirect to Receipt checkbox -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="redirect_to_receipt" value="1" id="printReceiptCheck" checked>
                        <label class="form-check-label small text-muted" for="printReceiptCheck">
                            <i class="fas fa-print me-1 text-teal"></i> Open printable receipt immediately after saving
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4 fw-semibold" style="background: #0f766e;">
                        <i class="fas fa-check me-1"></i> Save Collection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setQuickDate(type) {
    const today = new Date();
    const formatDate = (d) => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    let start = new Date();
    let end = new Date();

    if (type === 'today') {
        // start and end are today
    } else if (type === 'yesterday') {
        start.setDate(today.getDate() - 1);
        end.setDate(today.getDate() - 1);
    } else if (type === 'this_month') {
        start = new Date(today.getFullYear(), today.getMonth(), 1);
        end = today;
    } else if (type === 'last_30_days') {
        start.setDate(today.getDate() - 30);
        end = today;
    }

    document.getElementById('startDate').value = formatDate(start);
    document.getElementById('endDate').value = formatDate(end);
    document.getElementById('filterForm').submit();
}

function updateCustomerDue() {
    const select = document.getElementById('modalCustomerSelect');
    const selectedOption = select.options[select.selectedIndex];
    const alertDiv = document.getElementById('customerDueAlert');
    const dueText = document.getElementById('customerDueText');

    if (selectedOption && selectedOption.dataset.balance !== undefined) {
        const bal = parseFloat(selectedOption.dataset.balance) || 0;
        dueText.innerText = 'Rs. ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        alertDiv.classList.remove('d-none');
    } else {
        alertDiv.classList.add('d-none');
    }
}

function toggleChequeFields() {
    const method = document.getElementById('paymentMethodSelect').value;
    const chequeDiv = document.getElementById('chequeFields');
    if (method === 'cheque') {
        chequeDiv.classList.remove('d-none');
    } else {
        chequeDiv.classList.add('d-none');
    }
}
</script>
@endsection
