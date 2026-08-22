@extends('admin_panel.layout.app')
@section('content')
@include('admin_panel.partials.mgmt_cards')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .rp-mgmt { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2rem; }
    .rp-mgmt { --rp-accent: #64748b; --rp-accent-2: #475569; }
    .rp-mgmt * { font-family: inherit; }

    .rp-hdr {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #0f172a 0%, #475569 100%);
        border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.4rem;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .25);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem;
    }
    .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
    .rp-hdr h2 i { color: #cbd5e1; }
    .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .72); font-size: .85rem; font-weight: 500; }

    .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
    .rp-btn-ghost { background: rgba(255, 255, 255, .12); border: 1px solid rgba(255, 255, 255, .3); color: #fff; }
    .rp-btn-ghost:hover { background: rgba(255, 255, 255, .22); color: #fff; }

    .rp-card { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05); overflow: hidden; }
    .rp-card-body { padding: 1.4rem; }

    .rp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: .9rem; margin-bottom: 1.4rem; }
    .rp-stat { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; padding: .9rem 1.1rem; display: flex; align-items: center; gap: .8rem; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 6px 18px rgba(0, 0, 0, .04); }
    .rp-stat-ic { flex: 0 0 auto; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); color: #fff; box-shadow: 0 4px 12px rgba(100, 116, 139, .3); }
    .rp-stat-tx .t { font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .4px; color: #64748b; }
    .rp-stat-tx .v { font-size: 1.05rem; font-weight: 800; color: #0f172a; }

    .rp-mgmt table.rp-table.dataTable { border-collapse: separate; border-spacing: 0; font-size: .8rem; }
    .rp-mgmt .rp-table thead th { background: #0f172a !important; color: #94a3b8 !important; font-size: .64rem; font-weight: 700; text-transform: uppercase; letter-spacing: .45px; padding: .65rem .7rem; border: none; border-bottom: 2px solid #0f172a !important; text-align: left; white-space: nowrap; }
    .rp-mgmt .rp-table thead th:first-child { border-radius: 10px 0 0 0; }
    .rp-mgmt .rp-table thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-mgmt .rp-table tbody td { padding: .55rem .7rem; border: none; border-bottom: 1px solid #f1f4f9 !important; vertical-align: middle; color: #334155; }
    .rp-mgmt .rp-table tbody tr:nth-of-type(odd) td { background: transparent !important; }
    .rp-mgmt .rp-table tbody tr:hover td { background: #fafbfc !important; }
    .rp-mgmt .rp-table tbody tr:last-child td { border-bottom: none !important; }
    .rp-mgmt .rp-table .ci-nm { font-weight: 700; color: #0f172a; }

    .rp-mgmt .dataTables_wrapper { padding: .25rem; }
    .rp-mgmt .dataTables_length, .rp-mgmt .dataTables_info, .rp-mgmt .dataTables_filter { font-size: .78rem; font-weight: 600; color: #54657e; }
    .rp-mgmt .dataTables_filter input { border: 1.5px solid #e9edf2; border-radius: 9px; padding: .35rem .6rem; font-size: .8rem; outline: none; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .dataTables_filter input:focus { border-color: var(--rp-accent); box-shadow: 0 0 0 3px rgba(100, 116, 139, .12); }
    .rp-mgmt .dataTables_length select { border: 1.5px solid #e9edf2; border-radius: 8px; padding: .3rem .5rem; font-size: .78rem; font-weight: 600; color: #0b1a33; }
    .rp-mgmt .page-link { color: #334155; border: none; border-radius: 8px; margin: 0 2px; font-size: .75rem; font-weight: 700; padding: .3rem .6rem; }
    .rp-mgmt .page-item.active .page-link { background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); border: none; color: #fff; }

    .rp-mgmt .btn { border-radius: 8px; font-size: .7rem; font-weight: 700; letter-spacing: .2px; padding: .32rem .6rem; border: none; transition: all .2s ease; }
    .rp-mgmt .btn:hover { transform: translateY(-1px); }
    .rp-mgmt .btn-primary { background: linear-gradient(135deg, var(--rp-accent), var(--rp-accent-2)); color: #fff; }

    .rp-mgmt .rp-chip { display: inline-flex; align-items: center; font-size: .68rem; font-weight: 700; letter-spacing: .3px; padding: .3rem .6rem; border-radius: 7px; }
    .rp-mgmt .rp-chip.bg-slate { background: #e2e8f0 !important; color: #334155 !important; }
    .rp-mgmt .rp-chip.bg-rose { background: #fee2e2 !important; color: #b91c1c !important; }

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
                <h2><i class="bi bi-person-dash-fill"></i> Inactive Customers</h2>
                <p>Deactivated customer records</p>
            </div>
            <a href="{{ route('customers.index') }}" class="rp-btn rp-btn-ghost">
                <i class="bi bi-arrow-left"></i> Back to Active List
            </a>
        </div>

        <div class="rp-stats">
            <div class="rp-stat">
                <span class="rp-stat-ic"><i class="bi bi-person-x"></i></span>
                <div class="rp-stat-tx">
                    <div class="t">Inactive Customers</div>
                    <div class="v">{{ $customers->count() }}</div>
                </div>
            </div>
        </div>

        <div class="rp-card">
            <div class="rp-card-body p-0">
                <div class="table-responsive">
                    <table id="default-datatable" class="table rp-table mb-0">
                        <thead>
                            <tr>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Zone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td><span class="rp-chip bg-slate">{{ $customer->customer_id }}</span></td>
                                <td class="ci-nm"><i class="bi bi-person-x me-2 text-muted"></i>{{ $customer->customer_name }}</td>
                                <td><i class="bi bi-telephone me-2 text-muted"></i>{{ $customer->mobile }}</td>
                                <td><span class="rp-chip bg-rose">{{ $customer->zone }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rp-ucards" id="inactiveCustomerCards">
            @forelse($customers as $customer)
            <div class="rp-ucard">
                <div class="rp-uc-top">
                    <span class="rp-uc-av"><i class="bi bi-person-x"></i></span>
                    <div class="rp-uc-idf">
                        <span class="rp-uc-nm">{{ $customer->customer_name }}</span>
                        <span class="rp-uc-em">{{ $customer->mobile ?? '—' }}</span>
                    </div>
                    <span class="rp-uc-ix">#{{ $customer->customer_id }}</span>
                </div>
                <div class="rp-uc-info">
                    <span>Zone: {{ $customer->zone ?? '—' }}</span>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4">No inactive customers found</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#default-datatable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            order: [[0, 'desc']],
            language: {
                search: "Search Customers:",
                lengthMenu: "Show _MENU_ entries"
            }
        });
    });
</script>
@endsection