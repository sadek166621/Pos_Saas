<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        $totalBusinesses = Tenant::count();
        $activeCount = Tenant::where('status', 'active')->count();
        $trialCount = Tenant::where('status', 'trial')->count();
        $suspendedCount = Tenant::where('status', 'suspended')->count();

        // Calculate approximate MRR
        $mrr = Subscription::where('status', 'active')
            ->with('plan')
            ->get()
            ->sum(function ($sub) {
                if (!$sub->plan) return 0;
                return $sub->billing_cycle === 'yearly'
                    ? ($sub->plan->price_yearly / 12)
                    : $sub->plan->price_monthly;
            });

        $totalRevenue = SubscriptionInvoice::where('status', 'paid')->sum('total');

        $recentTenants = Tenant::with(['branches', 'activeSubscription.plan'])->latest('id')->take(8)->get();
        $recentTransactions = PaymentTransaction::latest('id')->take(8)->get();

        return view('admin.dashboard', compact(
            'totalBusinesses',
            'activeCount',
            'trialCount',
            'suspendedCount',
            'mrr',
            'totalRevenue',
            'recentTenants',
            'recentTransactions'
        ));
    }

    public function tenants(Request $request): View
    {
        $query = Tenant::with(['branches', 'users', 'activeSubscription.plan']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('name', 'LIKE', "%{$q}%")->orWhere('phone', 'LIKE', "%{$q}%")->orWhere('email', 'LIKE', "%{$q}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $tenants = $query->latest('id')->paginate(15);

        return view('admin.tenants', compact('tenants'));
    }

    public function toggleTenantStatus(int $id): RedirectResponse
    {
        $tenant = Tenant::findOrFail($id);
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        return back()->with('success', "Tenant '{$tenant->name}' status updated to {$newStatus}!");
    }

    public function plans(): View
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->orderBy('sort_order')->get();
        return view('admin.plans', compact('plans'));
    }

    public function updatePlan(Request $request, int $id): RedirectResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_branches' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'max_products' => 'required|integer|min:100',
            'sms_limit' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $plan->update([
            'name' => $validated['name'],
            'price_monthly' => $validated['price_monthly'],
            'price_yearly' => $validated['price_yearly'],
            'max_branches' => $validated['max_branches'],
            'max_users' => $validated['max_users'],
            'max_products' => $validated['max_products'],
            'sms_limit' => $validated['sms_limit'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Plan '{$plan->name}' successfully updated!");
    }

    public function transactions(): View
    {
        $transactions = PaymentTransaction::with('tenant')->latest('id')->paginate(20);
        return view('admin.transactions', compact('transactions'));
    }

    public function impersonateTenant(int $id): RedirectResponse
    {
        $tenant = Tenant::findOrFail($id);
        session(['admin_preview_tenant_id' => $tenant->id]);
        session()->forget('active_branch_id'); // Let it default to main branch of tenant
        return redirect()->route('dashboard')->with('success', "আপনি বর্তমানে '{$tenant->name}' এর স্টোরে সুপার অ্যাডমিন মোডে আছেন।");
    }

    public function exitImpersonation(): RedirectResponse
    {
        session()->forget(['admin_preview_tenant_id', 'active_branch_id']);
        return redirect()->route('admin.dashboard')->with('info', 'সুপার অ্যাডমিন প্যানেলে ফিরে এসেছেন।');
    }
}
