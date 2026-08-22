 @extends('admin_panel.layout.app')
 @section('content')
<style>
    .ct-page .table-responsive { overflow-x: hidden; }
    .ct-page table#default-datatable { table-layout: fixed; width: 100% !important; }
    .ct-page thead th, .ct-page tbody td { white-space: normal !important; overflow-wrap: anywhere; }

    @media (max-width: 991.98px) {
        .ct-page .table-responsive { overflow: visible; }
        .ct-page table { display: block !important; width: 100% !important; }
        .ct-page thead { display: none !important; }
        .ct-page tbody { display: block !important; }
        .ct-page tbody tr {
            display: grid !important;
            grid-template-columns: 1fr;
            gap: .3rem .5rem;
            background: #fff;
            border: 1px solid #e9edf2;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,.03), 0 2px 6px rgba(0,0,0,.04);
            padding: .55rem .65rem;
            margin-bottom: .55rem;
            overflow: hidden !important;
        }
        .ct-page tbody td {
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
        .ct-page tbody td::before {
            content: attr(data-label) ":";
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #94a3b8;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .ct-page tbody td:nth-child(1) { order: 0; justify-self: start; }
        .ct-page tbody td:nth-child(1)::before { content: none; }
        .ct-page tbody td:nth-child(2) { order: 1; font-weight: 700; color: #0f172a; }
        .ct-page tbody td:nth-child(3) { order: 2; }
        .ct-page tbody td:nth-child(3)::before { content: none; }
        .ct-page tbody td:nth-child(3) .btn { flex: 1 1 auto; min-height: 38px; display: inline-flex; align-items: center; justify-content: center; }
    }
</style>
<div class="main-content ct-page">
    <div class="main-content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Category</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"
                id="reset">Create</button>
        </div>
        <div class="border mt-1 shadow rounded " style="background-color: white;">
            <div class="col-lg-12 m-auto">
   <div class="table-responsive mt-5 mb-5 ">
    <table id="default-datatable" class="table ">
        <thead class="text-center">
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Name</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @foreach ($category as $company)
                <tr>
                    <td class="id" data-label="Id">{{ $company->id }}</td>
                    <td class="name" data-label="Name">{{ $company->name }}</td>
                    <td data-label="Action">
                        <button class="btn btn-primary btn-sm edit-btn"
                            data-url="{{ route('store.category') }}">
                            Edit
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn"
                            data-url="{{ route('delete.category', $company->id) }}"
                            data-msg="Are you sure you want to delete this title"
                            data-method="get"
                            onclick="logoutAndDeleteFunction(this)">
                            Delete
                        </button>
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
    </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add category</h5>
                </div>
                <div class="modal-body">
                    <form class="myform" action="{{ route('store.category') }}" method="POST">
                        @csrf
                        <input type="hidden" name="edit_id" id="id" />
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="name" class="form-control" id="name" />
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary save-btn">
                </div>
                </form>
            </div>
        </div>
    </div> 
<!-- DataTable CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTable JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script  src="{{ asset('assets/js/mycode.js') }}">  </script>
 <script>
    $(document).on('submit', '.myform', function(e) {
        e.preventDefault();
        var formdata = new FormData(this);
        url = $(this).attr('action');
        method = $(this).attr('method');
        $(this).find(':submit').attr('disabled', true);
        myAjax(url, formdata, method);
    });
    $(document).on('click', '.edit-btn', function () {

        var tr = $(this).closest("tr");
        var id = tr.find(".id").text();
        var name = tr.find(".name").text();
        $('#id').val(id);     // Set the ID in the hidden input field
        $('#name').val(name)
        $("#exampleModal").modal("show")


    });
   





</script>
<script>
    $(document).ready(function() {
        $('#default-datatable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "order": [[0, 'desc']],
            "language": {
                "search": "Search Category:",
                "lengthMenu": "Show _MENU_ entries"
            }
        });
    });
</script>

 @endsection
