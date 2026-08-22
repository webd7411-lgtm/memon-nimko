@extends('admin_panel.layout.app')

@section('content')
<style>
:root {
  --vl-bg: #f1f4f9;
  --vl-surface: #ffffff;
  --vl-border: #e9edf2;
  --vl-text: #0b1a33;
  --vl-sec: #54657e;
  --vl-muted: #8896ab;
  --vl-accent: #7c3aed;
  --vl-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
}
.vl-card { background: var(--vl-surface); border: 1px solid var(--vl-border); border-radius: 14px; box-shadow: var(--vl-shadow); }
.vl-desk { overflow-x: hidden; }
.vl-tbl { table-layout: fixed; width: 100% !important; margin: 0; border-collapse: separate; border-spacing: 0; font-size: .8rem; }
.vl-tbl thead th { background: #f8fafc; font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .45px; color: var(--vl-muted); padding: .5rem .65rem; border-bottom: 2px solid var(--vl-border); white-space: normal !important; overflow-wrap: break-word; }
.vl-tbl tbody td { padding: .45rem .65rem; border-bottom: 1px solid #f1f4f9; vertical-align: middle; color: var(--vl-sec); white-space: normal !important; overflow-wrap: break-word; }
.vl-tbl tbody tr:hover { background: #fafbfc; }
.vl-tbl .vl-cls { font-weight: 700; color: var(--vl-accent); white-space: nowrap; }

.vlc-cards { display: none; }
.vlc-card { background: var(--vl-surface); border: 1.5px solid var(--vl-border); border-left: 4px solid var(--vl-accent); border-radius: 9px; box-shadow: var(--vl-shadow); padding: .7rem .85rem; margin-bottom: .6rem; }
.vlc-top { display: flex; align-items: flex-start; justify-content: space-between; gap: .6rem; }
.vlc-party { font-size: .88rem; font-weight: 700; color: var(--vl-text); line-height: 1.3; overflow-wrap: break-word; }
.vlc-party i { color: var(--vl-accent); margin-right: 5px; }
.vlc-id { flex: 0 0 auto; font-size: .7rem; font-weight: 600; color: var(--vl-muted); white-space: nowrap; }
.vlc-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .45rem .9rem; margin-top: .6rem; padding-top: .55rem; border-top: 1px dashed var(--vl-border); }
.vlc-item { min-width: 0; }
.vlc-label { font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: var(--vl-muted); }
.vlc-val { font-size: .84rem; font-weight: 700; color: var(--vl-text); overflow-wrap: break-word; }
.vlc-val.strong { color: var(--vl-accent); font-size: .95rem; }
.vlc-empty { text-align: center; padding: 1.5rem 1rem; color: var(--vl-muted); font-size: .82rem; }
@media (max-width: 991.98px) {
  .vl-desk { display: none; }
  .vlc-cards { display: block; }
}
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">

            <div class="page-header row">
                <div class="page-title col-lg-6">
                    <h4>Vendor Ledger</h4>
                    <h6>Manage Vendors</h6>
                </div>
                <div class="page-btn text-end justify-content-end col-lg-6">
                    <a href="{{ url('vendor') }}" class="btn btn-sm btn-outline-danger">Back</a>

                </div>

            </div>

            <div class="card vl-card">
                <div class="card-body">
                    @if (session()->has('success'))
                    <div class="alert alert-success"><strong>Success!</strong> {{ session('success') }}</div>
                    @endif

                    <div class="vl-desk">
                        <table class="table datanew vl-tbl">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Party Name</th>
                                    <th>Party Address</th>
                                    <th>Opening Balance</th>
                                    <th>Previous Balance</th>
                                    <th>Closing Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($VendorLedgers->isEmpty())
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        document.getElementById("global-loader").style.display = "none";
                                    });
                                </script>
                                @endif
                                @forelse($VendorLedgers as $ledger)
                                <tr>
                                    <td>{{ $ledger->vendor_id }}</td>
                                    <td>{{ $ledger->updated_at->format('Y-m-d') }}</td>
                                    <td>{{ $ledger->vendor->name }}</td>
                                    <td>{{ $ledger->vendor->address }}</td>

                                    <td>{{ $ledger->opening_balance}}</td>
                                    <td>{{ $ledger->previous_balance }}</td>
                                    <td id="closing_balance_{{ $ledger->id }}" class="vl-cls">{{ number_format((float) ($ledger->closing_balance ?? 0), 0) }}</td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="vlc-cards">
                        @forelse($VendorLedgers as $ledger)
                        <div class="vlc-card">
                            <div class="vlc-top">
                                <span class="vlc-party"><i class="bi bi-truck"></i>{{ $ledger->vendor->name }}</span>
                                <span class="vlc-id"><i class="bi bi-calendar"></i>{{ $ledger->updated_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="vlc-grid">
                                <div class="vlc-item"><div class="vlc-label">Party Address</div><div class="vlc-val">{{ $ledger->vendor->address ?? '—' }}</div></div>
                                <div class="vlc-item"><div class="vlc-label">Opening Balance</div><div class="vlc-val">{{ $ledger->opening_balance }}</div></div>
                                <div class="vlc-item"><div class="vlc-label">Previous Balance</div><div class="vlc-val">{{ $ledger->previous_balance }}</div></div>
                                <div class="vlc-item"><div class="vlc-label">Closing Balance</div><div class="vlc-val strong">{{ number_format((float) ($ledger->closing_balance ?? 0), 0) }}</div></div>
                            </div>
                        </div>
                        @empty
                        <div class="vlc-empty">No records found.</div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>



@endsection

@section('scripts')
<script>
    function clearVendor() {
        $('#vendor_id').val('');
        $('#vname').val('');
        $('#vphone').val('');
        $('#vaddress').val('');
    }

    function editVendor(id, name, phone, address) {
        $('#vendor_id').val(id);
        $('#vname').val(name);
        $('#vphone').val(phone);
        $('#vaddress').val(address);
    }
    $('.datanew').DataTable();
</script>
@endsection