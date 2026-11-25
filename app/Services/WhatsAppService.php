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
     * Get base URL for WhatsApp microservice.
     *
     * @return string
     */
    public static function getBaseUrl()
    {
        $configured = config('services.whatsapp.api_url');
        if (!empty($configured) && !str_contains($configured, '127.0.0.1:3000')) {
            return preg_replace('#/(send-message|status|groups|logout)$#', '', rtrim($configured, '/'));
        }

        // Live domain and environment auto-detection
        $host = '';
        if (function_exists('request') && request()) {
            $host = request()->getHost();
        }
        if (empty($host) && isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        }

        $base = base_path();
        if (str_contains($host, 'binsultansweet.com') || 
            str_contains(config('app.url', ''), 'binsultansweet.com') ||
            str_contains($base, 'binsultansweet.com') ||
            str_contains($base, 'binsult1')) {
            return 'https://shell.binsultansweet.com/wa';
        }

        return 'http://127.0.0.1:3000';
    }

    /**
     * Get target API URL for WhatsApp service endpoints (send-message, status, groups, logout).
     *
     * @param string $endpoint
     * @return string
     */
    public static function getApiUrl($endpoint = 'send-message')
    {
        $base = self::getBaseUrl();
        $endpoint = ltrim($endpoint, '/');
        return "{$base}/{$endpoint}";
    }

    /**
     * Send automatic sale invoice / alerts to Customer, Company Official Number, and WhatsApp Group.
     *
     * @param \App\Models\Sale $sale
     * @return bool
     */
    public static function sendSaleInvoice($sale)
    {
        try {
            // Check if WhatsApp feature is enabled overall
            if (!config('services.whatsapp.enabled', true)) {
                return false;
            }

            // Notification preferences from database settings
            $notifyCustomer = sys_setting('whatsapp_notify_customer', '1') == '1';
            $notifyCompany  = sys_setting('whatsapp_notify_company', '0') == '1';
            $notifyGroup    = sys_setting('whatsapp_notify_group', '0') == '1';

            $companyNumber  = sys_setting('whatsapp_company_number', '');
            $groupJid       = sys_setting('whatsapp_group_jid', '');

            // If all notification destinations are disabled, return early
            if (!$notifyCustomer && (!$notifyCompany || empty($companyNumber)) && (!$notifyGroup || empty($groupJid))) {
                return false;
            }

            // 1. Extract Customer Phone & Name
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

            if (($customerName === 'Valued Customer' || empty($customerName)) && !empty($sale->customer_display_name)) {
                if ($sale->customer_display_name !== 'Walk-in Customer') {
                    $customerName = $sale->customer_display_name;
                }
            }

            $formattedCustomerPhone = self::formatNumber($phone);

            // 2. Extract Sale Financial & Meta Details
            $invoiceNo     = $sale->invoice_no ?: ('SAL-' . $sale->id);
            $totalBill     = number_format((float)($sale->total_bill_amount ?? $sale->total_net ?? 0), 2);
            $paidAmount    = number_format((float)($sale->cash ?? 0) + (float)($sale->card ?? 0), 2);
            $change        = number_format((float)($sale->change ?? 0), 2);
            $date          = $sale->created_at ? $sale->created_at->format('d-M-Y h:i A') : date('d-M-Y h:i A');
            $itemsList     = array_filter(explode(',', (string)($sale->product ?? '')));
            $itemsCount    = count($itemsList);
            $paymentMethod = ($sale->card > 0 && $sale->cash > 0) ? 'Cash & Card' : (($sale->card > 0) ? 'Card' : 'Cash');
            $cashierName   = $sale->user ? $sale->user->name : 'POS Counter';

            // 3. Generate Native PDF Document for WhatsApp Attachment (shared for all recipients)
            $pdfBase64 = self::generateInvoicePdf($sale);
            $hasPdf = !empty($pdfBase64);

            // Common Placeholder Replacements
            $replacements = [
                '{company_name}'    => shop_name(),
                '{customer_name}'   => $customerName,
                '{customer_phone}'  => $formattedCustomerPhone ?: 'N/A',
                '{invoice_no}'      => $invoiceNo,
                '{date}'            => $date,
                '{total_bill}'      => $totalBill,
                '{paid_amount}'     => $paidAmount,
                '{change}'          => $change,
                '{payment_method}'  => $paymentMethod,
                '{items_count}'     => $itemsCount,
                '{cashier_name}'    => $cashierName,
                '{attachment_note}' => $hasPdf ? "📎 _Aapka invoice PDF document sath attach kar diya gaya hai._" : "",
            ];

            $anySent = false;

            // ── RECIPIENT 1: CUSTOMER WHATSAPP ───────────────────────────────
            if ($notifyCustomer && !empty($formattedCustomerPhone)) {
                $custTemplate = self::getTemplate();
                $custMsg = str_replace(array_keys($replacements), array_values($replacements), $custTemplate);
                $custMsg = preg_replace("/\n{3,}/", "\n\n", trim($custMsg));

                $sent = self::sendDirectMessage($formattedCustomerPhone, $custMsg, $pdfBase64, "Invoice-{$invoiceNo}.pdf");
                if ($sent) {
                    $anySent = true;
                    Log::info("WhatsApp invoice sent to Customer ({$formattedCustomerPhone}) for {$invoiceNo}");
                }
            }

            // ── RECIPIENT 2: COMPANY OFFICIAL NUMBER ─────────────────────────
            if ($notifyCompany && !empty($companyNumber)) {
                $compTarget = self::formatNumber($companyNumber);
                if ($compTarget) {
                    $compTemplate = self::getCompanyTemplate();
                    $compMsg = str_replace(array_keys($replacements), array_values($replacements), $compTemplate);
                    $compMsg = preg_replace("/\n{3,}/", "\n\n", trim($compMsg));

                    $sendCompPdf = sys_setting('whatsapp_company_send_pdf', '1') == '1';
                    $sent = self::sendDirectMessage($compTarget, $compMsg, $sendCompPdf ? $pdfBase64 : null, "Invoice-{$invoiceNo}.pdf");
                    if ($sent) {
                        $anySent = true;
                        Log::info("WhatsApp sale alert sent to Company Official Number ({$compTarget}) for {$invoiceNo}");
                    }
                }
            }

            // ── RECIPIENT 3: WHATSAPP GROUP ──────────────────────────────────
            if ($notifyGroup && !empty($groupJid)) {
                $groupTemplate = self::getGroupTemplate();
                $groupMsg = str_replace(array_keys($replacements), array_values($replacements), $groupTemplate);
                $groupMsg = preg_replace("/\n{3,}/", "\n\n", trim($groupMsg));

                $sendGroupPdf = sys_setting('whatsapp_group_send_pdf', '0') == '1';
                $sent = self::sendDirectMessage($groupJid, $groupMsg, $sendGroupPdf ? $pdfBase64 : null, "Invoice-{$invoiceNo}.pdf");
                if ($sent) {
                    $anySent = true;
                    Log::info("WhatsApp sale alert sent to WhatsApp Group ({$groupJid}) for {$invoiceNo}");
                }
            }

            return $anySent;
        } catch (\Throwable $e) {
            // Keep fail-safe: sale process must never be interrupted if WhatsApp service encounters issues
            Log::error("WhatsAppService Error for Sale #{$sale->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Default WhatsApp invoice message template for Customer.
     *
     * @return string
     */
    public static function getDefaultTemplate()
    {
        return "🏪 *{company_name}*\n"
            . "Mohtaram *{customer_name}*, aapki purchase ka shukriya!\n\n"
            . "📄 *Invoice No:* {invoice_no}\n"
            . "📅 *Date:* {date}\n"
            . "💰 *Total Bill:* Rs. {total_bill}\n"
            . "💵 *Paid Amount:* Rs. {paid_amount}\n"
            . "⚖️ *Change / Balance:* Rs. {change}\n\n"
            . "{attachment_note}\n\n"
            . "_Thank you for shopping with {company_name}!_";
    }

    /**
     * Get active customer message template from settings or default.
     *
     * @return string
     */
    public static function getTemplate()
    {
        $custom = sys_setting('whatsapp_invoice_template');
        return !empty($custom) ? $custom : self::getDefaultTemplate();
    }

    /**
     * Default Company Owner / Admin Sale Alert Template.
     *
     * @return string
     */
    public static function getDefaultCompanyTemplate()
    {
        return "🏢 *{company_name} - NEW SALE ALERT*\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "📄 *Invoice No:* {invoice_no}\n"
            . "📅 *Date:* {date}\n"
            . "👤 *Customer:* {customer_name} ({customer_phone})\n"
            . "💰 *Total Bill:* Rs. {total_bill}\n"
            . "💳 *Paid:* Rs. {paid_amount} ({payment_method})\n"
            . "⚖️ *Change:* Rs. {change}\n"
            . "📦 *Items:* {items_count} item(s)\n"
            . "🧑‍💼 *Cashier:* {cashier_name}\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "{attachment_note}";
    }

    /**
     * Get active company sale alert template.
     *
     * @return string
     */
    public static function getCompanyTemplate()
    {
        $custom = sys_setting('whatsapp_company_template');
        return !empty($custom) ? $custom : self::getDefaultCompanyTemplate();
    }

    /**
     * Default WhatsApp Group Broadcast Template.
     *
     * @return string
     */
    public static function getDefaultGroupTemplate()
    {
        return "🔔 *NEW SALE COMPLETED - {company_name}*\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "📄 *Invoice:* {invoice_no}\n"
            . "👤 *Customer:* {customer_name}\n"
            . "💰 *Amount:* Rs. {total_bill}\n"
            . "💳 *Payment:* {payment_method}\n"
            . "🕒 *Time:* {date}\n"
            . "🧑‍💼 *Counter:* {cashier_name}\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "🌟 _Great job team!_";
    }

    /**
     * Get active group broadcast template.
     *
     * @return string
     */
    public static function getGroupTemplate()
    {
        $custom = sys_setting('whatsapp_group_template');
        return !empty($custom) ? $custom : self::getDefaultGroupTemplate();
    }

    /**
     * Send direct message to any individual phone number or group JID via WhatsApp microservice.
     *
     * @param string $recipient
     * @param string $message
     * @param string|null $pdfBase64
     * @param string|null $filename
     * @return bool
     */
    public static function sendDirectMessage($recipient, $message, $pdfBase64 = null, $filename = null)
    {
        try {
            $apiUrl = self::getApiUrl('send-message');
            $payload = [
                'number'  => $recipient,
                'message' => $message,
            ];

            if (!empty($pdfBase64)) {
                $payload['pdf_base64'] = $pdfBase64;
                $payload['filename']   = $filename ?: 'Document.pdf';
            }

            $response = Http::withoutVerifying()->timeout(12)->post($apiUrl, $payload);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("WhatsAppService::sendDirectMessage failed to [{$recipient}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all participating WhatsApp groups from connected WhatsApp account.
     *
     * @return array
     */
    public static function fetchWhatsAppGroups()
    {
        try {
            $apiUrl = self::getApiUrl('groups');
            $response = Http::withoutVerifying()->timeout(8)->get($apiUrl);
            if ($response->successful()) {
                $data = $response->json();
                return $data['groups'] ?? [];
            }
            return [];
        } catch (\Throwable $e) {
            Log::warning("WhatsAppService::fetchWhatsAppGroups error: " . $e->getMessage());
            return [];
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

            $fontDir = storage_path('fonts');
            if (!is_dir($fontDir)) {
                @mkdir($fontDir, 0777, true);
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin_panel.sale.pdf_invoice', [
                'sale'   => $sale,
                'items'  => $items,
                'branch' => $sale->branch,
            ]);
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('isRemoteEnabled', false);
            $pdf->setOption('fontDir', $fontDir);
            $pdf->setOption('fontCache', $fontDir);
            $pdf->setOption('tempDir', sys_get_temp_dir());

            return base64_encode($pdf->output());
        } catch (\Throwable $e) {
            Log::error("Invoice PDF generation error for Sale #{$sale->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send any custom text message or document to a WhatsApp number.
     *
     * @param string $phone
     * @param string $message
     * @param string|null $pdfBase64
     * @param string $filename
     * @return array ['success' => bool, 'message' => string]
     */
    public static function sendCustomMessage($phone, $message, $pdfBase64 = null, $filename = 'Invoice.pdf')
    {
        try {
            $formattedPhone = self::formatNumber($phone);
            if (empty($formattedPhone)) {
                return ['success' => false, 'message' => 'Invalid phone number format.'];
            }

            $apiUrl = self::getApiUrl();
            $payload = [
                'number'  => $formattedPhone,
                'message' => $message,
            ];

            if (!empty($pdfBase64)) {
                $payload['pdf_base64'] = $pdfBase64;
                $payload['filename']   = $filename;
            }

            $response = Http::withoutVerifying()->timeout(15)->post($apiUrl, $payload);

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
            $apiUrl = self::getApiUrl();
            $statusUrl = preg_replace('/\/send-message$/', '/status', $apiUrl);

            $response = Http::withoutVerifying()->timeout(5)->get($statusUrl);
            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'connected' => false, 'error' => 'Service unreachable at ' . $statusUrl];
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
            $apiUrl = self::getApiUrl();
            $logoutUrl = preg_replace('/\/send-message$/', '/logout', $apiUrl);

            $response = Http::withoutVerifying()->timeout(5)->post($logoutUrl);
            return ['success' => true, 'message' => 'WhatsApp disconnected successfully.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
