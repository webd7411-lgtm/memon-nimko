@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    .ast-form { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; padding-bottom: 3rem; }
    .ast-form * { font-family: inherit; }

    .form-section-title {
        font-size: .85rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
        color: #0369a1; margin-bottom: 1.1rem; padding-bottom: .4rem; border-bottom: 2px solid #e0f2fe;
        display: flex; align-items: center; gap: .5rem;
    }
    .form-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem 1.8rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 1.5rem; }
    .form-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #475569; margin-bottom: 4px; }
    .form-control, .form-select { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: .55rem .85rem; font-size: .85rem; font-weight: 600; color: #0f172a; }
    .form-control:focus, .form-select:focus { border-color: #0284c7; box-shadow: 0 0 0 3px rgba(2, 132, 199, .15); }
    .req-star { color: #ef4444; }
</style>

<div class="ast-form">
    <div class="container-fluid px-3 px-md-4 pt-3">

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Assets List
                </a>
                <h2 class="fw-bold text-dark mb-0"><i class="fas fa-plus-circle text-primary me-2"></i> Register New Asset</h2>
                <p class="text-muted small mb-0">Record factory machinery, store furniture, electronics, or equipment.</p>
            </div>
            <div>
                <button type="submit" form="assetCreateForm" class="btn btn-primary px-4 fw-bold">
                    <i class="fas fa-save me-1"></i> Save Asset
                </button>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-4">
                <div class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Please fix the following errors:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="assetCreateForm" action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Section 1: Basic & Location Info -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-info-circle"></i> 1. Asset Identification & Location
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Category <span class="req-star">*</span></label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" data-rate="{{ $cat->depreciation_rate }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Asset Code / Tag</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-barcode"></i></span>
                            <input type="text" name="asset_code" id="asset_code" class="form-control font-monospace" placeholder="AST-0001" value="{{ old('asset_code', $suggestedCode) }}">
                        </div>
                        <small class="text-muted" style="font-size: .7rem;">Auto-generated or custom barcode tag</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Asset Name <span class="req-star">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Automatic Nimko Fryer 50KG, Executive Cash Counter" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Branch <span class="req-star">*</span></label>
                        <select name="branch_id" class="form-select">
                            <option value="">-- Main Head Office / General --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id', $activeBranch) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Warehouse (Optional)</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">-- None / Direct Floor --</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}" {{ old('warehouse_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Specific Location / Floor</label>
                        <input type="text" name="location_details" class="form-control" placeholder="e.g. Production Line 2, Shop Counter 1" value="{{ old('location_details') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Custodian / Responsible Person</label>
                        <input type="text" name="custodian_name" class="form-control" placeholder="e.g. Chef / Manager Name" value="{{ old('custodian_name') }}">
                    </div>
                </div>
            </div>

            <!-- Section 2: Financial & Purchase Details -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-file-invoice-dollar"></i> 2. Purchase Cost & Valuation
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Purchase Cost (PKR) <span class="req-star">*</span></label>
                        <input type="number" step="0.01" name="purchase_cost" id="purchase_cost" class="form-control" placeholder="0.00" value="{{ old('purchase_cost') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Depreciation Rate (% Annual)</label>
                        <input type="number" step="0.01" name="depreciation_rate" id="depreciation_rate" class="form-control" placeholder="10.00" value="{{ old('depreciation_rate', 10.00) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Useful Life (Years)</label>
                        <input type="number" name="useful_life_years" class="form-control" placeholder="5" value="{{ old('useful_life_years', 5) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Salvage / Scrap Value (PKR)</label>
                        <input type="number" step="0.01" name="salvage_value" class="form-control" placeholder="0.00" value="{{ old('salvage_value', 0) }}">
                        <small class="text-muted" style="font-size: .7rem;">Estimated scrap value at end of life</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Vendor / Supplier</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">-- Direct Purchase / Local Market --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} ({{ $v->phone ?? 'No Phone' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Technical Specifications & Status -->
            <div class="form-card">
                <div class="form-section-title">
                    <i class="fas fa-cogs"></i> 3. Specifications & Condition
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Model No / Make</label>
                        <input type="text" name="model_no" class="form-control" placeholder="e.g. NF-500X" value="{{ old('model_no') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Serial Number</label>
                        <input type="text" name="serial_no" class="form-control font-monospace" placeholder="e.g. SN-998822" value="{{ old('serial_no') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Warranty Expiry Date</label>
                        <input type="date" name="warranty_expiry" class="form-control" value="{{ old('warranty_expiry') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Condition <span class="req-star">*</span></label>
                        <select name="condition" class="form-select" required>
                            <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>Brand New</option>
                            <option value="good" {{ old('condition', 'good') === 'good' ? 'selected' : '' }}>Good / Working</option>
                            <option value="fair" {{ old('condition') === 'fair' ? 'selected' : '' }}>Fair / Used</option>
                            <option value="needs_repair" {{ old('condition') === 'needs_repair' ? 'selected' : '' }}>Needs Repair</option>
                            <option value="damaged" {{ old('condition') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Operational Status <span class="req-star">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (In Use)</option>
                            <option value="under_maintenance" {{ old('status') === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="idle" {{ old('status') === 'idle' ? 'selected' : '' }}>Idle (Stored)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Asset Image / Photo</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted" style="font-size: .7rem;">Upload JPG/PNG photo of machinery/furniture</small>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Notes & Remarks</label>
                        <textarea name="notes" rows="2" class="form-control" placeholder="Maintenance notes, supplier guarantees, or special instructions...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('assets.index') }}" class="btn btn-secondary px-4 fw-bold">Cancel</a>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                    <i class="fas fa-check-circle me-1"></i> Register Asset
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const catSelect = document.getElementById('category_id');
        const codeInput = document.getElementById('asset_code');
        const deprInput = document.getElementById('depreciation_rate');

        catSelect.addEventListener('change', function() {
            const catId = this.value;
            const selectedOpt = this.options[this.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.rate) {
                deprInput.value = selectedOpt.dataset.rate;
            }

            if (catId) {
                fetch(`{{ route('assets.get_next_code') }}?category_id=${catId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.code) {
                            codeInput.value = data.code;
                        }
                    })
                    .catch(err => console.error(err));
            }
        });
    });
</script>
@endsection
