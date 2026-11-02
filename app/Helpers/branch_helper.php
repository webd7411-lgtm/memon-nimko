<?php

use App\Models\Branch;

if (!function_exists('active_branch_id')) {
    /**
     * Get the current active branch ID for the logged-in user or session.
     */
    function active_branch_id(): int
    {
        if (auth()->check()) {
            $user = auth()->user();
            $isSuperAdmin = ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin'));
            if (!$isSuperAdmin && !empty($user->branch_id)) {
                return (int) $user->branch_id;
            }
        }

        if (session()->has('active_branch_id') && session('active_branch_id') !== 'all') {
            return (int) session('active_branch_id');
        }

        if (auth()->check() && !empty(auth()->user()->branch_id)) {
            return (int) auth()->user()->branch_id;
        }

        return 1; // Default to Main Branch ID 1
    }
}

if (!function_exists('is_all_branches')) {
    /**
     * Check if the admin has selected 'All Branches' view.
     */
    function is_all_branches(): bool
    {
        if (auth()->check()) {
            $user = auth()->user();
            $isSuperAdmin = ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin'));
            if (!$isSuperAdmin && !empty($user->branch_id)) {
                return false;
            }
        }
        return session()->get('active_branch_id') === 'all';
    }
}

if (!function_exists('active_branch_name')) {
    /**
     * Get active branch display name.
     */
    function active_branch_name(): string
    {
        if (is_all_branches()) {
            return 'All Branches (Consolidated)';
        }

        $branchId = active_branch_id();
        $branch = Branch::find($branchId);

        return $branch ? $branch->name : 'Main Branch';
    }
}

if (!function_exists('sys_setting')) {
    /**
     * Get a setting value by key with optional default fallback.
     */
    function sys_setting(string $key, $default = null)
    {
        static $settingsCache = null;
        if ($settingsCache === null) {
            try {
                $settingsCache = \App\Models\Setting::pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                $settingsCache = [];
            }
        }
        return (isset($settingsCache[$key]) && $settingsCache[$key] !== '' && $settingsCache[$key] !== null)
            ? $settingsCache[$key] 
            : $default;
    }
}

if (!function_exists('shop_name')) {
    /**
     * Get the customer-facing Store / Shop / Company name for receipts, invoices, and vouchers.
     */
    function shop_name(): string
    {
        $name = sys_setting('shop_name');
        if (empty($name)) {
            $name = sys_setting('company_name');
        }
        if (empty($name)) {
            $name = sys_setting('software_name');
        }
        return !empty($name) ? $name : 'Proteinzo';
    }
}

if (!function_exists('shop_tagline')) {
    /**
     * Get the customer-facing Store / Shop tagline for receipts, invoices, and vouchers.
     */
    function shop_tagline(): string
    {
        return sys_setting('shop_tagline') ?: '';
    }
}

if (!function_exists('developer_name')) {
    /**
     * Get the software developer / vendor brand name.
     */
    function developer_name(): string
    {
        return sys_setting('developer_name') ?: 'ProWave Technologies';
    }
}

if (!function_exists('receipt_logo_url')) {
    /**
     * Get the receipt / store logo absolute URL if one has been uploaded and physically exists.
     * Returns null if no logo is configured or file is missing, so no empty/broken image or placeholder is rendered.
     */
    function receipt_logo_url(): ?string
    {
        $logo = sys_setting('shop_logo');
        if (empty($logo)) {
            $logo = sys_setting('receipt_logo');
        }

        if (empty($logo)) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $logo)) {
            return $logo;
        }

        $cleanPath = ltrim(str_replace('\\', '/', $logo), '/');
        $fullPath = public_path($cleanPath);

        if (file_exists($fullPath)) {
            return asset($cleanPath);
        }

        return null;
    }
}

if (!function_exists('format_consumption_qty')) {
    /**
     * Format a consumption quantity (e.g. Grams / ML).
     * If unit is gram/gm/grams/g and value >= 1000, converts to kg & gm (e.g. 1kg 200g or 37kg 984.45g).
     */
    function format_consumption_qty($val, $unit = 'gram'): string
    {
        $val = (float)$val;
        $unitLower = strtolower(trim($unit));
        $isGram = in_array($unitLower, ['gram', 'grams', 'gm', 'g', 'grm', 'grms']);

        if (!$isGram) {
            return number_format($val, 2) . ' ' . $unit;
        }

        $isNegative = $val < 0;
        $grams = abs($val);

        if ($grams >= 1000) {
            $kg = floor($grams / 1000);
            $gm = round(fmod($grams, 1000), 2);
            $prefix = $isNegative ? '-' : '';
            if ($gm > 0) {
                // If fractional gram exists, display it cleanly
                $gmStr = ($gm == floor($gm)) ? (int)$gm : number_format($gm, 2);
                return $prefix . number_format($kg) . 'kg ' . $gmStr . 'gm';
            }
            return $prefix . number_format($kg) . 'kg';
        }

        $gmStr = ($grams == floor($grams)) ? (int)$grams : number_format($grams, 2);
        return ($isNegative ? '-' : '') . $gmStr . ' gm';
    }
}

if (!function_exists('can_view_stock_values')) {
    /**
     * Check if the authenticated user is allowed to view financial stock valuation
     * (FG Value, Raw Material Value, Total Inventory Value).
     * ONLY allowed for Super Admin.
     */
    function can_view_stock_values($user = null): bool
    {
        if (!$user) {
            $user = auth()->user();
        }
        if (!$user) {
            return false;
        }

        // 1. Check Super Admin email
        if ($user->email === 'admin@admin.com') {
            return true;
        }

        // 2. Check Spatie hasRole for Super Admin
        if (method_exists($user, 'hasRole')) {
            try {
                if ($user->hasRole('super-admin') || $user->hasRole('Super Admin')) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Ignore if role check fails
            }
        }

        // 3. Check loaded roles relation
        if (method_exists($user, 'roles') && $user->roles) {
            foreach ($user->roles as $role) {
                $roleName = strtolower(trim($role->name ?? ''));
                if ($roleName === 'super-admin' || $roleName === 'super admin') {
                    return true;
                }
            }
        }

        return false;
    }
}
