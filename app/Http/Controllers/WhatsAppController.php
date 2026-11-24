<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;

class WhatsAppController extends Controller
{
    /**
     * Display the WhatsApp QR & Management screen in Admin Panel.
     */
    public function index()
    {
        $status = WhatsAppService::getStatus();
        return view('admin_panel.whatsapp.index', compact('status'));
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
     * Send test message from Admin Panel.
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string',
            'message' => 'required|string',
        ]);

        $res = WhatsAppService::sendCustomMessage($request->phone, $request->message);

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
}
