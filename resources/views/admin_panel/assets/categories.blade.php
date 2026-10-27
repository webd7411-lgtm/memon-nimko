@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    .cat-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; padding-bottom: 3rem; }
    .cat-mgmt * { font-family: inherit; }

    .cat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; }
</style>

<div class="cat-mgmt">
    <div class="container-fluid px-3 px-md-4 pt-3">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('assets.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Assets
                </a>
                <h2 class="fw-bold text-dark mb-0"><i class="fas fa-tags text-primary me-2"></i> Asset Categories</h2>
                <p class="text-muted small mb-0">Manage classifications and default depreciation rates for machinery and furniture.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createCatModal">
                    <i class="fas fa-plus-circle me-1"></i> Add Category
                </button>
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

        <div class="cat-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Category Name</th>
                            <th>Code Prefix</th>
                            <th>Default Depreciation</th>
                            <th>Assets Count</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td class="fw-bold text-dark">
                                <i class="fas fa-folder text-warning me-1"></i> {{ $cat->name }}
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace">{{ $cat->code ?: 'N/A' }}</span></td>
                            <td class="fw-bold text-success">{{ $cat->depreciation_rate }}% / year</td>
                            <td>
                                <a href="{{ route('assets.index', ['category_id' => $cat->id]) }}" class="badge bg-primary text-decoration-none">
                                    {{ $cat->assets_count }} Items
                                </a>
                            </td>
                            <td class="text-muted small">{{ $cat->description ?: '-' }}</td>
                            <td>
                                @if($cat->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCatModal{{ $cat->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($cat->assets_count == 0)
                                    <form action="{{ route('assets.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editCatModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('assets.categories.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="edit_id" value="{{ $cat->id }}">
                                    <div class="modal-content rounded-4 border-0">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title fw-bold">Edit Category: {{ $cat->name }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">Category Name *</label>
                                                <input type="text" name="name" class="form-control" value="{{ $cat->name }}" required>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small text-muted">Code Prefix</label>
                                                    <input type="text" name="code" class="form-control font-monospace" value="{{ $cat->code }}" placeholder="e.g. MCH, FRN">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold small text-muted">Depreciation Rate (%)</label>
                                                    <input type="number" step="0.01" name="depreciation_rate" class="form-control" value="{{ $cat->depreciation_rate }}" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small text-muted">Description</label>
                                                <textarea name="description" rows="2" class="form-control">{{ $cat->description }}</textarea>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="status" id="status{{ $cat->id }}" {{ $cat->status ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="status{{ $cat->id }}">Category Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No categories defined.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('assets.categories.store') }}" method="POST">
            @csrf
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-1"></i> Add New Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Category Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Plant & Machinery, Furniture & Fixtures" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Code Prefix</label>
                            <input type="text" name="code" class="form-control font-monospace" placeholder="e.g. MCH, FRN, ELC">
                            <small class="text-muted" style="font-size: .7rem;">Used in auto code (AST-MCH-0001)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Depreciation Rate (%)</label>
                            <input type="number" step="0.01" name="depreciation_rate" class="form-control" value="10.00" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">Description</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Category</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
