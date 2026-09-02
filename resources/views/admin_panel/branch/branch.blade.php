@extends('admin_panel.layout.app')
@section('content')
@include('admin_panel.partials.mgmt_cards')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .rp-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2rem; }
    .rp-mgmt { --rp-accent: #0ea5e9; --rp-accent-2: #0284c7; }
    .rp-mgmt * { font-family: inherit; }

    .rp-hdr {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #0c4a6e 0%, #0ea5e9 100%);
        border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.4rem;
        box-shadow: 0 16px 40px rgba(14, 165, 233, .25);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem;
    }
    .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
    .rp-hdr h2 i { color: #7dd3f7; }
    .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .78); font-size: .85rem; font-weight: 500; }

    .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
    .rp-btn-primary { background: linear-gradient(135deg, #0ea5e9, #0284c7); color: #fff; box-shadow: 0 6px 18px rgba(14, 165, 233, .35); }
    .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(14, 165, 233, .42); color: #fff; }

    .rp-card { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05); overflow: hidden; }
    .rp-card-body { padding: 1.4rem; }

    .rp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: .9rem; margin-bottom: 1.4rem; }
    .rp-stat { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; padding: .9rem 1.1rem; display: flex; align-items: center; gap: .8rem; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 6px 18px rgba(0, 0, 0, .04); }
    .rp-stat-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); color: #fff; box-shadow: 0 4px 12px rgba(14, 165, 233, .3); }
    .rp-stat-tx .t { font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .4px; color: #64748b; }
    .rp-stat-tx .v { font-size: 1.05rem; font-weight: 800; color: #0f172a; }

    .rp-mgmt table.rp-table.dataTable { border-collapse: separate; border-spacing: 0; font-size: .82rem; }
    .rp-mgmt .rp-table thead th { background: #0f172a !important; color: #94a3b8 !important; font-size: .64rem; font-weight: 700; text-transform: uppercase; letter-spacing: .45px; padding: .65rem .7rem; border: none; border-bottom: 2px solid #0f172a !important; text-align: left; white-space: nowrap; }
    .rp-mgmt .rp-table thead th:first-child { border-radius: 10px 0 0 0; }
    .rp-mgmt .rp-table thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-mgmt .rp-table tbody td { padding: .55rem .7rem; border: none; border-bottom: 1px solid #f1f4f9 !important; vertical-align: middle; color: #334155; }
    .rp-mgmt .rp-table tbody tr:nth-of-type(odd) td { background: transparent !important; }
    .rp-mgmt .rp-table tbody tr:hover td { background: #fafbfc !important; }
    .rp-mgmt .rp-table tbody tr:last-child td { border-bottom: none !important; }
    .rp-mgmt .rp-table .br-nm { font-weight: 700; color: #0f172a; }

    .rp-mgmt .dataTables_wrapper { padding: .25rem; }
    .rp-mgmt .dataTables_length, .rp-mgmt .dataTables_info, .rp-mgmt .dataTables_filter { font-size: .78rem; font-weight: 600; color: #54657e; }
    .rp-mgmt .dataTables_filter input { border: 1.5px solid #e9edf2; border-radius: 9px; padding: .35rem .6rem; font-size: .8rem; outline: none; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .dataTables_filter input:focus { border-color: var(--rp-accent); box-shadow: 0 0 0 3px rgba(14, 165, 233, .12); }
    .rp-mgmt .dataTables_length select { border: 1.5px solid #e9edf2; border-radius: 8px; padding: .3rem .5rem; font-size: .78rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .page-link { color: #334155; border: none; border-radius: 8px; margin: 0 2px; font-size: .75rem; font-weight: 700; padding: .3rem .6rem; }
    .rp-mgmt .page-item.active .page-link { background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); border: none; color: #fff; }

    .rp-mgmt .btn { border-radius: 8px; font-size: .7rem; font-weight: 700; letter-spacing: .2px; padding: .32rem .6rem; border: none; transition: all .2s ease; }
    .rp-mgmt .btn:hover { transform: translateY(-1px); }
    .rp-mgmt .btn-info { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; }
    .rp-mgmt .btn-primary { background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); color: #fff; }
    .rp-mgmt .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
    .rp-mgmt .rp-chip { display: inline-flex; align-items: center; font-size: .68rem; font-weight: 700; letter-spacing: .3px; padding: .3rem .6rem; border-radius: 7px; }
    .rp-mgmt .rp-chip.bg-id { background: #e0f2fe !important; color: #0c4a6e !important; }
    .rp-mgmt .rp-chip.bg-mail { background: #f0fdf4 !important; color: #15803d !important; }

    .rp-mgmt .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(2, 6, 23, .28); }
    .rp-mgmt .modal-header { background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border-bottom: none; }
    .rp-mgmt .modal-header .modal-title { color: #fff; font-weight: 800; font-size: 1.02rem; }
    .rp-mgmt .modal-body { padding: 1.4rem; }
    .rp-mgmt .modal-body .form-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .35px; color: #54657e; }
    .rp-mgmt .modal-body .form-control, .rp-mgmt .modal-body .form-select { border: 1.5px solid #e9edf2; border-radius: 10px; padding: .5rem .8rem; font-size: .85rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .modal-body .form-control:focus, .rp-mgmt .modal-body .form-select:focus { border-color: var(--rp-accent); box-shadow: 0 0 0 3px rgba(14, 165, 233, .12); }
    .rp-mgmt .modal-footer { border-top: 1px solid #eef2f7; padding: .9rem 1.4rem; }

    @media (max-width: 575.98px) {
        .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: flex-start; }
        .rp-hdr h2 { font-size: 1.15rem; }
        .rp-btn { width: 100%; }
    }
</style>
<div class="rp-mgmt">
    <div class="container-fluid px-3 px-md-4 py-3">
        <div class="rp-hdr">
            <div>
                <h2><i class="bi bi-geo-alt-fill"></i> Branches</h2>
                <p>Manage branch locations and their assigned users</p>
            </div>
            <button type="button" class="rp-btn rp-btn-primary" id="reset-form" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="bi bi-plus-lg"></i> Create Branch
            </button>
        </div>

        @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" style="border:none;border-radius:12px;">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="rp-stats">
            <div class="rp-stat">
                <span class="rp-stat-ic"><i class="bi bi-geo-alt"></i></span>
                <div class="rp-stat-tx">
                    <div class="t">Total Branches</div>
                    <div class="v">{{ $branches->count() }}</div>
                </div>
            </div>
        </div>

        <div class="rp-card">
            <div class="rp-card-body p-0">
                <div class="table-responsive">
                    <table id="default-datatable" class="table rp-table mb-0">
                        <thead class="text-center">
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Number</th>
                                <th>Manager Email</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($branches as $branch)
                            <tr>
                                <td><span class="rp-chip bg-id">#{{ $branch->id }}</span></td>
                                <td class="br-nm">{{ $branch->name }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>{{ $branch->number }}</td>
                                <td><span class="rp-chip bg-mail">{{ $branch->user->email ?? '-' }}</span></td>
                                <td class="text-center" style="white-space:nowrap;">
                                    <button class="btn btn-info btn-sm edit-btn me-1"
                                        data-id="{{ $branch->id }}"
                                        data-name="{{ $branch->name }}"
                                        data-address="{{ $branch->address }}"
                                        data-number="{{ $branch->number }}"
                                        data-email="{{ $branch->user->email ?? '' }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <a href="{{ route('branch.delete', $branch->id) }}" class="btn btn-danger btn-sm delete-btn"
                                        data-url="{{ route('branch.delete', $branch->id) }}"
                                        data-msg="Are you sure you want to delete this Branch?"
                                        data-method="DELETE"
                                        onclick="confirmedBox(this, event)">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Mobile / Tablet premium cards --}}
        <div class="rp-ucards" id="branchCards">
            @forelse($branches as $branch)
            <div class="rp-ucard">
                <div class="rp-uc-top">
                    <span class="rp-uc-av"><i class="bi bi-geo-alt"></i></span>
                    <div class="rp-uc-idf">
                        <span class="rp-uc-nm">{{ $branch->name }}</span>
                        <span class="rp-uc-em">{{ $branch->address ?? '—' }}</span>
                    </div>
                </div>
                <div class="rp-uc-bal">
                    <span class="bl">ID</span>
                    <b>#{{ $branch->id }}</b>
                </div>
                <div class="rp-uc-info">
                    <span>Number: {{ $branch->number ?? '—' }}</span>
                    <span>Manager Email: {{ $branch->user->email ?? '—' }}</span>
                </div>
                <div class="rp-uc-acts">
                    <button class="btn btn-info edit-btn"
                        data-id="{{ $branch->id }}"
                        data-name="{{ $branch->name }}"
                        data-address="{{ $branch->address }}"
                        data-number="{{ $branch->number }}"
                        data-email="{{ $branch->user->email ?? '' }}">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <a href="{{ route('branch.delete', $branch->id) }}" class="btn btn-danger delete-btn"
                        data-url="{{ route('branch.delete', $branch->id) }}"
                        data-msg="Are you sure you want to delete this Branch?"
                        data-method="DELETE"
                        onclick="confirmedBox(this, event)">
                        <i class="bi bi-trash"></i> Delete
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4">No branches found</div>
            @endforelse
        </div>
    </div>

    <!-- Add/Edit Branch Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-plus-circle me-1"></i> Add Branch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="myform" action="{{ route('branch.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="edit_id" id="id" />
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="name" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" id="address" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number</label>
                            <input type="text" name="number" class="form-control" id="number" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Branch Manager Email (Optional)</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="" selected>Select Manager Email (Optional)</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->email }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary save-btn" form="branchForm" value="Save Branch">
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/mycode.js') }}"></script>
    <script>
        // attach form id to submit button (button is outside form)
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.querySelector('.save-btn');
            if (btn) btn.setAttribute('form', 'branchForm');
            const form = document.querySelector('.myform');
            if (form) form.setAttribute('id', 'branchForm');
        });

        $(document).on('submit', '.myform', function(e) {
            e.preventDefault();
            var formdata = new FormData(this);
            url = $(this).attr('action');
            method = $(this).attr('method');
            $(this).find(':submit').attr('disabled', true);
            myAjax(url, formdata, method);
        });

        $(document).on('click', '.edit-btn', function() {
            $('#exampleModal .modal-title').html('<i class="bi bi-pencil-square me-1"></i> Edit Branch');
            $('#id').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#address').val($(this).data('address'));
            $('#number').val($(this).data('number'));
            var email = $(this).data('email');
            $('#user_id').val('');
            if (email) {
                $('#user_id option').each(function() {
                    if ($(this).text().trim() === email) $(this).prop('selected', true);
                });
            }
            $("#exampleModal").modal("show");
        });

        $(document).on('click', '#reset-form', function() {
            $('#id').val('');
            $('#name').val('');
            $('#address').val('');
            $('#number').val('');
            $('#user_id').val('');
            $('#exampleModal .modal-title').html('<i class="bi bi-plus-circle me-1"></i> Add Branch');
            $("#exampleModal").modal("show");
        });

        function confirmedBox(element, event) {
            event.preventDefault();
            const message = element.getAttribute('data-msg') || 'Are you sure?';
            const url = element.getAttribute('data-url') || element.getAttribute('href');
            const method = element.getAttribute('data-method') || 'DELETE';
            Swal.fire({
                title: 'Confirm Deletion',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: method,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.success || 'Deleted successfully.', 'success').then(() => {
                                if (response.reload) {
                                    window.location.href = response.reload;
                                } else {
                                    location.reload();
                                }
                            });
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        }

        $(document).ready(function() {
            $('#default-datatable').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                order: [[0, 'desc']],
                language: {
                    search: "Search Branches:",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        });
    </script>
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif
@endsection