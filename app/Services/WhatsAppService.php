<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\DistributorCustomer;
use App\Models\Distributor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Clean and format phone number to international WhatsApp format (e.g. 923001234567).
     *
     * @param string|null $phone
     * @return string|null
     */
    public static function formatNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Keep only digits
        $digits = preg_replace('/[^0-9]/', '', (string)$phone);

        // Normalize Pakistani phone formats
        if (str_starts_with($digits, '0092')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '03')) {
            $digits = '92' . substr($digits, 1);
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '3')) {
            $digits = '92' . $digits;
        }

        // WhatsApp number should be at least 10 digits
        if (strlen($digits) < 10) {
            return null;
        }

        return $digits;
    }

    /**
     * Send automatic invoice message to customer WhatsApp upon saving sale.
     *
     * @param \App\Models\Sale $sale
     * @return bool
     */
    public static function sendSaleInvoice($sale)
    {
        try {
            // Check if feature is enabled in config/services.php (.env)
            if (!config('services.whatsapp.enabled', true)) {
                return false;
            }

            // Extract Customer Phone & Name
            $phone = null;
            $customerName = 'Valued Customer';
            $rawCust = (string)($sale->customer ?? '');

            if ($sale->customer_relation) {
                $phone = $sale->customer_relation->mobile ?: $sale->customer_relation->phone;
                $customerName = $sale->customer_relation->customer_name ?: 'Valued Customer';
            } elseif (!empty($rawCust) && is_numeric($rawCust)) {
                $foundC = Customer::find($rawCust);
                if ($foundC) {
                    $phone = $foundC->mobile ?: $foundC->phone;
                    $customerName = $foundC->customer_name ?: 'Valued Customer';
                }
            } elseif (str_starts_with($rawCust, 'dc_')) {
                $dcId = (int) str_replace('dc_', '', $rawCust);
                $dc = DistributorCustomer::find($dcId);
                if ($dc) {
                    $phone = $dc->phone;
                    $customerName = $dc->shop_name ?: ($dc->owner_name ?: 'Retail Shop');
                }
            } elseif (str_starts_with($rawCust, 'dist_')) {
                $distId = (int) str_replace('dist_', '', $rawCust);
                $dist = Distributor::find($distId);
                if ($dist) {
                    $phone = $dist->phone ?? null;
                    $customerName = $dist->name ?: 'Distributor';
                }
            }

            // If still empty customer name, fallback to display name attribute
            if (($customerName === 'Valued Customer' || empty($customerName)) && !empty($sale->customer_display_name)) {
                if ($sale->customer_display_name !== 'Walk-in Customer') {
                    $customerName = $sale->customer_display_name;
                }
            }

            // Format number
            $formattedPhone = self::formatNumber($phone);
            if (empty($formattedPhone)) {
                // No valid mobile number for this customer/walk-in, skip quietly
                return false;
            }

            // Extract Sale Details
            $invoiceNo  = $sale->invoice_no ?: ('SAL-' . $sale->id);
            $totalBill  = number_format((float)($sale->total_bill_amount ?? $sale->total_net ?? 0), 2);
            $paidAmount = number_format((float)($sale->cash ?? 0) + (float)($sale->card ?? 0), 2);
            $change     = number_format((float)($sale->change ?? 0), 2);
            $date       = $sale->created_at ? $sale->created_at->format('d-M-Y h:i A') : date('d-M-Y h:i A');

            // Generate Native PDF for WhatsApp Attachment
            $pdfBase64 = self::generateInvoicePdf($sale);

            // Prepare Clean WhatsApp Message Body (No download links, direct PDF attachment)
            $message  = "🏪 *MEMON NIMKO*\n";
            $message .= "Mohtaram *{$customerName}*, aapki purchase ka shukriya!\n\n";
            $message .= "📄 *Invoice No:* {$invoiceNo}\n";
            $message .= "📅 *Date:* {$date}\n";
            $message .= "💰 *Total Bill:* Rs. {$totalBill}\n";
            $message .= "💵 *Paid Amount:* Rs. {$paidAmount}\n";
            if ((float)($sale->change ?? 0) > 0) {
                $message .= "⚖️ *Change / Balance:* Rs. {$change}\n";
            }
            $message .= "\n📎 _Aapka invoice PDF document sath attach kar diya gaya hai._\n\n";
            $message .= "_Thank you for shopping with Memon Nimko!_";

            // Send to local Node.js WhatsApp microservice
            $apiUrl = config('services.whatsapp.api_url', 'http://127.0.0.1:3000/send-message');

            $payload = [
                'number'     => $formattedPhone,
                'message'    => $message,
                'pdf_base64' => $pdfBase64,
                'filename'   => "Invoice-{$invoiceNo}.pdf",
            ];

            $response = Http::withoutVerifying()->timeout(10)->post($apiUrl, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp invoice sent successfully for Invoice {$invoiceNo} to {$formattedPhone}");
                return true;
            } else {
                Log::warning("WhatsApp send failed for Invoice {$invoiceNo} [Status: " . $response->status() . "]: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            // Keep fail-safe: sale process should never be interrupted if WhatsApp service is unreachable
            Log::error("WhatsAppService Error for Sale #{$sale->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate base64 PDF of the sale invoice.
     *
     * @param \App\Models\Sale $sale
     * @return string|null
     */
    public static function generateInvoicePdf($sale)
    {
        try {
            $products    = explode(',', $sale->product ?? '');
            $codes       = explode(',', $sale->product_code ?? '');
            $units       = explode(',', $sale->unit ?? '');
            $prices      = explode(',', $sale->per_price ?? '');
            $discounts   = explode(',', $sale->per_discount ?? '');
            $tradeOffers = !empty($sale->trade_offer) ? explode(',', $sale->trade_offer) : [];
            $qtys        = explode(',', $sale->qty ?? '');
            $totals      = explode(',', $sale->per_total ?? '');
            $variant_ids = explode(',', $sale->variant_id ?? '');

            $items = [];
            $productIds = array_unique(array_filter($products));
            $productMap = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');
            $variantMap = \App\Models\ProductVariant::whereIn('id', array_filter($variant_ids))->get()->keyBy('id');

            foreach ($products as $idx => $pid) {
                if (empty($pid)) continue;
                $prod = $productMap[$pid] ?? null;
                $var  = !empty($variant_ids[$idx]) ? ($variantMap[$variant_ids[$idx]] ?? null) : null;
                $items[] = [
                    'name'        => $prod ? $prod->item_name : ($codes[$idx] ?? 'Item'),
                    'code'        => $codes[$idx] ?? '',
                    'unit'        => $units[$idx] ?? '',
                    'qty'         => $qtys[$idx] ?? 0,
                    'price'       => $prices[$idx] ?? 0,
                    'discount'    => $discounts[$idx] ?? 0,
                    'trade_offer' => $tradeOffers[$idx] ?? 0,
                    'total'       => $totals[$idx] ?? 0,
                    'variant'     => $var ? ($var->color ?: $var->size) : '',
                ];
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin_panel.sale.pdf_invoice', [
                'sale'   => $sale,
                'items'  => $items,
                'branch' => $sale->branch,
            ]);
            $pdf->setPaper('a4', 'portrait');

            return base64_encode($pdf->output());
        } catch (\Exception $e) {
            Log::error("Invoice PDF generation error for Sale #{$sale->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send any custom text message to a WhatsApp number.
     *
     * @param string $phone
     * @param string $message
     * @return array ['success' => bool, 'message' => string]
     */
    public static function sendCustomMessage($phone, $message)
    {
        try {
            $formattedPhone = self::formatNumber($phone);
            if (empty($formattedPhone)) {
                return ['success' => false, 'message' => 'Invalid phone number format.'];
            }

            $apiUrl = config('services.whatsapp.api_url', 'http://127.0.0.1:3000/send-message');
            $response = Http::withoutVerifying()->timeout(5)->post($apiUrl, [
                'number'  => $formattedPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => "Message sent to {$formattedPhone} successfully."];
            }

            $body = $response->json();
            return [
                'success' => false,
                'message' => $body['error'] ?? 'Failed to send message via WhatsApp gateway.'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Service offline or error: ' . $e->getMessage()];
        }
    }

    /**
     * Check live connection status from Node.js service.
     *
     * @return array
     */
    public static function getStatus()
    {
        try {
            // Convert /send-message to /status or base url
            $apiUrl = config('services.whatsapp.api_url', 'http://127.0.0.1:3000/send-message');
            $statusUrl = preg_replace('/\/send-message$/', '/status', $apiUrl);

            $response = Http::withoutVerifying()->timeout(5)->get($statusUrl);
            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'connected' => false, 'error' => 'Service unreachable'];
        } catch (\Exception $e) {
            return ['success' => false, 'connected' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Disconnect / Logout WhatsApp session.
     *
     * @return array
     */
    public static function disconnect()
    {
        try {
            $apiUrl = config('services.whatsapp.api_url', 'http://127.0.0.1:3000/send-message');
            $logoutUrl = preg_replace('/\/send-message$/', '/logout', $apiUrl);

            $response = Http::withoutVerifying()->timeout(5)->all()->post($logoutUrl);
            return ['success' => true, 'message' => 'WhatsApp disconnected successfully.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
