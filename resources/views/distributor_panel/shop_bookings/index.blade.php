@extends('distributor_panel.layout.app')

@section('content')
<style>
:root {
    --sb-teal: #0f766e;
    --sb-teal-light: #f0fdfa;
    --sb-teal-border: #ccfbf1;
}

html, body {
    overflow-x: hidden !important;
    max-width: 100vw;
}

.sb-page-container {
    max-width: 100%;
    overflow-x: hidden;
    box-sizing: border-box;
}

.sb-kpi-card {
    border-radius: 14px !important;
    padding: 12px 14px !important;
    transition: transform 0.15s ease;
}

.sb-kpi-label {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    color: #475569;
    display: block;
}

.sb-kpi-value {
    font-size: 1.45rem;
    font-weight: 900;
    line-height: 1.2;
    margin: 3px 0 2px 0;
}

.sb-kpi-sub {
    font-size: 0.72rem;
    color: #64748b;
    display: block;
}

/* Mobile Booking Box Card */
.sb-mobile-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    margin-bottom: 10px;
    width: 100%;
    box-sizing: border-box;
}

.sb-booking-no-pill {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-weight: 800;
    font-size: 0.84rem;
    color: var(--sb-teal);
    background: var(--sb-teal-light);
    border: 1px solid var(--sb-teal-border);
    padding: 3px 8px;
    border-radius: 7px;
    display: inline-flex;
    align-items: center;
}

.sb-mobile-metrics {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 10px;
    padding: 8px 10px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}

.sb-action-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px dashed #e2e8f0;
}

.sb-action-btn {
    font-size: 0.76rem !important;
    font-weight: 700 !important;
    padding: 6px 10px !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    white-space: nowrap !important;
}

