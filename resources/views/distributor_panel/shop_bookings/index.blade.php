@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fas fa-boxes-packing text-teal me-2" style="color: #0f766e;"></i>
                Shop Bookings (Market Orders)
            </h4>
            <span class="text-muted small">Review market demand orders booked by order bookers and convert them to confirmed sales on dispatch</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('distributor.booker.create') }}" class="btn btn-outline-success fw-bold rounded-pill px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> New Booking
            </a>
            <a href="{{ route('distributor.bookers.index') }}" class="btn btn-dark fw-bold rounded-pill px-3 shadow-sm">
                <i class="fas fa-users-gear me-1"></i> Manage Bookers
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

    {{-- KPI Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 4px solid #f59e0b !important;">
                <span class="text-secondary small fw-bold text-uppercase">Pending Bookings</span>
                <h3 class="fw-bolder mb-0 text-dark">{{ $pendingBookingsCount }}</h3>
                <small class="text-muted">Orders awaiting dispatch</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-left: 4px solid #ef4444 !important;">
                <span class="text-secondary small fw-bold text-uppercase">Pending Value</span>
                <h3 class="fw-bolder mb-0 text-danger">Rs {{ number_format($pendingBookingsAmount, 0) }}</h3>
                <small class="text-muted">Total demand value</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 4px solid #10b981 !important;">
                <span class="text-secondary small fw-bold text-uppercase">Delivered Today</span>
                <h3 class="fw-bolder mb-0 text-success">{{ $deliveredTodayCount }}</h3>
                <small class="text-muted">Converted to sale today</small>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border-left: 4px solid #0f766e !important;">
                <span class="text-secondary small fw-bold text-uppercase">Delivered Value Today</span>
                <h3 class="fw-bolder mb-0" style="color: #0f766e;">Rs {{ number_format($deliveredTodayAmount, 0) }}</h3>
                <small class="text-muted">Billed &amp; dispatched</small>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
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

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Order Booker</label>
                    <select name="booker_id" class="form-select form-select-sm">
                        <option value="">All Bookers</option>
                        @foreach($bookers as $bkr)
                            <option value="{{ $bkr->id }}" {{ request('booker_id') == $bkr->id ? 'selected' : '' }}>{{ $bkr->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
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

                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('distributor.shop_bookings.index') }}" class="btn btn-sm btn-light border w-50">Reset</a>
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
                        <th>Order Booker</th>
                        <th>Retail Shop</th>
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
                                        <button type="button" class="btn btn-success btn-deliver-modal" data-id="{{ $b->id }}" data-no="{{ $b->booking_no }}" data-total="{{ $b->grand_total }}" data-shop="{{ $b->customer->shop_name ?? 'Shop' }}" title="Convert to Sale & Deliver">
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
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h5>No bookings found</h5>
                                <p class="small text-secondary">No order bookings match the selected filter criteria.</p>
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

{{-- â•â•â•â•â•â•â•â•â•â•â•â• MODAL: VIEW BOOKING ITEMS DETAILS â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="modal fade" id="modalBookingDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-clipboard-list text-teal me-2" style="color:#0f766e;"></i>
                    Booking Details: <span id="dtlBookingNo" class="font-monospace text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-2 mb-3 bg-light p-2.5 rounded-3">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Shop Name:</small>
                        <strong id="dtlShopName" class="text-dark">-</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Order Booker:</small>
                        <strong id="dtlBookerName" class="text-dark">-</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Booking Date:</small>
                        <strong id="dtlDate" class="text-dark">-</strong>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Total Value:</small>
                        <strong id="dtlTotal" class="text-success fs-6">-</strong>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product / Item</th>
                                <th class="text-center">Booked Qty</th>
                                <th class="text-center">Van Stock Status</th>
                                <th class="text-end">Unit Price</th>
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
            <div class="modal-footer border-0 py-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â• MODAL: CONFIRM DELIVERY & CONVERT TO SALE â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="modal fade" id="modalDeliverBooking" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="formDeliverBooking" method="POST" action="">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-success">
                        <i class="fas fa-truck-fast me-2"></i> Confirm Delivery &amp; Deduct Stock
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-warning border-0 small rounded-3 mb-3">
                        <i class="fas fa-info-circle me-1"></i> 
                        Upon confirming delivery:
                        <ul class="mb-0 ps-3 mt-1">
                            <li>Stock will be automatically deducted from your custody inventory.</li>
                            <li>A confirmed Sale Invoice will be posted to the customer's ledger.</li>
                            <li>Booking status will update to <strong>Delivered</strong>.</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Booking # &amp; Shop</label>
                        <input type="text" class="form-control bg-light fw-bold" id="delivShopInfo" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Total Bill Amount</label>
                        <input type="text" class="form-control bg-light fw-bold fs-5 text-dark" id="delivTotalAmount" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">
                            Cash Collected on Delivery (If Any)
                        </label>
                        <input type="number" step="any" min="0" class="form-control fw-bold" name="paid_amount" id="delivPaidAmount" value="0">
                        <small class="text-muted">Leave as 0 if the full order is supplied on credit.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 py-2">
                    <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-4" id="btnConfirmDeliver">
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
                $('#dtlItemsBody').html('<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin fs-4"></i> Loading items...</td></tr>');
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

                    if (b.notes) {
                        $('#dtlNotes').text(b.notes);
                        $('#dtlNotesContainer').show();
                    } else {
                        $('#dtlNotesContainer').hide();
                    }

                    let rows = '';
                    b.items.forEach(it => {
                        const pName = it.product ? it.product.item_name : 'Item #' + it.product_id;
                        const vName = it.variant ? ` (${it.variant.size_label || it.variant.variant_name})` : '';
                        const check = res.stock_check[it.id] || {available: 0, is_ok: false};

                        const stockBadge = check.is_ok
                            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">${check.available} Available</span>`
                            : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Shortage: ${check.available} in Van</span>`;

                        rows += `
                            <tr>
                                <td><strong>${pName}</strong>${vName}</td>
                                <td class="text-center fw-bold">${parseFloat(it.qty).toFixed(1)}</td>
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
                $('#dtlItemsBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load booking details.</td></tr>');
            }
        });
    });

    // 2. Deliver Modal Trigger
    $('.btn-deliver-modal').on('click', function() {
        const id = $(this).data('id');
        const no = $(this).data('no');
        const shop = $(this).data('shop');
        const total = parseFloat($(this).data('total') || 0);

        $('#delivShopInfo').val(no + ' - ' + shop);
        $('#delivTotalAmount').val('Rs ' + total.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#delivPaidAmount').val(0);

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
