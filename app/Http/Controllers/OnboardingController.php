<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function step1(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user->tenant_id && $user->tenant) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.step1');
    }

    public function postStep1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'bin_number' => 'nullable|string',
        ]);

        $user = Auth::user();

        $slug = Str::slug($validated['business_name']);
        if (Tenant::where('slug', $slug)->exists()) {
            $slug .= '-' . rand(100, 999);
        }

        $tenant = Tenant::create([
            'name' => $validated['business_name'],
            'slug' => $slug,
            'business_type' => $validated['business_type'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? $user->email,
            'address' => $validated['address'],
            'city' => $validated['city'],
            'bin_number' => $validated['bin_number'] ?? null,
            'currency' => 'BDT',
            'currency_symbol' => '৳',
            'timezone' => 'Asia/Dhaka',
            'status' => 'trial',
            'trial_ends_at' => Carbon::now('Asia/Dhaka')->addDays(14),
            'subscription_ends_at' => Carbon::now('Asia/Dhaka')->addDays(14),
        ]);

        $user->update(['tenant_id' => $tenant->id]);

        return redirect()->route('onboarding.step2');
    }

    public function step2(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user->tenant_id) {
            return redirect()->route('onboarding.step1');
        }

        return view('onboarding.step2', ['tenant' => $user->tenant]);
    }

    public function postStep2(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'nullable|string|max:20',
            'branch_phone' => 'nullable|string|max:20',
            'branch_address' => 'required|string',
            'city' => 'required|string',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['branch_name'],
            'code' => $validated['branch_code'] ?? 'MAIN-01',
            'phone' => $validated['branch_phone'] ?? $tenant->phone,
            'address' => $validated['branch_address'],
            'city' => $validated['city'],
            'manager_id' => $user->id,
            'is_main' => true,
            'status' => 'active',
        ]);

        Warehouse::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
            'name' => $branch->name . ' Warehouse',
            'code' => 'WH-' . ($branch->code ?? '01'),
            'address' => $branch->address,
            'status' => 'active',
        ]);

        $user->update(['branch_id' => $branch->id]);
        session(['active_branch_id' => $branch->id]);

        return redirect()->route('onboarding.step3');
    }

    public function step3(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user->tenant_id) {
            return redirect()->route('onboarding.step1');
        }

        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('onboarding.step3', compact('plans'));
    }

    public function postStep3(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;
        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        // Set 14-day trial for chosen plan
        Subscription::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'plan_id' => $plan->id,
                'billing_cycle' => $validated['billing_cycle'],
                'status' => 'trial',
                'starts_at' => Carbon::now('Asia/Dhaka'),
                'ends_at' => Carbon::now('Asia/Dhaka')->addDays(14),
                'trial_ends_at' => Carbon::now('Asia/Dhaka')->addDays(14),
                'auto_renew' => false,
            ]
        );

        $tenant->update([
            'status' => 'trial',
            'trial_ends_at' => Carbon::now('Asia/Dhaka')->addDays(14),
            'subscription_ends_at' => Carbon::now('Asia/Dhaka')->addDays(14),
        ]);

        return redirect()->route('dashboard')->with('success', 'স্বাগতম! আপনার ব্যবসা সফলভাবে সেটআপ করা হয়েছে। ১৪ দিনের ট্রায়াল সক্রিয় করা হয়েছে।');
    }
}
