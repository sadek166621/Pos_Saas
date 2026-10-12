<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function index(): View
    {
        $tenant = app('currentTenant');
        $branches = $tenant->branches()->with(['manager', 'users', 'warehouses'])->get();
        $quota = $this->subscriptionService->checkQuota($tenant, 'branches');

        return view('branches.index', compact('branches', 'quota', 'tenant'));
    }

    public function create(): View|RedirectResponse
    {
        $tenant = app('currentTenant');
        $quota = $this->subscriptionService->checkQuota($tenant, 'branches');

        if (!$quota['allowed']) {
            return redirect()->route('branches.index')->with('warning', "আপনার বর্তমান সাবস্ক্রিপশন প্ল্যানে সর্বোচ্চ {$quota['max']} টি ব্রাঞ্চ অনুমোদন আছে। নতুন ব্রাঞ্চ খুলতে প্ল্যান আপগ্রেড করুন বা ব্রাঞ্চ অ্যাড-অন কিনুন।");
        }

        $managers = $tenant->users()->whereIn('role', ['manager', 'admin', 'owner'])->get();
        return view('branches.create', compact('managers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');
        $quota = $this->subscriptionService->checkQuota($tenant, 'branches');

        if (!$quota['allowed']) {
            return redirect()->route('branches.index')->with('warning', 'Branch quota limit reached.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'manager_id' => $validated['manager_id'] ?? null,
            'is_main' => false,
            'status' => 'active',
        ]);

        // Auto create branch warehouse
        Warehouse::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
            'name' => $branch->name . ' Warehouse',
            'code' => 'WH-' . $branch->code,
            'address' => $branch->address,
            'status' => 'active',
        ]);

        return redirect()->route('branches.index')->with('success', "নতুন ব্রাঞ্চ '{$branch->name}' সফলভাবে যুক্ত হয়েছে!");
    }

    public function edit(int $id): View
    {
        $tenant = app('currentTenant');
        $branch = $tenant->branches()->findOrFail($id);
        $managers = $tenant->users()->whereIn('role', ['manager', 'admin', 'owner'])->get();

        return view('branches.edit', compact('branch', 'managers'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $tenant = app('currentTenant');
        $branch = $tenant->branches()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'ব্রাঞ্চ তথ্য সফলভাবে আপডেট হয়েছে!');
    }
}
