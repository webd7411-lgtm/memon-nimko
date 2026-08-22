@extends('admin_panel.layout.app')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    .nr-page .table-responsive { overflow-x: hidden; }
    .nr-page table#narrationsTable { table-layout: fixed; width: 100% !important; }
    .nr-page thead th, .nr-page tbody td { white-space: normal !important; overflow-wrap: anywhere; }

    @media (max-width: 991.98px) {
        .nr-page .table-responsive { overflow: visible; }
        .nr-page table { display: block !important; width: 100% !important; }
        .nr-page thead { display: none !important; }
        .nr-page tbody { display: block !important; }
        .nr-page tbody tr {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: .3rem .5rem;
            background: #fff;
            border: 1px solid #e9edf2;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,.03), 0 2px 6px rgba(0,0,0,.04);
            padding: .55rem .65rem;
            margin-bottom: .55rem;
            overflow: hidden !important;
        }
        .nr-page tbody td {
            display: flex !important;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 2px 4px;
            border: none !important;
            padding: 0 !important;
            text-align: left;
            min-width: 0;
            word-break: break-word;
            overflow-wrap: anywhere;
            font-size: .75rem;
            line-height: 1.35;
        }
        .nr-page tbody td::before {
            content: attr(data-label) ":";
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #94a3b8;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .nr-page tbody td:nth-child(1) { order: 0; justify-self: start; }
        .nr-page tbody td:nth-child(1)::before { content: none; }
        .nr-page tbody td:nth-child(2) { order: 1; }
        .nr-page tbody td:nth-child(3) { order: 2; grid-column: 1 / -1; font-weight: 700; color: #0f172a; }
        .nr-page tbody td:nth-child(3)::before { content: none; }
        .nr-page tbody td:nth-child(4) { order: 3; }
        .nr-page tbody td:nth-child(5) { order: 4; grid-column: 1 / -1; }
        .nr-page tbody td:nth-child(5)::before { content: none; }
        .nr-page tbody td:nth-child(5) .btn { min-height: 38px; display: inline-flex; align-items: center; justify-content: center; }
        .nr-page tbody td:nth-child(5) form { display: inline-block; margin-left: .35rem; }
    }
</style>

<div class="main-content nr-page">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-2 ">
            <h2 class="fw-bold mt-2">Narrations</h2>
            <button class="btn btn-primary mt-2" id="addBtn">
                <i class="bi bi-plus-lg"></i> Add Narration
            </button>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="narrationsTable" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Vouchers</th>
                                <th>Narration</th>
                                <th>Date</th>
                                <th style="width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($narrations as $key => $row)
                            <tr>
                                <td data-label="#"> {{ $key+1 }}</td>
                                <td data-label="Vouchers">{{ $row->expense_head }}</td>
                                <td data-label="Narration">{{ $row->narration }}</td>
                                <td data-label="Date">{{ $row->created_at->format('Y-m-d') }}</td>
                                <td data-label="Action">
                                    <button class="btn btn-warning btn-sm editBtn" 
                                        data-id="{{ $row->id }}"
                                        data-expense="{{ $row->expense_head }}"
                                        data-narration="{{ $row->narration }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('narrations.destroy', $row->id) }}" method="POST" style="display:inline-block;">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Delete this?')" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div class="modal fade" id="narrationModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content rounded-3 shadow">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="modalTitle">Add Narration</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('narrations.store') }}" method="POST" id="narrationForm">
                            @csrf
                            <input type="hidden" name="id" id="narration_id">

                            <div class="mb-3">
                                <label class="form-label">Select Voucher Head</label>
                                <select name="expense_head" id="expense_head" class="form-select form-control" required>
                                    <option value="" disabled>Choose...</option>
                                    <option value="Receipts Voucher">Receipts Voucher</option>
                                    <option value="Expense voucher">Expense voucher</option>
                                    <option value="Journal voucher">Journal voucher</option>
                                    <option value="Payment voucher">Payment voucher</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Narration</label>
                                <textarea name="narration" id="narration" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success" id="saveBtn">Save Narration</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function(){
    $('#narrationsTable').DataTable();

    // Add button click
    $('#addBtn').click(function(){
        $('#modalTitle').text('Add Narration');
        $('#narrationForm')[0].reset();
        $('#narration_id').val('');
        $('#narrationModal').modal('show');
    });

    // Edit button click
    $(document).on('click', '.editBtn', function(){
        $('#modalTitle').text('Edit Narration');
        $('#narration_id').val($(this).data('id'));
        $('#expense_head').val($(this).data('expense'));
        $('#narration').val($(this).data('narration'));
        $('#narrationModal').modal('show');
    });
});
</script>

@endsection
