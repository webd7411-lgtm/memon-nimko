 @extends('admin_panel.layout.app')
 @section('content')
 @include('admin_panel.partials.mgmt_cards')
 <style>
     @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

     .rp-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2rem; }
     .rp-mgmt { --rp-accent: #059669; --rp-accent-2: #047857; }
     .rp-mgmt * { font-family: inherit; }

     .rp-hdr {
         position: relative; overflow: hidden;
         background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
         border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.4rem;
         box-shadow: 0 16px 40px rgba(6, 78, 59, .2);
         display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem;
     }
     .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
     .rp-hdr h2 i { color: #6ee7b7; }
     .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .72); font-size: .85rem; font-weight: 500; }

     .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
     .rp-btn-primary { background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 6px 18px rgba(16, 185, 129, .3); }
     .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(16, 185, 129, .38); color: #fff; }

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
     .rp-mgmt .dataTables_filter input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .12); }
     .rp-mgmt .dataTables_length select { border: 1.5px solid #e9edf2; border-radius: 8px; padding: .3rem .5rem; font-size: .78rem; font-weight: 600; color: #0b1a33; }
     .rp-mgmt .page-link { color: #334155; border: none; border-radius: 8px; margin: 0 2px; font-size: .75rem; font-weight: 700; padding: .3rem .6rem; }
     .rp-mgmt .page-item.active .page-link { background: linear-gradient(135deg, #10b981, #059669); border: none; color: #fff; }

     .rp-mgmt .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(2, 6, 23, .28); }
     .rp-mgmt .modal-header { background: linear-gradient(135deg, #064e3b, #059669); border-bottom: none; }
     .rp-mgmt .modal-header .modal-title { color: #fff; font-weight: 800; font-size: 1.02rem; }
     .rp-mgmt .modal-body { padding: 1.4rem; }
     .rp-mgmt .modal-body .form-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #54657e; }
     .rp-mgmt .modal-body .form-control { border: 1.5px solid #e9edf2; border-radius: 10px; padding: .5rem .8rem; font-size: .85rem; font-weight: 600; color: #0b1a33; }
     .rp-mgmt .modal-body .form-control:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .12); }
     .rp-mgmt .modal-footer { border-top: 1px solid #eef2f7; padding: .9rem 1.4rem; }
     .rp-mgmt .btn { border-radius: 8px; font-size: .72rem; font-weight: 700; letter-spacing: .2px; padding: .32rem .65rem; border: none; transition: all .2s ease; }
     .rp-mgmt .btn:hover { transform: translateY(-1px); }
     .rp-mgmt .btn-primary { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
     .rp-mgmt .btn-secondary { background: #eef2f7; color: #334155; }

     @media (max-width: 575.98px) {
         .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: flex-start; }
         .rp-hdr h2 { font-size: 1.15rem; }
     }
 </style>
 <div class="rp-mgmt">
 <div class="container-fluid px-3 px-md-4 py-3">
    <div class="rp-hdr">
        <div>
            <h2><i class="bi bi-diagram-3-fill"></i> Permissions</h2>
            <p>Permissions that can be granted to roles across the system</p>
        </div>
    </div>
    <div class="rp-card">
        <div class="rp-card-body p-0">
            <div class="table-responsive">
                                 <table id="default-datatable" class="table rp-table mb-0">
                                     <thead class="text-center">
                                         <tr>
                                             <th class="text-center">Id</th>
                                             <th class="text-center">Name</th>
                                         </tr>
                                     </thead>
                                     <tbody class="text-center">
@foreach ($permissions as $permission)
                                          <tr>
                                              <td class="id">
                                                  <input type="hidden" class="edit-id" value="{{ $permission->id }}">
                                                  {{ $permission->id }}
                                              </td>
                                              <td class="name">{{ $permission->name }}</td>
                                          </tr>
                                          @endforeach
                                     </tbody>
                                 </table>
                             </div>

                             {{-- Mobile / Tablet premium cards --}}
                             <div class="rp-ucards" id="permissionCards">
                                 @foreach ($permissions as $permission)
                                 <div class="rp-ucard">
                                     <div class="rp-uc-top">
                                         <span class="rp-uc-av">{{ mb_substr($permission->name, 0, 1) }}</span>
                                         <div class="rp-uc-idf">
                                             <span class="rp-uc-nm name">{{ $permission->name }}</span>
                                         </div>
                                         <span class="rp-uc-ix">#{{ $permission->id }}</span>
                                     </div>
                                     <div class="rp-uc-info">
                                         <span class="bl">Permission Id</span>
                                         <b style="color:var(--rp-accent);">{{ $permission->id }}</b>
                                     </div>
                                 </div>
                                 @endforeach
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="exampleModalLabel">Add Permissions</h5>
             </div>
             <div class="modal-body">
                 <form class="myform" action="{{ route('permissions.store') }}" method="POST">
                     @csrf
                     <input type="hidden" name="edit_id" id="id" />
                     <div class="mb-3">
                         <label for="title" class="form-label">Name</label>
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
 <script src="{{ asset('assets/js/mycode.js') }}"> </script>
 <script>
     $(document).on('submit', '.myform', function(e) {
         // alert("as");
         e.preventDefault();
         var formdata = new FormData(this);
         url = $(this).attr('action');
         method = $(this).attr('method');
         $(this).find(':submit').attr('disabled', true);
         myAjax(url, formdata, method);
     });
     $(document).on('click', '.edit-btn', function() {
         $(".modal-title").text("Edit Permissions");
         var tr = $(this).closest("tr");
         var id = tr.find(".edit-id").val();
         // alert(id);
         var name = tr.find(".name").text();
         // var address = tr.find(".address").text();
         // var number = tr.find(".number").text();
         // var email = tr.find(".email").text().trim(); 
         $('#id').val(id);
         $('#name').val(name)
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

     $(document).on('click', '#reset-form', function() {
         // alert("sd");
         // Manually clear inputs
         $('#id').val('');
         $('#name').val('');
         $("#exampleModal").modal("show")
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
 <script>
     $(document).ready(function() {
         $('#default-datatable').DataTable({
             "pageLength": 10,
             "lengthMenu": [5, 10, 25, 50, 100],
             "order": [
                 [0, 'desc']
             ],
             "language": {
                 "search": "Search Permissions:",
                 "lengthMenu": "Show _MENU_ entries"
             }
         });
     });
 </script>

 @endsection