<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt</title>

    <!-- html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        body {
            width: 80mm;
            font-family: monospace;
            font-size: 12px;
            margin: 0;
            padding: 8px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
        }

        td {
            padding: 2px 0;
        }

        button {
            width: 100%;
            padding: 6px;
            margin-top: 8px;
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div id="receipt">
        @php
            $currentBranch = $payment->branch 
                ?? ($payment->branch_id ? \App\Models\Branch::find($payment->branch_id) : null)
                ?? (auth()->check() && auth()->user()->branch_id ? \App\Models\Branch::find(auth()->user()->branch_id) : null)
                ?? \App\Models\Branch::find(active_branch_id())
                ?? \App\Models\Branch::first();

            $bAddress = !empty($currentBranch?->address) ? $currentBranch->address : '';
            $rawPhone = !empty($currentBranch?->number) ? $currentBranch->number : (!empty($currentBranch?->phone) ? $currentBranch->phone : '');
            $bPhone = preg_replace('/^Phone:\s*/i', '', $rawPhone);
        @endphp

        <div class="center">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 50px; margin-bottom: 3px;">
        </div>

        <div class="center bold">
            {{ shop_name() }}
        </div>
        <div class="center" style="font-size:11px;">
            {{ shop_tagline() }}
        </div>
        @if(!empty($currentBranch?->name))
        <div class="center bold" style="font-size:11px; text-transform:uppercase; margin-top:2px;">
            BRANCH: {{ $currentBranch->name }}
        </div>
        @endif
        @if(!empty($bAddress))
        <div class="center" style="font-size:10px;">
            {{ $bAddress }}
        </div>
        @endif
        @if(!empty($bPhone))
        <div class="center" style="font-size:10px;">
            Phone: {{ $bPhone }}
        </div>
        @endif

        <div class="center bold" style="margin-top:4px; font-size:11px;">
            PAYMENT RECEIPT
        </div>

        <div class="center" style="font-size:10px;">
            {{ now()->format('d-m-Y h:i A') }}
        </div>

        <div class="line"></div>

        <table>
            <tr>
                <td>Payment No:</td>
                <td class="bold">{{ $payment->payment_no }}</td>
            </tr>
            <tr>
                <td>Vendor:</td>
                <td class="bold">{{ $payment->vendor->name }}</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>{{ $payment->payment_date }}</td>
            </tr>
            <tr>
                <td>Method:</td>
                <td class="bold">
                    {{ ucfirst($payment->payment_method ?: 'Cash') }}
                    @if(!empty($payment->cardAccount))
                        <br><span style="font-size:10px; font-weight:normal;">Bank/Card: {{ $payment->cardAccount->title }}</span>
                    @endif
                    @if($payment->cash > 0 && $payment->card > 0)
                        <br><span style="font-size:10px; font-weight:normal;">(Cash: Rs {{ number_format($payment->cash, 0) }} | Card: Rs {{ number_format($payment->card, 0) }})</span>
                    @endif
                </td>
            </tr>
        </table>

        <div class="line"></div>

        <table>
            @if($payment->cash > 0 && $payment->card > 0)
            <tr>
                <td>Cash Paid:</td>
                <td align="right">Rs {{ number_format($payment->cash, 2) }}</td>
            </tr>
            <tr>
                <td>Card Paid:</td>
                <td align="right">Rs {{ number_format($payment->card, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td class="bold">Total Amount:</td>
                <td class="bold" align="right">
                    Rs {{ number_format($payment->amount, 2) }}
                </td>
            </tr>
        </table>

        <div class="line"></div>

        <div>
            <strong>Amount in Words:</strong><br>
            <span id="amountWords"></span>
        </div>

        <div class="line"></div>

        <div>
            Note:<br>
            {{ $payment->note ?? '-' }}
        </div>

        <div class="line"></div>

        <div class="center">
            Paid by <strong>{{ shop_name() }} </strong>
        </div>

        <div class="center" style="font-size:10px; color:#555; margin-top:2px;">Develop By: <strong>{{ developer_name() }}</strong></div>
        <div class="center">
            {{ sys_setting('invoice_footer_note', 'Thank You') }}
        </div>

    </div>

    <!-- ACTION BUTTONS -->
    <div class="no-print">
        <button onclick="window.print()">🖨 Print Receipt</button>
        <button onclick="downloadReceipt()">📸 Download Screenshot</button>
    </div>

    <script>
        function downloadReceipt() {
            html2canvas(document.getElementById('receipt'), {
                scale: 2
            }).then(canvas => {
                let link = document.createElement('a');
                link.download = 'payment-receipt-{{ $payment->id }}.png';
                link.href = canvas.toDataURL();
                link.click();
            });
        }

        function numberToWords(num) {
            const a = [
                '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six',
                'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
                'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                'Seventeen', 'Eighteen', 'Nineteen'
            ];
            const b = [
                '', '', 'Twenty', 'Thirty', 'Forty',
                'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
            ];

            if (num === 0) return 'Zero';

            function inWords(n) {
                if (n < 20) return a[n];
                if (n < 100) return b[Math.floor(n / 10)] + ' ' + a[n % 10];
                if (n < 1000)
                    return a[Math.floor(n / 100)] + ' Hundred ' + inWords(n % 100);
                if (n < 100000)
                    return inWords(Math.floor(n / 1000)) + ' Thousand ' + inWords(n % 1000);
                if (n < 10000000)
                    return inWords(Math.floor(n / 100000)) + ' Lakh ' + inWords(n % 100000);
                return '';
            }

            return inWords(num);
        }

        // ✅ FIXED amount from backend
        document.addEventListener('DOMContentLoaded', function() {
            let amount = parseInt("{{ (int) $payment->amount }}");
            document.getElementById('amountWords').innerText =
                numberToWords(amount) + ' Rupees Only';
        });
    </script>

</body>

</html>