@media (max-width: 767.98px) {
    .container-fluid {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .sb-page-title {
        font-size: 1.1rem !important;
    }

    .sb-page-subtitle {
        font-size: 0.74rem !important;
        line-height: 1.3 !important;
    }

    .sb-header-btn {
        font-size: 0.78rem !important;
        padding: 6px 12px !important;
        white-space: nowrap !important;
        flex: 1;
        text-align: center;
    }

    .sb-kpi-card {
        padding: 10px 11px !important;
        border-radius: 12px !important;
    }

    .sb-kpi-label {
        font-size: 0.64rem !important;
    }

    .sb-kpi-value {
        font-size: 1.15rem !important;
    }

    .sb-kpi-sub {
        font-size: 0.65rem !important;
    }

    .sb-filter-card .card-body {
        padding: 10px 12px !important;
    }

    .sb-filter-card .form-label {
        font-size: 0.7rem !important;
        margin-bottom: 2px !important;
    }

    .sb-filter-card .form-select,
    .sb-filter-card .form-control {
        font-size: 0.8rem !important;
        padding: 5px 8px !important;
    }
}
</style>

<div class="container-fluid px-2 px-sm-3 px-md-4 py-2 py-md-3 sb-page-container">

    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2.5 mb-md-3">
        <div>
            <h4 class="fw-bold mb-0.5 text-dark sb-page-title d-flex align-items-center gap-2">
                <i class="fas fa-boxes-packing text-teal" style="color: #0f766e;"></i>
                <span>Shop Bookings (Market Orders)</span>
            </h4>
            <span class="text-muted small d-block sb-page-subtitle">Review market demand orders &amp; convert them to confirmed sales on dispatch</span>
        </div>
        <div class="d-flex gap-2 w-100 w-md-auto">
            <a href="{{ route('distributor.booker.create') }}" class="btn btn-outline-success fw-bold rounded-pill px-3 shadow-xs sb-header-btn">
                <i class="fas fa-plus me-1"></i> New Booking
            </a>
            <a href="{{ route('distributor.bookers.index') }}" class="btn btn-dark fw-bold rounded-pill px-3 shadow-xs sb-header-btn">
                <i class="fas fa-users-gear me-1"></i> Manage Bookers
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 small" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm py-2 px-3 small" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KPI Summary Cards (Compact 2x2 Grid on Mobile, 4-in-row on Desktop) --}}
    <div class="row g-2 g-md-3 mb-2.5 mb-md-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm sb-kpi-card h-100" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 4px solid #f59e0b !important;">
                <span class="sb-kpi-label">Pending Bookings</span>
                <div class="sb-kpi-value text-dark">{{ $pendingBookingsCount }}</div>
                <span class="sb-kpi-sub">Awaiting dispatch</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm sb-kpi-card h-100" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-left: 4px solid #ef4444 !important;">
                <span class="sb-kpi-label">Pending Value</span>
                <div class="sb-kpi-value text-danger">Rs {{ number_format($pendingBookingsAmount, 0) }}</div>
                <span class="sb-kpi-sub">Demand value</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm sb-kpi-card h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 4px solid #10b981 !important;">
                <span class="sb-kpi-label">Delivered Today</span>
                <div class="sb-kpi-value text-success">{{ $deliveredTodayCount }}</div>
                <span class="sb-kpi-sub">Converted to sale</span>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm sb-kpi-card h-100" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border-left: 4px solid #0f766e !important;">
                <span class="sb-kpi-label">Delivered Value</span>
                <div class="sb-kpi-value" style="color: #0f766e;">Rs {{ number_format($deliveredTodayAmount, 0) }}</div>
                <span class="sb-kpi-sub">Billed today</span>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card border-0 shadow-sm rounded-4 mb-2.5 mb-md-3 sb-filter-card">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('distributor.shop_bookings.index') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="all">All Statuses</option>
                        <option value="booked" {{ request('status', 'booked') == 'booked' ? 'selected' : '' }}>Pending / Booked</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered / Sold</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Order Type</label>
                    <select name="payment_type" class="form-select form-select-sm">
                        <option value="">All (Cash &amp; Credit)</option>
                        <option value="cash" {{ request('payment_type') === 'cash' ? 'selected' : '' }}>Order Cash</option>
                        <option value="credit" {{ request('payment_type') === 'credit' ? 'selected' : '' }}>Order Credit</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Order Booker</label>
                    <select name="booker_id" class="form-select form-select-sm">
                        <option value="">All Bookers</option>
                        @foreach($bookers as $bkr)
                            <option value="{{ $bkr->id }}" {{ request('booker_id') == $bkr->id ? 'selected' : '' }}>{{ $bkr->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Retail Shop</label>
                    <select name="customer_id" class="form-select form-select-sm">
                        <option value="">All Shops</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->shop_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>

                <div class="col-6 col-md-2 d-flex gap-1.5">
                    <button type="submit" class="btn btn-sm btn-dark flex-fill fw-bold py-1.5">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('distributor.shop_bookings.index') }}" class="btn btn-sm btn-light border px-2.5 py-1.5">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- DESKTOP TABLE VIEW (>= 768px) --}}
    <div class="d-none d-md-block card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark" style="background-color: #0f172a;">
                    <tr>
                        <th class="ps-3">Booking #</th>
                        <th>Date</th>
                        <th>Order Booker</th>
                        <th>Retail Shop</th>
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
                                @if($b->converted_sale_id && $b->sale)
                                    <div class="small font-monospace text-muted" style="font-size: 0.7rem;">
                                        Inv: <a href="{{ route('distributor.sales.receipt', $b->converted_sale_id) }}" target="_blank" class="text-decoration-none text-primary">{{ $b->sale->invoice_no }}</a>
                                    </div>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($b->booking_date)->format('d M, Y') }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                    <i class="fas fa-user-tie me-1"></i> {{ $b->booker->name ?? 'Staff' }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $b->customer->shop_name ?? 'N/A' }}</strong>
                                <small class="text-muted">{{ $b->customer->owner_name ?? '' }}</small>
                            </td>
                            <td class="text-center">
                                @if(($b->payment_type ?? 'cash') === 'credit')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                        <i class="fas fa-file-invoice-dollar me-1"></i> Credit
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-bold" style="font-size: 0.75rem;">
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
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('distributor.booker.receipt', $b->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print Slip">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <button type="button" class="btn btn-outline-primary btn-view-booking" data-id="{{ $b->id }}" title="View Items">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if($b->status === 'booked')
                                        <a href="{{ route('distributor.shop_bookings.edit', $b->id) }}" class="btn btn-warning text-dark fw-semibold" title="Edit Booking">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>

                                        <button type="button" class="btn btn-success btn-deliver-modal" data-id="{{ $b->id }}" data-no="{{ $b->booking_no }}" data-total="{{ $b->grand_total }}" data-shop="{{ $b->customer->shop_name ?? 'Shop' }}" data-type="{{ $b->payment_type ?? 'cash' }}" title="Convert to Sale & Deliver">
                                            <i class="fas fa-truck-fast me-1"></i> Deliver
                                        </button>

                                        <form action="{{ route('distributor.shop_bookings.cancel', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger" title="Cancel Booking">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h5>No bookings found</h5>
                                <p class="small text-secondary">No order bookings match the selected filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MOBILE NATIVE APP BOXES VIEW (< 768px) — ZERO HORIZONTAL SCROLL --}}
    <div class="d-md-none mb-2">
        @forelse($bookings as $b)
            <div class="sb-mobile-card">
                {{-- Top Header: Booking #, Invoice #, Type & Status --}}
                <div class="d-flex align-items-center justify-content-between gap-2 pb-2 mb-2 border-bottom">
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <span class="sb-booking-no-pill">{{ $b->booking_no }}</span>
                        @if($b->converted_sale_id && $b->sale)
                            <a href="{{ route('distributor.sales.receipt', $b->converted_sale_id) }}" target="_blank" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none font-monospace" style="font-size: 0.68rem;">
                                {{ $b->sale->invoice_no }}
                            </a>
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-1">
                        @if(($b->payment_type ?? 'cash') === 'credit')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.68rem;">Credit</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-bold" style="font-size: 0.68rem;">Cash</span>
                        @endif

                        @if($b->status === 'delivered')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                <i class="fas fa-check-double"></i> Sold
                            </span>
                        @elseif($b->status === 'cancelled')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                <i class="fas fa-times"></i> Cancelled
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Shop, Booker & Contact Row --}}
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="fw-bolder text-dark d-flex align-items-center gap-1.5" style="font-size: 0.94rem;">
                            <i class="fas fa-store text-teal" style="color: #0f766e; font-size: 0.85rem;"></i>
                            <span>{{ $b->customer->shop_name ?? 'N/A' }}</span>
                        </div>
                        <span class="badge bg-light text-secondary border" style="font-size: 0.68rem; white-space: nowrap;">
                            <i class="fas fa-user-tie me-1"></i>{{ $b->booker->name ?? 'Staff' }}
                        </span>
                    </div>
                    <div class="text-muted mt-1 d-flex flex-wrap align-items-center gap-2" style="font-size: 0.74rem;">
                        @if(!empty($b->customer->owner_name))
                            <span><i class="fas fa-user text-secondary me-1"></i>{{ $b->customer->owner_name }}</span>
                        @endif
                        @if(!empty($b->customer->area))
                            <span><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $b->customer->area }}</span>
                        @endif
                        @if(!empty($b->customer->phone))
                            <a href="tel:{{ $b->customer->phone }}" class="text-decoration-none text-dark fw-semibold">
                                <i class="fas fa-phone-alt text-success me-1"></i>{{ $b->customer->phone }}
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Compact Metrics Strip --}}
                <div class="sb-mobile-metrics">
                    <div>
                        <span class="text-muted d-block" style="font-size: 0.66rem;">Date &amp; Items</span>
                        <strong class="text-dark" style="font-size: 0.78rem;">{{ \Carbon\Carbon::parse($b->booking_date)->format('d M, Y') }}</strong>
                        <span class="text-muted" style="font-size: 0.72rem;"> &bull; {{ $b->total_items }} items ({{ number_format($b->total_qty, 0) }} qty)</span>
                    </div>
                    <div class="text-end">
                        <span class="text-muted d-block" style="font-size: 0.66rem;">Grand Total</span>
                        <strong class="text-teal" style="color: #0f766e; font-size: 0.98rem;">Rs {{ number_format($b->grand_total, 2) }}</strong>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="sb-action-grid">
                    <a href="{{ route('distributor.booker.receipt', $b->id) }}" target="_blank" class="btn btn-outline-secondary sb-action-btn flex-fill">
                        <i class="fas fa-print"></i> Slip
                    </a>
                    <button type="button" class="btn btn-outline-primary sb-action-btn flex-fill btn-view-booking" data-id="{{ $b->id }}">
                        <i class="fas fa-eye"></i> Items
                    </button>

                    @if($b->status === 'booked')
                        <a href="{{ route('distributor.shop_bookings.edit', $b->id) }}" class="btn btn-warning text-dark sb-action-btn flex-fill">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button type="button" class="btn btn-success sb-action-btn flex-fill btn-deliver-modal" data-id="{{ $b->id }}" data-no="{{ $b->booking_no }}" data-total="{{ $b->grand_total }}" data-shop="{{ $b->customer->shop_name ?? 'Shop' }}" data-type="{{ $b->payment_type ?? 'cash' }}">
                            <i class="fas fa-truck-fast"></i> Deliver
                        </button>
                        <form action="{{ route('distributor.shop_bookings.cancel', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger sb-action-btn px-2.5" title="Cancel Booking">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-4">
                <i class="fas fa-inbox fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                <h5>No bookings found</h5>
                <p class="small text-secondary mb-0">No order bookings match the selected filter criteria.</p>
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

