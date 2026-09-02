 @extends('admin_panel.layout.app')
 @section('content')
 @include('admin_panel.partials.mgmt_cards')
 <style>
     @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

     .rp-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2rem; }
     .rp-mgmt { --rp-accent: #4f46e5; --rp-accent-2: #4338ca; }
     .rp-mgmt * { font-family: inherit; }

     .rp-hdr {
         position: relative; overflow: hidden;
         background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);
         border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.4rem;
         box-shadow: 0 16px 40px rgba(30, 27, 75, .2);
         display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem;
     }
     .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
     .rp-hdr h2 i { color: #a5b4fc; }
     .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .72); font-size: .85rem; font-weight: 500; }

     .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
     .rp-btn-primary { background: linear-gradient(135deg, #4f46e5, #4338ca); color: #fff; box-shadow: 0 6px 18px rgba(79, 70, 229, .3); }
     .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(79, 70, 229, .38); color: #fff; }

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

     .rp-chip { display: inline-flex; align-items: center; font-size: .7rem; font-weight: 700; letter-spacing: .3px; padding: .3rem .65rem; border-radius: 7px; margin-right: .3rem; }
     .rp-mgmt .rp-chip.bg-success { background: #dcfce7 !important; color: #15803d !important; }
     .rp-mgmt .rp-chip.bg-danger { background: #fee2e2 !important; color: #b91c1c !important; }

     .rp-mgmt .btn { border-radius: 8px; font-size: .72rem; font-weight: 700; letter-spacing: .2px; padding: .32rem .65rem; border: none; transition: all .2s ease; }
     .rp-mgmt .btn:hover { transform: translateY(-1px); }
     .rp-mgmt .btn-info { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; }
     .rp-mgmt .btn-primary { background: linear-gradient(135deg, #4f46e5, #4338ca); color: #fff; }
     .rp-mgmt .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
     .rp-mgmt .btn-secondary { background: #eef2f7; color: #334155; }
     .rp-mgmt .btn-light { background: #eef2f7; color: #334155; }

     .rp-mgmt .dataTables_wrapper { padding: .25rem; }
     .rp-mgmt .dataTables_length, .rp-mgmt .dataTables_info, .rp-mgmt .dataTables_filter { font-size: .78rem; font-weight: 600; color: #54657e; }
     .rp-mgmt .dataTables_filter input { border: 1.5px solid #e9edf2; border-radius: 9px; padding: .35rem .6rem; font-size: .8rem; outline: none; font-weight: 600; color: #0b1a33; }
     .rp-mgmt .dataTables_filter input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, .12); }
     .rp-mgmt .dataTables_length select { border: 1.5px solid #e9edf2; border-radius: 8px; padding: .3rem .5rem; font-size: .78rem; font-weight: 600; color: #0b1a33; }
     .rp-mgmt .page-link { color: #334155; border: none; border-radius: 8px; margin: 0 2px; font-size: .75rem; font-weight: 700; padding: .3rem .6rem; }
     .rp-mgmt .page-item.active .page-link { background: linear-gradient(135deg, #4f46e5, #4338ca); border: none; color: #fff; }

     .rp-mgmt .modal-content { border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 24px 60px rgba(2, 6, 23, .28); }
     .rp-mgmt .modal-header { background: linear-gradient(135deg, #1e1b4b, #4338ca); border-bottom: none; }
     .rp-mgmt .modal-header .modal-title { color: #fff; font-weight: 800; font-size: 1.02rem; }
     .rp-mgmt .modal-body { padding: 1.4rem; }
     .rp-mgmt .modal-body .form-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #54657e; }
     .rp-mgmt .modal-body .form-control { border: 1.5px solid #e9edf2; border-radius: 10px; padding: .5rem .8rem; font-size: .85rem; font-weight: 600; color: #0b1a33; }
     .rp-mgmt .modal-body .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, .12); }
     .rp-mgmt .modal-footer { border-top: 1px solid #eef2f7; padding: .9rem 1.4rem; }
/* ── Permission Modal Header ── */
      .perm-modal-hdr { background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%); border-bottom: none; padding: 1.2rem 1.6rem; }
      .perm-modal-hdr-left { display: flex; align-items: center; gap: .8rem; }
      .perm-modal-hdr-icon { font-size: 1.6rem; color: #a5b4fc; }
      .perm-modal-hdr .modal-title { color: #fff; font-weight: 800; font-size: 1.1rem; margin: 0; }
      .perm-modal-subtitle { color: rgba(255,255,255,.65); font-size: .78rem; font-weight: 500; margin: 2px 0 0; }

      /* ── Modal Body ── */
      /* ── Modal Body ── */
      #edit-permission-modal .modal-content { height: 100%; display: flex; flex-direction: column; }
      #edit-permission-modal .modal-dialog.modal-fullscreen { display: flex; }
      .perm-modal-body { padding: 1.4rem 1.6rem; background: #f1f4f9; flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
      .perm-role-name-box { margin-bottom: 1.2rem; flex-shrink: 0; }
      .perm-role-name-box .form-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #54657e; }
      .perm-role-name-box .form-control { border: 1.5px solid #e9edf2; border-radius: 10px; padding: .5rem .8rem; font-size: .85rem; font-weight: 600; color: #0b1a33; background: #fff; }

      /* ── Toolbar ── */
      .perm-toolbar { display: flex; align-items: center; flex-wrap: wrap; gap: .8rem; margin-bottom: 1rem; flex-shrink: 0; }
      .perm-toolbar-label { display: flex; align-items: center; gap: .45rem; font-size: .88rem; font-weight: 800; color: #0b1a33; }
      .perm-toolbar-label i { font-size: 1.05rem; color: #4f46e5; }
      .perm-toolbar-actions { display: flex; align-items: center; gap: .5rem; margin-left: auto; flex-wrap: wrap; }

      .perm-search-box { position: relative; display: flex; align-items: center; }
      .perm-search-box i { position: absolute; left: .7rem; font-size: .85rem; color: #94a3b8; pointer-events: none; }
      .perm-search-box input { border: 1.5px solid #e9edf2; border-radius: 10px; padding: .5rem .8rem .5rem 2.2rem; font-size: .82rem; font-weight: 600; color: #0b1a33; width: 240px; background: #fff; transition: border-color .2s, box-shadow .2s; }
      .perm-search-box input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
      .perm-search-box input::placeholder { color: #94a3b8; font-weight: 500; }

      .perm-action-btn { display: inline-flex; align-items: center; gap: .35rem; border: none; border-radius: 9px; padding: .45rem .85rem; font-size: .78rem; font-weight: 700; cursor: pointer; transition: all .2s ease; white-space: nowrap; }
      .perm-action-select { background: #eef2ff; color: #4f46e5; border: 1.5px solid #c7d2fe; }
      .perm-action-select:hover { background: #4f46e5; color: #fff; border-color: #4f46e5; }
      .perm-action-remove { background: #fff; color: #64748b; border: 1.5px solid #e2e8f0; }
      .perm-action-remove:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }

      /* ── Groups Grid ── */
      .perm-groups-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem; flex: 1; min-height: 0; overflow-y: auto; padding-right: .4rem; }
      @media (max-width: 991.98px) { .perm-groups-grid { max-height: none; } }

      /* ── Group Card ── */
      .perm-group { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; overflow: hidden; transition: box-shadow .2s ease; }
      .perm-group:hover { box-shadow: 0 4px 16px rgba(0,0,0,.06); }
      .perm-group-header { display: flex; align-items: center; gap: .6rem; padding: .65rem .9rem; background: #f8fafc; border-bottom: 1px solid #eef2f7; }
      .perm-group-check { width: 16px; height: 16px; accent-color: #4f46e5; cursor: pointer; flex-shrink: 0; }
      .perm-group-title { font-size: .82rem; font-weight: 800; color: #0b1a33; flex: 1; }
      .perm-group-count { font-size: .68rem; font-weight: 700; color: #4f46e5; background: #eef2ff; padding: .15rem .55rem; border-radius: 999px; flex-shrink: 0; }
      .perm-group-body { padding: .6rem .7rem .7rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: .35rem .5rem; }

      /* ── Permission Item ── */
      .perm-item { display: flex; align-items: center; gap: .5rem; padding: .45rem .6rem; border-radius: 8px; cursor: pointer; transition: background .15s ease; user-select: none; }
      .perm-item:hover { background: #f1f5f9; }
      .perm-item input { width: 15px; height: 15px; accent-color: #4f46e5; cursor: pointer; flex-shrink: 0; }
      .perm-item span { font-size: .78rem; font-weight: 600; color: #1e293b; }
      .perm-item.perm-hidden { display: none; }

      /* ── Group hidden when all children filtered out ── */
      .perm-group.perm-group-hidden { display: none; }

      /* ── Footer ── */
      .perm-modal-footer { border-top: 1px solid #e2e8f0; padding: .9rem 1.6rem; background: #fff; }
      .perm-btn-cancel { background: #f1f5f9; color: #475569; border: 1.5px solid #e2e8f0; font-weight: 700; font-size: .82rem; border-radius: 10px; padding: .5rem 1.1rem; }
      .perm-btn-cancel:hover { background: #e2e8f0; color: #1e293b; }
      .perm-btn-save { background: linear-gradient(135deg, #4f46e5, #4338ca); color: #fff; font-weight: 700; font-size: .82rem; border-radius: 10px; padding: .5rem 1.2rem; box-shadow: 0 4px 14px rgba(79,70,229,.3); border: none; }
      .perm-btn-save:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(79,70,229,.38); color: #fff; }

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
            <h2><i class="bi bi-shield-lock-fill"></i> Roles</h2>
            <p>Manage roles and the permissions granted to them</p>
        </div>
        <button type="button" class="rp-btn rp-btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" id="reset-form">
            <i class="bi bi-plus-lg"></i> Create Role
        </button>
    </div>
    <div class="rp-card">
        <div class="rp-card-body p-0">
            <div class="table-responsive">
                                 <table id="default-datatable" class="table rp-table mb-0">
                                     <thead class="text-center">
                                         <tr>
                                             <th class="text-center">Id</th>
                                             <th class="text-center">Name</th>
                                             <th class="text-center">Permissions</th>
                                             <th class="text-center">Action</th>
                                             <th class="text-center d-none">Action</th>
                                         </tr>
                                     </thead>
                                     <tbody class="text-center">
                                         @foreach ($roles as $role)
                                         <tr>
                                             <span class="d-none" id="edit-id">{{ $role->id }}</span>
                                             <td class="d-none">
                                                 <input type="hidden" class="edit-id" value="{{ $role->id }}">
                                             </td>
                                             <td class="id">{{ $role->id }}</td>
                                             <td class="name">
                                                 {{ $role->name }}
                                             </td>
                                             <td class="rp-uc-roles">
                                                 @forelse ($role->getPermissionNames() as $permission)
                                                 <span class="badge bg-success rp-chip mb-2">{{ $permission }}</span>
                                                 @empty
                                                 <span class="badge bg-danger rp-chip">No Permission Assigned</span>
                                                 @endforelse
                                             </td>
                                             <td>
                                                 <button class="btn btn-info btn-sm edit-permission-btn p-1">
                                                     Edit Permissions
                                                 </button>
                                                 <button class="btn btn-primary btn-sm edit-btn p-1"
                                                     data-url="{{ route('roles.store') }}">
                                                     Edit
                                                 </button>
                                                 <a href="{{ route('roles.delete', $role->id) }}" class="btn btn-danger btn-sm delete-btn p-1"
                                                     data-url="{{ route('roles.delete', $role->id) }}"
                                                     data-msg="Are you sure you want to delete this Role"
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

                         {{-- Mobile / Tablet premium cards --}}
                         <div class="rp-ucards" id="roleCards">
                             @foreach ($roles as $role)
                             <div class="rp-ucard">
                                 <div class="rp-uc-top">
                                     <span class="rp-uc-av">{{ mb_substr($role->name, 0, 1) }}</span>
                                     <div class="rp-uc-idf">
                                         <span class="rp-uc-nm name">{{ $role->name }}</span>
                                     </div>
                                     <span class="rp-uc-ix">#{{ $role->id }}</span>
                                 </div>
                                 <div class="rp-uc-body">
                                     <div class="rp-uc-roles">
                                         @forelse ($role->getPermissionNames() as $permission)
                                         <span class="badge bg-success rp-chip">{{ $permission }}</span>
                                         @empty
                                         <span class="badge bg-danger rp-chip">No Permission Assigned</span>
                                         @endforelse
                                     </div>
                                     <input type="hidden" class="edit-id" value="{{ $role->id }}">
                                 </div>
                                 <div class="rp-uc-acts">
                                     <button class="btn btn-info btn-sm edit-permission-btn">Edit Permissions</button>
                                     <button class="btn btn-primary btn-sm edit-btn">Edit</button>
                                     <a href="{{ route('roles.delete', $role->id) }}" class="btn btn-danger btn-sm delete-btn"
                                         data-url="{{ route('roles.delete', $role->id) }}"
                                         data-msg="Are you sure you want to delete this Role"
                                         data-method="DELETE"
                                         onclick="confirmedBox(this, event)">
                                         Delete
                                     </a>
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
                 <h5 class="modal-title" id="exampleModalLabel">Add Roles</h5>
             </div>
             <div class="modal-body">
                 <form class="myform" action="{{ route('roles.store') }}" method="POST">
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

 <div class="modal fade" id="edit-permission-modal" tabindex="-1">
     <div class="modal-dialog modal-fullscreen">
         <div class="modal-content">

             <div class="modal-header perm-modal-hdr">
                 <div class="perm-modal-hdr-left">
                     <i class="bi bi-shield-lock-fill perm-modal-hdr-icon"></i>
                     <div>
                         <h5 class="modal-title">Edit Role</h5>
                         <p class="perm-modal-subtitle">Modify name and permission matrix</p>
                     </div>
                 </div>
                 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
             </div>

             <form action="{{ route('roles.update.permission') }}" method="POST" id="permission-form">
                 @csrf

                 <div class="modal-body perm-modal-body">

                     <input type="hidden" name="edit_id" id="edit-role-id" />

                     <div class="perm-role-name-box">
                         <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" id="permission-modal-name" readonly>
                     </div>

                     <div class="perm-toolbar">
                         <div class="perm-toolbar-label">
                             <i class="bi bi-shield-check"></i>
                             <span>Permissions</span>
                         </div>
                         <div class="perm-toolbar-actions">
                             <div class="perm-search-box">
                                 <i class="bi bi-search"></i>
                                 <input type="text" id="perm-search" placeholder="Search permissions..." autocomplete="off">
                             </div>
                             <button type="button" class="perm-action-btn perm-action-select" id="perm-select-all">
                                 <i class="bi bi-check-all"></i> Select All
                             </button>
                             <button type="button" class="perm-action-btn perm-action-remove" id="perm-remove-all">
                                 <i class="bi bi-x-circle"></i> Remove All
                             </button>
                         </div>
                     </div>

                     <div id="permission-checkbox-container" class="perm-groups-grid"></div>
                 </div>

                 <div class="modal-footer perm-modal-footer">
                     <button type="button" class="btn btn-light perm-btn-cancel" data-bs-dismiss="modal">
                         <i class="bi bi-x-lg"></i> Cancel
                     </button>
                     <button type="submit" class="btn perm-btn-save">
                         <i class="bi bi-check-lg"></i> Save Changes
                     </button>
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
         e.preventDefault();
         var formdata = new FormData(this);
         url = $(this).attr('action');
         method = $(this).attr('method');
         $(this).find(':submit').attr('disabled', true);
         myAjax(url, formdata, method);
     });
     $(document).on('click', '.edit-btn', function() {

         var box = $(this).closest("tr, .rp-ucard");
         var id = box.find(".edit-id").val();
         var name = box.find(".name").text();
         var address = box.find(".address").text();
         var number = box.find(".number").text();
         var email = box.find(".email").text().trim();
         $('#id').val(id);
         $('#name').val(name.trim())
         $("#exampleModal").modal("show")


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
     const permissionGroups = @json($permissionGroups);

     $(document).on('click', '#reset-form', function() {
         $('#id').val('');
         $('#name').val('');
         $("#exampleModal").modal("show")
     });

     // update Permission
     $(document).on('click', '.edit-permission-btn', function() {
         var box = $(this).closest("tr, .rp-ucard");
         var id = box.find(".edit-id").val();
         var name = box.find(".name").text();

         let assignedPermissions = [];
         box.find('.rp-uc-roles .badge').each(function() {
             assignedPermissions.push($(this).text().trim());
         });

         $('#permission-modal-name').val(name.trim());
         $('#edit-role-id').val(id);
         $('#perm-search').val('');

         $('#permission-checkbox-container').html('');

         permissionGroups.forEach(function(group, gIndex) {
             const checkedCount = group.permissions.filter(p => assignedPermissions.includes(p)).length;
             const groupChecked = (group.permissions.length > 0 && checkedCount === group.permissions.length);

             let html = `
         <div class="perm-group" data-group="${gIndex}">
             <div class="perm-group-header">
                 <input class="perm-group-check" type="checkbox" data-group="${gIndex}" ${groupChecked ? 'checked' : ''}>
                 <span class="perm-group-title">${group.page}</span>
                 <span class="perm-group-count" data-count-for="${gIndex}">${checkedCount} / ${group.permissions.length}</span>
             </div>
             <div class="perm-group-body">
         `;

             group.permissions.forEach(function(perm) {
                 let isChecked = assignedPermissions.includes(perm) ? 'checked' : '';
                 html += `
                 <label class="perm-item">
                     <input class="perm-check" type="checkbox" name="permissions[]" value="${perm}" data-group="${gIndex}" ${isChecked}>
                     <span>${perm}</span>
                 </label>
             `;
             });

             html += `</div></div>`;
             $('#permission-checkbox-container').append(html);
         });

         $("#edit-permission-modal").modal("show");
     });

     // Group checkbox toggles all permissions in that group
     $(document).off('change', '.perm-group-check').on('change', '.perm-group-check', function() {
         const gIndex = $(this).data('group');
         const checked = $(this).prop('checked');
         $('.perm-check[data-group="' + gIndex + '"]').prop('checked', checked);
         updateGroupCount(gIndex);
     });

     // Individual checkbox updates group checkbox & count
     $(document).off('change', '.perm-check').on('change', '.perm-check', function() {
         const gIndex = $(this).data('group');
         updateGroupCount(gIndex);
     });

     function updateGroupCount(gIndex) {
         const total = $('.perm-check[data-group="' + gIndex + '"]').length;
         const checked = $('.perm-check[data-group="' + gIndex + '"]:checked').length;
         $('[data-count-for="' + gIndex + '"]').text(checked + ' / ' + total);
         $('.perm-group-check[data-group="' + gIndex + '"]').prop('checked', total > 0 && checked === total);
     }

     // Select All - global
     $(document).off('click', '#perm-select-all').on('click', '#perm-select-all', function() {
         $('.perm-check:visible').prop('checked', true);
         $('.perm-group-check').each(function() {
             const gIndex = $(this).data('group');
             updateGroupCount(gIndex);
         });
     });

     // Remove All - global
     $(document).off('click', '#perm-remove-all').on('click', '#perm-remove-all', function() {
         $('.perm-check').prop('checked', false);
         $('.perm-group-check').prop('checked', false);
         $('.perm-group-count').each(function() {
             $(this).text('0 / ' + $(this).text().split('/')[1].trim());
         });
     });

     // Search filtering
     $(document).off('input', '#perm-search').on('input', '#perm-search', function() {
         const query = $(this).val().toLowerCase().trim();

         if (!query) {
             $('.perm-item').removeClass('perm-hidden');
             $('.perm-group').removeClass('perm-group-hidden');
             return;
         }

         $('.perm-group').each(function() {
             const $group = $(this);
             let hasVisible = false;

             $group.find('.perm-item').each(function() {
                 const text = $(this).find('span').text().toLowerCase();
                 if (text.includes(query)) {
                     $(this).removeClass('perm-hidden');
                     hasVisible = true;
                 } else {
                     $(this).addClass('perm-hidden');
                 }
             });

             if (hasVisible) {
                 $group.removeClass('perm-group-hidden');
             } else {
                 $group.addClass('perm-group-hidden');
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
 <script>
     $(document).ready(function() {
         $('#default-datatable').DataTable({
             "pageLength": 10,
             "lengthMenu": [5, 10, 25, 50, 100],
             "order": [
                 [0, 'desc']
             ],
             "language": {
                 "search": "Search Roles:",
                 "lengthMenu": "Show _MENU_ entries"
             }
         });
     });
 </script>

 @endsection