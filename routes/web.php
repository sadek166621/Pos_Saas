<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public SaaS Marketing Website
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/features', [LandingController::class, 'features'])->name('features');
Route::get('/solutions', [LandingController::class, 'solutions'])->name('solutions');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');
Route::get('/terms', [LandingController::class, 'terms'])->name('terms');
Route::get('/privacy', [LandingController::class, 'privacy'])->name('privacy');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Business Onboarding (Wizard)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/step-1', [OnboardingController::class, 'step1'])->name('step1');
    Route::post('/step-1', [OnboardingController::class, 'postStep1'])->name('step1.post');
    Route::get('/step-2', [OnboardingController::class, 'step2'])->name('step2');
    Route::post('/step-2', [OnboardingController::class, 'postStep2'])->name('step2.post');
    Route::get('/step-3', [OnboardingController::class, 'step3'])->name('step3');
    Route::post('/step-3', [OnboardingController::class, 'postStep3'])->name('step3.post');
});

/*
|--------------------------------------------------------------------------
| Super Admin Panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/tenants', [SuperAdminController::class, 'tenants'])->name('tenants');
    Route::post('/tenants/{id}/toggle-status', [SuperAdminController::class, 'toggleTenantStatus'])->name('tenants.toggle');
    Route::get('/plans', [SuperAdminController::class, 'plans'])->name('plans');
    Route::put('/plans/{id}', [SuperAdminController::class, 'updatePlan'])->name('plans.update');
    Route::get('/transactions', [SuperAdminController::class, 'transactions'])->name('transactions');
});

/*
|--------------------------------------------------------------------------
| Tenant Application (Business Owner, Managers, Cashiers)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant', 'subscription.active'])->group(function () {
    // Central Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/switch-branch', [DashboardController::class, 'switchBranch'])->name('switch-branch');

    // Advanced POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [PosController::class, 'searchProducts'])->name('pos.search');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/open-shift', [PosController::class, 'openShift'])->name('pos.open-shift');
    Route::post('/pos/close-shift', [PosController::class, 'closeShift'])->name('pos.close-shift');

    // Products & Catalog
    Route::get('/products/barcodes', [ProductController::class, 'barcodes'])->name('products.barcodes');
    Route::get('/products/branch-pricing', [ProductController::class, 'branchPricing'])->name('products.branch-pricing');
    Route::resource('products', ProductController::class);

    // Multi-Branch Management
    Route::resource('branches', BranchController::class)->except(['destroy']);

    // Inventory & Warehouses
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/movements', [InventoryController::class, 'movements'])->name('inventory.movements');
    Route::get('/inventory/transfers', [InventoryController::class, 'transfers'])->name('inventory.transfers');
    Route::get('/inventory/create-transfer', [InventoryController::class, 'createTransfer'])->name('inventory.create-transfer');
    Route::post('/inventory/transfers', [InventoryController::class, 'storeTransfer'])->name('inventory.store-transfer');
    Route::get('/inventory/adjustments', [InventoryController::class, 'adjustments'])->name('inventory.adjustments');
    Route::post('/inventory/adjustments', [InventoryController::class, 'storeAdjustment'])->name('inventory.store-adjustment');

    // Purchases & Suppliers
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'show']);
    Route::post('/suppliers/{id}/pay', [SupplierController::class, 'payDue'])->name('suppliers.pay');

    // Customers CRM & Due
    Route::get('/customers/dues', [CustomerController::class, 'dues'])->name('customers.dues');
    Route::resource('customers', CustomerController::class)->only(['index', 'store', 'show']);
    Route::post('/customers/{id}/collect-due', [CustomerController::class, 'collectDue'])->name('customers.collect-due');

    // Sales, Returns & Receipts
    Route::get('/sales/returns', [SaleController::class, 'returns'])->name('sales.returns');
    Route::get('/sales/{id}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::post('/sales/{id}/return', [SaleController::class, 'returnSale'])->name('sales.return');
    Route::resource('sales', SaleController::class)->only(['index', 'show']);

    // Accounting & Expenses
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::post('/expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.categories.store');
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounting/profit-loss', [AccountController::class, 'profitLoss'])->name('accounting.profit-loss');

    // Employees & Commissions
    Route::get('/employees/commissions', [EmployeeController::class, 'commissions'])->name('employees.commissions');
    Route::resource('employees', EmployeeController::class)->only(['index', 'store']);

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/customer-dues', [ReportController::class, 'customerDues'])->name('reports.customer-dues');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/business', [SettingController::class, 'updateBusiness'])->name('settings.business');
    Route::put('/settings/sms/{id}', [SettingController::class, 'updateSmsTemplate'])->name('settings.sms');

    // Subscription & Billing
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/renew', [SubscriptionController::class, 'renew'])->name('subscription.renew');
    Route::get('/subscription/payment/callback', [SubscriptionController::class, 'mockCallback'])->name('subscription.payment.mock-callback');
});
