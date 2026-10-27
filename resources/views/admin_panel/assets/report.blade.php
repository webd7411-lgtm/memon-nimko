@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    .ast-rep { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; padding-bottom: 3rem; }
    .ast-rep * { font-family: inherit; }

    .rep-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; }

    @media print {
        header, nav, .top_nav, .rt_nav_header, .navbar, .controls-bar, .ast-btn-print { display: none !important; }
        .ast-rep { background: #fff !important; padding: 0 !important; }
        .rep-card { border: none !important; box-shadow: none !important; }
        .print-header { display: block !important; }
    }
    .print-header { display: none; }
</style>

<div class="ast-rep">
    <div class="container-fluid px-3 px-md-4 pt-3">

        <!-- Controls Bar -->
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 controls-bar gap-2">
            <div>
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Assets
                </a>
                <h2 class="fw-bold text-dark mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> Fixed Assets Register & Valuation Report</h2>
                <p class="text-muted small mb-0">Complete inventory valuation and depreciation schedule of machinery & furniture.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary fw-bold" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Valuation Report
                </button>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="rep-card p-3 mb-4 controls-bar">
            <form method="GET" action="{{ route('assets.report') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all">🏢 All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">📁 All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">⚡ All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="under_maintenance" {{ request('status') === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        <option value="disposed" {{ request('status') === 'disposed' ? 'selected' : '' }}>Disposed / Scrapped</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">
                        <i class="fas fa-filter me-1"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>

        @php
            $repTotalCost = $assets->sum('purchase_cost');
            $repTotalBookValue = $assets->sum(function($a) { return $a->estimated_current_value; });
            $repTotalDepr = max(0, $repTotalCost - $repTotalBookValue);
        @endphp

        <!-- Printable Header -->
        <div class="print-header text-center mb-4 pb-2 border-bottom">
            <h2 class="fw-bold mb-1">{{ shop_name() }}</h2>
            <h5 class="text-uppercase fw-bold text-secondary mb-1">Fixed Assets Register & Valuation Report</h5>
            <div class="small text-muted">
                Generated on {{ date('d M Y, h:i A') }} |
                Branch: {{ $branchId ? ($branches->firstWhere('id', $branchId)->name ?? 'Selected') : 'All Branches' }}
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="p-3 bg-white rounded-3 border">
                    <div class="text-muted small fw-bold">TOTAL ASSETS</div>
                    <h3 class="fw-bold text-dark mb-0">{{ count($assets) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-white rounded-3 border">
                    <div class="text-muted small fw-bold">HISTORICAL COST</div>
                    <h3 class="fw-bold text-primary mb-0">Rs. {{ number_format($repTotalCost, 0) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-white rounded-3 border">
                    <div class="text-muted small fw-bold">ACC. DEPRECIATION</div>
                    <h3 class="fw-bold text-danger mb-0">Rs. {{ number_format($repTotalDepr, 0) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-white rounded-3 border">
                    <div class="text-muted small fw-bold">NET BOOK VALUE</div>
                    <h3 class="fw-bold text-success mb-0">Rs. {{ number_format($repTotalBookValue, 0) }}</h3>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="rep-card">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0" style="font-size: .83rem;">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Code</th>
                            <th>Asset Name / Model</th>
                            <th>Category</th>
                            <th>Branch / Location</th>
                            <th>Purchase Date</th>
                            <th class="text-end">Cost (PKR)</th>
                            <th class="text-center">Depr %</th>
                            <th class="text-end">Acc. Depr</th>
                            <th class="text-end">Net Book Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $idx => $a)
                        @php
                            $aBookVal = $a->estimated_current_value;
                            $aDepr = max(0, $a->purchase_cost - $aBookVal);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td class="font-monospace fw-bold">{{ $a->asset_code }}</td>
                            <td>
                                <strong>{{ $a->name }}</strong>
                                @if($a->model_no)
                                    <div class="text-muted" style="font-size: .72rem;">Model: {{ $a->model_no }}</div>
                                @endif
                            </td>
                            <td>{{ $a->category->name ?? 'N/A' }}</td>
                            <td>{{ $a->branch->name ?? 'Head Office' }}</td>
                            <td>{{ $a->purchase_date ? $a->purchase_date->format('d/m/Y') : '-' }}</td>
                            <td class="text-end fw-bold">{{ number_format($a->purchase_cost, 0) }}</td>
                            <td class="text-center">{{ $a->depreciation_rate }}%</td>
                            <td class="text-end text-danger">{{ number_format($aDepr, 0) }}</td>
                            <td class="text-end text-success fw-bold">{{ number_format($aBookVal, 0) }}</td>
                            <td>
                                <span class="badge {{ $a->status === 'active' ? 'bg-success' : ($a->status === 'under_maintenance' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $a->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">No assets found for the selected criteria.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="6" class="text-end">TOTALS:</td>
                            <td class="text-end text-primary">Rs. {{ number_format($repTotalCost, 0) }}</td>
                            <td></td>
                            <td class="text-end text-danger">Rs. {{ number_format($repTotalDepr, 0) }}</td>
                            <td class="text-end text-success">Rs. {{ number_format($repTotalBookValue, 0) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
