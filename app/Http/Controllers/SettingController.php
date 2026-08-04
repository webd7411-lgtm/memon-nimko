<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin_panel.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'software_name' => 'required|string|max:255',
            'software_name_urdu' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:500',
            'timezone' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'support_email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:500',
            'twitter' => 'nullable|string|max:500',
            'tiktok' => 'nullable|string|max:500',
            'youtube' => 'nullable|string|max:500',
            'linkedin' => 'nullable|string|max:500',
            'whatsapp' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $keys = [
            'software_name', 'software_name_urdu', 'tagline', 'footer_text',
            'timezone', 'language',
            'address', 'phone', 'mobile', 'email', 'support_email', 'website',
            'facebook', 'instagram', 'twitter', 'tiktok', 'youtube', 'linkedin', 'whatsapp',
        ];

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->$key ?? '']
            );
        }

        return response()->json([
            'success' => 'Settings Updated Successfully',
            'reload' => true
        ]);
    }
}
