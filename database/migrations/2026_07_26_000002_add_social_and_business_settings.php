<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            // Business Details
            ['key' => 'tagline', 'value' => 'Your Trusted Business Partner'],
            ['key' => 'footer_text', 'value' => '© 2025 All rights reserved.'],
            ['key' => 'currency', 'value' => 'PKR'],
            ['key' => 'currency_symbol', 'value' => 'Rs.'],
            ['key' => 'timezone', 'value' => 'Asia/Karachi'],
            ['key' => 'language', 'value' => 'English'],

            // Social Media
            ['key' => 'facebook', 'value' => ''],
            ['key' => 'instagram', 'value' => ''],
            ['key' => 'twitter', 'value' => ''],
            ['key' => 'tiktok', 'value' => ''],
            ['key' => 'youtube', 'value' => ''],
            ['key' => 'linkedin', 'value' => ''],
            ['key' => 'whatsapp', 'value' => ''],

            // Contact Details
            ['key' => 'mobile', 'value' => ''],
            ['key' => 'toll_free', 'value' => ''],
            ['key' => 'support_email', 'value' => ''],

            // Tax / Registration
            ['key' => 'ntn', 'value' => ''],
            ['key' => 'strn', 'value' => ''],
            ['key' => 'gst_no', 'value' => ''],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $keys = [
            'tagline', 'footer_text', 'currency', 'currency_symbol', 'timezone', 'language',
            'facebook', 'instagram', 'twitter', 'tiktok', 'youtube', 'linkedin', 'whatsapp',
            'mobile', 'toll_free', 'support_email',
            'ntn', 'strn', 'gst_no',
        ];
        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};
