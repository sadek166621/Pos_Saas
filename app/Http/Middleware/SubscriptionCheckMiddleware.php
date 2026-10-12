<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionCheckMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isSuperAdmin()) {
            return $next($request);
        }

        if (!app()->bound('currentTenant')) {
            return $next($request);
        }

        $tenant = app('currentTenant');

        // Check if tenant is suspended
        if ($tenant->status === 'suspended') {
            // If request is write action (POST, PUT, DELETE) and not renewal route
            if (!$request->isMethod('safe') && !$request->routeIs('subscription.*')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your subscription has expired and your account is currently suspended. Please renew your subscription to perform transactions.',
                    ], 403);
                }

                return redirect()->route('subscription.index')->with('warning', 'আপনার সাবস্ক্রিপশন শেষ হয়ে গেছে এবং অ্যাকাউন্ট সাসপেন্ড করা হয়েছে। অনুগ্রহ করে রিনিউ করুন।');
            }
        }

        // Check if in grace period
        if ($tenant->status === 'grace_period') {
            view()->share('gracePeriodWarning', true);
        }

        return $next($request);
    }
}