{{-- ════════════ MODAL: VIEW BOOKING ITEMS DETAILS ════════════ --}}
<div class="modal fade" id="modalBookingDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="fas fa-clipboard-list text-teal me-2" style="color:#0f766e;"></i>
                    Booking Details: <span id="dtlBookingNo" class="font-monospace text-primary"></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-2 mb-3 bg-light p-2.5 rounded-3 small">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Shop Name:</small>
                        <strong id="dtlShopName" class="text-dark">-</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Order Booker:</small>
                        <strong id="dtlBookerName" class="text-dark">-</strong>
                    </div>
                    <div class="col-6 col-md-2">
                        <small class="text-muted d-block">Booking Date:</small>
                        <strong id="dtlDate" class="text-dark">-</strong>
                    </div>
                    <div class="col-6 col-md-2">
                        <small class="text-muted d-block">Order Type:</small>
                        <span id="dtlOrderType" class="badge bg-success text-white px-2 py-1">-</span>
                    </div>
                    <div class="col-12 col-md-2">
                        <small class="text-muted d-block">Total Value:</small>
                        <strong id="dtlTotal" class="text-success fs-6">-</strong>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Product / Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Free</th>
                                <th class="text-center">Van Stock</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="dtlItemsBody">
                            {{-- Populated via AJAX --}}
                        </tbody>
                    </table>
                </div>

                <div id="dtlNotesContainer" class="p-2 bg-light rounded text-muted small mt-2" style="display:none;">
                    <strong>Booker Notes:</strong> <span id="dtlNotes"></span>
                </div>
            </div>
            <div class="modal-footer border-0 py-2 d-flex justify-content-between">
                <div>
                    <a href="#" id="dtlEditBtn" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3" style="display:none;">
                        <i class="fas fa-edit me-1"></i> Edit Booking
                    </a>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════════════ MODAL: CONFIRM DELIVERY & CONVERT TO SALE ════════════ --}}
