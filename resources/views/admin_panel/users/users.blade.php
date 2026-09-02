@extends('admin_panel.layout.app')
@section('content')
@include('admin_panel.partials.mgmt_cards')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .rp-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2rem; }
    .rp-mgmt { --rp-accent: #7c3aed; --rp-accent-2: #6d28d9; }
    .rp-mgmt * { font-family: inherit; }

    .rp-hdr {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #2e1065 0%, #7c3aed 100%);
        border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.4rem;
        box-shadow: 0 16px 40px rgba(124, 58, 237, .25);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem;
    }
    .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
    .rp-hdr h2 i { color: #ddd6fe; }
    .rp-hdr p { margin: 2px 0 0; color: rgba(255,255,255,.75); font-size: .85rem; font-weight: 500; }

    .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
    .rp-btn-primary { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: #fff; box-shadow: 0 6px 18px rgba(124, 58, 237, .35); }
    .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(124, 58, 237, .42); color: #fff; }

    .rp-card { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05); overflow: hidden; }
    .rp-card-body { padding: 1.4rem; }

    .rp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: .9rem; margin-bottom: 1.4rem; }
    .rp-stat { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; padding: .9rem 1.1rem; display: flex; align-items: center; gap: .8rem; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 6px 18px rgba(0, 0, 0, .04); }
    .rp-stat-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); color: #fff; box-shadow: 0 4px 12px rgba(124, 58, 237, .3); }
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

    .rp-mgmt .dataTables_wrapper { padding: .25rem; }
    .rp-mgmt .dataTables_length, .rp-mgmt .dataTables_info, .rp-mgmt .dataTables_filter { font-size: .78rem; font-weight: 600; color: #54657e; }
    .rp-mgmt .dataTables_filter input { border: 1.5px solid #e9edf2; border-radius: 9px; padding: .35rem .6rem; font-size: .8rem; outline: none; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .dataTables_filter input:focus { border-color: var(--rp-accent); box-shadow: 0 0 0 3px rgba(124, 58, 237, .12); }
    .rp-mgmt .dataTables_length select { border: 1.5px solid #e9edf2; border-radius: 8px; padding: .3rem .5rem; font-size: .78rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .page-link { color: #334155; border: none; border-radius: 8px; margin: 0 2px; font-size: .75rem; font-weight: 700; padding: .3rem .6rem; }
    .rp-mgmt .page-item.active .page-link { background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); border: none; color: #fff; }

    .rp-mgmt .btn { border-radius: 8px; font-size: .7rem; font-weight: 700; letter-spacing: .2px; padding: .32rem .6rem; border: none; transition: all .2s ease; }
    .rp-mgmt .btn:hover { transform: translateY(-1px); }
    .rp-mgmt .btn-info { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; }
    .rp-mgmt .btn-primary { background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); color: #fff; }
    .rp-mgmt .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
    .rp-mgmt .btn-secondary { background: #eef2f7; color: #334155; }
    .rp-mgmt .btn-light { background: #eef2f7; color: #334155; }

    .rp-mgmt .bg-violet { background: #ede9fe !important; color: #6d28d9 !important; }
    .rp-mgmt .rp-chip { display: inline-flex; align-items: center; font-size: .68rem; font-weight: 700; letter-spacing: .3px; padding: .3rem .6rem; border-radius: 7px; }

    .rp-mgmt .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(2, 6, 23, .28); }
    .rp-mgmt .modal-header { background: linear-gradient(135deg, #2e1065, #7c3aed); border-bottom: none; }
    .rp-mgmt .modal-header .modal-title { color: #fff; font-weight: 800; font-size: 1.02rem; }
    .rp-mgmt .modal-body { padding: 1.4rem; }
    .rp-mgmt .modal-body .form-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #54657e; }
    .rp-mgmt .modal-body .form-control, .rp-mgmt .modal-body .form-select { border: 1.5px solid #e9edf2; border-radius: 10px; padding: .5rem .8rem; font-size: .85rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .modal-body .form-control:focus, .rp-mgmt .modal-body .form-select:focus { border-color: var(--rp-accent); box-shadow: 0 0 0 3px rgba(124, 58, 237, .12); }
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
                <h2><i class="bi bi-people-fill"></i> Users</h2>
                <p>Manage users, roles, and opening balances</p>
            </div>
            <button type="button" class="rp-btn rp-btn-primary" id="reset-form" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="bi bi-plus-lg"></i> Create User
            </button>
        </div>

        <div class="rp-stats">
            <div class="rp-stat">
                <span class="rp-stat-ic"><i class="bi bi-person-check"></i></span>
                <div class="rp-stat-tx">
                    <div class="t">Total Users</div>
                    <div class="v">{{ $users->count() }}</div>
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
                                <th>Email</th>
                                <th>Branch</th>
                                <th>Roles</th>
                                <th>Opening Balance</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($users as $key => $user)
                            <tr>
                                <td><span class="rp-chip bg-violet">{{ $user->id }}</span></td>
                                <td class="fw-bold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="rp-chip bg-violet">{{ $user->branch->name ?? 'Main Branch' }}</span>
                                </td>
                                <td>
                                    @forelse ($user->getRoleNames() as $role)
                                        <span class="rp-chip bg-violet mb-1">{{ $role }}</span>
                                    @empty
                                        <span class="rp-chip" style="background:#fee2e2;color:#b91c1c;">No Role Assigned</span>
                                    @endforelse
                                </td>
                                <td>
                                    @if($user->today_opening)
                                        <span class="badge text-dark fw-bold" style="background:#f1f5f9;font-size:.8rem;">{{ number_format($user->today_opening->amount, 2) }}</span>
                                    @else
                                        <span class="text-muted">0.00</span>
                                    @endif
                                </td>
                                <td class="text-center" style="white-space:nowrap;">
                                    <button class="btn btn-info btn-sm edit-role-btn p-1 me-1" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                        <i class="bi bi-shield-lock"></i> Roles
                                    </button>
                                    <button class="btn btn-primary btn-sm edit-btn p-1 me-1" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-branch-id="{{ $user->branch_id }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <a href="{{ route('users.delete', $user->id) }}" class="btn btn-danger btn-sm delete-btn p-1"
                                        data-url="{{ route('users.delete', $user->id) }}"
                                        data-msg="Are you sure you want to delete this User?"
                                        data-method="DELETE"
                                        onclick="confirmedBox(this, event)">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                    <button class="btn btn-secondary btn-sm opening-balance-btn p-1" data-userid="{{ $user->id }}" data-username="{{ $user->name }}">
                                        <i class="bi bi-cash"></i> Op. Balance
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rp-ucards" id="userCards">
            @forelse($users as $user)
            <div class="rp-ucard">
                <div class="rp-uc-top">
                    <span class="rp-uc-av"><i class="bi bi-person-fill"></i></span>
                    <div class="rp-uc-idf">
                        <span class="rp-uc-nm">{{ $user->name }}</span>
                        <span class="rp-uc-em">{{ $user->email }}</span>
                    </div>
                    <span class="rp-uc-ix">#{{ $user->id }}</span>
                </div>
                <div class="rp-uc-bal">
                    <span class="bl">Branch</span>
                    <b>{{ $user->branch->name ?? 'Main' }}</b>
                </div>
                <div class="rp-uc-acts">
                    <button class="btn btn-info edit-role-btn" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}"><i class="bi bi-shield-lock"></i> Roles</button>
                    <button class="btn btn-primary edit-btn" data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-branch-id="{{ $user->branch_id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                    <a href="{{ route('users.delete', $user->id) }}" class="btn btn-danger delete-btn" data-url="{{ route('users.delete', $user->id) }}" data-msg="Are you sure you want to delete this User?" data-method="DELETE" onclick="confirmedBox(this, event)"><i class="bi bi-trash"></i> Delete</a>
                    <button class="btn btn-secondary opening-balance-btn" data-userid="{{ $user->id }}" data-username="{{ $user->name }}"><i class="bi bi-cash"></i> Op. Balance</button>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4">No users found</div>
            @endforelse
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="bi bi-person-plus-fill me-1"></i> Add User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="myform" action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="edit_id" id="id" />
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="name" />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" class="form-control" id="email" />
                        </div>
                        <div class="mb-3">
                            <label for="user_branch_id" class="form-label">Assigned Branch</label>
                            <select name="branch_id" id="user_branch_id" class="form-select">
                                <option value="">Main Branch (Default)</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Leave blank to keep unchanged on edit" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary save-btn" form="userForm" value="Save User">
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Roles Modal -->
    <div class="modal fade" id="edit-role-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-shield-lock-fill me-1"></i> Edit User Roles</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('users.update.roles') }}" method="POST" id="roleForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit-role-id" />
                        <div class="mb-2">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" readonly class="form-control" id="role-modal-name" />
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text" readonly class="form-control" id="role-modal-email" />
                        </div>
                        <label class="form-label fw-bold mb-1">Roles</label>
                        <div id="role-checkbox-container" class="row g-2"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary save-btn">Update Roles</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Opening Balance Modal -->
    <div class="modal fade" id="openingBalanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-cash-coin me-1"></i> Set Opening Cash</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('users.store_opening_balance') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="ob-user-id" />
                        <div class="mb-2">
                            <label class="form-label fw-bold">User: <span class="text-primary" id="ob-user-name"></span></label>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Opening Balance Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required />
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Note (Optional)</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save Balance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/mycode.js') }}"></script>
    <script>
        // form id attach
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.querySelector('.save-btn');
            if (btn) btn.setAttribute('form', 'userForm');
            const form = document.querySelector('.myform');
            if (form) form.setAttribute('id', 'userForm');
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
            var btn = $(this);
            var id = btn.data('id');
            var name = btn.data('name');
            var email = btn.data('email');
            var branchId = btn.data('branch-id');

            $('#id').val(id);
            $('#name').val(name);
            $('#email').val(email);
            $('#user_branch_id').val(branchId !== undefined && branchId !== null ? branchId : '');
            $('#password').val('');
            $('#exampleModal .modal-title').html('<i class="bi bi-pencil-square me-1"></i> Edit User');
            $("#exampleModal").modal("show");
        });

        $(document).on('click', '#reset-form', function() {
            $('#id').val('');
            $('#name').val('');
            $('#email').val('');
            $('#user_branch_id').val('');
            $('#password').val('');
            $('#exampleModal .modal-title').html('<i class="bi bi-person-plus-fill me-1"></i> Create User');
            $("#exampleModal").modal("show");
        });

        $(document).on('click', '.edit-role-btn', function() {
            var btn = $(this);
            var id = btn.data('id');
            var name = btn.data('name');
            var email = btn.data('email');
            var tr = btn.closest("tr");

            // assigned roles from badges
            let assignedRoles = [];
            if (tr.length) {
                tr.find('td:eq(4) .rp-chip').each(function () {
                    assignedRoles.push($(this).text().trim());
                });
            }

            $('#role-modal-name').val(name);
            $('#role-modal-email').val(email);
            $('#edit-role-id').val(id);
            $('#role-checkbox-container').html('');

            const allRoles = @json($allRoles);
            allRoles.forEach(function (role) {
                let isChecked = assignedRoles.includes(role.name) ? 'checked' : '';
                $('#role-checkbox-container').append(`
                    <div class="form-check d-flex align-items-center mb-1">
                        <input class="form-check-input me-2" type="checkbox" name="roles[]" value="${role.name}" ${isChecked}>
                        <label class="form-check-label pt-1">${role.name}</label>
                    </div>
                `);
            });

            $("#edit-role-modal").modal("show");
        });

        $(document).on('click', '.opening-balance-btn', function() {
            let id = $(this).data('userid');
            let name = $(this).data('username');
            let tr = $(this).closest('tr');
            let amount = tr.find('.opening-balance-val').val();
            let note = tr.find('.opening-balance-note').val();

            $('#ob-user-id').val(id);
            $('#ob-user-name').text(name);
            $('input[name="amount"]').val(amount == 0 ? '' : amount);
            $('textarea[name="note"]').val(note);
            $('#openingBalanceModal').modal('show');
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
                    search: "Search Users:",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        });
    </script>
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success', title: 'Success', text: "{{ session('success') }}",
            timer: 2000, showConfirmButton: false
        });
    </script>
    @endif
@endsection