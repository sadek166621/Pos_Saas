<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Check if a tenant can access a specific feature.
     */
    public function canAccessFeature(Tenant $tenant, string $feature): bool
    {
        return $tenant->hasFeature($feature);
    }

    /**
     * Check if tenant is within quota limits.
     */
    public function checkQuota(Tenant $tenant, string $resource): array
    {
        $sub = $tenant->activeSubscription;
        $plan = $sub ? $sub->plan : null;

        if (!$plan) {
            return ['allowed' => true, 'current' => 0, 'max' => 9999];
        }

        switch ($resource) {
            case 'branches':
                $current = $tenant->branches()->count();
                $max = $plan->max_branches;
                break;
            case 'users':
                $current = $tenant->users()->count();
                $max = $plan->max_users;
                break;
            case 'products':
                $current = $tenant->products()->count();
                $max = $plan->max_products;
                break;
            case 'warehouses':
                $current = $tenant->warehouses()->count();
                $max = $plan->max_warehouses;
                break;
            default:
                return ['allowed' => true, 'current' => 0, 'max' => 9999];
        }

        return [
            'allowed' => $current < $max,
            'current' => $current,
            'max' => $max,
        ];
    }

    /**
     * Process subscription renewal or initial purchase.
     */
    public function renewSubscription(Tenant $tenant, int $planId, string $billingCycle, string $paymentMethod = 'bkash'): array
    {
        return DB::transaction(function () use ($tenant, $planId, $billingCycle, $paymentMethod) {
            $plan = SubscriptionPlan::findOrFail($planId);
            $amount = $billingCycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;

            $durationMonths = $billingCycle === 'yearly' ? 12 : 1;
            $currentEnd = $tenant->subscription_ends_at && $tenant->subscription_ends_at->isFuture()
                ? $tenant->subscription_ends_at
                : Carbon::now('Asia/Dhaka');

            $newEnd = $currentEnd->copy()->addMonthsNoOverflow($durationMonths);

            // Update or create subscription
            $subscription = Subscription::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'plan_id' => $plan->id,
                    'billing_cycle' => $billingCycle,
                    'status' => 'active',
                    'starts_at' => Carbon::now('Asia/Dhaka'),
                    'ends_at' => $newEnd,
                    'grace_ends_at' => null,
                ]
            );

            // Create Invoice
            $invoiceNo = 'SUB-INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $invoice = SubscriptionInvoice::create([
                'tenant_id' => $tenant->id,
                'subscription_id' => $subscription->id,
                'invoice_no' => $invoiceNo,
                'subtotal' => $amount,
                'discount' => 0,
                'tax' => 0,
                'total' => $amount,
                'status' => 'paid',
                'due_date' => Carbon::now('Asia/Dhaka')->toDateString(),
                'paid_at' => Carbon::now('Asia/Dhaka'),
                'payment_method' => $paymentMethod,
            ]);

            // Update Tenant Status
            $tenant->update([
                'status' => 'active',
                'subscription_ends_at' => $newEnd,
                'grace_ends_at' => null,
            ]);

            return [
                'success' => true,
                'subscription' => $subscription,
                'invoice' => $invoice,
                'new_end' => $newEnd->format('d M Y'),
            ];
        });
    }

    /**
     * Check expiring subscriptions and handle grace period / suspension.
     */
    public function processExpiringSubscriptions(): array
    {
        $now = Carbon::now('Asia/Dhaka');
        $expiredCount = 0;
        $graceCount = 0;
        $suspendedCount = 0;

        $tenants = Tenant::whereNotNull('subscription_ends_at')->get();

        foreach ($tenants as $tenant) {
            if ($tenant->subscription_ends_at->isPast()) {
                if ($tenant->status === 'active') {
                    // Enter 3-day grace period
                    $graceEnd = $tenant->subscription_ends_at->copy()->addDays(3);
                    if ($now->lessThanOrEqualTo($graceEnd)) {
                        $tenant->update([
                            'status' => 'grace_period',
                            'grace_ends_at' => $graceEnd,
                        ]);
                        $graceCount++;
                    } else {
                        $tenant->update(['status' => 'suspended']);
                        $suspendedCount++;
                    }
                } elseif ($tenant->status === 'grace_period' && $tenant->grace_ends_at && $now->greaterThan($tenant->grace_ends_at)) {
                    $tenant->update(['status' => 'suspended']);
                    $suspendedCount++;
                }
                $expiredCount++;
            }
        }

        return [
            'checked' => $tenants->count(),
            'expired' => $expiredCount,
            'grace_period' => $graceCount,
            'suspended' => $suspendedCount,
        ];
    }
}
