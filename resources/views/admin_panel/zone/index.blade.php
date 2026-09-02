@extends('admin_panel.layout.app')
@section('content')
@include('admin_panel.partials.mgmt_cards')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .rp-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2rem; }
    .rp-mgmt { --rp-accent: #f59e0b; --rp-accent-2: #d97706; }
    .rp-mgmt * { font-family: inherit; }

    .rp-hdr {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #713f12 0%, #f59e0b 100%);
        border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.4rem;
        box-shadow: 0 16px 40px rgba(217, 119, 6, .25);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem;
    }
    .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
    .rp-hdr h2 i { color: #fcd34d; }
    .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .8); font-size: .85rem; font-weight: 500; }

    .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
    .rp-btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; box-shadow: 0 6px 18px rgba(217, 119, 6, .35); }
    .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(217, 119, 6, .42); color: #fff; }

    .rp-card { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05); overflow: hidden; }
    .rp-card-body { padding: 1.4rem; }

    .rp-mgmt table.rp-table.dataTable { border-collapse: separate; border-spacing: 0; font-size: .82rem; }
    .rp-mgmt .rp-table thead th { background: #0f172a !important; color: #94a3b8 !important; font-size: .66rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; padding: .65rem .7rem; border: none; border-bottom: 2px solid #0f172a !important; text-align: left; white-space: nowrap; }
    .rp-mgmt .rp-table thead th:first-child { border-radius: 10px 0 0 0; }
    .rp-mgmt .rp-table thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-mgmt .rp-table tbody td { padding: .6rem .7rem; border: none; border-bottom: 1px solid #f1f4f9 !important; vertical-align: middle; color: #1e293b; }
    .rp-mgmt .rp-table tbody tr:nth-of-type(odd) td { background: transparent !important; }
    .rp-mgmt .rp-table tbody tr:hover td { background: #fafbfc !important; }
    .rp-mgmt .rp-table tbody tr:last-child td { border-bottom: none !important; }

    .rp-mgmt .dataTables_wrapper { padding: .25rem; }
    .rp-mgmt .dataTables_length, .rp-mgmt .dataTables_info, .rp-mgmt .dataTables_filter { font-size: .78rem; font-weight: 600; color: #54657e; }
    .rp-mgmt .dataTables_filter input { border: 1.5px solid #e9edf2; border-radius: 9px; padding: .35rem .6rem; font-size: .8rem; outline: none; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .dataTables_filter input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, .12); }
    .rp-mgmt .dataTables_length select { border: 1.5px solid #e9edf2; border-radius: 8px; padding: .3rem .5rem; font-size: .78rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .page-link { color: #334155; border: none; border-radius: 8px; margin: 0 2px; font-size: .75rem; font-weight: 700; padding: .3rem .6rem; }
    .rp-mgmt .page-item.active .page-link { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; color: #fff; }

    .rp-mgmt .btn { border-radius: 8px; font-size: .72rem; font-weight: 700; letter-spacing: .2px; padding: .32rem .65rem; border: none; transition: all .2s ease; }
    .rp-mgmt .btn:hover { transform: translateY(-1px); }
    .rp-mgmt .btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
    .rp-mgmt .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
    .rp-mgmt .btn-secondary { background: #eef2f7; color: #334155; }

    .rp-mgmt .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(2, 6, 23, .28); }
    .rp-mgmt .modal-header { background: linear-gradient(135deg, #713f12, #f59e0b); border-bottom: none; }
    .rp-mgmt .modal-header .modal-title { color: #fff; font-weight: 800; font-size: 1.02rem; }
    .rp-mgmt .modal-body { padding: 1.4rem; }
    .rp-mgmt .modal-body .form-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #54657e; }
    .rp-mgmt .modal-body .form-control { border: 1.5px solid #e9edf2; border-radius: 10px; padding: .5rem .8rem; font-size: .85rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .modal-body .form-control:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, .12); }
    .rp-mgmt .modal-footer { border-top: 1px solid #eef2f7; padding: .9rem 1.4rem; }

    .rp-mgmt .rp-chip { display: inline-flex; align-items: center; font-size: .7rem; font-weight: 700; letter-spacing: .3px; padding: .3rem .65rem; border-radius: 7px; }
    .rp-mgmt .rp-chip.bg-amber { background: #fef3c7 !important; color: #b45309 !important; }

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
                <h2><i class="bi bi-signpost-split-fill"></i> Zones</h2>
                <p>Manage zones and territories used across the system</p>
            </div>
            <button type="button" class="rp-btn rp-btn-primary" data-bs-toggle="modal" data-bs-target="#createModal" id="reset">
                <i class="bi bi-plus-lg"></i> Create Zone
            </button>
        </div>

        <div class="rp-card">
            <div class="rp-card-body p-0">
                <div class="table-responsive">
                    <table id="default-datatable" class="table rp-table mb-0">
                        <thead class="text-center">
                            <tr>
                                <th class="text-center">Id</th>
                                <th class="text-center">Zone</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($zones as $zone)
                            <tr id="row-{{ $zone->id }}">
                                <td>
                                    <span class="rp-chip bg-amber">{{ $zone->id }}</span>
                                </td>
                                <td class="fw-bold">{{ $zone->zone }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-btn p-1" data-id="{{ $zone->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-btn p-1"
                                        data-id="{{ $zone->id }}"
                                        data-url="{{ route('zone.delete', $zone->id) }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Mobile / Tablet premium cards --}}
        <div class="rp-ucards" id="zoneCards">
            @foreach ($zones as $zone)
            <div class="rp-ucard" id="mrow-{{ $zone->id }}">
                <div class="rp-uc-top">
                    <span class="rp-uc-av">{{ mb_substr($zone->zone, 0, 1) }}</span>
                    <div class="rp-uc-idf">
                        <span class="rp-uc-nm">{{ $zone->zone }}</span>
                    </div>
                    <span class="rp-uc-ix">#{{ $zone->id }}</span>
                </div>
                <div class="rp-uc-acts">
                    <button class="btn btn-primary edit-btn" data-id="{{ $zone->id }}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-danger delete-btn"
                        data-id="{{ $zone->id }}"
                        data-url="{{ route('zone.delete', $zone->id) }}">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="myform" action="{{ route('zone.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-1"></i> Add Zone</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Zone Name</label>
                            <input type="text" name="zone" class="form-control" required placeholder="e.g. Zone A" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary save-btn px-4" value="Save Zone">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="editform" action="{{ route('zone.store') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id" />
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Edit Zone</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Zone Name</label>
                            <input type="text" name="zone" class="form-control" id="edit_zone" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary save-btn px-4" value="Update Zone">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).ready(function() {
        $('#default-datatable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [[0, 'desc']],
            language: {
                search: "Search Zones:",
                lengthMenu: "Show _MENU_ entries"
            }
        });

        // CREATE FORM
        $('.myform').submit(function(e) {
            e.preventDefault();
            var form = this;
            var url = $(form).attr('action');
            $.ajax({
                type: 'POST',
                url: url,
                data: new FormData(form),
                contentType: false,
                processData: false,
                success: function() {
                    $('#createModal').modal('hide');
                    Swal.fire('Success!', 'Zone created successfully.', 'success').then(() => location.reload());
                }
            });
        });

        // EDIT MODAL DATA
        $('.edit-btn').click(function() {
            var id = $(this).data('id');
            $.get("{{ url('zones/edit') }}/" + id, function(res) {
                $('#edit_id').val(res.id);
                $('#edit_zone').val(res.zone);
                $('#editModal').modal('show');
            });
        });

        // EDIT FORM
        $('.editform').submit(function(e) {
            e.preventDefault();
            var form = this;
            var url = $(form).attr('action');
            $.ajax({
                type: 'POST',
                url: url,
                data: new FormData(form),
                contentType: false,
                processData: false,
                success: function() {
                    $('#editModal').modal('hide');
                    Swal.fire('Updated!', 'Zone updated successfully.', 'success').then(() => location.reload());
                }
            });
        });

        // DELETE FUNCTION
        $('.delete-btn').click(function() {
            var id = $(this).data('id');
            var url = $(this).data('url');
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'DELETE',
                        url: url,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            $('#row-' + id).remove();
                            $('#mrow-' + id).remove();
                            Swal.fire('Deleted!', 'Zone has been deleted.', 'success');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection