<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use App\Models\Setting;

class WhatsAppController extends Controller
{
    /**
     * Display the WhatsApp QR & Management screen in Admin Panel.
     */
    public function index()
    {
        $status = WhatsAppService::getStatus();
        $template = WhatsAppService::getTemplate();
        $defaultTemplate = WhatsAppService::getDefaultTemplate();

        $companyTemplate = WhatsAppService::getCompanyTemplate();
        $defaultCompanyTemplate = WhatsAppService::getDefaultCompanyTemplate();

        $groupTemplate = WhatsAppService::getGroupTemplate();
        $defaultGroupTemplate = WhatsAppService::getDefaultGroupTemplate();

        // Multi-recipient preferences
        $notifyCustomer = sys_setting('whatsapp_notify_customer', '1');
        $notifyCompany  = sys_setting('whatsapp_notify_company', '0');
        $companyNumber  = sys_setting('whatsapp_company_number', '');
        $companySendPdf = sys_setting('whatsapp_company_send_pdf', '1');

        $notifyGroup    = sys_setting('whatsapp_notify_group', '0');
        $groupJid       = sys_setting('whatsapp_group_jid', '');
        $groupName      = sys_setting('whatsapp_group_name', '');
        $groupSendPdf   = sys_setting('whatsapp_group_send_pdf', '0');

        // Fetch groups if connected
        $groups = [];
        if (!empty($status['connected'])) {
            $groups = WhatsAppService::fetchWhatsAppGroups();
        }

        return view('admin_panel.whatsapp.index', compact(
            'status',
            'template',
            'defaultTemplate',
            'companyTemplate',
            'defaultCompanyTemplate',
            'groupTemplate',
            'defaultGroupTemplate',
            'notifyCustomer',
            'notifyCompany',
            'companyNumber',
            'companySendPdf',
            'notifyGroup',
            'groupJid',
            'groupName',
            'groupSendPdf',
            'groups'
        ));
    }

    /**
     * API endpoint for live AJAX polling of QR and connection state.
     */
    public function status()
    {
        $status = WhatsAppService::getStatus();
        return response()->json($status);
    }

    /**
     * API endpoint to dynamically load participating WhatsApp groups.
     */
    public function getGroups()
    {
        $groups = WhatsAppService::fetchWhatsAppGroups();
        return response()->json([
            'success' => true,
            'count'   => count($groups),
            'groups'  => $groups,
        ]);
    }

