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
    .btn-teal {
        background: linear-gradient(135deg, #0f766e, #0d9488);
        border: 1px solid #14b8a6;
        color: #ffffff;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-teal:hover {
        background: linear-gradient(135deg, #0d9488, #115e59);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 118, 110, 0.35);
    }
    .btn-outline-teal {
        border: 1px solid #0f766e;
        color: #0f766e;
        font-weight: 600;
    }
    .btn-outline-teal:hover {
        background: #0f766e;
        color: #ffffff;
    }
</style>

<div class="container-fluid dist-module-container">
    <!-- Horizontal Navigation Tabs -->
    @include('admin_panel.distributors.partials.navigation_tabs')

    <!-- Flash Messages -->
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
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-exclamation-triangle fs-5"></i>
        <div>{{ session('warning') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Top Subtitle Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 mt-1">
        <div>
            <h5 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                <i class="fas fa-money-check-alt text-teal" style="color: #0f766e;"></i>
                Distributor Payments & Approvals
                @if($pendingPaymentsCount > 0)
                <span class="badge rounded-pill shadow-sm" style="background: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fca5a5 !important; font-size: 0.72rem; font-weight: 700; padding: 3px 9px;">
                    <i class="fas fa-bell me-1"></i> {{ $pendingPaymentsCount }} Pending Verification
                </span>
                @else
                <span class="badge rounded-pill" style="background: #ecfdf5 !important; color: #047857 !important; border: 1px solid #a7f3d0 !important; font-size: 0.72rem; font-weight: 600; padding: 3px 9px;">
                    <i class="fas fa-check-circle me-1 text-success"></i> All Payments Verified
                </span>
                @endif
            </h5>
            <span class="text-muted" style="font-size: 0.78rem;">Verify payments submitted by distributors or record direct company collections</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('recepit-vochers') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5" style="font-size: 0.78rem;">
                <i class="fas fa-receipt me-1"></i> Receipts Voucher
            </a>
            <button type="button" class="btn btn-teal btn-sm rounded-pill px-3 py-1.5 shadow-sm" style="font-size: 0.78rem;"
                    data-bs-toggle="modal" data-bs-target="#receiveDirectPaymentModal">
                <i class="fas fa-plus-circle me-1"></i> + Receive Direct Payment
            </button>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Approved -->
        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #10b981 !important;">
                <div>
                    <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.74rem;">Total Verified Collections</span>
                    <h4 class="fw-bold mb-0 text-success mt-1" style="font-size: 1.35rem;">
                        Rs. {{ number_format($totalApproved, 2) }}
                    </h4>
                    <span class="text-muted" style="font-size: 0.72rem;">Credited into distributor ledgers</span>
                </div>
                <div class="rounded-3 p-2.5 bg-success-subtle text-success">
                    <i class="fas fa-check-double fs-4"></i>
                </div>
            </div>
        </div>

        <!-- 2. Pending Approvals -->
        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid {{ $pendingPaymentsCount > 0 ? '#ef4444' : '#f59e0b' }} !important; {{ $pendingPaymentsCount > 0 ? 'background: #fffafa !important;' : '' }}">
                <div>
                    <div class="d-flex align-items-center gap-1.5 mb-1">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.74rem;">Pending Verification</span>
                        @if($pendingPaymentsCount > 0)
                        <span class="badge rounded-pill shadow-sm" 
                              style="background: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fca5a5 !important; font-size: 0.68rem; font-weight: 700; padding: 2px 7px; vertical-align: middle;">
                            <i class="fas fa-bell me-0.5"></i> {{ $pendingPaymentsCount }} New
                        </span>
                        @else
                        <span class="badge rounded-pill" 
                              style="background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; font-size: 0.68rem; font-weight: 600; padding: 2px 7px; vertical-align: middle;">
                            <i class="fas fa-check-circle me-0.5 text-success"></i> 0 Pending
                        </span>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-0 mt-1" style="font-size: 1.35rem; color: {{ $pendingPaymentsCount > 0 ? '#dc2626' : '#d97706' }} !important;">
                        Rs. {{ number_format($totalPending, 2) }}
                    </h4>
                    <span class="text-muted" style="font-size: 0.72rem;">Submitted by distributors via portal</span>
                </div>
                <div class="rounded-3 p-2.5" style="background: {{ $pendingPaymentsCount > 0 ? '#fee2e2' : '#fef3c7' }} !important; color: {{ $pendingPaymentsCount > 0 ? '#dc2626' : '#d97706' }} !important;">
                    <i class="fas {{ $pendingPaymentsCount > 0 ? 'fa-bell' : 'fa-clock' }} fs-4"></i>
                </div>
            </div>
        </div>

        <!-- 3. Total Rejected -->
        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #ef4444 !important;">
                <div>
                    <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.74rem;">Total Rejected</span>
                    <h4 class="fw-bold mb-0 text-danger mt-1" style="font-size: 1.35rem;">
                        Rs. {{ number_format($totalRejected, 2) }}
                    </h4>
                    <span class="text-muted" style="font-size: 0.72rem;">Invalid or unverified submissions</span>
                </div>
                <div class="rounded-3 p-2.5 bg-danger-subtle text-danger">
                    <i class="fas fa-times-circle fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Filter Header -->
        <div class="card-header bg-white py-3 border-bottom">
            <form method="GET" action="{{ route('admin.distributors.payments') }}" class="row g-2 align-items-center">
                <!-- Distributor Filter -->
                <div class="col-12 col-sm-6 col-md-3">
                    <select name="distributor_id" class="form-select form-select-sm">
                        <option value="">All Distributors</option>
                        @foreach($distributors as $dist)
                        <option value="{{ $dist->id }}" {{ request('distributor_id') == $dist->id ? 'selected' : '' }}>
                            {{ $dist->name }} ({{ $dist->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-6 col-sm-3 col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending ({{ $pendingPaymentsCount }})</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Method Filter -->
                <div class="col-6 col-sm-3 col-md-2">
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">All Methods</option>
                        <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="cheque" {{ request('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="receipt_voucher" {{ request('payment_method') === 'receipt_voucher' ? 'selected' : '' }}>Receipt Voucher</option>
                        <option value="raast" {{ request('payment_method') === 'raast' ? 'selected' : '' }}>Raast</option>
                        <option value="easypaisa" {{ request('payment_method') === 'easypaisa' ? 'selected' : '' }}>EasyPaisa / JazzCash</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="col-6 col-sm-6 col-md-2">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" placeholder="Start Date">
                </div>
                <div class="col-6 col-sm-6 col-md-2">
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" placeholder="End Date">
                </div>

                <!-- Submit & Reset -->
                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 w-100" style="font-size: 0.78rem;">Filter</button>
                    @if(request()->hasAny(['distributor_id', 'status', 'payment_method', 'start_date', 'end_date']))
                    <a href="{{ route('admin.distributors.payments') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Reset">
                        <i class="fas fa-undo"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Desktop Table View -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.78rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Payment #</th>
                        <th>Date</th>
                        <th>Distributor</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reference / Trx ID</th>
                        <th class="text-center">Slip Proof</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions / Ledger</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($payments as $p)
                    <tr class="{{ $p->status === 'pending' ? 'bg-warning-subtle bg-opacity-25' : '' }}">
                        <!-- Payment # -->
                        <td class="ps-4 fw-bold font-monospace text-dark" style="font-size: 0.82rem;">
                            {{ $p->payment_no }}
                        </td>

                        <!-- Date -->
                        <td class="text-muted small">
                            {{ $p->payment_date ? $p->payment_date->format('d M, Y') : '-' }}
                        </td>

                        <!-- Distributor -->
                        <td>
                            @if($p->distributor)
                            <a href="{{ route('admin.distributors.ledger', $p->distributor->id) }}" class="fw-bold text-dark text-decoration-none hover-teal">
                                {{ $p->distributor->name }}
                            </a>
                            <div class="text-muted small" style="font-size: 0.75rem;">
                                <code>{{ $p->distributor->code }}</code> • {{ $p->distributor->phone }}
                            </div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>

                        <!-- Amount -->
                        <td>
                            <span class="fw-bold fs-6 text-success">
                                Rs. {{ number_format($p->amount, 2) }}
                            </span>
                        </td>

                        <!-- Method -->
                        <td>
                            <div class="fw-semibold text-dark text-capitalize" style="font-size: 0.84rem;">
                                <i class="fas fa-wallet text-muted me-1 small"></i>
                                {{ str_replace('_', ' ', $p->payment_method) }}
                            </div>
                            @if($p->account)
                            <div class="small fw-semibold" style="font-size: 0.74rem; color: #0f766e;">
                                <i class="fas fa-university me-1"></i> {{ $p->account->title }}
                                @if($p->account->head)
                                <span class="text-muted fw-normal" style="font-size: 0.68rem;">({{ $p->account->head->name }})</span>
                                @endif
                            </div>
                            @elseif($p->bank_name)
                            <div class="text-muted small" style="font-size: 0.74rem;">
                                {{ $p->bank_name }} {{ $p->account_number ? '('.$p->account_number.')' : '' }}
                            </div>
                            @endif
                        </td>

                        <!-- Reference -->
                        <td>
                            @if($p->transaction_reference)
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                {{ $p->transaction_reference }}
                            </span>
                            @else
                            <span class="text-muted small">-</span>
                            @endif
                        </td>

                        <!-- Slip Proof -->
                        <td class="text-center">
                            @if($p->slip_image)
                            <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-0.5 text-nowrap" style="font-size: 0.75rem;"
                                    onclick="viewSlip('{{ asset('uploads/distributor_payments/' . $p->slip_image) }}', '{{ $p->payment_no }}')">
                                <i class="fas fa-image me-1"></i> View Slip
                            </button>
                            @else
                            <span class="badge bg-light text-muted small" style="font-size: 0.72rem;">No Slip</span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td>
                            @if($p->status === 'approved')
                            <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                                <i class="fas fa-check-circle me-1"></i> Approved
                            </span>
                            <div class="text-muted mt-0.5" style="font-size: 0.7rem;">
                                {{ $p->approved_at ? $p->approved_at->format('d M, Y h:i A') : 'Credited' }}
                            </div>
                            @elseif($p->status === 'rejected')
                            <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 0.75rem;" title="{{ $p->rejection_reason }}">
                                <i class="fas fa-times-circle me-1"></i> Rejected
                            </span>
                            @if($p->rejection_reason)
                            <div class="text-danger mt-0.5" style="font-size: 0.7rem;">
                                {{ Str::limit($p->rejection_reason, 25) }}
                            </div>
                            @endif
                            @else
                            <span class="badge rounded-pill fw-bold px-2.5 py-1" style="background: #fef3c7 !important; color: #b45309 !important; border: 1px solid #fcd34d !important; font-size: 0.75rem;">
                                <i class="fas fa-clock me-1 text-warning"></i> Pending
                            </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="text-end pe-4">
                            @php
                                $pData = [
                                    'id'             => $p->id,
                                    'payment_no'     => $p->payment_no,
                                    'dist_name'      => $p->distributor ? $p->distributor->name : 'Distributor',
                                    'dist_code'      => $p->distributor ? $p->distributor->code : '',
                                    'dist_phone'     => $p->distributor ? $p->distributor->phone : '',
                                    'dist_balance'   => $p->distributor ? number_format($p->distributor->current_balance, 2) : '0.00',
                                    'amount'         => number_format($p->amount, 2),
                                    'date'           => $p->payment_date ? $p->payment_date->format('d M, Y') : '-',
                                    'method'         => ucwords(str_replace('_', ' ', $p->payment_method)),
                                    'bank_name'      => $p->bank_name ?: '',
                                    'account_number' => $p->account_number ?: '',
                                    'account_title'  => $p->account ? $p->account->title : '',
                                    'account_id'     => $p->account_id ?: '',
                                    'reference'      => $p->transaction_reference ?: '',
                                    'notes'          => $p->notes ?: '',
                                    'slip_url'       => $p->slip_image ? asset('uploads/distributor_payments/' . $p->slip_image) : '',
                                ];
                            @endphp
                            @if($p->status === 'pending')
                            <div class="d-inline-flex align-items-center gap-1.5">
                                <!-- Approve Button -->
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 text-nowrap fw-semibold shadow-sm" style="font-size: 0.75rem;"
                                        onclick='openApproveModal(@json($pData))'>
                                    <i class="fas fa-check me-1"></i> Approve
                                </button>

                                <!-- Reject Button -->
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 text-nowrap fw-semibold" style="font-size: 0.75rem;"
                                        onclick='openRejectModal(@json($pData))'>
                                    <i class="fas fa-times me-1"></i> Reject
                                </button>
                            </div>
                            @else
                            <div class="d-inline-flex align-items-center gap-1">
                                @if($p->distributor)
                                <a href="{{ route('admin.distributors.ledger', $p->distributor->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1 text-nowrap" style="font-size: 0.75rem;">
                                    <i class="fas fa-book me-1"></i> Ledger
                                </a>
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-file-invoice-dollar fs-1 text-muted opacity-25 d-block mb-2"></i>
                            No payment records found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile & Tablet Card View -->
        <div class="d-block d-lg-none p-3">
            @forelse($payments as $p)
            <div class="card border rounded-3 p-3 mb-3 shadow-none bg-white {{ $p->status === 'pending' ? 'border-warning' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="fw-bold text-dark fs-6">{{ $p->distributor->name ?? 'Distributor' }}</div>
                        <span class="font-monospace text-muted small">{{ $p->payment_no }}</span>
                        <span class="text-muted" style="font-size: 0.72rem;">• {{ $p->payment_date ? $p->payment_date->format('d M, Y') : '-' }}</span>
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
                            <i class="fas fa-clock me-0.5 text-warning"></i> Pending
                        </span>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom my-1">
                    <span class="text-muted small">Amount</span>
                    <span class="fw-bold fs-5 text-success">Rs. {{ number_format($p->amount, 2) }}</span>
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

                @if($p->status === 'rejected' && $p->rejection_reason)
                <div class="alert alert-danger p-2 mb-2 mt-2 rounded-2 small" style="font-size: 0.75rem;">
                    <strong>Rejected Reason:</strong> {{ $p->rejection_reason }}
                </div>
                @endif

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-2 border-top">
                    <div>
                        @if($p->slip_image)
                        <button type="button" class="btn btn-sm btn-outline-teal rounded-pill px-2.5 py-1" style="font-size: 0.75rem;"
                                onclick="viewSlip('{{ asset('uploads/distributor_payments/' . $p->slip_image) }}', '{{ $p->payment_no }}')">
                            <i class="fas fa-image me-1"></i> View Slip
                        </button>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-1.5 ms-auto">
                        @php
                            $pDataMobile = [
                                'id'             => $p->id,
                                'payment_no'     => $p->payment_no,
                                'dist_name'      => $p->distributor ? $p->distributor->name : 'Distributor',
                                'dist_code'      => $p->distributor ? $p->distributor->code : '',
                                'dist_phone'     => $p->distributor ? $p->distributor->phone : '',
                                'dist_balance'   => $p->distributor ? number_format($p->distributor->current_balance, 2) : '0.00',
                                'amount'         => number_format($p->amount, 2),
                                'date'           => $p->payment_date ? $p->payment_date->format('d M, Y') : '-',
                                'method'         => ucwords(str_replace('_', ' ', $p->payment_method)),
                                'bank_name'      => $p->bank_name ?: '',
                                'account_number' => $p->account_number ?: '',
                                'account_title'  => $p->account ? $p->account->title : '',
                                'account_id'     => $p->account_id ?: '',
                                'reference'      => $p->transaction_reference ?: '',
                                'notes'          => $p->notes ?: '',
                                'slip_url'       => $p->slip_image ? asset('uploads/distributor_payments/' . $p->slip_image) : '',
                            ];
                        @endphp
                        @if($p->status === 'pending')
                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold shadow-sm" style="font-size: 0.75rem;"
                                onclick='openApproveModal(@json($pDataMobile))'>
                            <i class="fas fa-check me-1"></i> Approve
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 0.75rem;"
                                onclick='openRejectModal(@json($pDataMobile))'>
                            Reject
                        </button>
                        @elseif($p->distributor)
                        <a href="{{ route('admin.distributors.ledger', $p->distributor->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 0.75rem;">
                            View Ledger
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-file-invoice-dollar fs-1 text-muted opacity-25 d-block mb-2"></i>
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

<!-- Modal 1: Receive Direct Payment (Admin Side) -->
<div class="modal fade" id="receiveDirectPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-2.5 px-3" style="background: linear-gradient(135deg, #090d16 0%, #0f766e 100%);">
                <div>
                    <h5 class="modal-title fw-bold fs-6 mb-0">
                        <i class="fas fa-hand-holding-usd me-1.5"></i> Receive Direct Payment from Distributor
                    </h5>
                    <div class="text-white-50" style="font-size: 0.72rem;">Directly record and auto-credit payment into distributor ledger</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.distributors.payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body px-3 py-2.5 bg-light">
                    <div class="row g-2">
                        <!-- Select Distributor -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">Distributor <span class="text-danger">*</span></label>
                            <select name="distributor_id" class="form-select" required>
                                <option value="">Select Distributor...</option>
                                @foreach($distributors as $dist)
                                <option value="{{ $dist->id }}" data-balance="{{ $dist->current_balance }}">
                                    {{ $dist->name }} ({{ $dist->code }}) - Bal: Rs. {{ number_format($dist->current_balance, 2) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company Cash / Bank Account Selector -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">
                                <i class="fas fa-university me-1" style="color: #0f766e;"></i> Company Account (Cash / Bank)
                            </label>
                            <select name="account_id" id="adminDirectAccountSelect" class="form-select" onchange="handleAdminDirectAccountChange(this)" style="border-color: #0f766e;">
                                <option value="">-- Select Cash or Bank Account --</option>
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
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Amount Received (PKR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white fw-bold py-1 px-2" style="font-size: 0.85rem;">Rs.</span>
                                <input type="number" step="0.01" min="1" name="amount" class="form-control fw-bold text-success"
                                       placeholder="50000" required>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Received Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-12 col-md-4">
                            <label class="form-label mb-1 small fw-bold text-dark">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="adminDirectPaymentMethodSelect" class="form-select" required>
                                <option value="cash" selected>Cash Collection</option>
                                <option value="bank_transfer">Bank Transfer / IBFT</option>
                                <option value="cheque">Cheque / Pay Order</option>
                                <option value="raast">Raast Instant Transfer</option>
                                <option value="easypaisa">EasyPaisa / JazzCash</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Bank Name -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">Bank / Counter Name</label>
                            <input type="text" name="bank_name" id="adminDirectBankNameInput" class="form-control" placeholder="e.g. Meezan Bank / Head Office Cash">
                        </div>

                        <!-- Reference / Trx ID / Cheque No -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">Reference # / Cheque # / Trx ID</label>
                            <input type="text" name="transaction_reference" class="form-control font-monospace" placeholder="e.g. TRX1234567">
                        </div>

                        <!-- Upload Slip -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">Upload Deposit Slip / Cheque Photo</label>
                            <input type="file" name="slip_image" class="form-control bg-white" accept="image/*,.pdf">
                        </div>

                        <!-- Notes -->
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1 small fw-bold text-dark">Notes / Narration</label>
                            <input type="text" name="notes" class="form-control" placeholder="Remarks or deposit details...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-teal rounded-pill px-4 shadow-sm fw-bold">
                        <i class="fas fa-check-circle me-1"></i> Record & Credit Ledger
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Approve Payment Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white py-2.5 px-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Approve Distributor Payment</h6>
                        <span class="text-white-50" style="font-size: 0.72rem;">Verify payment details and credit into company account & distributor ledger</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="approveForm" method="POST" action="">
                @csrf
                <div class="modal-body p-3 bg-light">
                    <!-- Detailed Info Card -->
                    <div class="card border-0 shadow-none rounded-3 p-3 bg-white mb-3">
                        <div class="row g-2">
                            <!-- Left: Sender & Amount -->
                            <div class="col-12 col-md-6 border-end-md pb-2 pb-md-0">
                                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.70rem;">Payment Received From</div>
                                <h6 class="fw-bold text-dark mb-0 mt-0.5" id="approveDistName">-</h6>
                                <div class="text-muted small" style="font-size: 0.75rem;" id="approveDistSub">-</div>

                                <div class="mt-2.5 p-2 rounded-2" style="background: #ecfdf5; border: 1px solid #a7f3d0;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success small fw-bold text-uppercase" style="font-size: 0.72rem;">Amount Received</span>
                                        <span class="fw-bold text-success fs-5">Rs. <span id="approveAmount">0.00</span></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1 pt-1 border-top" style="font-size: 0.72rem; border-color: rgba(16, 185, 129, 0.2) !important;">
                                        <span class="text-muted">Current Ledger Balance:</span>
                                        <span class="fw-bold text-danger" id="approveDistBalance">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Transaction Details -->
                            <div class="col-12 col-md-6 ps-md-3">
                                <div class="text-muted small text-uppercase fw-semibold" style="font-size: 0.70rem;">Submission Details</div>
                                <div class="d-flex justify-content-between py-1 border-bottom" style="font-size: 0.78rem;">
                                    <span class="text-muted">Payment Number:</span>
                                    <span class="font-monospace fw-bold text-dark" id="approvePaymentNo">-</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom" style="font-size: 0.78rem;">
                                    <span class="text-muted">Payment Date:</span>
                                    <span class="fw-semibold text-dark" id="approveDate">-</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom" style="font-size: 0.78rem;">
                                    <span class="text-muted">Payment Method:</span>
                                    <span class="fw-semibold text-dark text-capitalize" id="approveMethod">-</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom" style="font-size: 0.78rem;">
                                    <span class="text-muted">Deposited Into:</span>
                                    <span class="fw-bold text-teal" style="color: #0f766e;" id="approveSubmittedAccount">-</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom" style="font-size: 0.78rem;" id="approveRefRow">
                                    <span class="text-muted">Reference / Trx:</span>
                                    <span class="font-monospace fw-semibold text-dark" id="approveRef">-</span>
                                </div>
                                <div class="d-flex justify-content-between py-1" style="font-size: 0.78rem;" id="approveSlipRow">
                                    <span class="text-muted">Deposit Slip:</span>
                                    <a id="approveSlipLink" href="#" target="_blank" class="text-decoration-none fw-bold" style="color: #0f766e;">
                                        <i class="fas fa-image me-1"></i> View Receipt Slip
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Notes if present -->
                        <div class="mt-2 pt-2 border-top small text-muted" id="approveNotesRow" style="font-size: 0.75rem; display: none;">
                            <i class="fas fa-comment-alt me-1 text-muted"></i> <strong>Remarks:</strong> <span id="approveNotes"></span>
                        </div>
                    </div>

                    <!-- Target Company Account (Pre-selected) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="fas fa-university me-1" style="color: #0f766e;"></i> Credit To Company Account (Cash / Bank) <span class="text-danger">*</span>
                        </label>
                        <select name="account_id" id="approveAccountId" class="form-select fw-semibold" style="border-color: #0f766e;" required>
                            <option value="">-- Choose Account --</option>
                            @if(isset($companyAccounts) && $companyAccounts->count() > 0)
                                @php
                                    $grouped = $companyAccounts->groupBy(function($item) {
                                        return $item->head ? $item->head->name : 'Company Accounts';
                                    });
                                @endphp
                                @foreach($grouped as $headName => $accounts)
                                    <optgroup label="{{ $headName }}">
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}" data-title="{{ $acc->title }}" data-code="{{ $acc->account_code }}">
                                                {{ $acc->title }} {{ $acc->account_code ? '• ' . $acc->account_code : '' }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endif
                        </select>
                        <div class="form-text small text-muted">
                            Payment amount will be automatically added to this company account's software balance.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Approval Note (Optional)</label>
                        <input type="text" name="approval_notes" class="form-control" placeholder="e.g. Verified with bank statement">
                    </div>

                    <div class="alert alert-info py-2 px-3 rounded-3 small mb-0 d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                        <i class="fas fa-info-circle fs-5"></i>
                        <div>
                            Approving will immediately <strong>credit the distributor ledger</strong> and reduce their outstanding payable balance.
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-4 shadow-sm fw-bold">
                        <i class="fas fa-check me-1"></i> Confirm & Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Reject Payment Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-times-circle me-2"></i> Reject Payment Submission
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-none rounded-3 p-3 bg-white mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Payment Number:</span>
                            <span class="font-monospace fw-bold text-dark" id="rejectPaymentNo">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Distributor:</span>
                            <span class="fw-bold text-dark" id="rejectDistName">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                            <span class="text-muted small">Amount:</span>
                            <span class="fw-bold fs-6 text-danger">Rs. <span id="rejectAmount">0.00</span></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Slip amount mismatch, transaction not found in bank statement, duplicate submission, etc."></textarea>
                    </div>

                    <div class="alert alert-warning py-2 px-3 rounded-3 small mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        The distributor will see this rejection reason on their portal. No ledger entry will be posted.
                    </div>
                </div>

                <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold">
                        <i class="fas fa-times me-1"></i> Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4: View Slip Image -->
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

    function openApproveModal(data) {
        if (typeof data !== 'object') {
            data = {
                id: arguments[0],
                payment_no: arguments[1],
                dist_name: arguments[2],
                amount: arguments[3],
                account_id: arguments[4]
            };
        }

        document.getElementById('approvePaymentNo').innerText = data.payment_no || '-';
        document.getElementById('approveDistName').innerText = data.dist_name || '-';
        const distSubEl = document.getElementById('approveDistSub');
        if (distSubEl) {
            distSubEl.innerText = (data.dist_code ? 'Code: ' + data.dist_code : '') + (data.dist_phone ? ' • Phone: ' + data.dist_phone : '');
        }
        const distBalEl = document.getElementById('approveDistBalance');
        if (distBalEl) {
            distBalEl.innerText = 'Rs. ' + (data.dist_balance || '0.00');
        }
        document.getElementById('approveAmount').innerText = data.amount || '0.00';
        const dateEl = document.getElementById('approveDate');
        if (dateEl) dateEl.innerText = data.date || '-';
        const methodEl = document.getElementById('approveMethod');
        if (methodEl) methodEl.innerText = data.method || '-';

        let submitted = data.account_title || data.bank_name || '';
        if (data.account_number) {
            submitted += ' (' + data.account_number + ')';
        }
        const subAccEl = document.getElementById('approveSubmittedAccount');
        if (subAccEl) {
            subAccEl.innerText = submitted || 'Company Account';
        }

        // Reference
        const refRow = document.getElementById('approveRefRow');
        const refEl = document.getElementById('approveRef');
        if (refRow && refEl) {
            if (data.reference) {
                refEl.innerText = data.reference;
                refRow.style.display = 'flex';
            } else {
                refRow.style.display = 'none';
            }
        }

        // Slip Proof
        const slipRow = document.getElementById('approveSlipRow');
        const slipLink = document.getElementById('approveSlipLink');
        if (slipRow && slipLink) {
            if (data.slip_url) {
                slipLink.href = data.slip_url;
                slipRow.style.display = 'flex';
            } else {
                slipRow.style.display = 'none';
            }
        }

        // Notes
        const notesRow = document.getElementById('approveNotesRow');
        const notesEl = document.getElementById('approveNotes');
        if (notesRow && notesEl) {
            if (data.notes) {
                notesEl.innerText = data.notes;
                notesRow.style.display = 'block';
            } else {
                notesRow.style.display = 'none';
            }
        }

        // Target Account Dropdown Pre-selection
        const accSelect = document.getElementById('approveAccountId');
        if (accSelect) {
            accSelect.value = data.account_id || '';
            // If account_id was not explicitly passed, match by account title or bank name
            if (!accSelect.value && (data.account_title || data.bank_name)) {
                const searchKey = (data.account_title || data.bank_name).toLowerCase().trim();
                for (let i = 0; i < accSelect.options.length; i++) {
                    const optText = accSelect.options[i].text.toLowerCase();
                    if (searchKey && optText.includes(searchKey)) {
                        accSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }

        document.getElementById('approveForm').action = "{{ url('admin/distributors/payments') }}/" + data.id + "/approve";
        let modal = new bootstrap.Modal(document.getElementById('approveModal'));
        modal.show();
    }

    function openRejectModal(data) {
        const id = typeof data === 'object' ? data.id : data;
        const no = typeof data === 'object' ? data.payment_no : arguments[1];
        const distName = typeof data === 'object' ? data.dist_name : arguments[2];
        const amount = typeof data === 'object' ? data.amount : arguments[3];
        document.getElementById('rejectPaymentNo').innerText = no;
        document.getElementById('rejectDistName').innerText = distName;
        document.getElementById('rejectAmount').innerText = amount;
        document.getElementById('rejectForm').action = "{{ url('admin/distributors/payments') }}/" + id + "/reject";
        let modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }

    function handleAdminDirectAccountChange(select) {
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) return;
        const method = opt.dataset.method || 'cash';
        const title = opt.dataset.title || '';
        const methodSelect = document.getElementById('adminDirectPaymentMethodSelect');
        const bankInput = document.getElementById('adminDirectBankNameInput');
        if (methodSelect) {
            methodSelect.value = method;
        }
        if (bankInput && !bankInput.value) {
            bankInput.value = title;
        }
    }
</script>
@endsection
