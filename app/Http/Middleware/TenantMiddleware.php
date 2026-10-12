<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            // Check if super admin is previewing a tenant or fallback to first tenant
            $previewTenantId = session('admin_preview_tenant_id') ?? $user->tenant_id;
            $tenant = $previewTenantId ? \App\Models\Tenant::find($previewTenantId) : \App\Models\Tenant::first();

            if (!$tenant) {
                return redirect()->route('admin.dashboard')->with('error', 'কোনো রেজিস্টার্ড টেন্যান্ট পাওয়া যায়নি।');
            }

            // Share current tenant with the entire app
            app()->instance('currentTenant', $tenant);
            view()->share('currentTenant', $tenant);
            view()->share('isSuperAdminPreview', true);

            // Share active branch
            $activeBranchId = session('active_branch_id');
            $branch = null;
            if ($activeBranchId && $activeBranchId !== 'all') {
                $branch = $tenant->branches()->find($activeBranchId);
            }
            if (!$branch) {
                $branch = $tenant->branches()->where('is_main', true)->first() ?? $tenant->branches()->first();
            }

            if ($branch) {
                session(['active_branch_id' => $branch->id]);
                app()->instance('activeBranch', $branch);
                view()->share('activeBranch', $branch);
            }

            view()->share('allBranches', $tenant->branches()->where('status', 'active')->get());

            return $next($request);
        }

        if (!$user->tenant_id || !$user->tenant) {
            return redirect()->route('onboarding.step1');
        }

        // Share current tenant with the entire app
        app()->instance('currentTenant', $user->tenant);
        view()->share('currentTenant', $user->tenant);

        // Share active branch (stored in session or fallback to user branch or main branch)
        $activeBranchId = session('active_branch_id');
        $branch = null;

        if ($activeBranchId && $activeBranchId !== 'all') {
            $branch = $user->tenant->branches()->find($activeBranchId);
        }

        if (!$branch && $user->branch_id) {
            $branch = $user->branch;
        }

        if (!$branch) {
            $branch = $user->tenant->branches()->where('is_main', true)->first()
                   ?? $user->tenant->branches()->first();
        }

        if ($branch) {
            session(['active_branch_id' => $branch->id]);
            app()->instance('activeBranch', $branch);
            view()->share('activeBranch', $branch);
        }

        // Share all available branches for the branch switcher
        view()->share('allBranches', $user->tenant->branches()->where('status', 'active')->get());

        return $next($request);
    }
}
