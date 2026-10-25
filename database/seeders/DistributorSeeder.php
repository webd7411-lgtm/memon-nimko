<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Distributor;
use App\Models\DistributorOrder;
use App\Models\DistributorOrderItem;
use App\Models\DistributorStock;
use App\Models\DistributorCustomer;
use App\Models\DistributorCustomerLedger;
use App\Models\DistributorSale;
use App\Models\DistributorSaleItem;
use App\Models\DistributorLedger;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DistributorSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure Roles & Permissions
        $portalPerm = Permission::firstOrCreate(['name' => 'Distributor Portal']);
        $distRole = Role::firstOrCreate(['name' => 'Distributor']);
        if (!$distRole->hasPermissionTo('Distributor Portal')) {
            $distRole->givePermissionTo('Distributor Portal');
        }

        // 2. Create or Update Demo Distributor User
        $user = User::updateOrCreate(
            ['email' => 'distributor@gmail.com'],
            [
                'name'     => 'Tariq Mahmood (Distributor)',
                'password' => Hash::make('123456'),
                'usertype' => 'distributor',
            ]
        );
        $user->syncRoles(['Distributor']);

        // 3. Create or Update Distributor Profile
        $distributor = Distributor::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'            => 'Tariq Goods & Distribution',
                'code'            => 'DIST-1001',
                'phone'           => '0300-8765432',
                'cnic'            => '42201-1234567-1',
                'city'            => 'Karachi',
                'area'            => 'Malir & Korangi Market',
                'address'         => 'Plot # 12, Commercial Area, Malir, Karachi',
                'opening_balance' => 0.00,
                'current_balance' => 35000.00, // 85,000 received - 50,000 paid = 35,000 payable to company
                'status'          => 'active',
            ]
        );

        // 4. Products & Stock in Custody
        $products = Product::with('variants')->take(3)->get();
        if ($products->count() > 0) {
            foreach ($products as $idx => $prod) {
                $qty = 40 + ($idx * 25); // 40, 65, 90
                $price = (float) ($prod->wholesale_price ?? $prod->price ?? 200);
                if ($price <= 0) $price = 250;

                DistributorStock::updateOrCreate(
                    [
                        'distributor_id' => $distributor->id,
                        'product_id'     => $prod->id,
                        'variant_id'     => $prod->variants->first()->id ?? null,
                    ],
                    [
                        'qty'       => $qty,
                        'avg_price' => $price,
                    ]
                );
            }
        }

        // 5. Seed Company Ledger Transactions (Goods received from Company & Payment made to Company)
        DistributorLedger::where('distributor_id', $distributor->id)->delete();

        // Transaction 1: Goods Dispatched from Company
        DistributorLedger::create([
            'distributor_id' => $distributor->id,
            'date'           => now()->subDays(5)->format('Y-m-d'),
            'type'           => 'dispatch',
            'reference_no'   => 'DO-261020-0001',
            'description'    => 'Initial Stock Delivery from Head Office (150 Pcs Nimko & Bakery items)',
            'debit'          => 85000.00,
            'credit'         => 0.00,
            'balance'        => 85000.00,
            'created_by'     => 1,
        ]);

        // Transaction 2: Distributor deposited payment to company
        DistributorLedger::create([
            'distributor_id' => $distributor->id,
            'date'           => now()->subDays(2)->format('Y-m-d'),
            'type'           => 'payment_received',
            'reference_no'   => 'PAY-261022-841',
            'description'    => 'Cash Payment deposited to Head Office Cashier',
            'debit'          => 0.00,
            'credit'         => 50000.00,
            'balance'        => 35000.00,
            'created_by'     => 1,
        ]);

        // 6. Seed Retail Customers (Shops)
        $shopsData = [
            [
                'shop_name'       => 'Madina Super Store',
                'owner_name'      => 'Haji Aslam',
                'phone'           => '0312-1112223',
                'area'            => 'Malir Halt Market',
                'opening_balance' => 0,
                'current_balance' => 3000.00, // 15,000 sale - 12,000 received = 3,000 due
            ],
            [
                'shop_name'       => 'Bismillah General Store',
                'owner_name'      => 'Muhammad Bilal',
                'phone'           => '0333-4445556',
                'area'            => 'Korangi No. 4',
                'opening_balance' => 0,
                'current_balance' => 4000.00, // 12,000 sale - 8,000 received = 4,000 due
            ],
            [
                'shop_name'       => 'Al-Makkah Mini Mart',
                'owner_name'      => 'Tariq Siddiqui',
                'phone'           => '0345-7778889',
                'area'            => 'Shah Faisal Colony',
                'opening_balance' => 0,
                'current_balance' => 0.00, // Fully paid
            ],
        ];

        DistributorCustomer::where('distributor_id', $distributor->id)->delete();
        DistributorCustomerLedger::where('distributor_id', $distributor->id)->delete();
        DistributorSale::where('distributor_id', $distributor->id)->delete();

        foreach ($shopsData as $sIndex => $shop) {
            $customer = DistributorCustomer::create([
                'distributor_id'  => $distributor->id,
                'shop_name'       => $shop['shop_name'],
                'owner_name'      => $shop['owner_name'],
                'phone'           => $shop['phone'],
                'area'            => $shop['area'],
                'opening_balance' => 0,
                'current_balance' => $shop['current_balance'],
                'status'          => 'active',
            ]);

            if ($sIndex === 0) {
                // Madina Store: Sale of 15,000 -> 10,000 Cash at sale + 2,000 later recovery = 3,000 due
                DistributorSale::create([
                    'invoice_no'              => 'DSAL-261021-001',
                    'distributor_id'          => $distributor->id,
                    'distributor_customer_id' => $customer->id,
                    'sale_date'               => now()->subDays(3)->format('Y-m-d'),
                    'subtotal'                => 15000.00,
                    'grand_total'             => 15000.00,
                    'paid_amount'             => 10000.00,
                    'due_amount'              => 5000.00,
                    'payment_type'            => 'partial',
                ]);

                // Ledger sale
                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->subDays(3)->format('Y-m-d'),
                    'type'                    => 'sale',
                    'reference_no'            => 'DSAL-261021-001',
                    'description'             => 'Invoice #DSAL-261021-001 (Assorted Nimko Cartons)',
                    'debit'                   => 15000.00,
                    'credit'                  => 0.00,
                    'balance'                 => 15000.00,
                ]);

                // Initial cash
                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->subDays(3)->format('Y-m-d'),
                    'type'                    => 'payment_received',
                    'reference_no'            => 'RCP-001',
                    'description'             => 'Cash collected on invoice delivery',
                    'debit'                   => 0.00,
                    'credit'                  => 10000.00,
                    'balance'                 => 5000.00,
                ]);

                // Follow-up Recovery
                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->subDay()->format('Y-m-d'),
                    'type'                    => 'payment_received',
                    'reference_no'            => 'REC-261023-712',
                    'description'             => 'Weekly recovery collection from shop',
                    'debit'                   => 0.00,
                    'credit'                  => 2000.00,
                    'balance'                 => 3000.00,
                ]);
            } elseif ($sIndex === 1) {
                // Bismillah Store: Sale of 12,000 -> 8,000 Paid -> 4,000 Due
                DistributorSale::create([
                    'invoice_no'              => 'DSAL-261022-002',
                    'distributor_id'          => $distributor->id,
                    'distributor_customer_id' => $customer->id,
                    'sale_date'               => now()->subDays(2)->format('Y-m-d'),
                    'subtotal'                => 12000.00,
                    'grand_total'             => 12000.00,
                    'paid_amount'             => 8000.00,
                    'due_amount'              => 4000.00,
                    'payment_type'            => 'partial',
                ]);

                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->subDays(2)->format('Y-m-d'),
                    'type'                    => 'sale',
                    'reference_no'            => 'DSAL-261022-002',
                    'description'             => 'Invoice #DSAL-261022-002',
                    'debit'                   => 12000.00,
                    'credit'                  => 0.00,
                    'balance'                 => 12000.00,
                ]);

                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->subDays(2)->format('Y-m-d'),
                    'type'                    => 'payment_received',
                    'reference_no'            => 'RCP-002',
                    'description'             => 'Cash collected on delivery',
                    'debit'                   => 0.00,
                    'credit'                  => 8000.00,
                    'balance'                 => 4000.00,
                ]);
            } else {
                // Al-Makkah Mart: Full cash sale of 8,500
                DistributorSale::create([
                    'invoice_no'              => 'DSAL-261024-003',
                    'distributor_id'          => $distributor->id,
                    'distributor_customer_id' => $customer->id,
                    'sale_date'               => now()->format('Y-m-d'),
                    'subtotal'                => 8500.00,
                    'grand_total'             => 8500.00,
                    'paid_amount'             => 8500.00,
                    'due_amount'              => 0.00,
                    'payment_type'            => 'cash',
                ]);

                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->format('Y-m-d'),
                    'type'                    => 'sale',
                    'reference_no'            => 'DSAL-261024-003',
                    'description'             => 'Invoice #DSAL-261024-003',
                    'debit'                   => 8500.00,
                    'credit'                  => 0.00,
                    'balance'                 => 8500.00,
                ]);

                DistributorCustomerLedger::create([
                    'distributor_customer_id' => $customer->id,
                    'distributor_id'          => $distributor->id,
                    'date'                    => now()->format('Y-m-d'),
                    'type'                    => 'payment_received',
                    'reference_no'            => 'RCP-003',
                    'description'             => 'Full cash collection',
                    'debit'                   => 0.00,
                    'credit'                  => 8500.00,
                    'balance'                 => 0.00,
                ]);
            }
        }
    }
}