<div class="modal fade" id="modalDeliverBooking" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="formDeliverBooking" method="POST" action="">
                @csrf
                <div class="modal-header border-bottom py-2.5 px-3">
                    <h6 class="modal-title fw-bold text-success">
                        <i class="fas fa-truck-fast me-2"></i> Confirm Delivery &amp; Deduct Stock
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-warning border-0 small rounded-3 mb-2.5 py-2">
                        <i class="fas fa-info-circle me-1"></i> 
                        Upon confirming delivery:
                        <ul class="mb-0 ps-3 mt-1">
                            <li>Stock will be deducted from custody inventory.</li>
                            <li>Confirmed Sale Invoice posted to customer's ledger.</li>
                            <li>Status updates to <strong>Delivered</strong>.</li>
                        </ul>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label small fw-bold text-muted mb-1">Booking # &amp; Shop</label>
                        <input type="text" class="form-control form-control-sm bg-light fw-bold" id="delivShopInfo" readonly>
                    </div>

                    <div class="mb-2.5 p-2 rounded-3 border" style="background:#f8fafc;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-bold text-muted">Booker's Payment Term:</span>
                            <span id="delivOrderTypeBadge" class="badge px-2.5 py-1 fw-bold font-monospace"></span>
                        </div>
                    </div>

                    <div class="mb-2.5">
                        <label class="form-label small fw-bold text-muted mb-1">Total Bill Amount</label>
                        <input type="text" class="form-control bg-light fw-bold fs-6 text-dark" id="delivTotalAmount" readonly>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-muted mb-0">
                                Cash Collected on Delivery
                            </label>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-success py-0 px-2 fw-semibold" id="btnQuickFullCash" style="font-size:0.74rem;">Full Cash</button>
                                <button type="button" class="btn btn-outline-secondary py-0 px-2 fw-semibold" id="btnQuickZeroCredit" style="font-size:0.74rem;">Credit (0)</button>
                            </div>
                        </div>
                        <input type="number" step="any" min="0" class="form-control fw-bold fs-6 text-dark" name="paid_amount" id="delivPaidAmount" value="0">
                        <small class="text-muted" id="delivPaymentHelpText" style="font-size: 0.74rem;">Leave as 0 if the full order is supplied on credit.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 py-2">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success fw-bold rounded-pill px-3 py-1.5" id="btnConfirmDeliver">
                        <i class="fas fa-check-double me-1"></i> Approve Delivery &amp; Cut Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {

    // 1. View Booking Details via AJAX
    $('.btn-view-booking').on('click', function() {
        const id = $(this).data('id');
        const modal = $('#modalBookingDetails');

        $.ajax({
            url: "{{ url('/distributor-portal/shop-bookings') }}/" + id,
            type: "GET",
            dataType: "json",
            beforeSend: function() {
                $('#dtlItemsBody').html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin fs-4"></i> Loading items...</td></tr>');
                modal.modal('show');
            },
            success: function(res) {
                if (res.success) {
                    const b = res.booking;
                    $('#dtlBookingNo').text(b.booking_no);
                    $('#dtlShopName').text(b.customer ? b.customer.shop_name : 'N/A');
                    $('#dtlBookerName').text(b.booker ? b.booker.name : 'N/A');
                    const cleanDate = res.formatted_date || (b.booking_date ? (b.booking_date.includes('T') ? b.booking_date.split('T')[0] : b.booking_date) : '-');
                    $('#dtlDate').text(cleanDate);
                    $('#dtlTotal').text('Rs ' + parseFloat(b.grand_total).toLocaleString('en-US', {minimumFractionDigits: 2}));

                    const orderType = (b.payment_type === 'credit') ? '<i class="fas fa-file-invoice-dollar me-1"></i> Order Credit' : '<i class="fas fa-money-bill-wave me-1"></i> Order Cash';
                    const orderTypeClass = (b.payment_type === 'credit') ? 'badge bg-primary text-white px-2 py-1' : 'badge bg-success text-white px-2 py-1';
                    $('#dtlOrderType').attr('class', orderTypeClass).html(orderType);

                    if (b.notes) {
                        $('#dtlNotes').text(b.notes);
                        $('#dtlNotesContainer').show();
                    } else {
                        $('#dtlNotesContainer').hide();
                    }

                    if (b.status === 'booked') {
                        $('#dtlEditBtn').attr('href', "{{ url('/distributor-portal/shop-bookings') }}/" + b.id + "/edit").show();
                    } else {
                        $('#dtlEditBtn').hide();
                    }

                    let rows = '';
                    b.items.forEach(it => {
                        const pName = it.product ? it.product.item_name : 'Item #' + it.product_id;
                        const vName = it.variant ? ` (${it.variant.size_label || it.variant.variant_name})` : '';
                        const check = res.stock_check[it.id] || {available: 0, is_ok: false};

                        const stockBadge = check.is_ok
                            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">${check.available} OK</span>`
                            : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Short: ${check.available}</span>`;

                        const toVal = parseFloat(it.trade_offer || 0);
                        const tradeBadge = toVal > 0 
                            ? `<span class="badge bg-success-subtle text-success border border-success-subtle font-monospace fw-bold">+${toVal.toFixed(1)}</span>` 
                            : `<span class="text-muted">-</span>`;

                        rows += `
                            <tr>
                                <td><strong>${pName}</strong>${vName}</td>
                                <td class="text-center fw-bold">${parseFloat(it.qty).toFixed(1)}</td>
                                <td class="text-center">${tradeBadge}</td>
                                <td class="text-center">${stockBadge}</td>
                                <td class="text-end">Rs ${parseFloat(it.unit_price).toFixed(2)}</td>
                                <td class="text-end fw-bold">Rs ${parseFloat(it.subtotal).toFixed(2)}</td>
                            </tr>
                        `;
                    });

                    $('#dtlItemsBody').html(rows);
                }
            },
            error: function() {
                $('#dtlItemsBody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load booking details.</td></tr>');
            }
        });
    });

    // 2. Deliver Modal Trigger
    $('.btn-deliver-modal').on('click', function() {
        const id = $(this).data('id');
        const no = $(this).data('no');
        const shop = $(this).data('shop');
        const total = parseFloat($(this).data('total') || 0);
        const orderType = $(this).data('type') || 'cash';

        $('#delivShopInfo').val(no + ' - ' + shop);
        $('#delivTotalAmount').val('Rs ' + total.toLocaleString('en-US', {minimumFractionDigits: 2}));

        if (orderType === 'credit') {
            $('#delivOrderTypeBadge').attr('class', 'badge bg-primary text-white px-2.5 py-1 fw-bold font-monospace').html('<i class="fas fa-file-invoice-dollar me-1"></i> ORDER CREDIT');
            $('#delivPaidAmount').val(0);
            $('#delivPaymentHelpText').text('This booking was booked as Credit. Cash received is set to 0. Change if partial cash was taken.');
        } else {
            $('#delivOrderTypeBadge').attr('class', 'badge bg-success text-white px-2.5 py-1 fw-bold font-monospace').html('<i class="fas fa-money-bill-wave me-1"></i> ORDER CASH');
            $('#delivPaidAmount').val(total);
            $('#delivPaymentHelpText').text('This booking was booked as Cash. Full cash amount has been pre-filled.');
        }

        $('#btnQuickFullCash').off('click').on('click', function() {
            $('#delivPaidAmount').val(total);
        });
        $('#btnQuickZeroCredit').off('click').on('click', function() {
            $('#delivPaidAmount').val(0);
        });

        const formAction = "{{ url('/distributor-portal/shop-bookings') }}/" + id + "/deliver";
        $('#formDeliverBooking').attr('action', formAction);

        $('#modalDeliverBooking').modal('show');
    });

    $('#formDeliverBooking').on('submit', function() {
        $('#btnConfirmDeliver').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing Delivery...');
    });

});
</script>
@endsection
