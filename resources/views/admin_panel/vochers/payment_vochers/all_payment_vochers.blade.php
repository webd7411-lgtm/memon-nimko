@extends('admin_panel.layout.app')
@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    .apv-page .table-responsive { overflow-x: hidden; }
    .apv-page table#example { table-layout: fixed; width: 100% !important; }
    .apv-page thead th, .apv-page tbody td { white-space: normal !important; overflow-wrap: anywhere; }

    @media (max-width: 991.98px) {
        .apv-page .table-responsive { overflow: visible; }
        .apv-page table { display: block !important; width: 100% !important; }
        .apv-page thead { display: none !important; }
        .apv-page tbody { display: block !important; }
        .apv-page tbody tr {
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
        .apv-page tbody td {
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
        .apv-page tbody td::before {
            content: attr(data-label) ":";
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #94a3b8;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .apv-page tbody td:nth-child(1) { order: 0; }
        .apv-page tbody td:nth-child(1)::before { content: none; }
        .apv-page tbody td:nth-child(2) { order: 1; grid-column: 1 / -1; font-weight: 800; color: #0f172a; }
        .apv-page tbody td:nth-child(2)::before { content: none; }
        .apv-page tbody td:nth-child(6) { order: 2; font-weight: 700; }
        .apv-page tbody td:nth-child(3) { order: 3; }
        .apv-page tbody td:nth-child(4) { order: 4; }
        .apv-page tbody td:nth-child(5) { order: 5; }
        .apv-page tbody td:nth-child(7) { order: 6; }
        .apv-page tbody td:nth-child(8) { order: 7; grid-column: 1 / -1; }
        .apv-page tbody td:nth-child(9) { order: 8; }
        .apv-page tbody td:nth-child(10) { order: 9; font-weight: 800; color: #166534; }
        .apv-page tbody td:nth-child(11) { order: 10; grid-column: 1 / -1; }
        .apv-page tbody td:nth-child(12) { order: 11; grid-column: 1 / -1; }
        .apv-page tbody td:nth-child(12)::before { content: none; }
        .apv-page tbody td:nth-child(12) .btn { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; }
    }
</style>

<div class="main-content apv-page">
    <div class="container-fluid">
        <div class="card-header mt-2 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Payment Vouchers</h4>
            <a class="btn btn-primary mt-1 mb-1" href="{{ route('Payment-vochers') }}">Add Payment Voucher</a>
        </div>
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive mt-4 mb-4">
                    <table id="example" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Voucher No</th>
                                <th>Payment Date</th>
                                <th>Entry Date</th>
                                <th>Type</th>
                                <th>Party</th>
                                <th>Reference No</th>
                                <th>Remarks</th>
                                <th>Amount</th>
                                <th>Total Amount</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $item)
                            @php
                            // JSON decode for fields that are stored as arrays
                            $amounts = json_decode($item->amount, true);
                            $amount = is_array($amounts) ? (float)($amounts[0] ?? 0) : (float)$item->amount;

                            $refs = json_decode($item->reference_no, true);
                            $reference = is_array($refs) ? implode(', ', $refs) : $item->reference_no;

                            $narrations = json_decode($item->narration_id, true);
                            $narration = is_array($narrations) ? implode(', ', $narrations) : $item->narration_id;
                            @endphp
                            <tr>
                                <td data-label="ID">{{ $item->id }}</td>
                                <td data-label="Voucher No">{{ $item->pvid }}</td>
                                <td data-label="Payment Date">{{ $item->receipt_date }}</td>
                                <td data-label="Entry Date">{{ $item->entry_date }}</td>
                                <td data-label="Type">
                                    @if($item->type == 'vendor' || $item->type == 1)
                                    Vendor
                                    @elseif($item->type == 'customer' || $item->type == 2)
                                    Customer
                                    @else
                                    Other
                                    @endif
                                </td>
                                <td data-label="Party">{{ $item->party_id }}</td>
                                <td data-label="Reference No">{{ $reference }}</td>
                                <td data-label="Remarks">{{ $item->remarks }}</td>
                                <td data-label="Amount">{{ number_format($amount, 2) }}</td>
                                <td data-label="Total Amount">{{ number_format((float)$item->total_amount, 2) }}</td>
                                <td data-label="Created At">{{ $item->created_at }}</td>
                                <td data-label="Actions">
                                    <a href="{{ route('PaymentVoucher.print', $item->id) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-danger">
                                        <i class="bi bi-printer"></i>
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

@endsection

@section('scripts')


@endsection