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

    .rp-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4rem; border: none; border-radius: 10px; padding: .55rem 1.1rem; font-size: .83rem; font-weight: 700; cursor: pointer; transition: all .22s ease; min-height: 42px; text-decoration: none; }
    .rp-btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; box-shadow: 0 6px 18px rgba(245, 158, 11, .3); }
    .rp-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(245, 158, 11, .38); color: #fff; }

    .rp-card { background: #fff; border: 1px solid #e9edf2; border-radius: 14px; box-shadow: 0 1px 2px rgba(0, 0, 0, .03), 0 8px 24px rgba(0, 0, 0, .05); overflow: hidden; }
    .rp-card-body { padding: 1.4rem; }

    /* Table */
    .rp-disc table.rp-table { border-collapse: separate; border-spacing: 0; font-size: .82rem; }
    .rp-disc .rp-table thead th { background: #0f172a !important; color: #94a3b8 !important; font-size: .66rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; padding: .65rem .7rem; border: none; border-bottom: 2px solid #0f172a; text-align: left; white-space: nowrap; }
    .rp-disc .rp-table thead th:first-child { border-radius: 10px 0 0 0; }
    .rp-disc .rp-table thead th:last-child { border-radius: 0 10px 0 0; }
    .rp-disc .rp-table tbody td { padding: .6rem .7rem; border: none; border-bottom: 1px solid #f1f4f9; vertical-align: middle; color: #1e293b; }
    .rp-disc .rp-table tbody tr:hover td { background: #fafbfc; }
    .rp-disc .rp-table tbody tr:last-child td { border-bottom: none; }
    .rp-disc .rp-code { font-size: .72rem; font-weight: 600; color: #64748b; }
    .rp-disc .rp-orig { font-size: .8rem; font-weight: 700; color: #334155; }

    /* Inputs */
    .rp-disc .form-control { border: 1.5px solid #e9edf2; border-radius: 9px; padding: .4rem .6rem; font-size: .8rem; font-weight: 600; color: #0b1a33; background: #fff; height: auto; box-shadow: none; }
    .rp-disc .form-control:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, .14); }
    .rp-disc .form-control[readonly] { background: #f8fafc; color: #334155; cursor: default; }
    .rp-disc .finalPrice { background: #f0fdf4 !important; color: #15803d !important; font-weight: 800; }
    .rp-disc .form-control.is-invalid { border-color: #dc2626 !important; box-shadow: 0 0 0 3px rgba(220, 38, 38, .1) !important; }
    .rp-disc .text-danger, .rp-disc .live-error { font-size: .7rem; font-weight: 600; color: #dc2626 !important; }

    @media (max-width: 575.98px) {
        .rp-hdr { padding: 1.1rem 1.2rem; flex-direction: column; align-items: flex-start; }
        .rp-hdr h2 { font-size: 1.15rem; }
        .rp-btn { width: 100%; }
    }
</style>

<div class="rp-disc">
    <div class="container-fluid px-3 px-md-4 py-3">

        <div class="rp-hdr">
            <div>
                <h2><i class="bi bi-tag-fill"></i> Create Discount</h2>
                <p>Set discount percentage &amp; amount for the selected products</p>
            </div>
            <a href="{{ route('discount.index') }}" class="rp-btn rp-btn-ghost"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div class="rp-card">
            <div class="rp-card-body p-0">
                <form action="{{ route('discount.store') }}" method="POST" id="discountForm">
                    @csrf
                    <div class="table-responsive">
                        <table class="table rp-table mb-0" id="discountCreateTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Original Price</th>
                                    <th>Discount %</th>
                                    <th>Discount PKR</th>
                                    <th>Total Discount</th>
                                    <th>Final Price</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $key => $product)
                                <tr>
                                    <input type="hidden" name="product_id[]" value="{{ $product->id }}">
                                    <input type="hidden" name="actual_price[]" value="{{ $product->price }}">

                                    <td><strong>{{ $key + 1 }}</strong></td>
                                    <td class="rp-code">{{ $product->item_code }}</td>
                                    <td><strong>{{ $product->item_name }}</strong></td>
                                    <td class="rp-orig originalPrice">{{ number_format($product->price, 2) }}</td>

                                    <td style="min-width: 120px;">
                                        <input type="number" step="0.01" min="0" max="100"
                                               name="discount_percentage[]"
                                               class="form-control discountPercentage @error('discount_percentage.'.$key) is-invalid @enderror"
                                               value="{{ old('discount_percentage.'.$key, 0) }}">
                                        {{-- live error --}}
                                        <small class="text-danger live-error d-none"></small>
                                        {{-- server error --}}
                                        @error('discount_percentage.'.$key)
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>

                                    <td style="min-width: 120px;">
                                        <input type="number" step="0.01" min="0"
                                               name="discount_amount[]"
                                               class="form-control discountAmount @error('discount_amount.'.$key) is-invalid @enderror"
                                               value="{{ old('discount_amount.'.$key, 0) }}">
                                        {{-- live error --}}
                                        <small class="text-danger live-error d-none"></small>
                                        {{-- server error --}}
                                        @error('discount_amount.'.$key)
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>

                                    <td>
                                        <input type="number" step="0.01" name="total_discount[]"
                                               class="form-control totalDiscount" readonly
                                               value="{{ old('total_discount.'.$key) }}">
                                    </td>

                                    <td>
                                        <input type="number" name="final_price[]"
                                               class="form-control finalPrice fw-bold" readonly
                                               value="{{ old('final_price.'.$key) }}">
                                    </td>

                                   <td>
                                        <input type="date" name="date[]" class="form-control"
                                            value="{{ old('date.'.$key, now()->toDateString()) }}">
                                        @error('date.'.$key)
                                            <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>

                                    <td>
                                        <select name="status[]" class="form-control">
                                            <option value="1" {{ old('status.'.$key, 1)==1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status.'.$key, 1)==0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end px-3 py-3 border-top">
                        <button type="submit" class="rp-btn rp-btn-primary"><i class="bi bi-check2-circle"></i> Save Discounts</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){

    function showInlineError($inputs, msg){
        $inputs.each(function(){
            const $inp = $(this);
            $inp.addClass('is-invalid');
            const $err = $inp.closest('td').find('.live-error');
            $err.text(msg).removeClass('d-none');
        });
    }

    function clearInlineError($inputs){
        $inputs.each(function(){
            const $inp = $(this);
            $inp.removeClass('is-invalid');
            const $err = $inp.closest('td').find('.live-error');
            $err.text('').addClass('d-none');
        });
    }

    function clamp(val, min, max){
        return Math.min(Math.max(val, min), max);
    }

    function updateFinalPrice(row){
        const $percInput = row.find('.discountPercentage');
        const $amtInput  = row.find('.discountAmount');

        const original = parseFloat(row.find('input[name="actual_price[]"]').val()) || 0;
        let   perc     = parseFloat($percInput.val()) || 0;
        let   amt      = parseFloat($amtInput.val()) || 0;

        // local validations
        clearInlineError($percInput.add($amtInput));

        // % cap 0..100
        if (perc < 0 || perc > 100){
            showInlineError($percInput, 'Percentage must be between 0 and 100.');
            perc = clamp(perc, 0, 100);
            $percInput.val(perc);
        }

        // negative PKR not allowed
        if (amt < 0){
            showInlineError($amtInput, 'Discount PKR cannot be negative.');
            amt = 0;
            $amtInput.val(0);
        }

        // compute
        const percDiscount = (original * perc / 100);
        let   totalDiscount = percDiscount + amt;

        // combined limit
        if (totalDiscount > original) {
            const overflow = (totalDiscount - original).toFixed(2);
            showInlineError($percInput.add($amtInput), 'Total discount exceeds price by ' + overflow);
        }

        // final price not below 0
        const cappedTotal = Math.min(totalDiscount, original);
        const finalPrice  = original - cappedTotal;

        row.find('.totalDiscount').val(cappedTotal.toFixed(2));
        row.find('.finalPrice').val(finalPrice.toFixed(2));
    }

    // events
    $(document).on('input', '.discountPercentage, .discountAmount', function(){
        const row = $(this).closest('tr');
        updateFinalPrice(row);
    });

    // initial pass (handles old() values)
    $('#discountCreateTable tbody tr').each(function(){
        updateFinalPrice($(this));
    });
});
</script>
@endsection