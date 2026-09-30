@extends('admin_panel.layout.app')

@section('content')

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">

            <!-- Header -->
            <div class="page-header row">
                <div class="page-title col-lg-6">
                    <h4>Edit Vendor Payment</h4>
                    <h6>Update Payment Details</h6>
                </div>
                <div class="page-btn d-flex justify-content-end col-lg-6">
                    <a href="{{ route('vendor.payments') }}" class="btn btn-secondary">Back to Payments</a>
                </div>
            </div>

            <!-- Alert -->
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Edit Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('vendor.payments.update', $payment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Payment No #</label>
                                <input type="text" class="form-control" value="{{ $payment->payment_no }}" readonly style="background:#f8f9fa;">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Vendor</label>
                                <select name="vendor_id" class="form-control select2">
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ $payment->vendor_id == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Stock (Original Closing Balance)</label>
                                <input type="text" id="vendor_stock" class="form-control" readonly style="background:#f8f9fa;" value="{{ $original_balance }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $payment->payment_date) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Type</label>
                                <select name="adjustment_type" class="form-control" required>
                                    <option value="minus" {{ old('adjustment_type', $adjustment_type) == 'minus' ? 'selected' : '' }}>Minus (Payment)</option>
                                    <option value="plus" {{ old('adjustment_type', $adjustment_type) == 'plus' ? 'selected' : '' }}>Plus (Return / Advance)</option>
                                </select>
                            </div>

                            @php
                                $payMode = old('payment_mode', ($payment->cash > 0 && $payment->card > 0) ? 'split' : (($payment->card > 0 || $payment->payment_method === 'Card') ? 'card' : 'cash'));
                            @endphp

                            <div class="col-md-12 mb-3">
                                <label class="form-label d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Payment Mode</span>
                                    <span class="badge bg-light text-dark border">Select Mode</span>
                                </label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="payment_mode" id="vpay_mode_cash" value="cash" {{ $payMode === 'cash' ? 'checked' : '' }} onchange="toggleEditMode('cash')">
                                    <label class="btn btn-outline-success fw-bold" for="vpay_mode_cash">💵 Cash</label>

                                    <input type="radio" class="btn-check" name="payment_mode" id="vpay_mode_card" value="card" {{ $payMode === 'card' ? 'checked' : '' }} onchange="toggleEditMode('card')">
                                    <label class="btn btn-outline-primary fw-bold" for="vpay_mode_card">💳 Card / Bank</label>

                                    <input type="radio" class="btn-check" name="payment_mode" id="vpay_mode_split" value="split" {{ $payMode === 'split' ? 'checked' : '' }} onchange="toggleEditMode('split')">
                                    <label class="btn btn-outline-warning fw-bold text-dark" for="vpay_mode_split">⚡ Split (Cash + Card)</label>
                                </div>
                            </div>

                            {{-- Split Wrap --}}
                            <div class="col-md-12 mb-3" id="editSplitWrap" style="{{ $payMode === 'split' ? '' : 'display:none;' }}">
                                <div class="p-3 bg-light border rounded">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="fw-bold">💵 Cash Amount</label>
                                            <input type="number" step="any" name="cash" id="vpay_cash" class="form-control" value="{{ old('cash', $payment->cash ?: '') }}" placeholder="0" oninput="calcEditSplit()">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="fw-bold">💳 Card Amount</label>
                                            <input type="number" step="any" name="card" id="vpay_card" class="form-control" value="{{ old('card', $payment->card ?: '') }}" placeholder="0" oninput="calcEditSplit()">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editQuickSplit50()">⚡ 50/50 Split</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Card / Bank Account --}}
                            <div class="col-md-12 mb-3" id="editCardAccountWrap" style="{{ ($payMode === 'card' || $payMode === 'split') ? '' : 'display:none;' }}">
                                <div class="p-3 bg-light border rounded" style="border-color:#bfdbfe !important;">
                                    <label class="fw-bold text-primary mb-1">🏦 Select Card / Bank Account</label>
                                    <select name="card_account_id" id="card_account_id" class="form-control">
                                        <option value="">-- Choose Account / POS Machine --</option>
                                        @if(isset($bankAccounts))
                                            @foreach($bankAccounts as $acc)
                                                <option value="{{ $acc->id }}" {{ old('card_account_id', $payment->card_account_id) == $acc->id ? 'selected' : '' }}>
                                                    {{ $acc->title }} {{ $acc->head ? '('.$acc->head->name.')' : '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Amount <span class="text-danger">*</span></label>
                                <input type="number"
                                    step="0.01"
                                    name="amount"
                                    id="amount"
                                    class="form-control"
                                    value="{{ old('amount', $payment->amount) }}"
                                    {{ $payMode === 'split' ? 'readonly' : '' }}
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Payment Method Label</label>
                                <input type="text" name="payment_method" id="payment_method" class="form-control" placeholder="e.g. Cash, Bank" value="{{ old('payment_method', $payment->payment_method) }}">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Amount in Words</label>
                                <input type="text" id="amount_in_words"
                                    class="form-control"
                                    readonly
                                    style="background:#f8f9fa; font-weight:600;">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label>Note</label>
                                <textarea name="note" class="form-control" placeholder="Optional note">{{ old('note', $payment->note) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-100">Update Payment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        function numberToWords(num) {
            if (!num || num === 0) return 'Zero Rupees Only';

            const a = [
                '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven',
                'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen',
                'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
            ];
            const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty',
                'Sixty', 'Seventy', 'Eighty', 'Ninety'
            ];

            function inWords(n) {
                if (n < 20) return a[n];
                if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? ' ' + a[n % 10] : '');
                if (n < 1000)
                    return a[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' ' + inWords(n % 100) : '');
                if (n < 100000)
                    return inWords(Math.floor(n / 1000)) + ' Thousand' + (n % 1000 ? ' ' + inWords(n % 1000) : '');
                if (n < 10000000)
                    return inWords(Math.floor(n / 100000)) + ' Lakh' + (n % 100000 ? ' ' + inWords(n % 100000) : '');
                return inWords(Math.floor(n / 10000000)) + ' Crore' + (n % 10000000 ? ' ' + inWords(n % 10000000) : '');
            }

            let parts = num.toString().split('.');
            let rupees = parseInt(parts[0]);
            let paisa = parts[1] ? parseInt(parts[1].substring(0, 2)) : 0;

            let words = inWords(rupees) + ' Rupees';

            if (paisa > 0) {
                words += ' and ' + inWords(paisa) + ' Paisa';
            }

            return words + ' Only';
        }

        // Initialize amount in words on page load
        let initialAmount = $('#amount').val();
        if (initialAmount) {
            $('#amount_in_words').val(numberToWords(initialAmount));
        }

        // Update amount in words on input
        $(document).on('input', '#amount', function() {
            let val = $(this).val();
            $('#amount_in_words').val(numberToWords(val));
        });
    });

    function toggleEditMode(mode) {
        const splitWrap = document.getElementById('editSplitWrap');
        const accWrap = document.getElementById('editCardAccountWrap');
        const accSelect = document.getElementById('card_account_id');
        const amountInput = document.getElementById('amount');
        const methodInput = document.getElementById('payment_method');
        const cashInput = document.getElementById('vpay_cash');
        const cardInput = document.getElementById('vpay_card');

        if (mode === 'cash') {
            if (splitWrap) splitWrap.style.display = 'none';
            if (accWrap) accWrap.style.display = 'none';
            if (amountInput) amountInput.readOnly = false;
            if (methodInput) methodInput.value = 'Cash';
            if (accSelect) accSelect.value = '';
        } else if (mode === 'card') {
            if (splitWrap) splitWrap.style.display = 'none';
            if (accWrap) accWrap.style.display = 'block';
            if (amountInput) amountInput.readOnly = false;
            if (methodInput) methodInput.value = 'Card';
            if (accSelect && !accSelect.value && accSelect.options.length > 1) {
                accSelect.selectedIndex = 1;
            }
        } else if (mode === 'split') {
            if (splitWrap) splitWrap.style.display = 'block';
            if (accWrap) accWrap.style.display = 'block';
            if (amountInput) amountInput.readOnly = true;
            if (methodInput) methodInput.value = 'Split (Cash + Card)';
            if (accSelect && !accSelect.value && accSelect.options.length > 1) {
                accSelect.selectedIndex = 1;
            }
            calcEditSplit();
        }
    }

    function calcEditSplit() {
        const cs = parseFloat(document.getElementById('vpay_cash')?.value) || 0;
        const cd = parseFloat(document.getElementById('vpay_card')?.value) || 0;
        const tot = cs + cd;
        const amountInput = document.getElementById('amount');
        if (amountInput) {
            amountInput.value = tot > 0 ? tot.toFixed(2) : '';
            $(amountInput).trigger('input');
        }
    }

    function editQuickSplit50() {
        const base = parseFloat(document.getElementById('amount')?.value) || parseFloat(document.getElementById('vendor_stock')?.value) || 0;
        if (base <= 0) return;
        const half = Math.round(base / 2);
        document.getElementById('vpay_cash').value = half;
        document.getElementById('vpay_card').value = (base - half);
        calcEditSplit();
    }
</script>
@endsection