    /**
     * Save multi-recipient notification settings.
     */
    public function updateNotificationSettings(Request $request)
    {
        $settings = [
            'whatsapp_notify_customer'  => $request->has('whatsapp_notify_customer') ? '1' : '0',
            'whatsapp_notify_company'   => $request->has('whatsapp_notify_company') ? '1' : '0',
            'whatsapp_company_number'   => trim($request->input('whatsapp_company_number', '')),
            'whatsapp_company_send_pdf' => $request->has('whatsapp_company_send_pdf') ? '1' : '0',
            'whatsapp_notify_group'     => $request->has('whatsapp_notify_group') ? '1' : '0',
            'whatsapp_group_jid'        => trim($request->input('whatsapp_group_jid', '')),
            'whatsapp_group_name'       => trim($request->input('whatsapp_group_name', '')),
            'whatsapp_group_send_pdf'   => $request->has('whatsapp_group_send_pdf') ? '1' : '0',
        ];

        if ($request->has('whatsapp_company_template')) {
            $settings['whatsapp_company_template'] = $request->input('whatsapp_company_template');
        }
        if ($request->has('whatsapp_group_template')) {
            $settings['whatsapp_group_template'] = $request->input('whatsapp_group_template');
        }

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'WhatsApp notification recipients and preferences saved successfully!');
    }

    /**
     * Send test alert to Company Official Number.
     */
    public function sendCompanyTest(Request $request)
    {
        $request->validate(['company_number' => 'required|string']);
        $target = WhatsAppService::formatNumber($request->company_number);
        if (!$target) {
            return back()->with('error', 'Invalid company phone number format. Please provide a valid mobile number (e.g. 03001234567).');
        }

        $message = "🏢 *Shell Pakistan - Official Alert Test*\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "Assalam-o-Alaikum! Yeh Company Official Number par sale alert notification ka successful test message hai.\n"
            . "📅 Date: " . date('d-M-Y h:i A') . "\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "✅ System is active & ready.";

        $pdfBase64 = null;
        if ($request->boolean('send_pdf')) {
            $latestSale = \App\Models\Sale::latest()->first();
            if ($latestSale) {
                $pdfBase64 = WhatsAppService::generateInvoicePdf($latestSale);
            }
        }

        $sent = WhatsAppService::sendDirectMessage($target, $message, $pdfBase64, 'Company-Test-Invoice.pdf');
        if ($sent) {
            return back()->with('success', "Test alert successfully sent to Company Number (+{$target})!");
        }

        return back()->with('error', "Failed to send alert to Company Number. Ensure WhatsApp service is connected.");
    }

    /**
     * Send test alert to selected WhatsApp Group.
     */
    public function sendGroupTest(Request $request)
    {
        $request->validate(['group_jid' => 'required|string']);
        $groupJid = trim($request->group_jid);

        $message = "🔔 *Shell Pakistan - Group Alert Test*\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "Assalam-o-Alaikum team! Yeh Sales WhatsApp Group par automated sale notification ka successful test message hai.\n"
            . "📅 Date: " . date('d-M-Y h:i A') . "\n"
            . "━━━━━━━━━━━━━━━━━━━━━━\n"
            . "✅ Bot connected to this group successfully!";

        $pdfBase64 = null;
        if ($request->boolean('send_pdf')) {
            $latestSale = \App\Models\Sale::latest()->first();
            if ($latestSale) {
                $pdfBase64 = WhatsAppService::generateInvoicePdf($latestSale);
            }
        }

        $sent = WhatsAppService::sendDirectMessage($groupJid, $message, $pdfBase64, 'Group-Test-Invoice.pdf');
        if ($sent) {
            return back()->with('success', "Test message successfully delivered to WhatsApp Group!");
        }

        return back()->with('error', "Failed to deliver message to group. Ensure WhatsApp account is an active participant in this group.");
    }

    /**
     * Send test message to any customer number.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string',
            'message' => 'nullable|string',
        ]);

        $phone = $request->phone;
        $message = $request->message ?: 'Shell Pakistan: WhatsApp Invoice System is working!';
        $pdfBase64 = null;

        if ($request->boolean('send_pdf')) {
            $latestSale = \App\Models\Sale::latest()->first();
            if ($latestSale) {
                $pdfBase64 = WhatsAppService::generateInvoicePdf($latestSale);
                if (empty($pdfBase64)) {
                    return back()->with('error', 'PDF Generation failed! Check storage/logs/laravel.log')->withInput();
                }
            } else {
                return back()->with('error', 'No sale found in database to generate sample invoice PDF.')->withInput();
            }
        }

        $res = WhatsAppService::sendCustomMessage($phone, $message, $pdfBase64, 'Sample-Invoice.pdf');

        if ($res['success']) {
            return back()->with('success', $res['message']);
        }

        return back()->with('error', $res['message'])->withInput();
    }

    /**
     * Disconnect WhatsApp session / change number.
     */
    public function disconnect()
    {
        $res = WhatsAppService::disconnect();
        return back()->with('info', 'WhatsApp disconnected. Scan the new QR code below to connect.');
    }

    /**
     * Update WhatsApp invoice message template.
     */
    public function updateTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
        ]);

        Setting::updateOrCreate(
            ['key' => 'whatsapp_invoice_template'],
            ['value' => $request->template]
        );

        return back()->with('success', 'WhatsApp invoice message template saved successfully!');
    }

    /**
     * Reset WhatsApp message template to default.
     */
    public function resetTemplate()
    {
        Setting::where('key', 'whatsapp_invoice_template')->delete();
        return back()->with('info', 'WhatsApp message template reset to default!');
    }
}
