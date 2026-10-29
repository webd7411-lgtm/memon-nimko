@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fas fa-users-gear text-teal me-2" style="color: #0f766e;"></i>
                My Order Bookers
            </h4>
            <span class="text-muted small">Manage and monitor field order booking representatives for your distribution network</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success fw-bold rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegisterBooker">
                <i class="fas fa-user-plus me-1"></i> Register New Booker
            </button>
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

    {{-- Top Stats Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border-left: 4px solid #0f766e !important;">
                <span class="text-secondary small fw-bold text-uppercase">Total Active Bookers</span>
                <h3 class="fw-bolder mb-0" style="color: #0f766e;">{{ $bookers->total() }}</h3>
                <small class="text-muted">Field order booking staff</small>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 4px solid #f59e0b !important;">
                <span class="text-secondary small fw-bold text-uppercase">Bookings Taken Today</span>
                <h3 class="fw-bolder mb-0 text-dark">{{ $todayBookingsCount }}</h3>
                <small class="text-muted">Market orders recorded today</small>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 4px solid #10b981 !important;">
                <span class="text-secondary small fw-bold text-uppercase">Today's Booked Amount</span>
                <h3 class="fw-bolder mb-0 text-success">Rs {{ number_format($todayBookingsTotal, 0) }}</h3>
                <small class="text-muted">Total demand value today</small>
            </div>
        </div>
    </div>

    {{-- Search Form --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('distributor.bookers.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-8">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search booker by name or email..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                    <a href="{{ route('distributor.bookers.index') }}" class="btn btn-sm btn-light border w-50">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Bookers List Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark" style="background-color: #0f172a;">
                    <tr>
                        <th class="ps-3">Booker Name</th>
                        <th>Login Email</th>
                        <th>Role / Type</th>
                        <th>Created Date</th>
                        <th class="text-center">Total Bookings</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookers as $bkr)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm rounded-circle bg-teal text-white fw-bold d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background-color: #0f766e;">
                                        {{ strtoupper(substr($bkr->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong class="text-dark d-block">{{ $bkr->name }}</strong>
                                        <small class="text-muted">ID: #{{ $bkr->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-envelope text-secondary me-1"></i> {{ $bkr->email }}
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    Order Booker (Bookings Only)
                                </span>
                            </td>
                            <td>{{ $bkr->created_at ? $bkr->created_at->format('d M, Y') : '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2.5 py-1">
                                    {{ $bkr->shop_bookings_count ?? 0 }} Bookings
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <form action="{{ route('distributor.bookers.destroy', $bkr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove or deactivate this Order Booker?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" title="Remove Booker">
                                        <i class="fas fa-trash-alt me-1"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-user-xmark fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                <h5>No Order Bookers Registered</h5>
                                <p class="small text-secondary mb-3">Register order bookers to enable them to book retail market orders.</p>
                                <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalRegisterBooker">
                                    <i class="fas fa-user-plus me-1"></i> Register First Booker
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookers->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $bookers->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ════════════ MODAL: REGISTER NEW ORDER BOOKER ════════════ --}}
<div class="modal fade" id="modalRegisterBooker" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('distributor.bookers.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-user-plus text-teal me-2" style="color: #0f766e;"></i>
                        Register New Order Booker
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-info border-0 small rounded-3 mb-3">
                        <i class="fas fa-shield-alt me-1"></i>
                        This user will have exclusive access to the Order Booking screen to book orders for retail shops.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Booker Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Muhammad Ali" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Login Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="ali.booker@example.com" required>
                        <small class="text-muted">This email will be used by the booker to log in.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Login Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" minlength="6" placeholder="At least 6 characters" required>
                    </div>
                </div>
                <div class="modal-footer border-0 py-2">
                    <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-4">
                        <i class="fas fa-check-circle me-1"></i> Register Booker
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
