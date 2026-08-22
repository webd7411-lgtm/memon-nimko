<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stock Transfer Receipt #{{ $transfer->id ?? '' }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Courier New', Courier, monospace;
      font-size: 13px;
      color: #000000;
      background-color: #0f172a;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 20px 10px;
    }

    /* SCREEN ACTION BAR */
    .screen-actions {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(10px);
      padding: 10px 16px;
      border-radius: 40px;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-act {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #ffffff;
      color: #0f172a;
      border: none;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      font-family: sans-serif;
    }

    .btn-act:hover {
      background: #2563eb;
      color: #ffffff;
      transform: translateY(-1px);
    }

    .btn-act-sec {
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
    }

    .btn-act-sec:hover {
      background: rgba(255, 255, 255, 0.3);
      color: #ffffff;
    }

    /* THERMAL RECEIPT CONTAINER */
    .receipt-container {
      width: 100%;
      max-width: 360px;
      background: #ffffff;
      padding: 15px 12px;
      border-radius: 6px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
      color: #000000;
    }

    .center {
      text-align: center;
    }

    .bold {
      font-weight: bold;
    }

    .line {
      border-top: 1.5px dashed #000000;
      margin: 8px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      word-wrap: break-word;
    }

    th, td {
      padding: 4px 0;
      font-size: 13px;
      color: #000000;
      line-height: 1.3;
    }

    th {
      text-align: left;
      font-weight: bold;
    }

    td.right, th.right {
      text-align: right;
    }

    .company-title {
      font-size: 18px;
      font-weight: bold;
      text-transform: uppercase;
      margin-top: 4px;
    }

    .slip-title {
      font-size: 15px;
      font-weight: bold;
      text-transform: uppercase;
      margin: 4px 0;
    }

    .footer {
      text-align: center;
      font-size: 11.5px;
      margin-top: 10px;
      border-top: 1.5px dashed #000000;
      padding-top: 8px;
      color: #000000;
      font-weight: bold;
    }

    /* BLACK COPPER / POS PRINT MEDIA OVERRIDES */
    @media print {
      @page {
        margin: 0;
        size: auto;
      }

      html, body {
        background: #ffffff !important;
        color: #000000 !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: auto !important;
      }

      .screen-actions {
        display: none !important;
      }

      .receipt-container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 2mm 4mm !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        page-break-inside: avoid;
        page-break-after: avoid;
      }

      table, tr, td, th, tbody, thead, tfoot {
        page-break-inside: avoid !important;
      }

      table {
        table-layout: fixed;
        width: 100%;
        word-wrap: break-word;
      }

      th, td {
        white-space: normal !important;
        padding: 3px 0 !important;
        font-size: 13px !important;
        color: #000000 !important;
        font-weight: bold !important;
      }

      .company-title {
        font-size: 18px !important;
        font-weight: bold !important;
      }

      .slip-title {
        font-size: 15px !important;
        font-weight: bold !important;
      }

      body, div, span, table, th, td, p {
        font-family: 'Courier New', Courier, monospace !important;
        color: #000000 !important;
      }
    }
  </style>
</head>

<body>

  <!-- SCREEN ACTION BAR -->
  <div class="screen-actions">
    <a href="{{ route('stock_transfers.index') }}" class="btn-act btn-act-sec">
      <i class="bi bi-arrow-left"></i> Back
    </a>
    <button class="btn-act" onclick="window.print()">
      <i class="bi bi-printer-fill"></i> Print Receipt
    </button>
    <button class="btn-act" onclick="downloadPDF()">
      <i class="bi bi-file-earmark-pdf-fill"></i> Save PDF
    </button>
  </div>

  <!-- THERMAL RECEIPT CONTAINER -->
  <div class="receipt-container">

    <!-- HEADER LOGO & COMPANY NAME -->
    <div class="center">
      <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" style="max-height: 55px; margin-bottom: 4px;" onerror="this.style.display='none'">
      <div class="company-title">Memon Nimko</div>
      <p style="margin:0; font-size:12px; font-weight:bold;">Sweets & Bakers</p>
    </div>

    <div class="line"></div>
    <div class="center slip-title">Stock Transfer Slip</div>
    <div class="line"></div>

    <!-- META INFORMATION -->
    <table>
      <tr>
        <th width="40%">Transfer ID:</th>
        <td class="right bold">#{{ $transfer->id ?? '-' }}</td>
      </tr>
      <tr>
        <th>Date:</th>
        <td class="right bold">{{ $transfer->created_at?->format('d-m-Y') ?? '-' }}</td>
      </tr>
      <tr>
        <th>Time:</th>
        <td class="right bold">
          @php
            $formattedTime = ($transfer->created_at && $transfer->created_at->format('H:i:s') !== '00:00:00')
              ? $transfer->created_at->format('h:i A')
              : (($transfer->updated_at && $transfer->updated_at->format('H:i:s') !== '00:00:00')
                ? $transfer->updated_at->format('h:i A')
                : \Carbon\Carbon::now()->format('h:i A'));
          @endphp
          {{ $formattedTime }}
        </td>
      </tr>
      <tr>
        <th>From:</th>
        <td class="right bold">{{ $transfer->fromWarehouse->warehouse_name ?? 'Shop Stock' }}</td>
      </tr>
      <tr>
        <th>To:</th>
        <td class="right bold">
          @if($transfer->transfer_to === 'shop')
            {{ $transfer->shop_name ?? 'Shop' }}
          @else
            {{ $transfer->toWarehouse->warehouse_name ?? 'Warehouse' }}
          @endif
        </td>
      </tr>
      <tr>
        <th>Operator:</th>
        <td class="right bold">{{ auth()->user()->name ?? 'Admin' }}</td>
      </tr>
    </table>

    <div class="line"></div>

    <!-- ITEMS TABLE -->
    <table>
      <thead>
        <tr>
          <th width="62%">Item Description</th>
          <th width="38%" class="right">Transfer Qty</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($products as $product)
        <tr>
          <td class="bold">
            {{ $product->item_name }}
            @if(!empty($product->variant_name))
              <br><small style="font-size:11px; font-weight:normal;">({{ $product->variant_name }})</small>
            @endif
          </td>
          <td class="right bold" style="font-size:14px;">{{ number_format($product->transfer_qty, 2) }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="2" class="center">No items found</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <!-- UNIT TOTALS SUMMARY -->
    <div class="line"></div>
    <table>
      @foreach ($unitTotals as $unit => $qty)
      <tr>
        <th class="bold" style="font-size:13px;">Total {{ $unit }}:</th>
        <td class="right bold" style="font-size:14px;">{{ number_format($qty, 2) }}</td>
      </tr>
      @endforeach
    </table>

    @if(!empty($transfer->remarks))
    <div class="line"></div>
    <div style="font-size:12px; text-align:center; font-weight:bold;">
      Remarks: {{ $transfer->remarks }}
    </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
      *** Inventory Transfer Record ***
    </div>

  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    function downloadPDF() {
      const element = document.querySelector('.receipt-container');
      const opt = {
        margin: [2, 2, 2, 2],
        filename: 'Stock_Transfer_Receipt_{{ $transfer->id ?? "doc" }}.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 4, useCORS: true },
        jsPDF: { unit: 'mm', format: [80, 297], orientation: 'portrait' }
      };
      html2pdf().set(opt).from(element).save();
    }
  </script>

</body>

</html>