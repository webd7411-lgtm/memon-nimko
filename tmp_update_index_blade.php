<?php

$filePath = __DIR__ . '/resources/views/admin_panel/sale/index.blade.php';
$content = file_get_contents($filePath);

$targetHtml = '<div class="erp-calc-item">
                <span class="label">Card / Bank</span>
                <span class="val" id="mdlCard">PKR 0</span>
              </div>';

$replacementHtml = '<div class="erp-calc-item">
                <span class="label">Card / Bank</span>
                <span class="val" id="mdlCard">PKR 0</span>
              </div>
              <div class="erp-calc-item" id="mdlTradeOfferBox" style="display: none;">
                <span class="label text-warning-emphasis"><i class="bi bi-gift-fill me-1"></i> Trade Offer Expense</span>
                <span class="val text-warning fw-bold" id="mdlTradeOfferVal">PKR 0</span>
              </div>';

$targetJs = "$('#mdlNet').text('PKR ' + formatNumber(fin.net || 0));";

$replacementJs = "$('#mdlNet').text('PKR ' + formatNumber(fin.net || 0));

                    if (fin.trade_offer_amount > 0) {
                        $('#mdlTradeOfferBox').show();
                        var toHtml = 'PKR ' + formatNumber(fin.trade_offer_amount);
                        if (fin.trade_offer_voucher_evid) {
                            toHtml += ' <span class=\"badge bg-warning text-dark border ms-1\" title=\"Automated Company Expense Voucher\"><i class=\"bi bi-receipt\"></i> ' + esc(fin.trade_offer_voucher_evid) + '</span>';
                        }
                        $('#mdlTradeOfferVal').html(toHtml);
                    } else {
                        $('#mdlTradeOfferBox').hide();
                    }";

// Handle CRLF / LF
$matchedHtml = false;
if (strpos($content, $targetHtml) !== false) {
    $content = str_replace($targetHtml, $replacementHtml, $content);
    $matchedHtml = true;
} else {
    $targetHtmlCRLF = str_replace("\n", "\r\n", $targetHtml);
    $replacementHtmlCRLF = str_replace("\n", "\r\n", $replacementHtml);
    if (strpos($content, $targetHtmlCRLF) !== false) {
        $content = str_replace($targetHtmlCRLF, $replacementHtmlCRLF, $content);
        $matchedHtml = true;
    }
}

$matchedJs = false;
if (strpos($content, $targetJs) !== false) {
    $content = str_replace($targetJs, $replacementJs, $content);
    $matchedJs = true;
} else {
    $targetJsCRLF = str_replace("\n", "\r\n", $targetJs);
    $replacementJsCRLF = str_replace("\n", "\r\n", $replacementJs);
    if (strpos($content, $targetJsCRLF) !== false) {
        $content = str_replace($targetJsCRLF, $replacementJsCRLF, $content);
        $matchedJs = true;
    }
}

echo "Matched HTML: " . ($matchedHtml ? 'yes' : 'no') . "\n";
echo "Matched JS: " . ($matchedJs ? 'yes' : 'no') . "\n";

file_put_contents($filePath, $content);
