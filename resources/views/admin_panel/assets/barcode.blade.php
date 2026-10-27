<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Tag - {{ $asset->asset_code }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f1f5f9; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }

        .controls { margin-bottom: 20px; display: flex; gap: 10px; }
        .btn { padding: 8px 16px; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; border: none; }
        .btn-print { background: #0284c7; color: #fff; }
        .btn-back { background: #64748b; color: #fff; }

        /* Asset Tag Sticker Layout */
        .tag-card {
            width: 320px;
            background: #fff;
            border: 2px solid #0f172a;
            border-radius: 8px;
            padding: 12px 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            position: relative;
        }

        .tag-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .tag-company { font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; color: #0f172a; }
        .tag-type { font-size: 10px; font-weight: 700; background: #0f172a; color: #fff; padding: 2px 6px; border-radius: 3px; text-transform: uppercase; }

        .tag-body {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 8px;
        }

        .tag-qr img {
            width: 65px;
            height: 65px;
            display: block;
        }

        .tag-info {
            flex: 1;
            overflow: hidden;
        }
        .tag-name {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 4px;
            word-wrap: break-word;
        }
        .tag-meta {
            font-size: 10px;
            color: #475569;
            line-height: 1.4;
        }

        .tag-barcode {
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
        }
        .tag-barcode img {
            max-width: 100%;
            height: 38px;
            display: block;
            margin: 0 auto 3px;
        }
        .tag-code {
            font-family: monospace;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 1px;
            color: #0f172a;
        }

        @media print {
            body { background: #fff; padding: 0; justify-content: flex-start; }
            .controls { display: none; }
            .tag-card { box-shadow: none; border: 1.5px solid #000; page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="controls">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Tag
        </button>
        <button class="btn btn-back" onclick="window.close()">
            <i class="fas fa-times"></i> Close Window
        </button>
    </div>

    <!-- Asset Sticker Card -->
    <div class="tag-card">
        <div class="tag-header">
            <div class="tag-company">{{ shop_name() }}</div>
            <div class="tag-type">{{ $asset->category->code ?? 'ASSET' }}</div>
        </div>

        <div class="tag-body">
            @if($qrPng)
                <div class="tag-qr">
                    <img src="{{ $qrPng }}" alt="QR">
                </div>
            @endif
            <div class="tag-info">
                <div class="tag-name">{{ $asset->name }}</div>
                <div class="tag-meta">
                    <div><strong>Branch:</strong> {{ $asset->branch->name ?? 'Main' }}</div>
                    @if($asset->location_details)
                        <div><strong>Loc:</strong> {{ $asset->location_details }}</div>
                    @endif
                    @if($asset->model_no)
                        <div><strong>Model:</strong> {{ $asset->model_no }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="tag-barcode">
            @if($barcodePng)
                <img src="{{ $barcodePng }}" alt="Barcode">
            @endif
            <div class="tag-code">{{ $asset->asset_code }}</div>
        </div>
    </div>

</body>
</html>
