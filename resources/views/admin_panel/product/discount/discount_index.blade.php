@extends('admin_panel.layout.app')
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    .rp-disc { font-family: 'Inter', -apple-system, 'Segoe UI', sans-serif; background: #f1f4f9; min-height: 100vh; padding-bottom: 2rem; }
    .rp-disc * { font-family: inherit; }

    .rp-hdr {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%);
        border-radius: 16px; padding: 1.4rem 1.8rem; margin-bottom: 1.4rem;
        box-shadow: 0 16px 40px rgba(180, 83, 9, .2);
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .8rem;
    }
    .rp-hdr h2 { margin: 0; font-size: 1.4rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: .6rem; }
    .rp-hdr h2 i { color: #fde68a; }
    .rp-hdr p { margin: 2px 0 0; color: rgba(255, 255, 255, .78); font-size: .85rem; font-weight: 500; }
    .rp-hdr .rp-btn-ghost { background: rgba(255, 255, 255, .12); border: 1px solid rgba(255, 255, 255, .3); color: #fff; }
    .rp-hdr .rp-btn-ghost:hover { background: rgba(255, 255, 255, .2); color: #fff; }
    .rp-hdr .rp-btn-dark { background: #0f172a; color: #fff; border: none; }
    .rp-hdr .rp-btn-dark:hover { transform: translateY(-1px); color: #fff; }

    .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
    .rp-btn:hover { transform: translateY(-1px); }

    .rp-card { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05); overflow: hidden; }
    .rp-card-body { padding: 1.4rem; }

    /* Summary cards */
    .rp-stat { border-radius: 14px; padding: 1.1rem 1.3rem; position: relative; overflow: hidden; }
    .rp-stat::after { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; }
    .rp-stat .val { font-size: 1.6rem; font-weight: 900; line-height: 1.1; }
    .rp-stat .lbl { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #64748b; }
    .rp-stat.s-total { background: #f8fafc; border: 1px solid #e9edf2; } .rp-stat.s-total::after { background: linear-gradient(180deg, #64748b, #94a3b8); } .rp-stat.s-total .val { color: #0f172a; }
    .rp-stat.s-active { background: #f0fdf4; border: 1px solid #bbf7d0; } .rp-stat.s-active::after { background: linear-gradient(180deg, #16a34a, #4ade80); } .rp-stat.s-active .val { color: #15803d; }
    .rp-stat.s-inactive { background: #fef2f2; border: 1px solid #fecaca; } .rp-stat.s-inactive::after { background: linear-gradient(180deg, #dc2626, #f87171); } .rp-stat.s-inactive .val { color: #b91c1c; }
    .rp-stat.s-avg { background: #fffbeb; border: 1px solid #fde68a; } .rp-stat.s-avg::after { background: linear-gradient(180deg, #f59e0b, #fbbf24); } .rp-stat.s-avg .val { color: #b45309; }

    /* Table */
    .rp-disc table.rp-table.dataTable { border-collapse: separate; border-spacing: 0; font-size: .82rem; }
    .rp-disc .rp-table thead th { background: #0f172a !important; color: #94a3b8 !important; font-size: .66rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; padding: .65rem .7rem; border: none; border-bottom: 2px solid #0f172a !important; text-align: left; white-space: nowrap; }
    .rp-disc .rp-table thead th:first-child { border-radius: 10px 0 0 0; }
    .rp-disc .rp-table thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-disc .rp-table tbody td { padding: .6rem .7rem; border: none; border-bottom: 1px solid #f1f4f9 !important; vertical-align: middle; color: #1e293b; }
    .rp-disc .rp-table tbody tr:nth-of-type(odd) td { background: transparent !important; }
    .rp-disc .rp-table tbody tr:hover td { background: #fafbfc !important; }
    .rp-disc .rp-table tbody tr:last-child td { border-bottom: none !important; }
    .rp-disc .rp-row-off td { background: #fff1f2 !important; }
    .rp-disc .rp-row-off:hover td { background: #fde8e9 !important; }

    .rp-chip { display: inline-flex; align-items: center; font-size: .7rem; font-weight: 700; letter-spacing: .3px; padding: .3rem .65rem; border-radius: 7px; margin-right: .3rem; }
    .rp-disc .rp-chip.bg-info { background: #e0f2fe !important; color: #0369a1 !important; }
    .rp-disc .rp-chip.bg-warning { background: #fef3c7 !important; color: #b45309 !important; }
    .rp-disc .badge.bg-secondary { background: #eef2f7 !important; color: #334155 !important; font-size: .68rem !important; }

    .rp-disc .btn { border-radius: 8px; font-size: .72rem; font-weight: 700; letter-spacing: .2px; padding: .32rem .65rem; border: none; transition: all .2s ease; }
    .rp-disc .btn:hover { transform: translateY(-1px); }
    .rp-disc .btn-success { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; }
    .rp-disc .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
    .rp-disc .btn-outline-primary { border: 1.5px solid #f59e0b; background: #fffbeb; color: #b45309; }
    .rp-disc .btn-outline-primary:hover { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }

    .rp-disc .product-img { width: 45px; height: 45px; object-fit: cover; border-radius: 10px; margin-right: .6rem; box-shadow: 0 2px 6px rgba(0, 0, 0, .12); }

    /* DataTable controls */
    .rp-disc .dataTables_wrapper { padding: .25rem; }
    .rp-disc .dataTables_length, .rp-disc .dataTables_info { font-size: .78rem; font-weight: 600; color: #54657e; }
    .rp-disc .dataTables_filter { margin-bottom: .25rem; }
    .rp-disc .dataTables_filter input { border: 1.5px solid #e9edf2; border-radius: 9px; padding: .4rem .7rem; font-size: .8rem; outline: none; font-weight: 600; color: #0b1a33; min-width: 220px; }
    .rp-disc .dataTables_filter input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, .14); }
    .rp-disc .dataTables_length select { border: 1.5px solid #e9edf2; border-radius: 8px; padding: .3rem .5rem; font-size: .78rem; font-weight: 600; color: #0b1a33; }
    .rp-disc .page-link { color: #334155; border: none; border-radius: 8px; margin: 0 2px; font-size: .75rem; font-weight: 700; padding: .3rem .6rem; }
    .rp-disc .page-item.active .page-link { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; color: #fff; }

    /* Alert */
    .rp-disc .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; color: #15803d; font-weight: 600; font-size: .85rem; }

    @media (max-width: 575.98px) {
        .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: flex-start; }
        .rp-hdr h2 { font-size: 1.15rem; }
        .rp-btn { width: 100%; }
        .rp-stat { padding: .9rem 1rem; }
        .rp-stat .val { font-size: 1.3rem; }
    }

    /* Desktop: fixed layout = table NEVER overflows */
    .rp-disc .table-responsive { overflow-x: hidden; }
    .rp-disc table#discountTable { table-layout: fixed; width: 100% !important; }
    .rp-disc #discountTable thead th,
    .rp-disc #discountTable tbody td { white-space: normal !important; overflow-wrap: anywhere; }

    /* Mobile: premium grid cards */
    @media (max-width: 991.98px) {
        .rp-disc .table-responsive { overflow: visible; }
        .rp-disc .dataTables_filter { width: 100% !important; }
        .rp-disc .dataTables_filter input { width: 100% !important; min-width: 0; min-height: 42px; margin-bottom: .4rem; }
        .rp-disc .dataTables_length, .rp-disc .dataTables_info { text-align: center !important; }
        .rp-disc .dataTables_paginate { text-align: center !important; }
        .rp-disc #discountTable { display: block !important; width: 100% !important; }
        .rp-disc #discountTable thead { display: none !important; }
        .rp-disc #discountTable tbody { display: block !important; }
        .rp-disc #discountTable tbody tr {
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
        .rp-disc #discountTable tbody tr.rp-row-off td { background: transparent !important; }
        .rp-disc #discountTable tbody td {
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
        .rp-disc #discountTable tbody td::before {
            content: attr(data-label) ":";
            font-size: .55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #94a3b8;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .rp-disc #discountTable tbody td:nth-child(1) { order: 0; justify-self: start; }
        .rp-disc #discountTable tbody td:nth-child(1)::before { content: none; }
        .rp-disc #discountTable tbody td:nth-child(2) { order: 1; grid-column: 1 / -1; }
        .rp-disc #discountTable tbody td:nth-child(2)::before { content: none; }
        .rp-disc #discountTable tbody td:nth-child(3) { order: 2; }
        .rp-disc #discountTable tbody td:nth-child(4) { order: 3; }
        .rp-disc #discountTable tbody td:nth-child(5) { order: 4; }
        .rp-disc #discountTable tbody td:nth-child(6) { order: 5; font-weight: 800; color: #166534; }
        .rp-disc #discountTable tbody td:nth-child(7) { order: 6; grid-column: 1 / -1; }
        .rp-disc #discountTable tbody td:nth-child(7)::before { content: none; }
        .rp-disc #discountTable tbody td:nth-child(7) .btn { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; width: 100%; }
        .rp-disc #discountTable tbody td:nth-child(8) { order: 7; grid-column: 1 / -1; }
        .rp-disc #discountTable tbody td:nth-child(8)::before { content: none; }
        .rp-disc #discountTable tbody td:nth-child(8) form { width: 100%; }
        .rp-disc #discountTable tbody td:nth-child(8) form .btn { min-height: 40px; width: 100%; }
        .rp-disc .product-img { width: 36px; height: 36px; }
    }
</style>

<div class="rp-disc">
    <div class="container-fluid px-3 px-md-4 py-3">

        <div class="rp-hdr">
            <div>
                <h2><i class="bi bi-tags-fill"></i> Product Discounts</h2>
                <p>Manage all product discounts, pricing and barcode labels</p>
            </div>
            @if (auth()->user()->can('Create Discount') || auth()->user()->email === 'admin@admin.com')
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ url()->previous() }}" class="rp-btn rp-btn-ghost"><i class="bi bi-arrow-left"></i> Back</a>
                    <a href="{{ route('product') }}" class="rp-btn rp-btn-dark"><i class="bi bi-box-seam"></i> View Product</a>
                </div>
            @endif
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                ✅ {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $totalDisc = $discounts->count();
            $activeDisc = 0; $inactiveDisc = 0; $sumPct = 0;
            foreach ($discounts as $d) {
                $d->status ? $activeDisc++ : $inactiveDisc++;
                $sumPct += (float) $d->discount_percentage;
            }
            $avgPct = $totalDisc ? round($sumPct / $totalDisc, 1) : 0;
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="rp-stat s-total">
                    <div class="val">{{ $totalDisc }}</div>
                    <div class="lbl">Total Discounts</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rp-stat s-active">
                    <div class="val">{{ $activeDisc }}</div>
                    <div class="lbl">Active</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rp-stat s-inactive">
                    <div class="val">{{ $inactiveDisc }}</div>
                    <div class="lbl">Inactive</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="rp-stat s-avg">
                    <div class="val">{{ $avgPct }}%</div>
                    <div class="lbl">Avg Discount</div>
                </div>
            </div>
        </div>

        <div class="rp-card">
            <div class="rp-card-body p-0">
                <div class="table-responsive">
                    <table class="table rp-table mb-0" id="discountTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Descriptions</th>
                                <th>Original Price</th>
                                <th>Discount</th>
                                <th>Date</th>
                                <th>Discount Price</th>
                                <th>Barcode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($discounts as $key => $discount)
                                <tr class="{{ $discount->status ? '' : 'rp-row-off' }}">
                                    <td data-label="#"><strong>{{ $key + 1 }}</strong></td>

                                    {{-- Product Info Column --}}
                                    <td data-label="Item Descriptions">
                                        <div class="d-flex align-items-center">
                                            @if ($discount->product->image)
                                                <img src="{{ asset('uploads/products/' . $discount->product->image) }}"
                                                    class="product-img">
                                            @else
                                                <span class="badge bg-secondary me-2">No Img</span>
                                            @endif
                                            <div>
                                                <strong class="text-slate-800">{{ $discount->product->item_name }}</strong><br>
                                                <small class="text-muted">Code: {{ $discount->product->item_code }}</small><br>
                                                <small class="text-muted">Brand:
                                                    {{ $discount->product->brand->name ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Prices --}}
                                    <td data-label="Original Price">{{ number_format($discount->actual_price, 2) }}</td>

                                    {{-- Discount Column --}}
                                    <td data-label="Discount">
                                        <span class="badge bg-info rp-chip">{{ $discount->discount_percentage }}%</span>
                                        <span class="badge bg-warning rp-chip">{{ number_format($discount->discount_amount, 2) }} PKR</span>
                                    </td>

                                    {{-- Date --}}
                                    <td data-label="Date">{{ \Carbon\Carbon::parse($discount->date)->format('d-M-Y') }}</td>

                                    {{-- Final Price --}}
                                    <td data-label="Discount Price"><strong class="text-success fs-6">{{ number_format($discount->final_price, 2) }}</strong>
                                    </td>

                                    {{-- Barcode --}}
                                    <td data-label="Barcode">
                                        <a href="{{ route('discount.barcode', $discount->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            🏷 Barcode
                                        </a>
                                    </td>

                                    {{-- Status --}}
                                    <td data-label="Status">
                                        <form action="{{ route('discount.toggleStatus', $discount->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm {{ $discount->status ? 'btn-success' : 'btn-danger' }}">
                                                {{ $discount->status ? '✔ Active' : '✖ Inactive' }}
                                            </button>
                                        </form>
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
    <script>
        $(document).ready(function() {
            $('#discountTable').DataTable({
                responsive: false,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                order: [
                    [0, 'asc']
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search discounts...",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        });
    </script>
@endsection