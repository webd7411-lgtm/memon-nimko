@extends('admin_panel.layout.app')

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0 rounded-lg mt-4">
                        <div class="card-header bg-primary text-white text-center py-3">
                            <h3 class="mb-0 fw-bold text-white"><i class="fas fa-user-plus me-2"></i> Add New Customer</h3>
                        </div>
                        <div class="card-body p-5">
                            <form action="{{ route('customers.store') }}" method="POST">
                                @csrf

                                @if ($errors->any())
                                <div class="alert alert-danger shadow-sm rounded">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <!-- Customer ID & Type Row -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-secondary">Customer ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-id-badge text-primary"></i></span>
                                            <input type="text" class="form-control fw-bold text-dark" name="customer_id" readonly
                                                value="{{ $latestId }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-secondary">Customer Type</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-users text-primary"></i></span>
                                            <select class="form-select" name="customer_type">
                                                <option value="Main Customer">Main Customer</option>
                                                <option value="Walking Customer">Walking Customer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-secondary">Customer Category</label>
                                        <div class="d-flex align-items-center mt-2 border rounded p-2 bg-light">
                                            <div class="form-check form-check-inline me-3">
                                                <input class="form-check-input" type="radio" name="customer_category" id="catWholesaler" value="Wholesaler" checked>
                                                <label class="form-check-label fw-semibold" for="catWholesaler">Wholesaler</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="customer_category" id="catRetailer" value="Retailer">
                                                <label class="form-check-label fw-semibold" for="catRetailer">Retailer</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Name & Phone Row -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-secondary">Customer Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-user text-primary"></i></span>
                                            <input type="text" class="form-control" name="customer_name" placeholder="Enter full name" value="{{ old('customer_name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-secondary">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-phone text-primary"></i></span>
                                            <input type="text" class="form-control" name="mobile" placeholder="03XXXXXXXXX" value="{{ old('mobile') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Address Row -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-secondary">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                        <textarea rows="3" class="form-control" name="address" placeholder="Enter complete address">{{ old('address') }}</textarea>
                                    </div>
                                </div>

                                <!-- Branch & Opening Balance Row -->
                                <div class="row mb-5">
                                    @php
                                        $user = auth()->user();
                                        $canSelectBranch = ($user && ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin') || $user->hasRole('Admin')));
                                    @endphp
                                    <div class="{{ $canSelectBranch ? 'col-md-6' : 'col-md-12' }}">
                                        <label class="form-label fw-bold text-secondary">Opening Balance (Rs)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-wallet text-success"></i></span>
                                            <input type="number" step="0.01" class="form-control" name="opening_balance" placeholder="0.00" value="{{ old('opening_balance') }}">
                                        </div>
                                        <div class="form-text text-muted">Positive value = Receivable (Debit)</div>
                                    </div>
                                    @if($canSelectBranch)
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-secondary">Branch <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-code-branch text-primary"></i></span>
                                            <select class="form-select" name="branch_id" required>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ (old('branch_id', $activeBranchId) == $branch->id) ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @else
                                    <input type="hidden" name="branch_id" value="{{ $activeBranchId }}">
                                    @endif
                                </div>

                                {{-- Credit Allowed Toggle --}}
                                <div class="mb-5">
                                    <div class="d-flex align-items-center gap-3 border rounded p-3 bg-light">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                   name="credit_allowed" id="creditAllowedToggle" value="1"
                                                   {{ old('credit_allowed') ? 'checked' : '' }}
                                                   style="width:2.5em;height:1.3em;">
                                        </div>
                                        <div>
                                            <label for="creditAllowedToggle" class="fw-bold text-dark mb-0" style="cursor:pointer;">
                                                <i class="fas fa-credit-card text-success me-1"></i> Credit (Udhar) Allowed
                                            </label>
                                            <div class="text-muted" style="font-size:12px;">Agar on ho to is customer ko POS me partial payment par sale karna allowed hoga</div>
                                        </div>
                                        <span id="creditBadge" class="badge ms-auto bg-secondary" style="font-size:13px;">❌ OFF</span>
                                    </div>
                                </div>
                                <script>
                                document.getElementById('creditAllowedToggle').addEventListener('change', function() {
                                    const badge = document.getElementById('creditBadge');
                                    if (this.checked) {
                                        badge.textContent = '✅ ON'; badge.className = 'badge ms-auto bg-success';
                                    } else {
                                        badge.textContent = '❌ OFF'; badge.className = 'badge ms-auto bg-secondary';
                                    }
                                });
                                </script>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('customers.index') }}" class="btn btn-light btn-lg px-4 fw-bold text-secondary">Cancel</a>
                                    <button class="btn btn-primary btn-lg px-5 fw-bold shadow-sm" type="submit">
                                        <i class="fas fa-save me-2"></i> Save Customer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
