<?php

use App\Models\Branch;

if (!function_exists('active_branch_id')) {
    /**
     * Get the current active branch ID for the logged-in user or session.
     */
    function active_branch_id(): int
    {
        if (session()->has('active_branch_id') && session('active_branch_id') !== 'all') {
            return (int) session('active_branch_id');
        }

        if (auth()->check() && auth()->user()->branch_id) {
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
