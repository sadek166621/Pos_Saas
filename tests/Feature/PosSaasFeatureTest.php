<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\BranchProductPrice;
use App\Models\BranchProductStock;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PosService;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PosSaasFeatureTest extends TestCase
{
    use DatabaseTransactions;

    protected Tenant $tenant;
    protected User $owner;
    protected Branch $branchDhaka;
    protected Branch $branchSylhet;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::first();
        $this->owner = User::where('email', 'owner@rahmanfashion.com')->first();
        $this->branchDhaka = Branch::where('code', 'DHK-01')->first();
        $this->branchSylhet = Branch::where('code', 'SYL-01')->first();
        $this->product = Product::where('sku', 'CHG-SAM-005')->first();
    }

    public function test_public_marketing_pages_load_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SmartPOS SaaS');

        $pricingResponse = $this->get('/pricing');
        $pricingResponse->assertStatus(200);
        $pricingResponse->assertSee('Starter');
    }

    public function test_branch_specific_pricing_resolution(): void
    {
        // Global Price is 1000
        // Dhaka Price is 1050
        // Sylhet Price is 1100
        $dhakaPrice = $this->product->getPriceForBranch($this->branchDhaka->id);
        $sylhetPrice = $this->product->getPriceForBranch($this->branchSylhet->id);

        $this->assertEquals(1050.00, (float) $dhakaPrice);
        $this->assertEquals(1100.00, (float) $sylhetPrice);
    }

    public function test_subscription_quota_enforcement(): void
    {
        $service = app(SubscriptionService::class);

        // Check quota on branches
        $branchQuota = $service->checkQuota($this->tenant, 'branches');
        $this->assertArrayHasKey('allowed', $branchQuota);
        $this->assertArrayHasKey('current', $branchQuota);
        $this->assertArrayHasKey('max', $branchQuota);
        $this->assertEquals(3, $branchQuota['current']);
    }

    public function test_pos_checkout_creates_sale_and_decrements_stock(): void
    {
        $posService = app(PosService::class);
        $customer = Customer::where('tenant_id', $this->tenant->id)->first();

        // Check initial stock in Dhaka
        $stockRecord = BranchProductStock::where([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branchDhaka->id,
            'product_id' => $this->product->id,
        ])->first();

        $initialQty = $stockRecord ? $stockRecord->quantity : 0;

        $saleData = [
            'customer_id' => $customer->id,
            'payment_method' => 'bkash',
            'discount_amount' => 50,
            'vat_amount' => 0,
            'paid_amount' => 1000,
            'notes' => 'Test POS Transaction',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'unit_price' => 1050,
                ],
            ],
            'payments' => [
                [
                    'payment_method' => 'bkash',
                    'amount' => 1000,
                    'transaction_ref' => 'TXN-TEST-123',
                ],
            ],
        ];

        $sale = $posService->processSale($this->tenant, $this->branchDhaka, $saleData, $this->owner->id);

        $this->assertNotNull($sale);
        $this->assertEquals('completed', $sale->status);
        $this->assertEquals(1000.00, (float) $sale->total); // 1050 - 50 discount
        $this->assertEquals(1000.00, (float) $sale->paid);
        $this->assertEquals(0.00, (float) $sale->due);

        $this->assertEquals(0.00, (float) $sale->due);

        // Verify stock decremented by 1
        $stockRecord->refresh();
        $this->assertEquals($initialQty - 1, $stockRecord->quantity);
    }

    public function test_non_super_admin_cannot_access_admin_dashboard(): void
    {
        // Authenticate regular owner
        $response = $this->actingAs($this->owner)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $superAdmin = User::where('is_super_admin', true)->first();
        $response = $this->actingAs($superAdmin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('সুপার অ্যাডমিন অ্যানালিটিক্স');
    }

    public function test_super_admin_can_access_business_dashboard_without_binding_error(): void
    {
        $superAdmin = User::where('is_super_admin', true)->first();
        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('সুপার অ্যাডমিন প্রিভিউ মোড');
    }

    public function test_super_admin_can_impersonate_tenant(): void
    {
        $superAdmin = User::where('is_super_admin', true)->first();
        $response = $this->actingAs($superAdmin)->get("/admin/tenants/{$this->tenant->id}/impersonate");
        $response->assertRedirect('/dashboard');
        $this->assertEquals($this->tenant->id, session('admin_preview_tenant_id'));
    }
}
