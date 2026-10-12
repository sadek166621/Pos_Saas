<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Addon;
use App\Models\Branch;
use App\Models\BranchProductPrice;
use App\Models\BranchProductStock;
use App\Models\Brand;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\SmsTemplate;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions Setup
        $permissions = [
            'view_dashboard',
            'manage_pos',
            'view_sales',
            'create_sales',
            'refund_sales',
            'view_products',
            'manage_products',
            'view_inventory',
            'manage_inventory',
            'stock_transfer',
            'view_purchases',
            'manage_purchases',
            'view_customers',
            'manage_customers',
            'view_suppliers',
            'manage_suppliers',
            'view_accounting',
            'manage_expenses',
            'view_reports',
            'manage_branches',
            'manage_users',
            'manage_settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);

        $ownerRole->syncPermissions(Permission::all());
        $managerRole->syncPermissions([
            'view_dashboard', 'manage_pos', 'view_sales', 'create_sales', 'view_products',
            'view_inventory', 'stock_transfer', 'view_customers', 'manage_customers',
            'manage_expenses', 'view_reports',
        ]);
        $cashierRole->syncPermissions([
            'view_dashboard', 'manage_pos', 'view_sales', 'create_sales', 'view_customers', 'manage_customers',
        ]);
        $accountantRole->syncPermissions([
            'view_dashboard', 'view_sales', 'view_purchases', 'view_accounting', 'manage_expenses', 'view_reports',
        ]);

        // 2. Super Admin User
        $superAdmin = User::create([
            'name' => 'SaaS Super Admin',
            'email' => 'admin@possaas.com',
            'phone' => '01700000001',
            'role' => 'super_admin',
            'is_super_admin' => true,
            'status' => 'active',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole($superAdminRole);

        // 3. SaaS Subscription Plans
        $starterPlan = SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'description' => 'Ideal for single retail shops and new grocery businesses.',
            'price_monthly' => 499.00,
            'price_yearly' => 4990.00,
            'max_branches' => 1,
            'max_users' => 2,
            'max_products' => 2000,
            'max_warehouses' => 1,
            'max_storage_mb' => 1024,
            'sms_limit' => 200,
            'features' => ['pos', 'sales', 'inventory', 'customers', 'receipts_thermal'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $businessPlan = SubscriptionPlan::create([
            'name' => 'Business',
            'slug' => 'business',
            'description' => 'For growing retail chains with multiple branches & warehouse needs.',
            'price_monthly' => 999.00,
            'price_yearly' => 9990.00,
            'max_branches' => 3,
            'max_users' => 5,
            'max_products' => 10000,
            'max_warehouses' => 3,
            'max_storage_mb' => 5120,
            'sms_limit' => 500,
            'features' => ['pos', 'sales', 'inventory', 'multi_branch', 'branch_pricing', 'transfers', 'purchase', 'suppliers', 'customers', 'customer_due', 'accounting', 'reports', 'sms_notifications'],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $professionalPlan = SubscriptionPlan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'description' => 'Advanced enterprise solution for large businesses with unlimited scale.',
            'price_monthly' => 1999.00,
            'price_yearly' => 19990.00,
            'max_branches' => 10,
            'max_users' => 15,
            'max_products' => 50000,
            'max_warehouses' => 10,
            'max_storage_mb' => 20480,
            'sms_limit' => 2000,
            'features' => ['*'],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // 4. Add-ons
        Addon::create(['name' => 'Extra Branch', 'slug' => 'extra-branch', 'type' => 'extra_branch', 'price_monthly' => 300, 'price_yearly' => 3000, 'unit_value' => 1]);
        Addon::create(['name' => 'Extra User', 'slug' => 'extra-user', 'type' => 'extra_user', 'price_monthly' => 100, 'price_yearly' => 1000, 'unit_value' => 1]);
        Addon::create(['name' => 'Extra 500 SMS', 'slug' => 'extra-sms-500', 'type' => 'extra_sms', 'price_monthly' => 250, 'price_yearly' => 2500, 'unit_value' => 500]);
        Addon::create(['name' => 'Extra 5GB Storage', 'slug' => 'extra-storage-5gb', 'type' => 'extra_storage', 'price_monthly' => 200, 'price_yearly' => 2000, 'unit_value' => 5120]);

        // 5. Demo Tenant: "Rahman Fashion & Retail"
        $tenant = Tenant::create([
            'name' => 'Rahman Fashion & Retail',
            'slug' => 'rahman-fashion',
            'business_type' => 'fashion',
            'bin_number' => '123456789-0101',
            'tin_number' => '8765432190',
            'phone' => '01711000000',
            'email' => 'info@rahmanfashion.com',
            'address' => 'House 42, Road 11, Dhanmondi, Dhaka - 1209',
            'city' => 'Dhaka',
            'currency' => 'BDT',
            'currency_symbol' => '৳',
            'timezone' => 'Asia/Dhaka',
            'status' => 'active',
            'trial_ends_at' => Carbon::now()->addDays(14),
            'subscription_ends_at' => Carbon::now()->addMonths(6),
        ]);

        // Tenant Subscription
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $businessPlan->id,
            'billing_cycle' => 'monthly',
            'status' => 'active',
            'starts_at' => Carbon::now()->subDays(15),
            'ends_at' => Carbon::now()->addDays(15),
            'auto_renew' => true,
        ]);

        // 6. Multi-Branch Setup
        $dhakaBranch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Dhaka Dhanmondi (HQ)',
            'code' => 'DHK-01',
            'phone' => '01711000001',
            'email' => 'dhaka@rahmanfashion.com',
            'address' => 'Dhanmondi 27, Dhaka',
            'city' => 'Dhaka',
            'is_main' => true,
            'status' => 'active',
        ]);

        $ctgBranch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Chittagong GEC Branch',
            'code' => 'CTG-01',
            'phone' => '01711000002',
            'email' => 'ctg@rahmanfashion.com',
            'address' => 'GEC Circle, Chittagong',
            'city' => 'Chittagong',
            'is_main' => false,
            'status' => 'active',
        ]);

        $sylhetBranch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Sylhet Zindabazar Branch',
            'code' => 'SYL-01',
            'phone' => '01711000003',
            'email' => 'sylhet@rahmanfashion.com',
            'address' => 'Zindabazar Point, Sylhet',
            'city' => 'Sylhet',
            'is_main' => false,
            'status' => 'active',
        ]);

        // Warehouses
        $dhakaWarehouse = Warehouse::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'name' => 'Dhaka Central Warehouse',
            'code' => 'WH-DHK',
            'address' => 'Tejgaon Industrial Area, Dhaka',
            'status' => 'active',
        ]);

        $ctgWarehouse = Warehouse::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $ctgBranch->id,
            'name' => 'Chittagong Port Warehouse',
            'code' => 'WH-CTG',
            'address' => 'Agrabad Commercial Area, Chittagong',
            'status' => 'active',
        ]);

        // 7. Users for Tenant
        $owner = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Mizanur Rahman',
            'email' => 'owner@rahmanfashion.com',
            'phone' => '01711000000',
            'role' => 'owner',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $owner->assignRole($ownerRole);

        $dhakaManager = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'name' => 'Kazi Tariqul Islam',
            'email' => 'manager.dhaka@rahmanfashion.com',
            'phone' => '01711000010',
            'role' => 'manager',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $dhakaManager->assignRole($managerRole);
        $dhakaBranch->update(['manager_id' => $dhakaManager->id]);

        $ctgManager = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $ctgBranch->id,
            'name' => 'Farhan Chowdhury',
            'email' => 'manager.ctg@rahmanfashion.com',
            'phone' => '01711000020',
            'role' => 'manager',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $ctgManager->assignRole($managerRole);
        $ctgBranch->update(['manager_id' => $ctgManager->id]);

        $cashier = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'name' => 'Sabbir Hossain',
            'email' => 'cashier1@rahmanfashion.com',
            'phone' => '01711000030',
            'role' => 'cashier',
            'status' => 'active',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $cashier->assignRole($cashierRole);

        // Employees
        Employee::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'user_id' => $dhakaManager->id,
            'name' => 'Kazi Tariqul Islam',
            'phone' => '01711000010',
            'email' => 'manager.dhaka@rahmanfashion.com',
            'designation' => 'Branch Manager',
            'salary' => 35000.00,
            'commission_rate' => 1.5,
        ]);

        Employee::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'user_id' => $cashier->id,
            'name' => 'Sabbir Hossain',
            'phone' => '01711000030',
            'email' => 'cashier1@rahmanfashion.com',
            'designation' => 'Lead Cashier',
            'salary' => 18000.00,
            'commission_rate' => 0.5,
        ]);

        // 8. Units, Categories, Brands
        $unitPcs = Unit::create(['tenant_id' => $tenant->id, 'name' => 'Piece', 'code' => 'pcs']);
        $unitBox = Unit::create(['tenant_id' => $tenant->id, 'name' => 'Box', 'code' => 'box']);
        $unitKg = Unit::create(['tenant_id' => $tenant->id, 'name' => 'Kilogram', 'code' => 'kg']);

        $catPanjabi = Category::create(['tenant_id' => $tenant->id, 'name' => 'Panjabi & Ethnic', 'slug' => 'panjabi-ethnic']);
        $catShirts = Category::create(['tenant_id' => $tenant->id, 'name' => 'Formal & Casual Shirts', 'slug' => 'shirts']);
        $catPants = Category::create(['tenant_id' => $tenant->id, 'name' => 'Jeans & Trousers', 'slug' => 'pants']);
        $catAcc = Category::create(['tenant_id' => $tenant->id, 'name' => 'Accessories', 'slug' => 'accessories']);
        $catGadget = Category::create(['tenant_id' => $tenant->id, 'name' => 'Gadgets & Tech', 'slug' => 'gadgets']);

        $brandAarong = Brand::create(['tenant_id' => $tenant->id, 'name' => 'Aarong Handloom', 'slug' => 'aarong']);
        $brandYellow = Brand::create(['tenant_id' => $tenant->id, 'name' => 'Yellow Fashion', 'slug' => 'yellow']);
        $brandApex = Brand::create(['tenant_id' => $tenant->id, 'name' => 'Apex Footwear', 'slug' => 'apex']);
        $brandSamsung = Brand::create(['tenant_id' => $tenant->id, 'name' => 'Samsung BD', 'slug' => 'samsung']);

        // 9. Products with Barcode & Branch-Specific Pricing
        $productsData = [
            [
                'name' => 'Premium Cotton Embroidered Panjabi',
                'sku' => 'PANJ-EMB-001',
                'barcode' => '890123450001',
                'category_id' => $catPanjabi->id,
                'brand_id' => $brandYellow->id,
                'unit_id' => $unitPcs->id,
                'purchase_price' => 1100.00,
                'selling_price' => 1800.00,
                'wholesale_price' => 1400.00,
                'vat_percent' => 5.0,
                'min_stock_alert' => 5,
                // Branch overrides: Dhaka 1800, Chittagong 1850, Sylhet 1900
                'overrides' => [
                    $ctgBranch->id => 1850.00,
                    $sylhetBranch->id => 1900.00,
                ],
                'stocks' => [
                    $dhakaBranch->id => 50,
                    $ctgBranch->id => 30,
                    $sylhetBranch->id => 20,
                ]
            ],
            [
                'name' => 'Executive Slim Fit White Shirt',
                'sku' => 'SHIRT-SLM-002',
                'barcode' => '890123450002',
                'category_id' => $catShirts->id,
                'brand_id' => $brandYellow->id,
                'unit_id' => $unitPcs->id,
                'purchase_price' => 650.00,
                'selling_price' => 1200.00,
                'wholesale_price' => 950.00,
                'vat_percent' => 5.0,
                'min_stock_alert' => 10,
                'overrides' => [],
                'stocks' => [
                    $dhakaBranch->id => 80,
                    $ctgBranch->id => 45,
                    $sylhetBranch->id => 25,
                ]
            ],
            [
                'name' => 'Stretch Denim Jeans - Navy Blue',
                'sku' => 'JEAN-NVY-003',
                'barcode' => '890123450003',
                'category_id' => $catPants->id,
                'brand_id' => $brandYellow->id,
                'unit_id' => $unitPcs->id,
                'purchase_price' => 850.00,
                'selling_price' => 1500.00,
                'wholesale_price' => 1200.00,
                'vat_percent' => 5.0,
                'min_stock_alert' => 8,
                'overrides' => [],
                'stocks' => [
                    $dhakaBranch->id => 60,
                    $ctgBranch->id => 40,
                    $sylhetBranch->id => 15,
                ]
            ],
            [
                'name' => 'Genuine Leather Bifold Wallet',
                'sku' => 'ACC-WLT-004',
                'barcode' => '890123450004',
                'category_id' => $catAcc->id,
                'brand_id' => $brandApex->id,
                'unit_id' => $unitPcs->id,
                'purchase_price' => 320.00,
                'selling_price' => 650.00,
                'wholesale_price' => 500.00,
                'vat_percent' => 5.0,
                'min_stock_alert' => 6,
                'overrides' => [],
                'stocks' => [
                    $dhakaBranch->id => 40,
                    $ctgBranch->id => 25,
                    $sylhetBranch->id => 12,
                ]
            ],
            [
                'name' => 'Samsung 25W Fast Type-C Charger',
                'sku' => 'CHG-SAM-005',
                'barcode' => '890123450005',
                'category_id' => $catGadget->id,
                'brand_id' => $brandSamsung->id,
                'unit_id' => $unitBox->id,
                'purchase_price' => 650.00,
                'selling_price' => 1000.00,
                'wholesale_price' => 850.00,
                'vat_percent' => 5.0,
                'min_stock_alert' => 5,
                // Global 1000, Dhaka 1050, Chittagong 1000, Sylhet 1100 (as described in Master prompt #7)
                'overrides' => [
                    $dhakaBranch->id => 1050.00,
                    $ctgBranch->id => 1000.00,
                    $sylhetBranch->id => 1100.00,
                ],
                'stocks' => [
                    $dhakaBranch->id => 45,
                    $ctgBranch->id => 30,
                    $sylhetBranch->id => 20,
                ]
            ],
            [
                'name' => 'Wireless Noise Cancelling Earbuds',
                'sku' => 'EAR-ANC-006',
                'barcode' => '890123450006',
                'category_id' => $catGadget->id,
                'brand_id' => $brandSamsung->id,
                'unit_id' => $unitBox->id,
                'purchase_price' => 850.00,
                'selling_price' => 1400.00,
                'wholesale_price' => 1150.00,
                'vat_percent' => 5.0,
                'min_stock_alert' => 5,
                'overrides' => [],
                'stocks' => [
                    $dhakaBranch->id => 35,
                    $ctgBranch->id => 20,
                    $sylhetBranch->id => 10,
                ]
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $pData) {
            $overrides = $pData['overrides'];
            $stocks = $pData['stocks'];
            unset($pData['overrides'], $pData['stocks']);

            $pData['tenant_id'] = $tenant->id;
            $product = Product::create($pData);
            $createdProducts[] = $product;

            // Branch price overrides
            foreach ($overrides as $bId => $price) {
                BranchProductPrice::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $bId,
                    'product_id' => $product->id,
                    'selling_price' => $price,
                ]);
            }

            // Branch stock
            foreach ($stocks as $bId => $qty) {
                BranchProductStock::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $bId,
                    'warehouse_id' => ($bId == $dhakaBranch->id ? $dhakaWarehouse->id : $ctgWarehouse->id),
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'alert_quantity' => 5,
                ]);
            }
        }

        // 10. Suppliers & CRM
        $sup1 = Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => 'Textile Mills BD Ltd',
            'company_name' => 'Textile Mills BD Ltd',
            'phone' => '01819000111',
            'email' => 'sales@textilemillsbd.com',
            'address' => 'Narayanganj BSCIC Area',
            'city' => 'Narayanganj',
            'previous_due' => 10000.00,
            'current_due' => 15000.00,
        ]);

        SupplierLedger::create([
            'tenant_id' => $tenant->id,
            'supplier_id' => $sup1->id,
            'type' => 'opening',
            'credit' => 10000.00,
            'balance' => 10000.00,
            'notes' => 'Opening supplier balance',
        ]);

        $sup2 = Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => 'Smart Accessories Wholesale',
            'company_name' => 'Smart Accessories Ltd',
            'phone' => '01912000222',
            'email' => 'order@smartaccessories.com',
            'address' => 'Stadium Market, Chittagong',
            'city' => 'Chittagong',
            'previous_due' => 5000.00,
            'current_due' => 8500.00,
        ]);

        // Customers
        $walkin = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Walk-in Customer (সাধারণ গ্রাহক)',
            'phone' => '01700000000',
            'address' => 'Counter Sale',
            'city' => 'Dhaka',
            'type' => 'regular',
            'credit_limit' => 0,
            'current_due' => 0,
        ]);

        $cust1 = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Rafiqul Islam',
            'phone' => '01712345678',
            'email' => 'rafiq@gmail.com',
            'address' => 'Mirpur-10, Dhaka',
            'city' => 'Dhaka',
            'type' => 'regular',
            'credit_limit' => 5000.00,
            'previous_due' => 1200.00,
            'current_due' => 1200.00,
            'loyalty_points' => 35,
        ]);

        CustomerLedger::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $cust1->id,
            'type' => 'opening',
            'debit' => 1200.00,
            'balance' => 1200.00,
            'notes' => 'Initial due opening balance',
        ]);

        $cust2 = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Tanvir Ahmed (VIP)',
            'phone' => '01898765432',
            'email' => 'tanvir.ahmed@corporate.bd',
            'address' => 'Gulshan-2, Dhaka',
            'city' => 'Dhaka',
            'type' => 'vip',
            'credit_limit' => 20000.00,
            'previous_due' => 0.00,
            'current_due' => 0.00,
            'loyalty_points' => 180,
        ]);

        $cust3 = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Agrabad Mart (Wholesale)',
            'phone' => '01911223344',
            'email' => 'contact@agrabadmart.com',
            'address' => 'Agrabad, Chittagong',
            'city' => 'Chittagong',
            'type' => 'wholesale',
            'credit_limit' => 50000.00,
            'previous_due' => 8500.00,
            'current_due' => 8500.00,
            'loyalty_points' => 95,
        ]);

        // 11. Cash Shift & Completed Sales with Split Payment
        $shift = CashShift::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'user_id' => $cashier->id,
            'opening_cash' => 5000.00,
            'cash_sales' => 5400.00,
            'cash_in' => 0.00,
            'cash_out' => 0.00,
            'expense_total' => 450.00,
            'status' => 'open',
            'opened_at' => Carbon::now()->startOfDay()->addHours(9),
            'notes' => 'Morning shift open with ৳5,000 float',
        ]);

        // Sample Sale 1: Split Payment (Cash ৳500 + bKash ৳550 = ৳1050)
        $sale1 = Sale::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'customer_id' => $cust2->id,
            'shift_id' => $shift->id,
            'invoice_no' => 'INV-' . date('Ymd') . '-0001',
            'subtotal' => 1050.00,
            'discount_amount' => 0,
            'vat_amount' => 52.50,
            'total' => 1102.50,
            'paid' => 1102.50,
            'due' => 0,
            'payment_status' => 'paid',
            'status' => 'completed',
            'payment_method' => 'split',
            'sold_by' => $cashier->id,
            'sale_date' => Carbon::now()->subHours(2),
            'notes' => 'Split payment via Cash and bKash',
        ]);

        SaleItem::create([
            'sale_id' => $sale1->id,
            'product_id' => $createdProducts[4]->id, // Fast charger
            'product_name' => $createdProducts[4]->name,
            'quantity' => 1,
            'unit_price' => 1050.00,
            'cost_price' => 650.00,
            'vat' => 52.50,
            'subtotal' => 1050.00,
        ]);

        SalePayment::create([
            'tenant_id' => $tenant->id,
            'sale_id' => $sale1->id,
            'customer_id' => $cust2->id,
            'payment_no' => 'PAY-001',
            'method' => 'cash',
            'amount' => 500.00,
            'paid_at' => Carbon::now()->subHours(2),
        ]);

        SalePayment::create([
            'tenant_id' => $tenant->id,
            'sale_id' => $sale1->id,
            'customer_id' => $cust2->id,
            'payment_no' => 'PAY-002',
            'method' => 'bkash',
            'amount' => 602.50,
            'trx_id' => '9H78BJK21',
            'paid_at' => Carbon::now()->subHours(2),
        ]);

        // Sample Sale 2: Panjabi Sale
        $sale2 = Sale::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'customer_id' => $walkin->id,
            'shift_id' => $shift->id,
            'invoice_no' => 'INV-' . date('Ymd') . '-0002',
            'subtotal' => 1800.00,
            'discount_amount' => 100.00,
            'vat_amount' => 85.00,
            'total' => 1785.00,
            'paid' => 1785.00,
            'due' => 0,
            'payment_status' => 'paid',
            'status' => 'completed',
            'payment_method' => 'cash',
            'sold_by' => $cashier->id,
            'sale_date' => Carbon::now()->subMinutes(45),
            'notes' => 'Store promo ৳100 discount',
        ]);

        SaleItem::create([
            'sale_id' => $sale2->id,
            'product_id' => $createdProducts[0]->id, // Panjabi
            'product_name' => $createdProducts[0]->name,
            'quantity' => 1,
            'unit_price' => 1800.00,
            'cost_price' => 1100.00,
            'discount' => 100.00,
            'vat' => 85.00,
            'subtotal' => 1700.00,
        ]);

        SalePayment::create([
            'tenant_id' => $tenant->id,
            'sale_id' => $sale2->id,
            'customer_id' => $walkin->id,
            'payment_no' => 'PAY-003',
            'method' => 'cash',
            'amount' => 1785.00,
            'paid_at' => Carbon::now()->subMinutes(45),
        ]);

        // 12. Accounting & Expenses
        $accCash = Account::create([
            'tenant_id' => $tenant->id,
            'name' => 'Main Cash Drawer (ঢাকা ক্যাশ)',
            'type' => 'cash',
            'balance' => 45000.00,
        ]);

        $accBank = Account::create([
            'tenant_id' => $tenant->id,
            'name' => 'City Bank Current A/C',
            'type' => 'bank',
            'account_number' => '1102938475001',
            'bank_name' => 'City Bank PLC',
            'balance' => 380000.00,
        ]);

        $accBkash = Account::create([
            'tenant_id' => $tenant->id,
            'name' => 'bKash Merchant Account',
            'type' => 'mobile_banking',
            'account_number' => '01711000000',
            'bank_name' => 'bKash',
            'balance' => 84500.00,
        ]);

        $expCatUtility = ExpenseCategory::create(['tenant_id' => $tenant->id, 'name' => 'Electricity & Utilities']);
        $expCatSnacks = ExpenseCategory::create(['tenant_id' => $tenant->id, 'name' => 'Office Tea & Snacks']);
        $expCatTransport = ExpenseCategory::create(['tenant_id' => $tenant->id, 'name' => 'Courier & Transport']);

        Expense::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $dhakaBranch->id,
            'category_id' => $expCatSnacks->id,
            'shift_id' => $shift->id,
            'expense_no' => 'EXP-001',
            'title' => 'Guest Tea & Refreshment',
            'amount' => 450.00,
            'payment_method' => 'cash',
            'date' => Carbon::now()->toDateString(),
            'notes' => 'Store afternoon refreshments',
            'created_by' => $cashier->id,
        ]);

        // 13. SMS Templates (Bangla + English)
        SmsTemplate::create([
            'tenant_id' => $tenant->id,
            'type' => 'sale_invoice',
            'template_en' => 'Thank you for shopping at {business_name}! Invoice: {invoice_no}. Total: ৳{total}. Paid: ৳{paid}. Have a nice day!',
            'template_bn' => '{business_name}-এ কেনাকাটার জন্য ধন্যবাদ! ইনভয়েস: {invoice_no}। মোট: ৳{total}। পরিশোধ: ৳{paid}। আবার আসবেন!',
            'is_active' => true,
        ]);

        SmsTemplate::create([
            'tenant_id' => $tenant->id,
            'type' => 'due_reminder',
            'template_en' => 'Dear {customer_name}, your current outstanding balance at {business_name} is ৳{due_amount}. Please pay by {date}.',
            'template_bn' => 'প্রিয় {customer_name}, {business_name}-এ আপনার বর্তমান বকেয়া ৳{due_amount}। অনুগ্রহ করে সময়মতো পরিশোধ করুন।',
            'is_active' => true,
        ]);

        SmsTemplate::create([
            'tenant_id' => $tenant->id,
            'type' => 'subscription_reminder',
            'template_en' => 'Your POS subscription expires in {days} days. Please renew to enjoy uninterrupted services.',
            'template_bn' => 'আপনার POS subscription {days} দিন পর শেষ হবে। নিরবচ্ছিন্ন সেবার জন্য অনুগ্রহ করে renew করুন।',
            'is_active' => true,
        ]);
    }
}
