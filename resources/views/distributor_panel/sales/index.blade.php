@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-2">

    <!-- Top Action Header Banner -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-3 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #0f172a 60%, #062e24 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('distributor.dashboard') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-white-50">•</span>
                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(20, 184, 166, 0.25); border: 1px solid rgba(20, 184, 166, 0.45); color: #5eead4; font-size: 0.72rem;">
                    <i class="fas fa-cash-register me-1"></i> Sales Register
                </span>
            </div>
            <h4 class="mb-0 fw-bold text-white">Retail Sales History &amp; Invoices</h4>
            <p class="mb-0 text-white-50 small">Track all confirmed sales, counter billing, cash collected, and outstanding shop balances</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.shop_bookings.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold">
                <i class="fas fa-boxes-packing me-1 text-warning"></i> Shop Bookings
            </a>
            <a href="{{ route('distributor.sales.create') }}" class="btn btn-sm btn-teal rounded-pill px-3 py-2 shadow-sm fw-bold">
                <i class="fas fa-cart-plus me-1"></i> + Make New Sale
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Executive KPI Summary Cards -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border-left: 4px solid #0f766e !important;">
                <span class="text-secondary small fw-bold text-uppercase">Total Sales Value</span>
                <h3 class="fw-bolder mb-0" style="color: #0f766e;">Rs {{ number_format($stats['total_sales'] ?? 0, 0) }}</h3>
                <small class="text-muted">Total net billing in scope</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 4px solid #d97706 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary small fw-bold text-uppercase">Total Discount Given</span>
                    <a href="{{ route('distributor.claims.index') }}" class="badge bg-white text-dark text-decoration-none shadow-xs" style="font-size: 0.68rem;">Claim Discount &rarr;</a>
                </div>
                <h3 class="fw-bolder mb-0 text-warning-emphasis">Rs {{ number_format($stats['total_discount'] ?? 0, 0) }}</h3>
                <small class="text-muted fw-semibold">Unclaimed: <strong class="text-success">Rs {{ number_format($stats['unclaimed_discount'] ?? 0, 0) }}</strong></small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-left: 4px solid #ef4444 !important;">
                <span class="text-secondary small fw-bold text-uppercase">Total Due / Credit</span>
                <h3 class="fw-bolder mb-0 text-danger">Rs {{ number_format($stats['total_due'] ?? 0, 0) }}</h3>
                <small class="text-muted">Added to shop balances</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-left: 4px solid #475569 !important;">
                <span class="text-secondary small fw-bold text-uppercase">Total Invoices</span>
                <h3 class="fw-bolder mb-0 text-dark">{{ number_format($stats['total_invoices'] ?? 0) }}</h3>
                <small class="text-muted">Sales receipts recorded</small>
            </div>
        </div>
    </div>

    <!-- Systematic Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('distributor.sales.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Invoice # or Shop Name...">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Retail Customer</label>
                    <select name="customer_id" class="form-select form-select-sm">
                        <option value="">All Retail Stores</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->shop_name }} {{ $c->area ? '• ' . $c->area : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Payment Mode</label>
                    <select name="payment_type" class="form-select form-select-sm">
                        <option value="">All Modes</option>
                        <option value="cash" {{ request('payment_type') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="credit" {{ request('payment_type') === 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="partial" {{ request('payment_type') === 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                </div>

                <div class="col-6 col-md-1">
                    <label class="form-label small fw-bold text-muted mb-1">From</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                </div>

                <div class="col-6 col-md-1">
                    <label class="form-label small fw-bold text-muted mb-1">To</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white rounded-pill w-100 fw-bold" style="background: #0f766e;">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('distributor.sales.index') }}" class="btn btn-sm btn-light border rounded-pill w-50">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Systematic Sales Register Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #0f172a; color: #ffffff; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 14%;">Invoice #</th>
                        <th style="width: 20%;">Retail Customer</th>
                        <th style="width: 13%;">Staff / Booker</th>
                        <th style="width: 12%;">Date &amp; Time</th>
                        <th style="width: 10%;" class="text-center">Mode</th>
                        <th style="width: 10%;" class="text-end">Total Amount</th>
                        <th style="width: 10%;" class="text-end">Cash Paid</th>
                        <th style="width: 11%;" class="text-end">Due Balance</th>
                        <th style="width: 14%;" class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.85rem;">
                    @forelse($sales as $sale)
                    @php
                        $rowTotal = (float)$sale->grand_total;
                        $rowDue = (float)$sale->due_amount;
                        if ($sale->relationLoaded('items') && $sale->items && $sale->items->count() > 0) {
                            $cG = 0;
                            $cD = 0;
                            foreach ($sale->items as $it) {
                                $cG += ((float)$it->qty * (float)$it->unit_price);
                                $cD += (float)($it->discount ?? 0);
                            }
                            $trueNet = max(0, $cG - $cD);
                            if ($cD > 0 && abs($rowTotal - $cG) < 0.05) {
                                $rowTotal = $trueNet;
                                $rowDue = max(0, $trueNet - (float)$sale->paid_amount);
                            }
                        }
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <span class="fw-bold font-monospace text-teal" style="color: #0f766e; font-size: 0.88rem;">{{ $sale->invoice_no }}</span>
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $sale->customer->shop_name ?? 'Walk-in Customer' }}</strong>
                            <small class="text-muted d-block">
                                {{ $sale->customer->owner_name ?? '' }}
                                @if(!empty($sale->customer->phone))
                                    &bull; <i class="fas fa-phone text-secondary" style="font-size: 0.7rem;"></i> {{ $sale->customer->phone }}
                                @endif
                            </small>
                        </td>
                        <td>
                            @if($sale->booker)
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    <i class="fas fa-user-tie me-1"></i> {{ $sale->booker->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark border px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                    <i class="fas fa-store me-1"></i> Counter / Direct
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark fw-semibold">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}</span>
                            @if($sale->created_at)
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $sale->created_at->timezone(config('app.timezone', 'Asia/Karachi'))->format('h:i A') }}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($sale->payment_type === 'cash')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.74rem;">
                                    <i class="fas fa-money-bill-wave me-1"></i> Cash
                                </span>
                            @elseif($sale->payment_type === 'credit')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.74rem;">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Credit
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.74rem;">
                                    <i class="fas fa-adjust me-1"></i> Partial
                                </span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rs. {{ number_format($rowTotal, 2) }}
                        </td>
                        <td class="text-end text-success fw-semibold">
                            Rs. {{ number_format($sale->paid_amount, 2) }}
                        </td>
                        <td class="text-end">
                            @if($rowDue > 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace px-2 py-1" style="font-size: 0.78rem;">
                                    Rs {{ number_format($rowDue, 2) }}
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace px-2 py-1" style="font-size: 0.74rem;">
                                    <i class="fas fa-check me-1"></i> Clear
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('distributor.sales.receipt', $sale->id) }}" target="_blank" class="btn btn-outline-secondary" title="Thermal POS Slip">
                                    <i class="fas fa-receipt"></i>
                                </a>
                                <a href="{{ route('distributor.sales.invoice_a4', $sale->id) }}" target="_blank" class="btn btn-outline-primary" title="A4 Commercial Invoice">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('distributor.returns.create', ['sale_id' => $sale->id]) }}" class="btn btn-outline-danger" title="Sales Return">
                                    <i class="fas fa-undo-alt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                            <h5 class="fw-semibold">No Sales Invoices Found</h5>
                            <p class="small text-secondary mb-3">No sales match the specified filters or date criteria.</p>
                            <a href="{{ route('distributor.sales.create') }}" class="btn btn-sm btn-teal rounded-pill px-3">
                                <i class="fas fa-cart-plus me-1"></i> Make New Sale
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Systematic Pagination Footer -->
        <div class="card-footer bg-white py-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="small text-muted">
                Showing <strong class="text-dark">{{ $sales->firstItem() ?? 0 }}</strong> to <strong class="text-dark">{{ $sales->lastItem() ?? 0 }}</strong> of <strong class="text-dark">{{ $sales->total() }}</strong> sales invoices
            </div>
            <div>
                {{ $sales->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
