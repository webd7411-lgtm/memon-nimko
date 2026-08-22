 @extends('admin_panel.layout.app')
 @section('content')
<style>
    .br-page .table-responsive { overflow-x: hidden; }
    .br-page table#default-datatable { table-layout: fixed; width: 100% !important; }
    .br-page thead th, .br-page tbody td { white-space: normal !important; overflow-wrap: anywhere; }

    @media (max-width: 991.98px) {
        .br-page .table-responsive { overflow: visible; }
        .br-page table { display: block !important; width: 100% !important; }
        .br-page thead { display: none !important; }
        .br-page tbody { display: block !important; }
        .br-page tbody tr {
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
        .br-page tbody td {
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
        .br-page tbody td::before {
            content: attr(data-label) ":";
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #94a3b8;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .br-page tbody td:nth-of-type(1) { order: 0; justify-self: start; }
        .br-page tbody td:nth-of-type(1)::before { content: none; }
        .br-page tbody td:nth-of-type(2) { order: 1; grid-column: 1 / -1; font-weight: 700; color: #0f172a; }
        .br-page tbody td:nth-of-type(2)::before { content: none; }
        .br-page tbody td:nth-of-type(3) { order: 2; }
        .br-page tbody td:nth-of-type(4) { order: 3; }
        .br-page tbody td:nth-of-type(5) { order: 4; }
        .br-page tbody td:nth-of-type(6) { order: 5; grid-column: 1 / -1; }
        .br-page tbody td:nth-of-type(6)::before { content: none; }
        .br-page tbody td:nth-of-type(6) .btn { flex: 1 1 auto; min-height: 38px; display: inline-flex; align-items: center; justify-content: center; }
    }
</style>
<div class="main-content br-page">
    <div class="main-content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Branch</h3>
            <button type="button" class="btn btn-primary"
                id="reset-form">Create</button>
        </div>
        <div class="border mt-1 shadow rounded " style="background-color: white;">
            <div class="col-lg-12 m-auto">
   <div class="table-responsive mt-5 mb-5 ">
    <table id="default-datatable" class="table ">
        <thead class="text-center">
            <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Name</th>
                <th class="text-center">Address</th>
                <th class="text-center">Number</th>
                <th class="text-center">Email</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @foreach ($branches as $branch)
                <tr>
                    <span class="d-none" id="edit-id">{{ $branch->id }}</span>
                    <td class="id" data-label="Id">{{ $branch->id }}</td>
                    <td class="name" data-label="Name">{{ $branch->name }}</td>
                    <td class="address" data-label="Address">{{ $branch->address }}</td>
                    <td class="number" data-label="Number">{{ $branch->number }}</td>
                    <td class="email" data-label="Email">{{ $branch->user->email }}</td>
                    <td data-label="Action">
                        <button class="btn btn-primary btn-sm edit-btn"
                            data-url="{{ route('branch.store') }}">
                            Edit
                        </button>
                        <a href="{{ route('branch.delete', $branch->id) }}" class="btn btn-danger btn-sm delete-btn"
                            data-url="{{ route('branch.delete', $branch->id) }}"
                            data-msg="Are you sure you want to delete this Branch"
                            data-method="DELETE"
                            onclick="confirmedBox(this, event)">
                            Delete
                        </a>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Branch</h5>
                </div>
                <div class="modal-body">
                    <form class="myform" action="{{ route('branch.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="edit_id" id="id" />
                        <div class="mb-3">
                            <label for="title" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="name" />
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" id="address" />
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Number</label>
                            <input type="text" name="number" class="form-control" id="number" />
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">User Email</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option selected disabled>Select Email</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->email }}</option>
                                @endforeach
                            </select>
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
        var id = tr.find("#edit-id").text();
        var name = tr.find(".name").text();
        var address = tr.find(".address").text();
        var number = tr.find(".number").text();
        var email = tr.find(".email").text().trim(); 
        $('#id').val(id);
        $('#name').val(name)
        $('#address').val(address)
        $('#number').val(number)
        $('#user_id option').filter(function () {
                return $(this).text().trim() === email;
        }).prop('selected', true);
        $("#exampleModal").modal("show")


    });
    $(document).on('click', '#reset-form', function () {
        // alert("sd");
        // Manually clear inputs
        $('#id').val('');
        $('#name').val('');
        $('#address').val('');
        $('#number').val('');
        $('#user_id').val(''); // If dropdown
        $("#exampleModal").modal("show")
    });
   

    function confirmedBox(element, event) {
        event.preventDefault(); // Stop immediate redirect

        const message = element.getAttribute('data-msg') || 'Are you sure?';
        const url = element.getAttribute('href');

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
                // Redirect manually after confirmation
                window.location.href = url;
            }
        });
    }
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
<script>
    $(document).ready(function() {
        $('#default-datatable').DataTable({
            "pageLength": 10,
            "lengthMenu": [5, 10, 25, 50, 100],
            "order": [[0, 'desc']],
            "language": {
                "search": "Search Branch:",
                "lengthMenu": "Show _MENU_ entries"
            }
        });
    });
</script>

 @endsection
