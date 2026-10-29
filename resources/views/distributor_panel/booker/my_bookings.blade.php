@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fas fa-clipboard-list text-teal me-2" style="color: #0f766e;"></i>
                My Shop Bookings
            </h4>
            <span class="text-muted small">Track and review all shop market orders booked by you</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('distributor.booker.create') }}" class="btn btn-success fw-bold rounded-pill px-3 shadow-sm">
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

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('distributor.booker.my_bookings') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Status Filter</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>Pending / Booked</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered / Sold</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">Date</label>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('distributor.booker.my_bookings') }}" class="btn btn-sm btn-light border w-50">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Bookings Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark" style="background-color: #0f172a;">
                    <tr>
                        <th class="ps-3">Booking #</th>
                        <th>Date</th>
                        <th>Shop Name</th>
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
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

        @if($bookings->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
