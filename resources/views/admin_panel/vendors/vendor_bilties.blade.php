@extends('admin_panel.layout.app')

@section('content')
<style>
:root {
  --vb-bg: #f1f4f9;
  --vb-surface: #ffffff;
  --vb-border: #e9edf2;
  --vb-text: #0b1a33;
  --vb-sec: #54657e;
  --vb-muted: #8896ab;
  --vb-accent: #ea580c;
  --vb-shadow: 0 1px 2px rgba(0,0,0,.03), 0 1px 3px rgba(0,0,0,.05);
}
.vb-card { background: var(--vb-surface); border: 1px solid var(--vb-border); border-radius: 14px; box-shadow: var(--vb-shadow); }
.vb-desk { overflow-x: hidden; }
.vb-tbl { table-layout: fixed; width: 100% !important; margin: 0; border-collapse: separate; border-spacing: 0; font-size: .8rem; }
.vb-tbl thead th { background: #f8fafc; font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .45px; color: var(--vb-muted); padding: .5rem .65rem; border-bottom: 2px solid var(--vb-border); white-space: normal !important; overflow-wrap: break-word; }
.vb-tbl tbody td { padding: .45rem .65rem; border-bottom: 1px solid #f1f4f9; vertical-align: middle; color: var(--vb-sec); white-space: normal !important; overflow-wrap: break-word; }
.vb-tbl tbody tr:hover { background: #fafbfc; }
.vb-tbl .vb-amt { font-weight: 700; color: var(--vb-accent); white-space: nowrap; }

.vbc-cards { display: none; }
.vbc-card { background: var(--vb-surface); border: 1.5px solid var(--vb-border); border-left: 4px solid var(--vb-accent); border-radius: 9px; box-shadow: var(--vb-shadow); padding: .7rem .85rem; margin-bottom: .6rem; }
.vbc-top { display: flex; align-items: flex-start; justify-content: space-between; gap: .6rem; }
.vbc-bilty { font-size: .85rem; font-weight: 700; color: var(--vb-text); line-height: 1.3; overflow-wrap: break-word; }
.vbc-bilty i { color: var(--vb-accent); margin-right: 5px; }
.vbc-date { flex: 0 0 auto; font-size: .7rem; font-weight: 600; color: var(--vb-muted); white-space: nowrap; }
.vbc-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: .45rem .9rem; margin-top: .6rem; padding-top: .55rem; border-top: 1px dashed var(--vb-border); }
.vbc-item { min-width: 0; }
.vbc-label { font-size: .6rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px; color: var(--vb-muted); }
.vbc-val { font-size: .84rem; font-weight: 700; color: var(--vb-text); overflow-wrap: break-word; }
.vbc-val.amt { color: var(--vb-accent); font-size: .95rem; }
.vbc-empty { text-align: center; padding: 1.5rem 1rem; color: var(--vb-muted); font-size: .82rem; }
@media (max-width: 991.98px) {
  .vb-desk { display: none; }
  .vbc-cards { display: block; }
}
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">

            <div class="page-header row">
                <div class="page-title col-lg-6">
                    <h4>Vendor Bilties</h4>
                    <h6>Manage Transport & Delivery Records</h6>
                </div>
                <div class="page-btn d-flex justify-content-end col-lg-6">
                    <button class="btn btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#biltyModal" onclick="clearBiltyForm()">Add Bilty</button>
                </div>
            </div>

            @if (session()->has('success'))
            <div class="alert alert-success"><strong>Success!</strong> {{ session('success') }}</div>
            @endif

            <div class="card vb-card">
                <div class="card-body">
                    <div class="vb-desk">
                        <table class="table datanew vb-tbl">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vendor</th>
                                    <th>Purchase</th>
                                    <th>Bilty No</th>
                                    <th>Vehicle No</th>
                                    <th>Transporter</th>
                                    <th>Amount</th>
                                    <th>Delivery Date</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bilties as $key => $b)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $b->vendor->name ?? 'N/A' }}</td>
                                    <td>{{ $b->purchase->invoice_no ?? 'N/A' }}</td>
                                    <td>{{ $b->bilty_no }}</td>
                                    <td>{{ $b->vehicle_no }}</td>
                                    <td>{{ $b->transporter_name }}</td>
                                    <td class="vb-amt">{{ number_format($b->amount, 2) }}</td>
                                    <td>{{ $b->delivery_date }}</td>
                                    <td>{{ $b->note }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="vbc-cards">
                        @forelse($bilties as $b)
                        <div class="vbc-card">
                            <div class="vbc-top">
                                <span class="vbc-bilty"><i class="bi bi-truck"></i>{{ $b->bilty_no }}</span>
                                <span class="vbc-date"><i class="bi bi-calendar"></i>{{ $b->delivery_date ?? '—' }}</span>
                            </div>
                            <div class="vbc-grid">
                                <div class="vbc-item"><div class="vbc-label">Vendor</div><div class="vbc-val">{{ $b->vendor->name ?? 'N/A' }}</div></div>
                                <div class="vbc-item"><div class="vbc-label">Purchase</div><div class="vbc-val">{{ $b->purchase->invoice_no ?? 'N/A' }}</div></div>
                                <div class="vbc-item"><div class="vbc-label">Vehicle No</div><div class="vbc-val">{{ $b->vehicle_no ?? '—' }}</div></div>
                                <div class="vbc-item"><div class="vbc-label">Transporter</div><div class="vbc-val">{{ $b->transporter_name ?? '—' }}</div></div>
                                <div class="vbc-item"><div class="vbc-label">Amount</div><div class="vbc-val amt">{{ number_format($b->amount, 2) }}</div></div>
                                <div class="vbc-item"><div class="vbc-label">Note</div><div class="vbc-val">{{ $b->note ?? '—' }}</div></div>
                            </div>
                        </div>
                        @empty
                        <div class="vbc-empty">No bilties found</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bilty Modal -->
<div class="modal fade" id="biltyModal">
    <div class="modal-dialog">
        <form action="{{ route('vendor.bilties.store') }}" method="POST">@csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Vendor Bilty</h5></div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Vendor</label>
                        <select name="vendor_id" class="form-control" required>
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label>Related Purchase (optional)</label>
                        <select name="purchase_id" class="form-control">
                            <option value="">None</option>
                            @foreach($purchases as $purchase)
                                <option value="{{ $purchase->id }}">{{ $purchase->invoice_no }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2"><input class="form-control" name="bilty_no" placeholder="Bilty No"></div>
                    <div class="mb-2"><input class="form-control" name="vehicle_no" placeholder="Vehicle No"></div>
                    <div class="mb-2"><input class="form-control" name="transporter_name" placeholder="Transporter Name"></div>
                    <div class="mb-2">
                        <label>Bilty Amount</label>
                        <input type="number" step="any" class="form-control" name="amount" placeholder="Amount" required>
                    </div>
                    <div class="mb-2"><input type="date" class="form-control" name="delivery_date"></div>
                    <div class="mb-2"><textarea class="form-control" name="note" placeholder="Note (optional)"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function clearBiltyForm() {
    $('#biltyModal select[name="vendor_id"]').val('');
    $('#biltyModal select[name="purchase_id"]').val('');
    $('#biltyModal input[name="bilty_no"]').val('');
    $('#biltyModal input[name="vehicle_no"]').val('');
    $('#biltyModal input[name="transporter_name"]').val('');
    $('#biltyModal input[name="amount"]').val('');
    $('#biltyModal input[name="delivery_date"]').val('');
    $('#biltyModal textarea[name="note"]').val('');
}

$('.datanew').DataTable();
</script>
@endsection