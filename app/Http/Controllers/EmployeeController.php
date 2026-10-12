<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function index(): View
    {
        $tenant = app('currentTenant');
        $employees = Employee::where('tenant_id', $tenant->id)->with(['branch', 'user'])->latest('id')->paginate(15);
        $branches = $tenant->branches()->where('status', 'active')->get();
        $quota = $this->subscriptionService->checkQuota($tenant, 'users');

        return view('employees.index', compact('employees', 'branches', 'quota'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');
        $quota = $this->subscriptionService->checkQuota($tenant, 'users');

        if (!$quota['allowed']) {
            return back()->with('warning', "আপনার প্ল্যানের সর্বোচ্চ ইউজার লিমিট ({$quota['max']}) পূর্ণ হয়ে গেছে। প্ল্যান আপগ্রেড করুন।");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'branch_id' => 'required|exists:branches,id',
            'designation' => 'required|string|max:100',
            'role' => 'required|in:manager,cashier,salesman,accountant',
            'salary' => 'required|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'password' => 'required|string|min:6',
        ]);

        // Create User
        $user = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $validated['branch_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'status' => 'active',
            'password' => Hash::make($validated['password']),
        ]);

        // Create Employee Profile
        Employee::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $validated['branch_id'],
            'user_id' => $user->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'designation' => $validated['designation'],
            'salary' => $validated['salary'],
            'commission_rate' => $validated['commission_rate'] ?? 0,
            'status' => true,
        ]);

        return back()->with('success', 'কর্মচারী এবং ইউজার অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে!');
    }

    public function commissions(): View
    {
        $tenant = app('currentTenant');
        $employees = Employee::where('tenant_id', $tenant->id)->with(['user', 'branch'])->get();

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $commissionData = [];
        foreach ($employees as $emp) {
            $totalSales = 0;
            if ($emp->user_id) {
                $totalSales = Sale::where('tenant_id', $tenant->id)
                    ->where('sold_by', $emp->user_id)
                    ->whereBetween('sale_date', [$monthStart, $monthEnd])
                    ->sum('total');
            }

            $commissionEarned = ($totalSales * (float) $emp->commission_rate) / 100;

            $commissionData[] = [
                'employee' => $emp,
                'monthly_sales' => $totalSales,
                'rate' => $emp->commission_rate,
                'commission_earned' => $commissionEarned,
                'total_payable' => (float) $emp->salary + $commissionEarned,
            ];
        }

        return view('employees.commissions', compact('commissionData'));
    }
}
