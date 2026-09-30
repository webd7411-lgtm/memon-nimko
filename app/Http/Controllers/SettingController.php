<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $branches = Branch::all();
        return view('admin_panel.setting.index', compact('settings', 'branches'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_name' => 'nullable|string|max:255',
            'shop_tagline' => 'nullable|string|max:500',
            'developer_name' => 'nullable|string|max:255',
            'developer_phone' => 'nullable|string|max:50',
            'developer_website' => 'nullable|string|max:255',
            'software_name' => 'required|string|max:255',
            'software_name_urdu' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:500',
            'currency' => 'nullable|string|max:50',
            'currency_symbol' => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:50',
            'default_branch_id' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'toll_free' => 'nullable|string|max:50',
            'email' => 'nullable|string|max:255',
            'support_email' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'ntn' => 'nullable|string|max:100',
            'strn' => 'nullable|string|max:100',
            'gst_no' => 'nullable|string|max:100',
            'facebook' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:500',
            'twitter' => 'nullable|string|max:500',
            'tiktok' => 'nullable|string|max:500',
            'youtube' => 'nullable|string|max:500',
            'linkedin' => 'nullable|string|max:500',
            'invoice_footer_note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $keys = [
            'shop_name', 'shop_tagline', 'developer_name', 'developer_phone', 'developer_website',
            'software_name', 'software_name_urdu', 'tagline', 'footer_text',
            'currency', 'currency_symbol', 'timezone', 'language',
            'default_branch_id', 'address', 'phone', 'mobile', 'toll_free',
            'email', 'support_email', 'website', 'whatsapp',
            'ntn', 'strn', 'gst_no',
            'facebook', 'instagram', 'twitter', 'tiktok', 'youtube', 'linkedin',
            'invoice_footer_note',
        ];

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => trim((string)$request->input($key, ''))]
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => 'Settings Updated Successfully',
                'reload' => true
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Settings Updated Successfully');
    }
}
