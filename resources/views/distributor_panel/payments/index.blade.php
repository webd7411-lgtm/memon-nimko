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
                <span class="text-white-50">•</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 118, 110, 0.4); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4;">
                    Payments to Head Office
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white">Company Payment Submissions</h4>
            <p class="mb-0 text-white-50 small">Submit your payments made to the company and track verification & ledger credit status</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.company_ledger.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                <i class="fas fa-file-invoice-dollar me-1"></i> View Ledger
            </a>
            <button type="button" class="btn btn-teal btn-sm rounded-pill px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#submitPaymentModal">
                <i class="fas fa-plus-circle me-1"></i> Submit Payment
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #ef4444 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Payable to Company</span>
                        <h4 class="fw-bold mb-0 {{ $currentBalance > 0 ? 'text-danger' : 'text-success' }} mt-1">
                            Rs. {{ number_format($currentBalance, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Current outstanding in company ledger</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-danger">
                        <i class="fas fa-balance-scale fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Approved & Credited</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">
                            Rs. {{ number_format($totalApproved, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Verified by Head Office & deducted from balance</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-success">
                        <i class="fas fa-check-double fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid {{ $pendingCount > 0 ? '#f59e0b' : '#0f766e' }} !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-1.5 mb-1">
                            <span class="text-muted small fw-semibold text-uppercase">Pending Approval</span>
                            @if($pendingCount > 0)
                            <span class="badge rounded-pill shadow-sm" style="background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fcd34d !important; font-size: 0.68rem; font-weight: 700; padding: 2px 7px;">
                                <i class="fas fa-clock me-0.5"></i> {{ $pendingCount }} Pending
                            </span>
                            @else
                            <span class="badge rounded-pill" style="background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; font-size: 0.68rem; font-weight: 600; padding: 2px 7px;">
                                <i class="fas fa-check-circle me-0.5 text-success"></i> 0 Pending
                            </span>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-0 mt-1" style="color: #d97706 !important; font-size: 1.35rem;">
                            Rs. {{ number_format($totalPending, 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Submitted to Head Office, awaiting verification</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light" style="color: #d97706;">
                        <i class="fas fa-clock fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-money-check-alt text-teal" style="color: #0f766e;"></i>
                <h6 class="mb-0 fw-bold text-dark">Payment History & Status</h6>
            </div>

            <!-- Filter Form -->
            <form method="GET" action="{{ route('distributor.payments.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                <select name="status" class="form-select form-select-sm" style="width: auto;">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Verification</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" style="width: auto;">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" style="width: auto;">
                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Filter</button>
                @if(request()->hasAny(['status', 'start_date', 'end_date']))
                <a href="{{ route('distributor.payments.index') }}" class="btn btn-sm btn-link text-muted p-0 ms-1">Reset</a>
                @endif
            </form>
        </div>

        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Payment #</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method & Details</th>
                        <th>Reference / Trx ID</th>
                        <th class="text-center">Slip Proof</th>
                        <th>Approval Status</th>
                        <th>Ledger Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($payments as $p)
                    @php
                        $editPayload = [
                            'id'                    => $p->id,
                            'payment_no'            => $p->payment_no,
                            'amount'                => $p->amount,
                            'payment_date'          => $p->payment_date ? $p->payment_date->format('Y-m-d') : date('Y-m-d'),
                            'payment_method'        => $p->payment_method,
                            'account_id'            => $p->account_id,
                            'bank_name'             => $p->bank_name,
                            'account_number'        => $p->account_number,
                            'transaction_reference' => $p->transaction_reference,
                            'notes'                 => $p->notes,
                            'status'                => $p->status,
                            'slip_url'              => $p->slip_image ? asset('uploads/distributor_payments/' . $p->slip_image) : '',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-dark font-monospace" style="font-size: 0.82rem;">
                            {{ $p->payment_no }}
                        </td>
                        <td class="text-muted small">
                            {{ $p->payment_date ? $p->payment_date->format('d M, Y') : '-' }}
                        </td>
                        <td>
                            <span class="fw-bold fs-6 text-success">
                                Rs. {{ number_format($p->amount, 2) }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark text-capitalize">
                                <i class="fas fa-credit-card text-muted me-1 small"></i>
                                {{ str_replace('_', ' ', $p->payment_method) }}
                            </div>
                            @if($p->account)
                            <div class="small fw-semibold" style="font-size: 0.76rem; color: #0f766e;">
                                <i class="fas fa-university me-1"></i> {{ $p->account->title }}
                                @if($p->account->head)
                                <span class="text-muted fw-normal" style="font-size: 0.70rem;">({{ $p->account->head->name }})</span>
                                @endif
                            </div>
                            @elseif($p->bank_name || $p->account_number)
                            <div class="text-muted small" style="font-size: 0.75rem;">
                                {{ $p->bank_name }} {{ $p->account_number ? '('.$p->account_number.')' : '' }}
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($p->transaction_reference)
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                {{ $p->transaction_reference }}
                            </span>
                            @else
                            <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($p->slip_image)
                            <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-1 text-nowrap"
                                    onclick="viewSlip('{{ asset('uploads/distributor_payments/' . $p->slip_image) }}', '{{ $p->payment_no }}')">
                                <i class="fas fa-image me-1"></i> View Slip
                            </button>
                            @else
                            <span class="badge bg-light text-muted small">No Slip</span>
                            @endif
                        </td>
                        <td>
                            @if($p->status === 'approved')
                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-semibold">
                                <i class="fas fa-check-circle me-1"></i> Approved
                            </span>
                            @elseif($p->status === 'rejected')
                            <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill fw-semibold" title="{{ $p->rejection_reason }}">
                                <i class="fas fa-times-circle me-1"></i> Rejected
                            </span>
                            @else
                            <span class="badge rounded-pill fw-bold px-2.5 py-1" style="background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fcd34d !important; font-size: 0.75rem;">
                                <i class="fas fa-hourglass-half me-1 text-warning"></i> Pending Verification
                            </span>
                            @endif
                        </td>
                        <td>
                            @if($p->status === 'approved')
                            <div class="small text-success fw-semibold">
                                <i class="fas fa-check me-1"></i> Credited to Ledger
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                Approved on {{ $p->approved_at ? $p->approved_at->format('d M, Y') : 'Verified' }}
                            </div>
                            @elseif($p->status === 'rejected')
                            <div class="small text-danger fw-semibold">
                                Not Credited
                            </div>
                            @if($p->rejection_reason)
                            <div class="text-danger" style="font-size: 0.72rem;">
                                Reason: {{ Str::limit($p->rejection_reason, 35) }}
                            </div>
                            @endif
                            @else
                            <div class="small text-muted">
                                <i class="fas fa-info-circle me-1 text-warning"></i> Awaiting Head Office approval
                            </div>
                            @endif
                        </td>
                        <td class="text-end pe-4 text-nowrap">
                            @if($p->status === 'pending' || $p->status === 'rejected')
                            <div class="d-inline-flex align-items-center gap-1.5">
                                <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;"
                                        title="Edit Payment Details" onclick='openEditPaymentModal(@json($editPayload))'>
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 0.75rem;"
                                        title="Delete Submission" onclick="confirmDeletePayment('{{ $p->id }}', '{{ $p->payment_no }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            @else
                            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;" title="Approved payments cannot be edited or deleted by distributor.">
                                <i class="fas fa-lock text-success me-1"></i> Locked
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fs-1 text-muted opacity-25 d-block mb-2"></i>
                            No payment submissions found.
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-teal rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#submitPaymentModal">
                                    + Submit Your First Payment
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="d-block d-md-none p-3">
            @forelse($payments as $p)
            <div class="card border rounded-3 p-3 mb-3 shadow-none bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="font-monospace text-dark fw-bold small">{{ $p->payment_no }}</span>
                        <div class="text-muted" style="font-size: 0.72rem;">{{ $p->payment_date ? $p->payment_date->format('d M, Y') : '-' }}</div>
                    </div>
                    <div>
                        @if($p->status === 'approved')
                        <span class="badge bg-success text-white px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                            Approved
                        </span>
                        @elseif($p->status === 'rejected')
                        <span class="badge bg-danger text-white px-2 py-0.5 rounded-pill" style="font-size: 0.7rem;">
                            Rejected
                        </span>
                        @else
                        <span class="badge rounded-pill fw-bold px-2 py-0.5" style="background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fcd34d !important; font-size: 0.7rem;">
                            <i class="fas fa-hourglass-half me-0.5 text-warning"></i> Pending Approval
                        </span>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom my-1">
                    <span class="text-muted small">Amount</span>
                    <span class="fw-bold fs-6 text-success">Rs. {{ number_format($p->amount, 2) }}</span>
                </div>

                <div class="small text-muted my-1">
                    <i class="fas fa-wallet me-1"></i> Method: <strong class="text-dark text-capitalize">{{ str_replace('_', ' ', $p->payment_method) }}</strong>
                    @if($p->account)
                    <span class="badge bg-light border ms-1" style="color: #0f766e;"><i class="fas fa-university me-1"></i>{{ $p->account->title }}</span>
                    @elseif($p->bank_name)
                    <span>• {{ $p->bank_name }}</span>
                    @endif
                </div>

                @if($p->transaction_reference)
                <div class="small text-muted my-0.5">
                    <i class="fas fa-hashtag me-1"></i> Ref: <code class="text-dark">{{ $p->transaction_reference }}</code>
                </div>
                @endif

                @if($p->status === 'approved')
                <div class="alert alert-success p-2 mb-2 mt-2 rounded-2 small d-flex align-items-center gap-1.5" style="font-size: 0.75rem;">
                    <i class="fas fa-check-circle"></i>
                    <span>Balance Credited to your ledger.</span>
                </div>
                @elseif($p->status === 'rejected')
                <div class="alert alert-danger p-2 mb-2 mt-2 rounded-2 small" style="font-size: 0.75rem;">
                    <strong>Rejected:</strong> {{ $p->rejection_reason ?? 'Please contact company administration.' }}
                </div>
                @endif

                @php
                    $editPayloadMobile = [
                        'id'                    => $p->id,
                        'payment_no'            => $p->payment_no,
                        'amount'                => $p->amount,
                        'payment_date'          => $p->payment_date ? $p->payment_date->format('Y-m-d') : date('Y-m-d'),
                        'payment_method'        => $p->payment_method,
                        'account_id'            => $p->account_id,
                        'bank_name'             => $p->bank_name,
                        'account_number'        => $p->account_number,
                        'transaction_reference' => $p->transaction_reference,
                        'notes'                 => $p->notes,
                        'status'                => $p->status,
                        'slip_url'              => $p->slip_image ? asset('uploads/distributor_payments/' . $p->slip_image) : '',
                    ];
                @endphp

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-2 border-top">
                    <div>
                        @if($p->slip_image)
                        <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-1" style="font-size: 0.75rem;"
                                onclick="viewSlip('{{ asset('uploads/distributor_payments/' . $p->slip_image) }}', '{{ $p->payment_no }}')">
                            <i class="fas fa-image me-1"></i> Slip
                        </button>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-1.5 ms-auto">
                        @if($p->status === 'pending' || $p->status === 'rejected')
                        <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.75rem;"
                                onclick='openEditPaymentModal(@json($editPayloadMobile))'>
                            <i class="fas fa-edit me-1"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 0.75rem;"
                                onclick="confirmDeletePayment('{{ $p->id }}', '{{ $p->payment_no }}')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        @else
                        <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                            <i class="fas fa-lock text-success me-1"></i> Locked
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-receipt fs-1 text-muted opacity-25 d-block mb-2"></i>
                No payments found.
            </div>
            @endforelse
        </div>

        @if($payments->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Submit Payment to Company -->
<div class="modal fade" id="submitPaymentModal" tabindex="-1" aria-labelledby="submitPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-2.5 px-3" style="background: linear-gradient(135deg, #090d16 0%, #0f766e 100%);">
                <div>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="submitPaymentModalLabel">
                        <i class="fas fa-money-bill-wave me-1.5"></i> Submit Payment to Company
                    </h5>
                    <div class="text-white-50" style="font-size: 0.72rem;">Deposit payment to company account and submit slip for instant verification</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('distributor.payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-3 py-2.5 bg-light">
                    <!-- Current Outstanding Reminder -->
                    <div class="py-1.5 px-3 mb-2 rounded-3 bg-white border border-danger-subtle d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.72rem;">Your Current Outstanding:</span>
                            <span class="fs-6 fw-bold text-danger">Rs. {{ number_format($currentBalance, 2) }}</span>
                        </div>
                        <span class="badge bg-danger-subtle text-danger px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                            Head Office Ledger
                        </span>
                    </div>

                    <div class="row g-2">
                        <!-- ROW 1: Deposit Account & Bank/Counter Name (SAME LINE) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">
                                <i class="fas fa-university me-1" style="color: #0f766e;"></i> Deposit To Company Account (Cash / Bank) <span class="text-danger">*</span>
                            </label>
                            <select name="account_id" id="distCompanyAccountSelect" class="form-select" onchange="handleDistAccountChange(this)" style="border-color: #0f766e;">
                                <option value="">-- Select Company Cash or Bank Account --</option>
                                @if(isset($companyAccounts) && $companyAccounts->count() > 0)
                                    @php
                                        $grouped = $companyAccounts->groupBy(function($item) {
                                            return $item->head ? $item->head->name : 'Company Accounts';
                                        });
                                    @endphp
                                    @foreach($grouped as $headName => $accounts)
                                        <optgroup label="{{ $headName }}">
                                            @foreach($accounts as $acc)
                                                @php
                                                    $isCash = stripos($headName, 'cash') !== false || stripos($acc->title, 'cash') !== false;
                                                    $method = $isCash ? 'cash' : 'bank_transfer';
                                                @endphp
                                                <option value="{{ $acc->id }}"
                                                        data-title="{{ $acc->title }}"
                                                        data-code="{{ $acc->account_code }}"
                                                        data-method="{{ $method }}"
                                                        data-head="{{ $headName }}">
                                                    {{ $acc->title }} {{ $acc->account_code ? '• ' . $acc->account_code : '' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                                <option value="" data-manual="1">-- Other / Manual Account Details --</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6" id="bankNameField">
                            <label class="form-label mb-1 small fw-bold text-dark">
                                <i class="fas fa-store me-1 text-muted"></i> Bank / Counter Name
                            </label>
                            <input type="text" name="bank_name" id="distBankNameInput" class="form-control"
                                   placeholder="e.g. Meezan Bank / Head Office Cash">
                        </div>

                        <!-- ROW 2: Amount, Date, Method -->
                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Payment Amount (PKR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white fw-bold py-1 px-2" style="font-size: 0.85rem;">Rs.</span>
                                <input type="number" step="0.01" min="1" name="amount" class="form-control fw-bold text-success"
                                       placeholder="50000" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required id="distPaymentMethodSelect">
                                <option value="bank_transfer" selected>Bank Transfer / Online IBFT</option>
                                <option value="cash">Cash Deposit to Head Office</option>
                                <option value="cheque">Cheque / Pay Order</option>
                                <option value="raast">Raast Instant Transfer</option>
                                <option value="easypaisa">EasyPaisa / JazzCash</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- ROW 3: Account Code/Number & Transaction ID -->
                        <div class="col-12 col-md-6" id="accountNumberField">
                            <label class="form-label mb-1 small fw-bold text-dark">Account Code / Number</label>
                            <input type="text" name="account_number" id="distAccountNumberInput" class="form-control font-monospace"
                                   placeholder="e.g. 0101-01010101">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">Transaction ID / Cheque # / Slip Ref</label>
                            <input type="text" name="transaction_reference" class="form-control font-monospace"
                                   placeholder="e.g. TRX98273648 or Cheque #004512">
                        </div>

                        <!-- ROW 4: Upload Slip & Remarks (SAME LINE) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">
                                <i class="fas fa-file-upload me-1 text-muted"></i> Upload Deposit Slip / Screenshot
                            </label>
                            <input type="file" name="slip_image" class="form-control bg-white" accept="image/*,.pdf">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">
                                <i class="fas fa-comment-dots me-1 text-muted"></i> Remarks / Additional Notes
                            </label>
                            <input type="text" name="notes" class="form-control" placeholder="Any additional notes...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-teal rounded-pill px-4 shadow-sm fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Payment Submission (Before Approval) -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-2.5 px-3" style="background: linear-gradient(135deg, #090d16 0%, #0f766e 100%);">
                <div>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="editPaymentModalLabel">
                        <i class="fas fa-edit me-1.5"></i> Edit Payment - <span id="editModalPaymentNo"></span>
                    </h5>
                    <div class="text-white-50" style="font-size: 0.72rem;">Modify payment submission details before company approval</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editPaymentForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-3 py-2.5 bg-light">
                    <div class="row g-2">
                        <!-- ROW 1: Deposit Account & Bank/Counter Name -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">
                                <i class="fas fa-university me-1" style="color: #0f766e;"></i> Deposit To Company Account <span class="text-danger">*</span>
                            </label>
                            <select name="account_id" id="editAccountSelect" class="form-select" onchange="handleEditAccountChange(this)" style="border-color: #0f766e;">
                                <option value="">-- Select Company Cash or Bank Account --</option>
                                @if(isset($companyAccounts) && $companyAccounts->count() > 0)
                                    @php
                                        $grouped = $companyAccounts->groupBy(function($item) {
                                            return $item->head ? $item->head->name : 'Company Accounts';
                                        });
                                    @endphp
                                    @foreach($grouped as $headName => $accounts)
                                        <optgroup label="{{ $headName }}">
                                            @foreach($accounts as $acc)
                                                @php
                                                    $isCash = stripos($headName, 'cash') !== false || stripos($acc->title, 'cash') !== false;
                                                    $method = $isCash ? 'cash' : 'bank_transfer';
                                                @endphp
                                                <option value="{{ $acc->id }}"
                                                        data-title="{{ $acc->title }}"
                                                        data-code="{{ $acc->account_code }}"
                                                        data-method="{{ $method }}"
                                                        data-head="{{ $headName }}">
                                                    {{ $acc->title }} {{ $acc->account_code ? '• ' . $acc->account_code : '' }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                                <option value="" data-manual="1">-- Other / Manual Account Details --</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">
                                <i class="fas fa-store me-1 text-muted"></i> Bank / Counter Name
                            </label>
                            <input type="text" name="bank_name" id="editBankNameInput" class="form-control"
                                   placeholder="e.g. Meezan Bank / Head Office Cash">
                        </div>

                        <!-- ROW 2: Amount, Date, Method -->
                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Payment Amount (PKR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white fw-bold py-1 px-2" style="font-size: 0.85rem;">Rs.</span>
                                <input type="number" step="0.01" min="1" name="amount" id="editAmountInput" class="form-control fw-bold text-success" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="editDateInput" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="editMethodSelect" class="form-select" required>
                                <option value="bank_transfer">Bank Transfer / Online IBFT</option>
                                <option value="cash">Cash Deposit to Head Office</option>
                                <option value="cheque">Cheque / Pay Order</option>
                                <option value="raast">Raast Instant Transfer</option>
                                <option value="easypaisa">EasyPaisa / JazzCash</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- ROW 3: Account Code/Number & Transaction ID -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">Account Code / Number</label>
                            <input type="text" name="account_number" id="editAccountNumberInput" class="form-control font-monospace"
                                   placeholder="e.g. 0101-01010101">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">Transaction ID / Cheque # / Slip Ref</label>
                            <input type="text" name="transaction_reference" id="editTrxInput" class="form-control font-monospace"
                                   placeholder="e.g. TRX98273648 or Cheque #">
                        </div>

                        <!-- ROW 4: Replace Slip & Remarks -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-file-upload me-1 text-muted"></i> Replace Slip Proof (Optional)</span>
                                <span id="editExistingSlipBadge"></span>
                            </label>
                            <input type="file" name="slip_image" class="form-control bg-white" accept="image/*,.pdf">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">
                                <i class="fas fa-comment-dots me-1 text-muted"></i> Remarks / Additional Notes
                            </label>
                            <input type="text" name="notes" id="editNotesInput" class="form-control" placeholder="Any additional notes...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-teal rounded-pill px-4 shadow-sm fw-bold">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Payment Form -->
<form id="deletePaymentForm" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Modal: View Slip Image -->
<div class="modal fade" id="slipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-2.5">
                <h6 class="modal-title fw-bold" id="slipModalTitle">Receipt Slip</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-light">
                <img id="slipModalImg" src="" alt="Payment Slip" class="img-fluid rounded-3 shadow-sm border" style="max-height: 75vh;">
            </div>
            <div class="modal-footer bg-white py-2">
                <a id="slipDownloadLink" href="#" target="_blank" class="btn btn-sm btn-outline-teal rounded-pill px-3">
                    <i class="fas fa-external-link-alt me-1"></i> Open Full Image
                </a>
                <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function viewSlip(url, no) {
        document.getElementById('slipModalImg').src = url;
        document.getElementById('slipDownloadLink').href = url;
        document.getElementById('slipModalTitle').innerText = 'Receipt Slip - ' + no;
        let modal = new bootstrap.Modal(document.getElementById('slipModal'));
        modal.show();
    }

    function handleDistAccountChange(select) {
        const opt = select.options[select.selectedIndex];
        const bankNameInput = document.getElementById('distBankNameInput');
        const accountNumInput = document.getElementById('distAccountNumberInput');
        const methodSelect = document.getElementById('distPaymentMethodSelect');

        if (!opt || !opt.value) {
            if (opt && opt.dataset.manual) {
                if (bankNameInput) bankNameInput.value = '';
                if (accountNumInput) accountNumInput.value = '';
            }
            return;
        }

        const title = opt.dataset.title || '';
        const code = opt.dataset.code || '';
        const method = opt.dataset.method || 'bank_transfer';

        if (methodSelect) {
            methodSelect.value = method;
        }
        if (bankNameInput) {
            bankNameInput.value = title;
        }
        if (accountNumInput) {
            accountNumInput.value = code;
        }
    }

    function openEditPaymentModal(data) {
        document.getElementById('editModalPaymentNo').innerText = data.payment_no;
        document.getElementById('editAmountInput').value = data.amount;
        document.getElementById('editDateInput').value = data.payment_date;
        document.getElementById('editMethodSelect').value = data.payment_method;
        document.getElementById('editBankNameInput').value = data.bank_name || '';
        document.getElementById('editAccountNumberInput').value = data.account_number || '';
        document.getElementById('editTrxInput').value = data.transaction_reference || '';
        document.getElementById('editNotesInput').value = data.notes || '';

        // Preselect account dropdown
        const accSelect = document.getElementById('editAccountSelect');
        if (accSelect) {
            accSelect.value = data.account_id || '';
        }

        // Existing slip badge
        const slipBadge = document.getElementById('editExistingSlipBadge');
        if (data.slip_url) {
            slipBadge.innerHTML = '<a href="' + data.slip_url + '" target="_blank" class="text-teal fw-bold" style="font-size: 0.72rem; color: #0f766e;"><i class="fas fa-eye me-1"></i>View Current Slip</a>';
        } else {
            slipBadge.innerHTML = '<span class="text-muted" style="font-size: 0.72rem;">No slip uploaded</span>';
        }

        document.getElementById('editPaymentForm').action = "{{ url('distributor-portal/payments') }}/" + data.id + "/update";
        let modal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
        modal.show();
    }

    function handleEditAccountChange(select) {
        const opt = select.options[select.selectedIndex];
        const bankNameInput = document.getElementById('editBankNameInput');
        const accountNumInput = document.getElementById('editAccountNumberInput');
        const methodSelect = document.getElementById('editMethodSelect');

        if (!opt || !opt.value) {
            if (opt && opt.dataset.manual) {
                if (bankNameInput) bankNameInput.value = '';
                if (accountNumInput) accountNumInput.value = '';
            }
            return;
        }

        const title = opt.dataset.title || '';
        const code = opt.dataset.code || '';
        const method = opt.dataset.method || 'bank_transfer';

        if (methodSelect) {
            methodSelect.value = method;
        }
        if (bankNameInput) {
            bankNameInput.value = title;
        }
        if (accountNumInput) {
            accountNumInput.value = code;
        }
    }

    function confirmDeletePayment(id, paymentNo) {
        if (confirm('Are you sure you want to delete payment submission #' + paymentNo + '? This will permanently remove this record.')) {
            const form = document.getElementById('deletePaymentForm');
            form.action = "{{ url('distributor-portal/payments') }}/" + id + "/delete";
            form.submit();
        }
    }
</script>
@endsection
