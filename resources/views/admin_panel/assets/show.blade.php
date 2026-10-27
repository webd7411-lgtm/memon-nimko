@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    .ast-profile { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; padding-bottom: 3rem; }
    .ast-profile * { font-family: inherit; }

    .prof-hdr {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 1.5rem 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03); margin-bottom: 1.5rem;
    }
    .prof-img { width: 100px; height: 100px; border-radius: 16px; object-fit: cover; border: 2px solid #e2e8f0; }
    .prof-img-placeholder { width: 100px; height: 100px; border-radius: 16px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #94a3b8; border: 2px dashed #cbd5e1; }

    .ast-stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1rem 1.25rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .ast-stat-card .lbl { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; margin-bottom: 3px; }
    .ast-stat-card .val { font-size: 1.3rem; font-weight: 900; color: #0f172a; }

    .nav-tabs .nav-link { font-weight: 700; font-size: .85rem; color: #64748b; border: none; border-bottom: 3px solid transparent; padding: .75rem 1.2rem; }
    .nav-tabs .nav-link.active { color: #0284c7; border-bottom-color: #0284c7; background: transparent; }

    .info-list .info-item { display: flex; justify-content: space-between; padding: .65rem 0; border-bottom: 1px solid #f1f5f9; font-size: .84rem; }
    .info-list .info-item .k { color: #64748b; font-weight: 600; }
    .info-list .info-item .v { color: #0f172a; font-weight: 700; text-align: right; }
</style>

<div class="ast-profile">
    <div class="container-fluid px-3 px-md-4 pt-3">

        <!-- Top Navigation Link -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Assets List
            </a>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('assets.barcode', $asset->id) }}" target="_blank" class="btn btn-sm text-white fw-bold shadow-sm" style="background: #1e293b; border-radius: 8px;">
                    <i class="fas fa-barcode me-1 text-warning"></i> Print Barcode & QR Tag
                </a>
                <button type="button" class="btn btn-sm text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#maintenanceModal" style="background: linear-gradient(135deg, #d97706, #b45309); border: none; border-radius: 8px;">
                    <i class="fas fa-wrench me-1 text-white"></i> Log Maintenance
                </button>
                <button type="button" class="btn btn-sm text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#transferModal" style="background: linear-gradient(135deg, #0d9488, #0f766e); border: none; border-radius: 8px;">
                    <i class="fas fa-exchange-alt me-1 text-white"></i> Transfer Branch
                </button>
                <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm text-white fw-bold shadow-sm" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none; border-radius: 8px;">
                    <i class="fas fa-edit me-1 text-white"></i> Edit Asset
                </a>
                @if($asset->status !== 'disposed' && $asset->status !== 'scrapped')
                    <button type="button" class="btn btn-sm text-danger fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#disposeModal" style="background: #fff; border: 1.5px solid #ef4444; border-radius: 8px;">
                        <i class="fas fa-ban me-1 text-danger"></i> Dispose / Scrap
                    </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-3" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Profile Header Card -->
        <div class="prof-hdr">
            <div class="d-flex flex-wrap align-items-center gap-4">
                @if($asset->image)
                    <img src="{{ asset($asset->image) }}" alt="{{ $asset->name }}" class="prof-img">
                @else
                    <div class="prof-img-placeholder">
                        @if($asset->category && str_contains(strtolower($asset->category->name), 'machin'))
                            <i class="fas fa-industry"></i>
                        @elseif($asset->category && str_contains(strtolower($asset->category->name), 'furnit'))
                            <i class="fas fa-chair"></i>
                        @else
                            <i class="fas fa-cubes"></i>
                        @endif
                    </div>
                @endif

                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span class="badge bg-primary font-monospace px-2.5 py-1.5" style="font-size: .8rem;">{{ $asset->asset_code }}</span>
                        <span class="badge bg-light text-dark border">{{ $asset->category->name ?? 'Uncategorized' }}</span>
                        @if($asset->status === 'active')
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                        @elseif($asset->status === 'under_maintenance')
                            <span class="badge bg-warning text-dark"><i class="fas fa-wrench me-1"></i> In Repair</span>
                        @elseif($asset->status === 'idle')
                            <span class="badge bg-secondary"><i class="fas fa-pause me-1"></i> Idle</span>
                        @else
                            <span class="badge bg-danger"><i class="fas fa-ban me-1"></i> {{ ucfirst($asset->status) }}</span>
                        @endif
                        <span class="badge bg-info text-white"><i class="fas fa-heartbeat me-1"></i> {{ ucfirst(str_replace('_', ' ', $asset->condition)) }}</span>
                    </div>

                    <h2 class="fw-bold text-dark mb-1">{{ $asset->name }}</h2>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        <span><i class="fas fa-building text-secondary me-1"></i> Branch: <strong>{{ $asset->branch->name ?? 'Head Office' }}</strong></span>
                        @if($asset->location_details)
                            <span><i class="fas fa-map-marker-alt text-secondary me-1"></i> Floor/Room: <strong>{{ $asset->location_details }}</strong></span>
                        @endif
                        @if($asset->custodian_name)
                            <span><i class="fas fa-user-shield text-secondary me-1"></i> Custodian: <strong>{{ $asset->custodian_name }}</strong></span>
                        @endif
                        @if($asset->model_no)
                            <span><i class="fas fa-tag text-secondary me-1"></i> Model: <strong>{{ $asset->model_no }}</strong></span>
                        @endif
                    </div>
                </div>

                @if($barcodePng)
                    <div class="text-center d-none d-lg-block bg-light p-2 rounded-3 border">
                        <img src="{{ $barcodePng }}" alt="Barcode" style="height: 38px;">
                        <div class="font-monospace fw-bold text-dark" style="font-size: .75rem;">{{ $asset->asset_code }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- 4 Valuation & Financial Cards -->
        @php
            $totalMaintenanceCost = $asset->maintenances->sum('cost');
            $depreciationAmount = max(0, $asset->purchase_cost - $asset->estimated_current_value);
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="ast-stat-card border-start border-4 border-primary">
                    <div class="lbl">Original Purchase Cost</div>
                    <div class="val text-primary">Rs. {{ number_format($asset->purchase_cost, 0) }}</div>
                    <small class="text-muted">Bought on: {{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : 'N/A' }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="ast-stat-card border-start border-4 border-success">
                    <div class="lbl">Estimated Book Value</div>
                    <div class="val text-success">Rs. {{ number_format($asset->estimated_current_value, 0) }}</div>
                    <small class="text-muted">Rate: {{ $asset->depreciation_rate }}% / year</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="ast-stat-card border-start border-4 border-secondary">
                    <div class="lbl">Acc. Depreciation</div>
                    <div class="val text-secondary">Rs. {{ number_format($depreciationAmount, 0) }}</div>
                    <small class="text-muted">Scrap Value: Rs. {{ number_format($asset->salvage_value, 0) }}</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="ast-stat-card border-start border-4 border-warning">
                    <div class="lbl">Total Maintenance Cost</div>
                    <div class="val text-warning">Rs. {{ number_format($totalMaintenanceCost, 0) }}</div>
                    <small class="text-muted">{{ $asset->maintenances->count() }} service records</small>
                </div>
            </div>
        </div>

        <!-- Tabs Container -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom pt-3 pb-0 px-4">
                <ul class="nav nav-tabs card-header-tabs" id="assetTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i> Specifications & Details
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="maintenance-tab" data-bs-toggle="tab" data-bs-target="#maintenance" type="button" role="tab">
                            <i class="fas fa-tools me-1"></i> Maintenance & Repairs ({{ $asset->maintenances->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="transfers-tab" data-bs-toggle="tab" data-bs-target="#transfers" type="button" role="tab">
                            <i class="fas fa-exchange-alt me-1"></i> Branch Transfers ({{ $asset->transfers->count() }})
                        </button>
                    </li>
                    @if($asset->status === 'disposed' || $asset->status === 'scrapped')
                    <li class="nav-item">
                        <button class="nav-link text-danger" id="disposal-tab" data-bs-toggle="tab" data-bs-target="#disposal" type="button" role="tab">
                            <i class="fas fa-ban me-1"></i> Disposal Record
                        </button>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="assetTabsContent">

                    <!-- Tab 1: Overview & Specs -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-sliders-h text-primary me-2"></i> Technical Specifications</h6>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="k">Serial Number:</span>
                                        <span class="v font-monospace">{{ $asset->serial_no ?: 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="k">Model / Make:</span>
                                        <span class="v">{{ $asset->model_no ?: 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="k">Warranty Expiry:</span>
                                        <span class="v">
                                            @if($asset->warranty_expiry)
                                                {{ $asset->warranty_expiry->format('d M Y') }}
                                                @if($asset->warranty_expiry->isPast())
                                                    <span class="badge bg-danger ms-1">Expired</span>
                                                @else
                                                    <span class="badge bg-success ms-1">Valid</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="k">Vendor / Supplier:</span>
                                        <span class="v">{{ $asset->vendor->name ?? 'Direct Purchase' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="k">Registered By:</span>
                                        <span class="v">{{ $asset->creator->name ?? 'Admin' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fas fa-coins text-success me-2"></i> Valuation & Depreciation Schedule</h6>
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="k">Depreciation Method:</span>
                                        <span class="v">Straight Line Method</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="k">Annual Depreciation Rate:</span>
                                        <span class="v">{{ $asset->depreciation_rate }}%</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="k">Estimated Useful Life:</span>
                                        <span class="v">{{ $asset->useful_life_years }} Years</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="k">Salvage / Scrap Value:</span>
                                        <span class="v">Rs. {{ number_format($asset->salvage_value, 0) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="k">Net Book Value Today:</span>
                                        <span class="v text-success fw-bold">Rs. {{ number_format($asset->estimated_current_value, 0) }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($asset->notes)
                            <div class="col-12 mt-3">
                                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-sticky-note text-warning me-2"></i> Notes & Remarks</h6>
                                <div class="p-3 bg-light rounded-3 text-secondary" style="font-size: .88rem; white-space: pre-line;">
                                    {{ $asset->notes }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tab 2: Maintenance History -->
                    <div class="tab-pane fade" id="maintenance" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Maintenance & Servicing Log</h6>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                                <i class="fas fa-plus me-1"></i> Add Service Log
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Service Type</th>
                                        <th>Cost</th>
                                        <th>Paid From</th>
                                        <th>Performed By</th>
                                        <th>Parts Replaced / Work Done</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($asset->maintenances as $m)
                                    <tr>
                                        <td class="fw-semibold">{{ $m->maintenance_date->format('d M Y') }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $m->service_type }}</span></td>
                                        <td class="fw-bold text-danger">Rs. {{ number_format($m->cost, 0) }}</td>
                                        <td>
                                            @if($m->account)
                                                <div class="small fw-semibold text-dark"><i class="fas fa-wallet text-success me-1"></i> {{ $m->account->title }}</div>
                                                @if($m->expenseVoucher)
                                                    <span class="badge bg-dark font-monospace" style="font-size: .65rem;">{{ $m->expenseVoucher->evid }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-light text-secondary border" style="font-size: .7rem;">Record Only</span>
                                            @endif
                                        </td>
                                        <td>{{ $m->performed_by ?: 'In-house' }}</td>
                                        <td>
                                            <div>{{ $m->description }}</div>
                                            @if($m->parts_replaced)
                                                <small class="text-muted"><strong>Parts:</strong> {{ $m->parts_replaced }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($m->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($m->status === 'in_progress')
                                                <span class="badge bg-warning text-dark">In Progress</span>
                                            @else
                                                <span class="badge bg-secondary">Scheduled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editMaintenanceModal{{ $m->id }}" title="Edit Maintenance">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('assets.maintenance.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Delete this maintenance record? If payment was deducted, it will be refunded.');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Edit Modal for Maintenance $m -->
                                            <div class="modal fade" id="editMaintenanceModal{{ $m->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('assets.maintenance.update', $m->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-content rounded-4 border-0 text-start">
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i> Edit Maintenance Record</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small text-muted">Service Date *</label>
                                                                    <input type="date" name="maintenance_date" class="form-control" value="{{ $m->maintenance_date->format('Y-m-d') }}" required>
                                                                </div>
                                                                <div class="row g-2 mb-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-bold small text-muted">Service Type *</label>
                                                                        <select name="service_type" class="form-select" required>
                                                                            <option value="Routine Service" {{ $m->service_type === 'Routine Service' ? 'selected' : '' }}>Routine Service</option>
                                                                            <option value="Emergency Repair" {{ $m->service_type === 'Emergency Repair' ? 'selected' : '' }}>Emergency Repair</option>
                                                                            <option value="Part Replacement" {{ $m->service_type === 'Part Replacement' ? 'selected' : '' }}>Part Replacement</option>
                                                                            <option value="Oiling & Greasing" {{ $m->service_type === 'Oiling & Greasing' ? 'selected' : '' }}>Oiling & Greasing</option>
                                                                            <option value="Inspection" {{ $m->service_type === 'Inspection' ? 'selected' : '' }}>Inspection</option>
                                                                            <option value="Complete Overhaul" {{ $m->service_type === 'Complete Overhaul' ? 'selected' : '' }}>Complete Overhaul</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-bold small text-muted">Repair Cost (PKR) *</label>
                                                                        <input type="number" step="0.01" name="cost" class="form-control" value="{{ $m->cost }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="row g-2 mb-3">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-bold small text-muted">Performed By</label>
                                                                        <input type="text" name="performed_by" class="form-control" value="{{ $m->performed_by }}" placeholder="e.g. Master Tech / External">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fw-bold small text-muted">Status *</label>
                                                                        <select name="status" class="form-select" required>
                                                                            <option value="completed" {{ $m->status === 'completed' ? 'selected' : '' }}>Completed (Working)</option>
                                                                            <option value="in_progress" {{ $m->status === 'in_progress' ? 'selected' : '' }}>In Progress (Asset Down)</option>
                                                                            <option value="scheduled" {{ $m->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small text-muted"><i class="fas fa-wallet text-success me-1"></i> Paid From Account</label>
                                                                    <select name="account_id" class="form-select">
                                                                        <option value="">-- Do Not Deduct Cash (Record Only) --</option>
                                                                        @foreach($paymentAccounts as $acc)
                                                                            <option value="{{ $acc->id }}" {{ (string)$m->account_id === (string)$acc->id ? 'selected' : '' }}>
                                                                                {{ $acc->head->name ?? 'Account' }}: {{ $acc->title }} (Bal: Rs. {{ number_format($acc->opening_balance, 0) }})
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <small class="text-muted" style="font-size: .72rem;">Account tabdeel karne par purana account refund hoga aur naye account se balance deduct ho jaye ga.</small>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small text-muted">Parts Replaced</label>
                                                                    <input type="text" name="parts_replaced" class="form-control" value="{{ $m->parts_replaced }}">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small text-muted">Work Description</label>
                                                                    <textarea name="description" rows="2" class="form-control">{{ $m->description }}</textarea>
                                                                </div>
                                                                <div class="mb-0">
                                                                    <label class="form-label fw-bold small text-muted">Next Recommended Service Date</label>
                                                                    <input type="date" name="next_service_date" class="form-control" value="{{ $m->next_service_date ? $m->next_service_date->format('Y-m-d') : '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary fw-bold">Update Record</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            No maintenance records found for this asset.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Branch Transfers -->
                    <div class="tab-pane fade" id="transfers" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0">Branch Movement & Transfer Log</h6>
                            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#transferModal">
                                <i class="fas fa-exchange-alt me-1"></i> Transfer Branch
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>From Branch</th>
                                        <th>To Branch</th>
                                        <th>New Location</th>
                                        <th>Transferred By</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($asset->transfers as $t)
                                    <tr>
                                        <td class="fw-semibold">{{ $t->transfer_date->format('d M Y') }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $t->fromBranch->name ?? 'Head Office' }}</span></td>
                                        <td><span class="badge bg-primary">{{ $t->toBranch->name ?? 'N/A' }}</span></td>
                                        <td>{{ $t->to_location ?: 'Floor' }}</td>
                                        <td>{{ $t->user->name ?? 'Admin' }}</td>
                                        <td>{{ $t->remarks ?: '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No transfer history. Asset is currently at its original assigned branch ({{ $asset->branch->name ?? 'Head Office' }}).
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Disposal Details (if disposed) -->
                    @if($asset->status === 'disposed' || $asset->status === 'scrapped')
                    <div class="tab-pane fade" id="disposal" role="tabpanel">
                        <div class="alert alert-danger mb-4">
                            <h5 class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Asset {{ ucfirst($asset->status) }}</h5>
                            <p class="mb-0">This asset is no longer in active company operations.</p>
                        </div>

                        <div class="info-list" style="max-width: 500px;">
                            <div class="info-item">
                                <span class="k">Disposal Status:</span>
                                <span class="v text-danger fw-bold">{{ ucfirst($asset->status) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="k">Disposal Date:</span>
                                <span class="v">{{ $asset->disposal_date ? $asset->disposal_date->format('d M Y') : 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="k">Sale / Scrap Amount:</span>
                                <span class="v fw-bold text-success">Rs. {{ number_format($asset->disposal_amount, 0) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="k">Reason / Justification:</span>
                                <span class="v">{{ $asset->disposal_reason }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal 1: Maintenance Log -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('assets.maintenance.store', $asset->id) }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-wrench me-2 text-warning"></i> Log Maintenance / Repair</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Service Date *</label>
                        <input type="date" name="maintenance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Service Type *</label>
                            <select name="service_type" class="form-select" required>
                                <option value="Routine Service">Routine Service</option>
                                <option value="Emergency Repair">Emergency Repair</option>
                                <option value="Part Replacement">Part Replacement</option>
                                <option value="Oiling & Greasing">Oiling & Greasing</option>
                                <option value="Inspection">Inspection</option>
                                <option value="Complete Overhaul">Complete Overhaul</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Repair Cost (PKR) *</label>
                            <input type="number" step="0.01" name="cost" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Performed By (Mechanic/Vendor)</label>
                            <input type="text" name="performed_by" class="form-control" placeholder="e.g. Master Tech / External">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Status *</label>
                            <select name="status" class="form-select" required>
                                <option value="completed">Completed (Working)</option>
                                <option value="in_progress">In Progress (Asset Down)</option>
                                <option value="scheduled">Scheduled</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted"><i class="fas fa-wallet text-success me-1"></i> Deduct Payment From (Cash / Bank)</label>
                        <select name="account_id" class="form-select">
                            <option value="">-- Do Not Deduct (Record In File Only) --</option>
                            @foreach($paymentAccounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->head->name ?? 'Account' }}: {{ $acc->title }} (Bal: Rs. {{ number_format($acc->opening_balance, 0) }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: .72rem;">Account select karne par payment cashbook se foran minus hogi aur Expense Voucher ban jaye ga.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Parts Replaced</label>
                        <input type="text" name="parts_replaced" class="form-control" placeholder="e.g. Motor belt, Thermostat, Heating coil">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Work Description / Details</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Details of work carried out..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">Next Recommended Service Date</label>
                        <input type="date" name="next_service_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Maintenance Record</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Transfer Branch -->
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('assets.transfer.store', $asset->id) }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-exchange-alt me-2"></i> Transfer Asset to Another Branch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">Current Location: <strong>{{ $asset->branch->name ?? 'Head Office' }}</strong> ({{ $asset->location_details ?: 'Main Floor' }})</p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Destination Branch *</label>
                        <select name="to_branch_id" class="form-select" required>
                            <option value="">-- Select Destination Branch --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Transfer Date *</label>
                        <input type="date" name="transfer_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">New Specific Location / Section</label>
                        <input type="text" name="to_location" class="form-control" placeholder="e.g. Counter 2, Ground Floor">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">Remarks / Purpose of Movement</label>
                        <textarea name="remarks" rows="2" class="form-control" placeholder="Reason for transfer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white fw-bold">Confirm Transfer</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Dispose / Scrap Asset -->
<div class="modal fade" id="disposeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('assets.dispose', $asset->id) }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-ban me-2"></i> Dispose / Scrap Asset</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-danger fw-semibold mb-3">Marking this asset as disposed/scrapped will remove it from active operational listings.</p>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Action Type *</label>
                            <select name="disposal_status" class="form-select" required>
                                <option value="disposed">Sold / Disposed</option>
                                <option value="scrapped">Scrapped (Zero Value)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Disposal Date *</label>
                            <input type="date" name="disposal_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Sale / Recovery Amount (PKR)</label>
                        <input type="number" step="0.01" name="disposal_amount" class="form-control" placeholder="0.00" value="0.00">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">Reason for Disposal *</label>
                        <textarea name="disposal_reason" rows="2" class="form-control" placeholder="e.g. Beyond economic repair, upgraded to newer model, broken..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">Confirm Disposal</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
