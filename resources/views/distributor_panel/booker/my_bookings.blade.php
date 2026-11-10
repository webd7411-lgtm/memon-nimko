@extends('distributor_panel.layout.app')

@section('content')
<style>
:root {
    --bk-teal: #0f766e;
    --bk-teal-light: #f0fdfa;
    --bk-teal-border: #ccfbf1;
}

/* Prevent any horizontal overflow */
.booker-page-container {
    max-width: 100%;
    overflow-x: hidden;
}

.booking-badge-no {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
    font-weight: 800;
    font-size: 0.92rem;
    color: var(--bk-teal);
    background: var(--bk-teal-light);
    border: 1px solid var(--bk-teal-border);
    padding: 4px 10px;
    border-radius: 8px;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
}

.booking-app-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 14px 16px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    width: 100%;
    box-sizing: border-box;
}

.booking-app-card:active {
    background-color: #fcfdfe;
}

@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 10px !important;
        padding-right: 10px !important;
        overflow-x: hidden !important;
    }
    .page-title-text {
        font-size: 1.15rem !important;
    }
    .filter-card {
        border-radius: 16px !important;
    }
}
</style>

<div class="container-fluid px-2 px-sm-3 px-md-4 py-2 py-md-3 booker-page-container">

    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark page-title-text d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-list text-teal" style="color: #0f766e;"></i>
                <span>My Shop Bookings</span>
            </h4>
            <span class="text-muted small">Track and review all shop market orders booked by you</span>
        </div>
        <div class="d-flex gap-2 w-sm-auto w-100 justify-content-end">
            <a href="{{ route('distributor.booker.create') }}" class="btn btn-success fw-bold rounded-pill px-3 py-2 shadow-sm w-100 w-sm-auto text-center" style="background: linear-gradient(135deg, #10b981 0%, #0f766e 100%); border: none;">
                <i class="fas fa-plus-circle me-1"></i> + Book New Order
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

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3 filter-card">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('distributor.booker.my_bookings') }}" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Status Filter</label>
                    <select name="status" class="form-select form-select-sm py-2">
                        <option value="">All Statuses</option>
                        <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>Pending / Booked</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered / Sold</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Order Type</label>
                    <select name="payment_type" class="form-select form-select-sm py-2">
                        <option value="">All Types (Cash &amp; Credit)</option>
                        <option value="cash" {{ request('payment_type') === 'cash' ? 'selected' : '' }}>Order Cash</option>
                        <option value="credit" {{ request('payment_type') === 'credit' ? 'selected' : '' }}>Order Credit</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm py-2" value="{{ request('date') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark flex-fill fw-bold py-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('distributor.booker.my_bookings') }}" class="btn btn-sm btn-light border flex-fill py-2 text-center">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- DESKTOP TABLE VIEW (Visible on tablet & desktop >= 768px) --}}
    <div class="d-none d-md-block card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark" style="background-color: #0f172a;">
                    <tr>
                        <th class="ps-3">Booking #</th>
                        <th>Date</th>
                        <th>Shop Name</th>
                        <th class="text-center">Order Type</th>
                        <th>Area / Phone</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td class="ps-3 fw-bold font-monospace text-teal" style="color: #0f766e;">
                                {{ $b->booking_no }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($b->booking_date)->format('d M, Y') }}</td>
                            <td>
                                <strong class="text-dark d-block">{{ $b->customer->shop_name ?? 'N/A' }}</strong>
                                <small class="text-muted">{{ $b->customer->owner_name ?? '' }}</small>
                            </td>
                            <td class="text-center">
                                @if(($b->payment_type ?? 'cash') === 'credit')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill fw-semibold">
                                        <i class="fas fa-file-invoice-dollar me-1"></i> Credit
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-semibold">
                                        <i class="fas fa-money-bill-wave me-1"></i> Cash
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $b->customer->area ?? '-' }}</div>
                                <small class="text-muted"><i class="fas fa-phone text-secondary me-1"></i> {{ $b->customer->phone ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $b->total_items }} items</span>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ number_format($b->total_qty, 1) }} Qty</div>
                            </td>
                            <td class="text-end fw-bolder text-dark">
                                Rs {{ number_format($b->grand_total, 2) }}
                            </td>
                            <td class="text-center">
                                @if($b->status === 'delivered')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill">
                                        <i class="fas fa-check-double me-1"></i> Delivered (Sold)
                                    </span>
                                @elseif($b->status === 'cancelled')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-pill">
                                        <i class="fas fa-times me-1"></i> Cancelled
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1.5 rounded-pill">
                                        <i class="fas fa-clock me-1"></i> Booked (Pending)
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('distributor.booker.receipt', $b->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" title="Print Booking Slip">
                                    <i class="fas fa-print"></i> Slip
                                </a>
                                @if($b->status === 'booked')
                                    <a href="{{ route('distributor.booker.edit', $b->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 ms-1" title="Edit Booking">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h5>No Bookings Found</h5>
                                <p class="small text-secondary mb-3">You have not booked any orders yet or no records match your filter criteria.</p>
                                <a href="{{ route('distributor.booker.create') }}" class="btn btn-success rounded-pill px-4">
                                    <i class="fas fa-plus me-1"></i> Start First Booking
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MOBILE NATIVE APP CARDS VIEW (Visible on Mobile Screens < 768px) --}}
    <div class="d-md-none mb-3">
        @forelse($bookings as $b)
            <div class="booking-app-card mb-3">
                {{-- Header Row: Booking #, Order Type, Status --}}
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                    <span class="booking-badge-no">
                        <i class="fas fa-hashtag me-1" style="color: #0f766e;"></i>{{ $b->booking_no }}
                    </span>
                    <div class="d-flex align-items-center gap-1">
                        @if(($b->payment_type ?? 'cash') === 'credit')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 small fw-bold">
                                Credit
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small fw-bold">
                                Cash
                            </span>
                        @endif

                        @if($b->status === 'delivered')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small">
                                <i class="fas fa-check-double"></i> Delivered
                            </span>
                        @elseif($b->status === 'cancelled')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 small">
                                <i class="fas fa-times"></i> Cancelled
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2 py-1 small">
                                <i class="fas fa-clock"></i> Booked
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Shop & Customer Information --}}
                <div class="mb-2">
                    <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2 mb-1">
                        <i class="fas fa-store text-teal" style="color: #0f766e;"></i>
                        <span>{{ $b->customer->shop_name ?? 'N/A' }}</span>
                    </div>
                    <div class="text-muted small d-flex flex-wrap align-items-center gap-2">
                        @if(!empty($b->customer->owner_name))
                            <span><i class="fas fa-user text-secondary me-1"></i>{{ $b->customer->owner_name }}</span>
                        @endif
                        @if(!empty($b->customer->area))
                            <span><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $b->customer->area }}</span>
                        @endif
                    </div>
                    @if(!empty($b->customer->phone))
                        <div class="mt-1">
                            <a href="tel:{{ $b->customer->phone }}" class="text-decoration-none small text-dark fw-semibold">
                                <i class="fas fa-phone-alt text-success me-1"></i>{{ $b->customer->phone }}
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Key Financial & Summary Box --}}
                <div class="p-2.5 rounded-3 bg-light border mb-2.5" style="background-color: #f8fafc !important;">
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                        <span class="text-secondary small"><i class="fas fa-calendar-alt me-1 text-muted"></i> Date:</span>
                        <span class="text-dark fw-bold small">{{ \Carbon\Carbon::parse($b->booking_date)->format('d M, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light-subtle">
                        <span class="text-secondary small"><i class="fas fa-box-open me-1 text-muted"></i> Items / Qty:</span>
                        <span class="badge bg-white text-dark border">{{ $b->total_items }} items &bull; {{ number_format($b->total_qty, 1) }} Qty</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-dark fw-bold"><i class="fas fa-receipt me-1 text-teal" style="color: #0f766e;"></i> Grand Total:</span>
                        <span class="fw-bolder fs-5 text-teal" style="color: #0f766e;">Rs {{ number_format($b->grand_total, 2) }}</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('distributor.booker.receipt', $b->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill flex-fill py-2 fw-semibold text-center">
                        <i class="fas fa-print me-1"></i> Booking Slip
                    </a>
                    @if($b->status === 'booked')
                        <a href="{{ route('distributor.booker.edit', $b->id) }}" class="btn btn-warning btn-sm rounded-pill flex-fill py-2 fw-bold text-dark text-center">
                            <i class="fas fa-edit me-1"></i> Edit Order
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-4">
                <i class="fas fa-clipboard fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                <h5>No Bookings Found</h5>
                <p class="small text-secondary mb-3">You have not booked any orders yet or no records match your filter criteria.</p>
                <a href="{{ route('distributor.booker.create') }}" class="btn btn-success rounded-pill px-4 py-2">
                    <i class="fas fa-plus me-1"></i> Start First Booking
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($bookings->hasPages())
        <div class="card border-0 bg-transparent py-2">
            <div class="d-flex justify-content-center">
                {{ $bookings->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
