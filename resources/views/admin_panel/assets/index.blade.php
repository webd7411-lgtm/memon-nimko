@extends('admin_panel.layout.app')

@section('content')
@include('admin_panel.partials.mgmt_cards')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .ast-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; padding-bottom: 2.5rem; }
    .ast-mgmt * { font-family: inherit; }

    .ast-hdr {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0369a1 100%);
        border-radius: 18px; padding: 1.5rem 2rem; margin-bottom: 1.5rem;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .18);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;
    }
    .ast-hdr h2 { margin: 0; font-size: 1.45rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .7rem; }
    .ast-hdr h2 i { color: #38bdf8; }
    .ast-hdr p { margin: 3px 0 0; color: rgba(255, 255, 255, .78); font-size: .85rem; font-weight: 500; }

    .ast-btn { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; border: none; border-radius: 10px; padding: .55rem 1.15rem; font-size: .84rem; font-weight: 700; cursor: pointer; transition: all .2s ease; min-height: 40px; text-decoration: none; }
    .ast-btn-primary { background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff; box-shadow: 0 6px 18px rgba(2, 132, 199, .35); }
    .ast-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(2, 132, 199, .42); color: #fff; }
    .ast-btn-outline { background: rgba(255, 255, 255, 0.12); color: #fff; border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(4px); }
    .ast-btn-outline:hover { background: rgba(255, 255, 255, 0.22); color: #fff; }

    .ast-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .ast-stat { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.1rem 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02), 0 6px 18px rgba(0,0,0,0.04); transition: transform .2s ease; }
    .ast-stat:hover { transform: translateY(-2px); }
    .ast-stat-ic { flex: 0 0 auto; width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #fff; }
    .ast-stat-tx .t { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; margin-bottom: 2px; }
    .ast-stat-tx .v { font-size: 1.25rem; font-weight: 900; color: #0f172a; line-height: 1.2; }
    .ast-stat-tx .s { font-size: .75rem; color: #94a3b8; font-weight: 600; }

    .ast-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, .03); overflow: hidden; }
    .ast-filter-bar { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; justify-content: space-between; }
    .ast-filter-bar .filter-group { display: flex; flex-wrap: wrap; gap: .6rem; align-items: center; }

    .ast-badge { display: inline-flex; align-items: center; gap: 4px; font-size: .7rem; font-weight: 700; padding: 3px 9px; border-radius: 9999px; }
    .badge-active { background: #dcfce7; color: #15803d; }
    .badge-repair { background: #fef3c7; color: #b45309; }
    .badge-idle { background: #f1f5f9; color: #475569; }
    .badge-disposed { background: #fee2e2; color: #b91c1c; }

    .badge-cond-new { background: #e0f2fe; color: #0369a1; }
    .badge-cond-good { background: #ecfdf5; color: #047857; }
    .badge-cond-repair { background: #fff7ed; color: #c2410c; }
    .badge-cond-damaged { background: #fef2f2; color: #dc2626; }

    .ast-table thead th { background: #0f172a; color: #cbd5e1; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; padding: .75rem .9rem; border: none; white-space: nowrap; }
    .ast-table tbody td { padding: .75rem .9rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: .83rem; color: #334155; }
    .ast-table tbody tr:hover td { background: #f8fafc; }
</style>

<div class="ast-mgmt">
    <div class="container-fluid px-3 px-md-4 pt-3">

        <!-- Top Header -->
        <div class="ast-hdr">
            <div>
                <h2><i class="fas fa-boxes-stacked"></i> Assets & Fixed Assets Management</h2>
                <p>Track, manage, and monitor company machinery, furniture, equipment, and their lifecycle across branches.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('assets.create') }}" class="ast-btn ast-btn-primary">
                    <i class="fas fa-plus-circle"></i> Register New Asset
                </a>
                <a href="{{ route('assets.categories.index') }}" class="ast-btn ast-btn-outline">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="{{ route('assets.report') }}" class="ast-btn ast-btn-outline">
                    <i class="fas fa-file-invoice-dollar"></i> Valuation Report
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-3" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- KPI Metrics Row -->
        <div class="ast-stats">
            <!-- Total Assets -->
            <div class="ast-stat">
                <div class="ast-stat-ic" style="background: linear-gradient(135deg, #0284c7, #0369a1); box-shadow: 0 4px 12px rgba(2, 132, 199, .3);">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="ast-stat-tx">
                    <div class="t">Total Registered</div>
                    <div class="v">{{ number_format($totalAssets) }}</div>
                    <div class="s">Cost: Rs. {{ number_format($totalCost, 0) }}</div>
                </div>
            </div>

            <!-- Machinery & Plant -->
            <div class="ast-stat">
                <div class="ast-stat-ic" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, .3);">
                    <i class="fas fa-industry"></i>
                </div>
                <div class="ast-stat-tx">
                    <div class="t">Plant & Machinery</div>
                    <div class="v">{{ number_format($machineryCount) }} Units</div>
                    <div class="s">Cost: Rs. {{ number_format($machineryCost, 0) }}</div>
                </div>
            </div>

            <!-- Furniture & Fixtures -->
            <div class="ast-stat">
                <div class="ast-stat-ic" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); box-shadow: 0 4px 12px rgba(139, 92, 246, .3);">
                    <i class="fas fa-chair"></i>
                </div>
                <div class="ast-stat-tx">
                    <div class="t">Furniture & Fixtures</div>
                    <div class="v">{{ number_format($furnitureCount) }} Units</div>
                    <div class="s">Cost: Rs. {{ number_format($furnitureCost, 0) }}</div>
                </div>
            </div>

            <!-- Est. Book Value -->
            <div class="ast-stat">
                <div class="ast-stat-ic" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, .3);">
                    <i class="fas fa-sack-dollar"></i>
                </div>
                <div class="ast-stat-tx">
                    <div class="t">Current Book Value</div>
                    <div class="v text-success">Rs. {{ number_format($totalCurrentValue, 0) }}</div>
                    <div class="s">After Depreciation</div>
                </div>
            </div>

            <!-- Under Maintenance -->
            <div class="ast-stat">
                <div class="ast-stat-ic" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239, 68, 68, .3);">
                    <i class="fas fa-wrench"></i>
                </div>
                <div class="ast-stat-tx">
                    <div class="t">In Maintenance</div>
                    <div class="v text-danger">{{ number_format($inMaintenanceCount) }}</div>
                    <div class="s">Active Repairs</div>
                </div>
            </div>
        </div>

        <!-- Main Assets Table Card -->
        <div class="ast-card">
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('assets.index') }}" class="ast-filter-bar">
                <div class="filter-group">
                    <!-- Branch Filter -->
                    <select name="branch_id" class="form-select form-select-sm" style="min-width: 170px;" onchange="this.form.submit()">
                        <option value="all">🏢 All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Category Filter -->
                    <select name="category_id" class="form-select form-select-sm" style="min-width: 170px;" onchange="this.form.submit()">
                        <option value="">📁 All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select name="status" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
                        <option value="">⚡ All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Working)</option>
                        <option value="under_maintenance" {{ request('status') === 'under_maintenance' ? 'selected' : '' }}>In Maintenance</option>
                        <option value="idle" {{ request('status') === 'idle' ? 'selected' : '' }}>Idle / Stored</option>
                        <option value="disposed" {{ request('status') === 'disposed' ? 'selected' : '' }}>Disposed / Sold</option>
                        <option value="scrapped" {{ request('status') === 'scrapped' ? 'selected' : '' }}>Scrapped</option>
                    </select>
                </div>

                <div class="filter-group">
                    <div class="input-group input-group-sm" style="max-width: 280px;">
                        <input type="text" name="search" class="form-control" placeholder="Search code, name, model..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        @if(request()->anyFilled(['branch_id', 'category_id', 'status', 'search']))
                            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Table View -->
            <div class="table-responsive">
                <table class="table ast-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Asset Details</th>
                            <th>Category</th>
                            <th>Branch / Location</th>
                            <th>Purchase Cost</th>
                            <th>Est. Book Value</th>
                            <th>Condition / Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <!-- Code -->
                            <td>
                                <a href="{{ route('assets.show', $asset->id) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                    {{ $asset->asset_code }}
                                </a>
                                @if($asset->serial_no)
                                    <div class="small text-muted" style="font-size: .72rem;">S/N: {{ $asset->serial_no }}</div>
                                @endif
                            </td>

                            <!-- Asset Details -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($asset->image)
                                        <img src="{{ asset($asset->image) }}" alt="{{ $asset->name }}" class="rounded-2 border" style="width: 36px; height: 36px; object-fit: cover;">
                                    @else
                                        <div class="rounded-2 d-flex align-items-center justify-content-center bg-light border text-secondary" style="width: 36px; height: 36px;">
                                            @if($asset->category && str_contains(strtolower($asset->category->name), 'machin'))
                                                <i class="fas fa-industry"></i>
                                            @elseif($asset->category && str_contains(strtolower($asset->category->name), 'furnit'))
                                                <i class="fas fa-chair"></i>
                                            @else
                                                <i class="fas fa-box"></i>
                                            @endif
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('assets.show', $asset->id) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $asset->name }}
                                        </a>
                                        @if($asset->model_no)
                                            <div class="small text-muted" style="font-size: .75rem;">Model: {{ $asset->model_no }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td>
                                <span class="badge bg-light text-dark border fw-semibold">
                                    {{ $asset->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>

                            <!-- Branch / Location -->
                            <td>
                                <div class="fw-semibold text-dark"><i class="fas fa-building text-secondary me-1"></i> {{ $asset->branch->name ?? 'Head Office' }}</div>
                                @if($asset->location_details)
                                    <small class="text-muted" style="font-size: .72rem;">{{ $asset->location_details }}</small>
                                @endif
                            </td>

                            <!-- Purchase Cost -->
                            <td>
                                <div class="fw-bold text-dark">Rs. {{ number_format($asset->purchase_cost, 0) }}</div>
                                <small class="text-muted" style="font-size: .72rem;">{{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : 'N/A' }}</small>
                            </td>

                            <!-- Book Value -->
                            <td>
                                <div class="fw-bold text-success">Rs. {{ number_format($asset->estimated_current_value, 0) }}</div>
                                <small class="text-muted" style="font-size: .72rem;">Depr: {{ $asset->depreciation_rate }}%</small>
                            </td>

                            <!-- Status / Condition -->
                            <td>
                                @if($asset->status === 'active')
                                    <span class="ast-badge badge-active"><i class="fas fa-check-circle"></i> Active</span>
                                @elseif($asset->status === 'under_maintenance')
                                    <span class="ast-badge badge-repair"><i class="fas fa-wrench"></i> In Repair</span>
                                @elseif($asset->status === 'idle')
                                    <span class="ast-badge badge-idle"><i class="fas fa-pause-circle"></i> Idle</span>
                                @else
                                    <span class="ast-badge badge-disposed"><i class="fas fa-times-circle"></i> {{ ucfirst($asset->status) }}</span>
                                @endif

                                <div class="mt-1">
                                    @if($asset->condition === 'new')
                                        <span class="ast-badge badge-cond-new">Brand New</span>
                                    @elseif($asset->condition === 'good')
                                        <span class="ast-badge badge-cond-good">Good</span>
                                    @elseif($asset->condition === 'needs_repair')
                                        <span class="ast-badge badge-cond-repair">Needs Repair</span>
                                    @elseif($asset->condition === 'damaged')
                                        <span class="ast-badge badge-cond-damaged">Damaged</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-info text-white" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assets.barcode', $asset->id) }}" target="_blank" class="btn btn-sm btn-dark" title="Print Barcode Tag">
                                        <i class="fas fa-barcode"></i>
                                    </a>
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete asset {{ $asset->name }}?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-boxes fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h5>No Assets Registered Yet</h5>
                                    <p class="small mb-3">Register your first machinery, furniture or equipment item.</p>
                                    <a href="{{ route('assets.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Add New Asset
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
