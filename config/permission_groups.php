<?php

/*
|--------------------------------------------------------------------------
| Permission Groups
|--------------------------------------------------------------------------
| Maps each page/module to the permissions that belong to it.
| Used by the Roles page to render "Update Role Permissions" grouped by page.
| Any permission not listed here falls into the "Others" group.
*/

return [

    'groups' => [

        'Dashboard' => [
            'Dashboard',
        ],

        'Products' => [
            'Products',
            'View Product',
            'Create Product',
            'Edit Product',
            'Delete Product',
            'Print Product Barcode',
            'Bulk Edit Products',
            'Reset Product Stock',
            'Manage Product BOM',
        ],

        'Category' => [
            'Category',
            'Create Category',
            'Edit Category',
            'Delete Category',
        ],

        'Brands' => [
            'Brands',
            'Create Brand',
            'Edit Brand',
            'Delete Brand',
        ],

        'Sub Category' => [
            'Sub Category',
            'Create Sub Category',
            'Edit Sub Category',
            'Delete Sub Category',
        ],

        'Unit' => [
            'Unit',
            'Units',
            'Create Unit',
            'Edit Unit',
            'Delete Unit',
        ],

        'Discount Products' => [
            'Discount Products',
            'Create Discount',
            'Update Discount Status',
            'Print Discount Barcode',
        ],

        'Customer' => [
            'Customer',
            'Create Customer',
            'Edit Customer',
            'Delete Customer',
            'Toggle Customer Status',
            'Customer Payments',
            'Print Customer Receipt',
        ],

        'Vendor' => [
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
        ],

        'List Warehouse' => [
            'List Warehouse',
            'Create Warehouse',
            'Edit Warehouse',
            'Delete Warehouse',
        ],

        'Branch' => [
            'Branch',
            'Create Branch',
            'Delete Branch',
            'Switch Branch',
        ],

        'Zone' => [
            'Zone',
            'Create Zone',
            'Edit Zone',
            'Delete Zone',
        ],

        'Sales Officer' => [
            'Sales Officer',
            'Create Sales Officer',
            'Edit Sales Officer',
            'Delete Sales Officer',
        ],

        'Purchase' => [
            'Purchase',
            'Create Purchase',
            'Edit Purchase',
            'Delete Purchase',
            'Print Purchase Invoice',
            'Manage Inward Bill',
        ],

        'Production' => [
            'Production',
            'Own Production',
            'Create Production',
            'Edit Production',
            'Delete Production',
            'Print Production Gatepass',
        ],

        'Raw Materials' => [
            'Raw Materials',
            'Create Raw Material',
            'Edit Raw Material',
            'Delete Raw Material',
            'Create Raw Material Purchase',
            'Delete Raw Material Purchase',
            'Print Raw Material Purchase',
        ],

        'List Inwards' => [
            'List Inwards',
            'Create Inward Gatepass',
            'Edit Inward Gatepass',
            'Delete Inward Gatepass',
            'Print Inward Gatepass',
            'Add Inward Details',
        ],

        'Purchase Return' => [
            'Purchase Return',
            'Create Purchase Return',
            'Delete Purchase Return',
            'Print Purchase Return',
        ],

        'Sales' => [
            'Sales',
            'Create Sale',
            'Edit Sale',
            'Delete Sale',
            'Sale Exchange',
            'Print Sale Invoice',
            'Print Sale DC',
            'Print Sale Receipt',
            'Print POS Bill',
        ],

        'Sale Return' => [
            'Sale Return',
            'Create Sale Return',
            'Delete Sale Return',
            'Print Sale Return',
        ],

        'Bookings' => [
            'Bookings',
            'Create Booking',
            'Edit Booking',
            'Delete Booking',
            'Convert Booking To Sale',
            'Print Booking Receipt',
        ],

        'Warehouse Stock' => [
            'Warehouse Stock',
            'Create Warehouse Stock',
            'Edit Warehouse Stock',
            'Delete Warehouse Stock',
            'Print Warehouse Stock',
        ],

        'Stock Transfer' => [
            'Stock Transfer',
            'Create Stock Transfer',
            'Edit Stock Transfer',
            'Delete Stock Transfer',
            'Print Stock Transfer',
            'Accept Stock Transfer',
            'Reject Stock Transfer',
        ],

        'Stock Adjustment' => [
            'Stock Adjustment',
            'Create Stock Adjustment',
            'View Stock Adjustment',
            'Delete Stock Adjustment',
            'Print Stock Adjustment Report',
            'Stock Adjustment Audit Report',
        ],

        'Item Stock Report' => [
            'Item Stock Report',
            'Branch Stock Report',
        ],

        'Purchase Report' => [
            'Purchase Report',
        ],

        'Sale Report' => [
            'Sale Report',
            'Category Sale Report',
            'Daily Sale Closing Report',
            'Print Sale Closing',
            'Profit Loss Report',
        ],

        'Customer Ledger' => [
            'Customer Ledger',
        ],

        'Vendor Ledger' => [
            'Vendor Ledger',
        ],

        'Expense Report' => [
            'Expense Report',
            'Expense Audit Report',
        ],

        'System Reports' => [
            'System Reports',
        ],

        'Cashbook' => [
            'Cashbook',
        ],

        'Char Of Accounts' => [
            'Char Of Accounts',
            'Create Account Head',
            'Create Account',
            'Delete Account',
        ],

        'Narrations' => [
            'Narrations',
            'Create Narration',
            'Delete Narration',
        ],

        'Receipts Voucher' => [
            'Receipts Voucher',
            'Create Receipt Voucher',
            'Delete Receipt Voucher',
            'Print Receipt Voucher',
        ],

        'Payment Voucher' => [
            'Payment Voucher',
            'Create Payment Voucher',
            'Delete Payment Voucher',
            'Print Payment Voucher',
        ],

        'Expense Voucher' => [
            'Expense Voucher',
            'Create Expense Voucher',
            'Delete Expense Voucher',
            'Print Expense Voucher',
        ],

        'Roles' => [
            'Roles',
            'Create Role',
            'Edit Role',
            'Delete Role',
            'Edit Role Permissions',
        ],

        'Permissions' => [
            'Permissions',
            'Create Permission',
            'Edit Permission',
            'Delete Permission',
        ],

        'Users' => [
            'Users',
            'Create User',
            'Edit User',
            'Delete User',
            'Assign User Roles',
            'Set Opening Balance',
        ],

        'Settings' => [
            'Settings',
            'Update Settings',
        ],

        'Tables' => [
            'Tables',
            'Create Table',
            'Edit Table',
            'Delete Table',
        ],
    ],

];