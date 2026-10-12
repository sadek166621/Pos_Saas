<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Services\PaymentGatewayService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected PaymentGatewayService $paymentService
    ) {}

    public function index(): View
    {
        $tenant = app('currentTenant');
        $subscription = $tenant->activeSubscription;
        $plan = $subscription?->plan ?? SubscriptionPlan::first();

        // Calculate Usage Quotas
        $userCount = $tenant->users()->count();
        $branchCount = $tenant->branches()->count();
        $productCount = $tenant->products()->count();
        $warehouseCount = $tenant->warehouses()->count();

        $invoices = SubscriptionInvoice::where('tenant_id', $tenant->id)->latest('id')->take(10)->get();
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $addons = Addon::where('is_active', true)->get();

        return view('subscription.index', compact(
            'tenant',
            'subscription',
            'plan',
            'userCount',
            'branchCount',
            'productCount',
            'warehouseCount',
            'invoices',
            'plans',
            'addons'
        ));
    }

    public function renew(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'gateway' => 'required|in:bkash,nagad,rocket,sslcommerz,bank',
        ]);

        $res = $this->subscriptionService->renewSubscription(
            $tenant,
            $validated['plan_id'],
            $validated['billing_cycle'],
            $validated['gateway']
        );

        return redirect()->route('subscription.index')->with('success', "সাবস্ক্রিপশন সফলভাবে রিনিউ হয়েছে! নতুন মেয়াদ: {$res['new_end']}");
    }

    public function mockCallback(Request $request): RedirectResponse
    {
        $trxId = $request->query('trx');
        if ($trxId) {
            $this->paymentService->verifyPayment($trxId);
        }

        return redirect()->route('subscription.index')->with('success', 'পেমেন্ট ভেরিফিকেশন সফল হয়েছে!');
    }
}
