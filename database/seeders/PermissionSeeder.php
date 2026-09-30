<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Complete permission inventory for the whole software.
     * Idempotent: existing permissions are kept, new ones are created.
     */
    public function run(): void
    {
        $permissions = [
            // ===================== DASHBOARD =====================
            'Dashboard',

            // ===================== PRODUCTS =====================
            'Products',
            'View Product',
            'Create Product',
            'Edit Product',
            'Delete Product',
            'Print Product Barcode',
            'Bulk Edit Products',
            'Reset Product Stock',
            'Manage Product BOM',

            // ===================== CATEGORY =====================
            'Category',
            'Create Category',
            'Edit Category',
            'Delete Category',

            // ===================== BRANDS =====================
            'Brands',
            'Create Brand',
            'Edit Brand',
            'Delete Brand',

            // ===================== SUB CATEGORY =====================
            'Sub Category',
            'Create Sub Category',
            'Edit Sub Category',
            'Delete Sub Category',

            // ===================== UNIT =====================
            'Unit',
            'Units',
            'Create Unit',
            'Edit Unit',
            'Delete Unit',

            // ===================== DISCOUNT PRODUCTS =====================
            'Discount Products',
            'Create Discount',
            'Update Discount Status',
            'Print Discount Barcode',

            // ===================== SALES & POS =====================
            'Sales',
            'Create Sale',
            'Edit Sale',
            'Delete Sale',
            'Sale Exchange',
            'Print Sale Invoice',
            'Print Sale DC',
            'Print Sale Receipt',
            'Print POS Bill',

            // ===================== SALE RETURN =====================
            'Sale Return',
            'Create Sale Return',
            'Delete Sale Return',
            'Print Sale Return',

            // ===================== BOOKINGS =====================
            'Bookings',
            'Create Booking',
            'Edit Booking',
            'Delete Booking',
            'Convert Booking To Sale',
            'Print Booking Receipt',

            // ===================== CUSTOMER =====================
            'Customer',
            'Create Customer',
            'Edit Customer',
            'Delete Customer',
            'Toggle Customer Status',
            'Customer Payments',
            'Print Customer Receipt',

            // ===================== VENDOR =====================
            'Vendor',
            'Create Vendor',
            'Edit Vendor',
            'Delete Vendor',
            'Vendor Payments',
            'Create Vendor Payment',
            'Edit Vendor Payment',
            'Delete Vendor Payment',
            'Vendor Bilties',
            'Create Vendor Bilty',
            'Print Vendor Receipt',

            // ===================== PURCHASE =====================
            'Purchase',
            'Create Purchase',
            'Edit Purchase',
            'Delete Purchase',
            'Print Purchase Invoice',
            'Manage Inward Bill',

            // ===================== PURCHASE RETURN =====================
            'Purchase Return',
            'Create Purchase Return',
            'Delete Purchase Return',
            'Print Purchase Return',

            // ===================== PRODUCTION =====================
            'Production',
            'Own Production',
            'Create Production',
            'Edit Production',
            'Delete Production',
            'Print Production Gatepass',

            // ===================== RAW MATERIALS =====================
            'Raw Materials',
            'Create Raw Material',
            'Edit Raw Material',
            'Delete Raw Material',
            'Create Raw Material Purchase',
            'Delete Raw Material Purchase',
            'Print Raw Material Purchase',

            // ===================== INWARD GATEPASS =====================
            'List Inwards',
            'Create Inward Gatepass',
            'Edit Inward Gatepass',
            'Delete Inward Gatepass',
            'Print Inward Gatepass',
            'Add Inward Details',

            // ===================== WAREHOUSE =====================
            'List Warehouse',
            'Create Warehouse',
            'Edit Warehouse',
            'Delete Warehouse',

            // ===================== WAREHOUSE STOCK =====================
            'Warehouse Stock',
            'Create Warehouse Stock',
            'Edit Warehouse Stock',
            'Delete Warehouse Stock',
            'Print Warehouse Stock',

            // ===================== STOCK TRANSFER =====================
            'Stock Transfer',
            'Create Stock Transfer',
            'Edit Stock Transfer',
            'Delete Stock Transfer',
            'Print Stock Transfer',
            'Accept Stock Transfer',
            'Reject Stock Transfer',

            // ===================== STOCK ADJUSTMENT =====================
            'Stock Adjustment',
            'Create Stock Adjustment',
            'View Stock Adjustment',
            'Delete Stock Adjustment',
            'Print Stock Adjustment Report',
            'Stock Adjustment Audit Report',

            // ===================== CHAR OF ACCOUNTS =====================
            'Char Of Accounts',
            'Create Account Head',
            'Create Account',
            'Delete Account',

            // ===================== NARRATIONS =====================
            'Narrations',
            'Create Narration',
            'Delete Narration',

            // ===================== VOUCHERS =====================
            'Receipts Voucher',
            'Create Receipt Voucher',
            'Delete Receipt Voucher',
            'Print Receipt Voucher',
            'Payment Voucher',
            'Create Payment Voucher',
            'Delete Payment Voucher',
            'Print Payment Voucher',
            'Expense Voucher',
            'Create Expense Voucher',
            'Delete Expense Voucher',
            'Print Expense Voucher',

            // ===================== REPORTS =====================
            'Item Stock Report',
            'Branch Stock Report',
            'Purchase Report',
            'Sale Report',
            'Category Sale Report',
            'Daily Sale Closing Report',
            'Print Sale Closing',
            'Profit Loss Report',
            'Customer Ledger',
            'Vendor Ledger',
            'Expense Report',
            'Expense Audit Report',
            'System Reports',
            'Cashbook',

            // ===================== USERS =====================
            'Users',
            'Create User',
            'Edit User',
            'Delete User',
            'Assign User Roles',
            'Set Opening Balance',

            // ===================== ROLES =====================
            'Roles',
            'Create Role',
            'Edit Role',
            'Delete Role',
            'Edit Role Permissions',

            // ===================== PERMISSIONS =====================
            'Permissions',
            'Create Permission',
            'Edit Permission',
            'Delete Permission',

            // ===================== BRANCH =====================
            'Branch',
            'Create Branch',
            'Delete Branch',
            'Switch Branch',

            // ===================== ZONE =====================
            'Zone',
            'Create Zone',
            'Edit Zone',
            'Delete Zone',

            // ===================== SALES OFFICER =====================
            'Sales Officer',
            'Create Sales Officer',
            'Edit Sales Officer',
            'Delete Sales Officer',

            // ===================== TABLES =====================
            'Tables',
            'Create Table',
            'Edit Table',
            'Delete Table',

            // ===================== SETTINGS =====================
            'Settings',
            'Update Settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Super admin and Admin must have full permissions synced
        $allPermissions = Permission::all();

        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($allPermissions);

        $superAdminRoleCap = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRoleCap->syncPermissions($allPermissions);

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);
    }
}