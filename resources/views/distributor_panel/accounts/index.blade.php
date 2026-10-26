@extends('distributor_panel.layout.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header Banner -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 mb-4 rounded-4 shadow-sm"
         style="background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f766e 100%); color: #fff;">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('distributor.dashboard') }}" class="text-white-50 text-decoration-none small">
                    <i class="fas fa-arrow-left me-1"></i> Dashboard
                </a>
                <span class="text-white-50">•</span>
                <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(15, 118, 110, 0.4); border: 1px solid rgba(20, 184, 166, 0.4); color: #5eead4;">
                    Distributor Chart of Accounts
                </span>
            </div>
            <h4 class="mb-0 mt-1 fw-bold text-white"><i class="fas fa-landmark me-2 text-teal"></i> My Bank & Cash Accounts</h4>
            <p class="mb-0 text-white-50 small">Manage your secondary sales bank accounts, cash registers, and mobile payment accounts.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('distributor.sales.create') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2">
                <i class="fas fa-cash-register me-1"></i> POS / Secondary Sale
            </a>
            <button type="button" class="btn btn-teal btn-sm rounded-pill px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#accountModal" onclick="openCreateModal()">
                <i class="fas fa-plus-circle me-1"></i> Open New Account
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-check-circle fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #0f766e !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Accounts</span>
                        <h4 class="fw-bold mb-0 text-dark mt-1">{{ $stats['total_accounts'] }}</h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Configured in your ledger</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-teal" style="color: #0f766e;">
                        <i class="fas fa-folder-tree fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #2563eb !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Bank Accounts</span>
                        <h4 class="fw-bold mb-0 text-primary mt-1">{{ $stats['bank_accounts'] }}</h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Commercial & corporate banks</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-primary">
                        <i class="fas fa-university fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Cash & Wallets</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">{{ $stats['cash_accounts'] }}</h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Van cash & mobile wallets</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-success">
                        <i class="fas fa-wallet fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Initial Capital</span>
                        <h4 class="fw-bold mb-0 text-purple mt-1" style="color: #7c3aed;">
                            Rs. {{ number_format($stats['opening_balance'], 2) }}
                        </h4>
                        <div class="small text-muted mt-0.5" style="font-size: 0.72rem;">Sum of opening balances</div>
                    </div>
                    <div class="rounded-3 p-2 bg-light text-purple" style="color: #7c3aed;">
                        <i class="fas fa-coins fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list-check me-2 text-teal" style="color: #0f766e;"></i> Accounts Ledger</h5>
                <small class="text-muted">These accounts appear exclusively in your secondary sale screen for customer settlements</small>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill font-monospace">
                Distributor ID: #{{ $distributor->code }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light text-muted text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4">Account Title / Bank</th>
                        <th>Category (Head)</th>
                        <th>Account Code</th>
                        <th>Account / IBAN Number</th>
                        <th>Branch</th>
                        <th class="text-end">Opening Balance</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $acc)
                    @php
                        $isBank = str_contains(strtolower($acc->head->name ?? ''), 'bank');
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-xs" 
                                     style="width: 38px; height: 38px; background: {{ $isBank ? '#eff6ff' : '#f0fdf4' }}; color: {{ $isBank ? '#2563eb' : '#16a34a' }};">
                                    <i class="fas {{ $isBank ? 'fa-university' : 'fa-wallet' }}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $acc->title }}</div>
                                    @if($acc->bank_name && $acc->bank_name !== $acc->title)
                                        <small class="text-muted">{{ $acc->bank_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge rounded-pill px-2.5 py-1 fw-semibold {{ $isBank ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}">
                                <i class="fas {{ $isBank ? 'fa-building-columns' : 'fa-money-bill' }} me-1"></i>
                                {{ $acc->head->name ?? 'General Head' }}
                            </span>
                        </td>
                        <td>
                            <span class="font-monospace fw-semibold text-secondary bg-light px-2 py-0.5 rounded border">
                                {{ $acc->account_code }}
                            </span>
                        </td>
                        <td>
                            @if($acc->account_number)
                                <span class="font-monospace text-dark fw-medium">{{ $acc->account_number }}</span>
                            @else
                                <span class="text-muted fst-italic small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ $acc->branch_name ?: '—' }}</span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            Rs. {{ number_format($acc->opening_balance, 2) }}
                        </td>
                        <td class="text-center">
                            @if($acc->status)
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="fas fa-check-circle me-0.5"></i> Active
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary px-2 py-1">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1"
                                        title="Edit Account"
                                        onclick="openEditModal({{ json_encode($acc) }})">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <form action="{{ route('distributor.accounts.destroy', $acc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" title="Delete Account">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="py-4">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: #f1f5f9; color: #94a3b8;">
                                    <i class="fas fa-university fs-3"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">No Custom Accounts Opened Yet</h6>
                                <p class="text-muted small mb-3">You can open your bank accounts, cash registers, or digital wallets here.<br>They will appear automatically in your POS Payment Methods dropdown.</p>
                                <button type="button" class="btn btn-teal btn-sm rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#accountModal" onclick="openCreateModal()">
                                    <i class="fas fa-plus-circle me-1"></i> Open First Account
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add / Edit Account -->
<div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #090d16 0%, #0f766e 100%);">
                <h5 class="modal-title fw-bold" id="accountModalLabel">
                    <i class="fas fa-landmark me-2"></i> Open New Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('distributor.accounts.store') }}" method="POST" id="accountForm">
                @csrf
                <input type="hidden" name="account_id" id="modalAccountId" value="">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Account Category / Head <span class="text-danger">*</span></label>
                        <select name="head_id" id="modalHeadId" class="form-select rounded-3 shadow-none" required>
                            @foreach($heads as $head)
                                <option value="{{ $head->id }}">{{ $head->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Account Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="modalTitle" class="form-control rounded-3 shadow-none" placeholder="e.g. Meezan Bank (Shop Account) or Cash Drawer" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Bank Name (Optional)</label>
                            <input type="text" name="bank_name" id="modalBankName" class="form-control rounded-3 shadow-none" placeholder="e.g. Meezan Bank">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Branch Name / City</label>
                            <input type="text" name="branch_name" id="modalBranchName" class="form-control rounded-3 shadow-none" placeholder="e.g. Nazimabad Branch">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Account / IBAN Number</label>
                            <input type="text" name="account_number" id="modalAccountNumber" class="form-control rounded-3 shadow-none" placeholder="e.g. 010203040506">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Account Code (Optional)</label>
                            <input type="text" name="account_code" id="modalAccountCode" class="form-control rounded-3 shadow-none" placeholder="Leave blank for auto">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Opening Balance (PKR)</label>
                            <input type="number" step="any" min="0" name="opening_balance" id="modalOpeningBalance" class="form-control rounded-3 shadow-none" placeholder="0.00" value="0">
                        </div>
                        <div class="col-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="modalStatus" checked>
                                <label class="form-check-label fw-semibold small text-dark" for="modalStatus">Active Account</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-teal rounded-pill px-4" id="modalSubmitBtn">
                        <i class="fas fa-save me-1"></i> Save Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openCreateModal() {
    $('#modalAccountId').val('');
    $('#accountModalLabel').html('<i class="fas fa-landmark me-2"></i> Open New Account');
    $('#modalSubmitBtn').html('<i class="fas fa-save me-1"></i> Save Account');
    $('#modalTitle').val('');
    $('#modalBankName').val('');
    $('#modalBranchName').val('');
    $('#modalAccountNumber').val('');
    $('#modalAccountCode').val('');
    $('#modalOpeningBalance').val('0');
    $('#modalStatus').prop('checked', true);
}

function openEditModal(acc) {
    $('#modalAccountId').val(acc.id);
    $('#accountModalLabel').html('<i class="fas fa-pencil-alt me-2"></i> Edit Account');
    $('#modalSubmitBtn').html('<i class="fas fa-save me-1"></i> Update Account');
    $('#modalHeadId').val(acc.head_id);
    $('#modalTitle').val(acc.title);
    $('#modalBankName').val(acc.bank_name || '');
    $('#modalBranchName').val(acc.branch_name || '');
    $('#modalAccountNumber').val(acc.account_number || '');
    $('#modalAccountCode').val(acc.account_code || '');
    $('#modalOpeningBalance').val(acc.opening_balance || '0');
    $('#modalStatus').prop('checked', acc.status == 1);
    
    var modal = new bootstrap.Modal(document.getElementById('accountModal'));
    modal.show();
}
</script>
@endpush
@endsection
