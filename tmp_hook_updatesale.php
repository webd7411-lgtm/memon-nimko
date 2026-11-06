<?php

$filePath = __DIR__ . '/app/Http/Controllers/SaleController.php';
$content = file_get_contents($filePath);

$target = "                }\n            }\n\n            DB::commit();\n\n            \$rawCust = (string)(\$sale->customer ?? '');";
$replacement = "                }\n            }\n\n            \$this->syncTradeOfferExpenseVoucher(\$sale, \$combined_trade_offers, \$combined_prices);\n\n            DB::commit();\n\n            \$rawCust = (string)(\$sale->customer ?? '');";

if (strpos($content, $target) !== false) {
    $content = str_replace($target, $replacement, $content);
    file_put_contents($filePath, $content);
    echo "Hooked into updatesale DB::commit successfully (LF).\n";
} else {
    $targetCRLF = str_replace("\n", "\r\n", $target);
    $replacementCRLF = str_replace("\n", "\r\n", $replacement);
    if (strpos($content, $targetCRLF) !== false) {
        $content = str_replace($targetCRLF, $replacementCRLF, $content);
        file_put_contents($filePath, $content);
        echo "Hooked into updatesale DB::commit successfully (CRLF).\n";
    } else {
        echo "Failed to match target in updatesale!\n";
    }
}
