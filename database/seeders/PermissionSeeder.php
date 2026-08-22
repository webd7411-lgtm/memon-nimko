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
            // ===================== PAGES =====================
            'System Reports',
            'Category',
            'Brands',
            'Sub Category',
            'Unit',
            'Products',
            'Discount Products',
            'Customer',
            'Vendor',
            'List Warehouse',
            'Branch',
            'Zone',
            'Sales Officer',
            'Roles',
            'Permissions',
            'Users',
            'Purchase',
            'Production',
            'Raw Materials',
            'List Inwards',
            'Purchase Return',
            'Sales',
            'Sale Return',
            'Bookings',
            'Warehouse Stock',
            'Stock Transfer',
            'Stock Adjustment',
            'Item Stock Report',
            'Purchase Report',
            'Sale Report',
            'Customer Ledger',
            'Vendor Ledger',
            'Char Of Accounts',
            'Narrations',
            'Receipts Voucher',
            'Payment Voucher',
            'Expense Voucher',
            'Expense Report',
            'Cashbook',
            'Tables',
            'Settings',

            // ===================== PRODUCTS =====================
            'View Product',
            'Create Product',
            'Edit Product',
            'Delete Product',
            'Print Product Barcode',
            'Bulk Edit Products',
            'Reset Product Stock',
            'Manage Product BOM',

            // ===================== CATEGORY =====================
            'Create Category',
            'Edit Category',
            'Delete Category',

            // ===================== BRANDS =====================
            'Create Brand',
            'Edit Brand',
            'Delete Brand',

            // ===================== SUB CATEGORY =====================
            'Create Sub Category',
            'Edit Sub Category',
            'Delete Sub Category',

            // ===================== UNIT =====================
            'Create Unit',
            'Edit Unit',
            'Delete Unit',

            // ===================== DISCOUNT PRODUCTS =====================
            'Create Discount',
            'Update Discount Status',
            'Print Discount Barcode',

            // ===================== CUSTOMER =====================
            'Create Customer',
            'Edit Customer',
            'Delete Customer',
            'Toggle Customer Status',
            'Customer Payments',
            'Print Customer Receipt',

            // ===================== VENDOR =====================
            'Create Vendor',
            'Edit Vendor',
            'Delete Vendor',
            'Vendor Payments',
            'Vendor Bilties',
            'Print Vendor Receipt',

            // ===================== WAREHOUSE =====================
            'Create Warehouse',
            'Delete Warehouse',

            // ===================== BRANCH =====================
            'Create Branch',
            'Delete Branch',

            // ===================== ZONE =====================
            'Create Zone',
            'Edit Zone',
            'Delete Zone',

            // ===================== SALES OFFICER =====================
            'Create Sales Officer',
            'Edit Sales Officer',
            'Delete Sales Officer',

            // ===================== ROLES =====================
            'Create Role',
            'Edit Role',
            'Delete Role',
            'Edit Role Permissions',

            // ===================== PERMISSIONS =====================
            'Create Permission',
            'Edit Permission',
            'Delete Permission',

            // ===================== USERS =====================
            'Create User',
            'Edit User',
            'Delete User',
            'Assign User Roles',
            'Set Opening Balance',

            // ===================== PURCHASE =====================
            'Create Purchase',
            'Edit Purchase',
            'Delete Purchase',
            'Print Purchase Invoice',
            'Manage Inward Bill',

            // ===================== PRODUCTION =====================
            'Create Production',
            'Edit Production',
            'Print Production Gatepass',

            // ===================== RAW MATERIALS =====================
            'Create Raw Material',
            'Delete Raw Material',
            'Create Raw Material Purchase',
            'Delete Raw Material Purchase',
            'Print Raw Material Purchase',

            // ===================== INWARD GATEPASS =====================
            'Create Inward Gatepass',
            'Edit Inward Gatepass',
            'Delete Inward Gatepass',
            'Print Inward Gatepass',
            'Add Inward Details',

            // ===================== PURCHASE RETURN =====================
            'Create Purchase Return',
            'Print Purchase Return',

            // ===================== SALES =====================
            'Create Sale',
            'Edit Sale',
            'Print Sale Invoice',
            'Print Sale DC',
            'Print Sale Receipt',
            'Create Sale Return',
            'Print Sale Return',

            // ===================== BOOKINGS =====================
            'Create Booking',
            'Delete Booking',
            'Print Booking Receipt',

            // ===================== WAREHOUSE STOCK / STOCK TRANSFER =====================
            'Create Warehouse Stock',
            'Edit Warehouse Stock',
            'Delete Warehouse Stock',
            'Print Warehouse Stock',
            'Create Stock Transfer',
            'Edit Stock Transfer',
            'Delete Stock Transfer',
            'Print Stock Transfer',

            // ===================== STOCK ADJUSTMENT =====================
            'Create Stock Adjustment',
            'Print Stock Adjustment Report',

            // ===================== CHAR OF ACCOUNTS =====================
            'Create Account Head',
            'Create Account',
            'Delete Account',

            // ===================== NARRATIONS =====================
            'Create Narration',
            'Delete Narration',

            // ===================== VOUCHERS =====================
            'Create Receipt Voucher',
            'Print Receipt Voucher',
            'Create Payment Voucher',
            'Print Payment Voucher',
            'Create Expense Voucher',
            'Print Expense Voucher',

            // ===================== SETTINGS =====================
            'Update Settings',

            // ===================== TABLES =====================
            'Create Table',
            'Delete Table',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super admin must always have the full set so the app keeps working
        // immediately when this seeder is run against an existing database.
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());
    }
}