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
    .dist-mobile-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #eef2f6 !important;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .dist-mobile-card:active {
        transform: scale(0.99);
    }
    .dist-mobile-stat-box {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 10px;
        padding: 8px 10px;
        height: 100%;
    }
</style>

<div class="container-fluid dist-module-container">
    <!-- Horizontal Navigation Tabs -->
    @include('admin_panel.distributors.partials.navigation_tabs')

    <!-- Top Subtitle Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 mt-1">
        <div>
            <h5 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                <i class="fas fa-users-cog text-teal" style="color: #0f766e;"></i>
                {{ request('type') === 'booker' ? 'Order Bookers Directory' : 'Distributors & Order Bookers' }}
                <span class="badge rounded-pill text-white" style="background: #0f766e; font-size: 0.68rem; padding: 3px 8px;">B2B</span>
            </h5>
            <span class="text-muted" style="font-size: 0.78rem;">Manage authorized distributors, stock custody, credit balances and sales</span>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #0f766e !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Active Distributors</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.4rem;">{{ $activeDistributorsCount }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(15, 118, 110, 0.1); color: #0f766e; font-size: 1.15rem;">
                    <i class="fas fa-users-cog"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #3b82f6 !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pending Demands</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.4rem;">{{ $pendingOrdersCount }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; font-size: 1.15rem;">
                    <i class="fas fa-inbox"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="dist-kpi-card" style="border-left: 4px solid #f59e0b !important;">
                <div>
                    <span class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Receivable</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.4rem;">Rs. {{ number_format($totalReceivable, 2) }}</h3>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 ms-2"
                     style="width: 42px; height: 42px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 1.15rem;">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-check-circle"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- ============================================== -->
    <!-- DESKTOP TABLE VIEW (Visible on Tablets & PCs)  -->
    <!-- ============================================== -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white d-none d-md-block">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-id-card me-2 text-teal" style="color: #0f766e;"></i> Registered Distributors Directory
            </h5>
            <form method="GET" action="{{ route('admin.distributors.index') }}" class="d-flex align-items-center gap-2">
                @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <div class="input-group input-group-sm" style="max-width: 280px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-start-pill" placeholder="Search name, phone, code...">
                    <button class="btn btn-outline-secondary rounded-end-pill" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #475569;">
                    <tr>
                        <th class="ps-4">Code & Name</th>
                        <th>Contact / Area</th>
                        <th>Login Email</th>
                        <th class="text-center">Active Retailers</th>
                        <th class="text-end">Balance (Receivable)</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.88rem;">
                    @forelse($distributors as $dist)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                     style="width: 38px; height: 38px; background: #6366f1; font-size: 0.85rem;">
                                    {{ strtoupper(substr($dist->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $dist->name }}</div>
                                    <span class="badge bg-light text-secondary border px-2 py-0.5" style="font-size: 0.72rem;">
                                        {{ $dist->code }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-dark fw-semibold"><i class="fas fa-phone-alt me-1 text-muted" style="font-size: 0.75rem;"></i> {{ $dist->phone }}</div>
                            <div class="text-muted small"><i class="fas fa-map-marker-alt me-1 text-muted" style="font-size: 0.75rem;"></i> {{ $dist->city ?? 'Karachi' }} {{ $dist->area ? "({$dist->area})" : '' }}</div>
                        </td>
                        <td>
                            <span class="text-muted small"><i class="fas fa-envelope me-1"></i> {{ $dist->user->email ?? 'N/A' }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.distributors.customers', ['distributor_id' => $dist->id]) }}" 
                               class="badge rounded-pill bg-light text-primary border text-decoration-none px-2.5 py-1" 
                               title="Click to view all shops for this distributor">
                                <i class="fas fa-store me-1 text-primary"></i> {{ $dist->customers_count }} shops
                            </a>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold {{ $dist->current_balance > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 0.95rem;">
                                Rs. {{ number_format($dist->current_balance, 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill {{ $dist->status === 'active' ? 'bg-success-subtle text-success border border-success' : 'bg-secondary text-white' }} px-2.5 py-1">
                                {{ ucfirst($dist->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.distributors.ledger', $dist->id) }}" class="btn btn-outline-secondary rounded-pill px-2.5" title="View Statement / Ledger">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Ledger
                                </a>
                                <a href="{{ route('admin.distributors.customers', ['distributor_id' => $dist->id]) }}" class="btn btn-outline-secondary rounded-pill px-2.5 ms-1" title="View Customers / Retailers">
                                    <i class="fas fa-store me-1"></i> Shops
                                </a>
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-2.5 ms-1"
                                        data-bs-toggle="modal" data-bs-target="#editDistributorModal{{ $dist->id }}" title="Edit Profile">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-truck-loading fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-1 fw-semibold">No Distributors Registered Yet</p>
                            <span class="small">Click "Add Distributor" above to create your first order booker / distributor account.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($distributors->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
            {{ $distributors->links() }}
        </div>
        @endif
    </div>

    <!-- ============================================== -->
    <!-- MOBILE CARD VIEW (Refined & Spacious)          -->
    <!-- ============================================== -->
    <div class="d-block d-md-none mb-4">
        <!-- Search bar for mobile with proper padding -->
        <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white p-2" style="border: 1px solid #edf2f7 !important;">
            <form method="GET" action="{{ route('admin.distributors.index') }}">
                @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-0 pe-1 text-muted">
                        <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 bg-transparent ps-2" placeholder="Search name, phone, code..." style="font-size: 0.82rem;">
                    @if(request('search'))
                    <a href="{{ route('admin.distributors.index', request('type') ? ['type' => request('type')] : []) }}" class="btn btn-sm btn-link text-muted p-1" title="Clear">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                    <button class="btn btn-sm text-white rounded-pill px-3 fw-semibold shadow-sm" type="submit" style="background: #0f766e; font-size: 0.74rem;">
                        Search
                    </button>
                </div>
            </form>
        </div>

        @forelse($distributors as $dist)
        <div class="dist-mobile-card">
            <!-- Top Row: Avatar, Name & Code, Status, 3-dots Menu -->
            <div class="d-flex align-items-start justify-content-between gap-2 mb-2.5">
                <div class="d-flex align-items-center gap-2.5">
                    <!-- Circular Initials Avatar -->
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                         style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #4f46e5); font-size: 0.88rem;">
                        {{ strtoupper(substr($dist->name, 0, 2)) }}
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0.5 lh-sm" style="font-size: 0.92rem;">
                            {{ $dist->name }}
                        </h6>
                        <span class="text-muted font-monospace" style="font-size: 0.74rem;">
                            {{ $dist->code }}
                        </span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-1">
                    <!-- Status Badge -->
                    @if($dist->status === 'active')
                    <span class="badge rounded-pill px-2.5 py-1 text-success fw-semibold" style="background: #e8f8f0; border: 1px solid #d1fae5; font-size: 0.7rem;">
                        <i class="fas fa-circle me-1" style="font-size: 5px;"></i> Active
                    </span>
                    @else
                    <span class="badge rounded-pill px-2.5 py-1 text-secondary fw-semibold bg-light border" style="font-size: 0.7rem;">
                        <i class="fas fa-circle me-1" style="font-size: 5px;"></i> Inactive
                    </span>
                    @endif

                    <!-- 3-Dots Dropdown Menu -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-muted p-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                            <i class="fas fa-ellipsis-v" style="font-size: 0.88rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 py-1" style="font-size: 0.82rem;">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('admin.distributors.ledger', $dist->id) }}">
                                    <i class="fas fa-file-invoice-dollar me-2 text-primary"></i> View Statement / Ledger
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#editDistributorModal{{ $dist->id }}">
                                    <i class="fas fa-edit me-2 text-secondary"></i> Edit Details
                                </a>
                            </li>
                            @if($dist->phone)
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2 text-success" href="tel:{{ $dist->phone }}">
                                    <i class="fas fa-phone-alt me-2"></i> Call {{ $dist->phone }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 text-success" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $dist->phone) }}" target="_blank">
                                    <i class="fab fa-whatsapp me-2"></i> WhatsApp
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contact Information Rows -->
            <div class="mb-3 ps-1" style="font-size: 0.8rem;">
                <!-- Phone -->
                <div class="d-flex align-items-center gap-2 mb-1.5 text-dark">
                    <i class="fas fa-phone-alt text-muted" style="width: 15px; font-size: 0.76rem;"></i>
                    <a href="tel:{{ $dist->phone }}" class="text-dark text-decoration-none fw-semibold">
                        {{ $dist->phone }}
                    </a>
                </div>

                <!-- Location / Area -->
                <div class="d-flex align-items-center gap-2 mb-1.5 text-secondary">
                    <i class="fas fa-map-marker-alt text-muted" style="width: 15px; font-size: 0.78rem;"></i>
                    <span>{{ $dist->city ?? 'Karachi' }} {{ $dist->area ? "({$dist->area})" : '' }}</span>
                </div>

                <!-- Email (Vibrant Blue) -->
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-envelope text-primary" style="width: 15px; font-size: 0.78rem;"></i>
                    <a href="mailto:{{ $dist->user->email ?? 'distributor@gmail.com' }}" class="text-primary text-decoration-none fw-medium text-truncate" style="max-width: 250px;">
                        {{ $dist->user->email ?? 'distributor@gmail.com' }}
                    </a>
                </div>
            </div>

            <!-- Two Bottom Stat KPI Boxes (Side by Side - Clean & Never Truncated) -->
            <div class="row g-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                <!-- Left: Active Retailers -->
                <div class="col-6">
                    <div class="dist-mobile-stat-box">
                        <div class="d-flex align-items-center gap-1.5 mb-1 text-muted" style="font-size: 0.72rem;">
                            <i class="fas fa-store" style="color: #64748b; font-size: 0.75rem;"></i>
                            <span class="fw-medium">Active Retailers</span>
                        </div>
                        <div class="fw-bold text-dark ps-0.5" style="font-size: 0.88rem;">
                            {{ $dist->customers_count ?? 0 }} shops
                        </div>
                    </div>
                </div>

                <!-- Right: Balance (Receivable) -->
                <div class="col-6">
                    <div class="dist-mobile-stat-box">
                        <div class="d-flex align-items-center gap-1.5 mb-1 text-muted" style="font-size: 0.72rem;">
                            <i class="fas fa-money-bill-wave text-success" style="font-size: 0.75rem;"></i>
                            <span class="fw-medium">Balance (Due)</span>
                        </div>
                        <div class="fw-bold text-success ps-0.5" style="font-size: 0.88rem; white-space: nowrap;">
                            Rs. {{ number_format($dist->current_balance ?? 0, 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted bg-white">
            <i class="fas fa-truck-loading fa-3x mb-2 text-secondary opacity-50"></i>
            <p class="mb-0 fw-semibold">No Distributors Registered Yet</p>
        </div>
        @endforelse

        @if($distributors->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $distributors->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ============================================== -->
<!-- EDIT MODALS (Shared across Desktop & Mobile)   -->
<!-- ============================================== -->
@foreach($distributors as $dist)
<div class="modal fade" id="editDistributorModal{{ $dist->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.distributors.update', $dist->id) }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #090d16, #0f766e);">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i> Edit Distributor: {{ $dist->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-semibold">Distributor / Booker Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ $dist->name }}" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Account Type</label>
                            <select name="type" class="form-select">
                                <option value="distributor" {{ ($dist->type ?? 'distributor') === 'distributor' ? 'selected' : '' }}>Distributor</option>
                                <option value="order_booker" {{ ($dist->type ?? '') === 'order_booker' ? 'selected' : '' }}>Order Booker</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Phone *</label>
                            <input type="text" name="phone" class="form-control" value="{{ $dist->phone }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">CNIC</label>
                            <input type="text" name="cnic" class="form-control" value="{{ $dist->cnic }}">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ $dist->city }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Area</label>
                            <input type="text" name="area" class="form-control" value="{{ $dist->area }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Address</label>
                        <textarea name="address" rows="2" class="form-control">{{ $dist->address }}</textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $dist->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $dist->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Change Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background: #0f766e;">Update Details</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
</div>

<!-- Add Distributor Modal -->
<div class="modal fade" id="addDistributorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('admin.distributors.store') }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #090d16 0%, #0f766e 100%);">
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0"><i class="fas fa-user-plus me-2"></i> Register New Distributor / Order Booker</h5>
                        <p class="mb-0 text-white-50 small">Creates both Distributor Profile & Dedicated Portal Login</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Account Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="distributor" {{ request('type') !== 'booker' ? 'selected' : '' }}>Distributor</option>
                                <option value="order_booker" {{ request('type') === 'booker' ? 'selected' : '' }}>Order Booker</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Distributor / Booker Full Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Tariq Traders" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contact Mobile *</label>
                            <input type="text" name="phone" class="form-control" placeholder="0300-1234567" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">CNIC</label>
                            <input type="text" name="cnic" class="form-control" placeholder="42201-XXXXXXX-X">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" placeholder="Karachi" value="Karachi">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Assigned Area / Territory</label>
                            <input type="text" name="area" class="form-control" placeholder="e.g. Gulshan / Malir">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Office / Godown Address</label>
                            <textarea name="address" rows="2" class="form-control" placeholder="Shop/Office complete address..."></textarea>
                        </div>

                        <div class="col-md-12"><hr class="my-1"></div>

                        <!-- Login Credentials -->
                        <div class="col-md-12">
                            <h6 class="fw-bold text-dark mb-1"><i class="fas fa-key me-2 text-teal" style="color: #0f766e;"></i> Portal Login Credentials</h6>
                            <p class="text-muted small mb-3">Distributor will use these credentials to log in to their dedicated order booking panel</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Login Email *</label>
                            <input type="email" name="email" class="form-control" placeholder="distributor@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Login Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>

                        <div class="col-md-12"><hr class="my-1"></div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Initial Opening Balance (Receivable / Debit)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="0.01" min="0" name="opening_balance" class="form-control" value="0.00">
                            </div>
                            <span class="text-muted small">Enter if this distributor already owes money to the company.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background: #0f766e; border: 1px solid #14b8a6;">
                        <i class="fas fa-check me-1"></i> Register Distributor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
