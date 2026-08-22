@extends('admin_panel.layout.app')
@section('content')
<style>
    .so-page .table-responsive { overflow-x: hidden; }
    .so-page table#default-datatable { table-layout: fixed; width: 100% !important; }
    .so-page thead th, .so-page tbody td { white-space: normal !important; overflow-wrap: anywhere; }

    @media (max-width: 991.98px) {
        .so-page .table-responsive { overflow: visible; }
        .so-page table { display: block !important; width: 100% !important; }
        .so-page thead { display: none !important; }
        .so-page tbody { display: block !important; }
        .so-page tbody tr {
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
        .so-page tbody td {
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
        .so-page tbody td::before {
            content: attr(data-label) ":";
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #94a3b8;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .so-page tbody td:nth-child(1) { order: 0; justify-self: start; }
        .so-page tbody td:nth-child(1)::before { content: none; }
        .so-page tbody td:nth-child(2) { order: 1; font-weight: 700; color: #0f172a; }
        .so-page tbody td:nth-child(2)::before { content: none; }
        .so-page tbody td:nth-child(3) { order: 2; }
        .so-page tbody td:nth-child(4) { order: 3; }
        .so-page tbody td:nth-child(5) { order: 4; grid-column: 1 / -1; }
        .so-page tbody td:nth-child(5)::before { content: none; }
        .so-page tbody td:nth-child(5) .btn { flex: 1 1 auto; min-height: 38px; display: inline-flex; align-items: center; justify-content: center; }
    }
</style>

    <div class="main-content so-page">
        <div class="main-content-inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3>Sales Officers</h3>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createModal" id="reset">
                                Create
                            </button>
                        </div>

                        <div class="border mt-1 shadow rounded" style="background-color: white;">
                            <div class="col-lg-12 m-auto">
                                <div class="table-responsive mt-5 mb-5">
                                    <table id="default-datatable" class="table">
                                        <thead class="text-center">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Name Urdu</th>
                                                <th>Mobile</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach ($officers as $officer)
                                                <tr id="row-{{ $officer->id }}">
                                                    <td data-label="ID">{{ $officer->id }}</td>
                                                    <td data-label="Name">{{ $officer->name }}</td>
                                                    <td data-label="Name Urdu">{{ $officer->name_urdu }}</td>
                                                    <td data-label="Mobile">{{ $officer->mobile }}</td>
                                                    <td data-label="Action">
                                                        <button class="btn btn-primary btn-sm edit-btn"
                                                            data-id="{{ $officer->id }}">Edit</button>
                                                        <button class="btn btn-danger btn-sm delete-btn"
                                                            data-id="{{ $officer->id }}">Delete</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="create-form" action="{{ route('sales-officer.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Sales Officer</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نام</label>
                            <input type="text" name="name_urdu" class="form-control text-end" dir="rtl" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" class="form-control" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" value="Save">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="edit-form" action="{{ route('sales-officer.store') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Sales Officer</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نام</label>
                            <input type="text" name="name_urdu" id="edit_name_urdu" class="form-control text-end" dir="rtl" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile" id="edit_mobile" class="form-control" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" value="Update">
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')


   
    <script>
        $(document).ready(function () {
            $('#default-datatable').DataTable({
                pageLength: 10,
                order: [[0, 'desc']]
            });

            // CREATE
            $('.create-form').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#createModal').modal('hide');
                        Swal.fire('Success', 'Sales Officer Created', 'success').then(() => location.reload());
                    }
                });
            });

            // LOAD EDIT DATA
            $('.edit-btn').click(function () {
                let id = $(this).data('id');
                $.get("{{ url('sales-officers/edit') }}/" + id, function (data) {
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_name_urdu').val(data.name_urdu);
                    $('#edit_mobile').val(data.mobile);
                    $('#editModal').modal('show');
                });
            });

            // UPDATE
            $('.edit-form').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function () {
                        $('#editModal').modal('hide');
                        Swal.fire('Updated', 'Sales Officer Updated', 'success').then(() => location.reload());
                    }
                });
            });

            // DELETE
            $('.delete-btn').click(function () {
                let id = $(this).data('id');
                let url = `/sales-officers/${id}`; // dynamically build route
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You can't undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                $('#row-' + id).remove();
                                Swal.fire('Deleted!', 'Sales Officer has been deleted.', 'success');
                            },
                            error: function (xhr) {
                                Swal.fire('Error', 'Delete failed. Please try again.', 'error');
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            });

        });
    </script>

    @endsection