<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\SalePayment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $tenant = app('currentTenant');
        $customers = Customer::where('tenant_id', $tenant->id)->latest('id')->paginate(15);
        $totalCustomerDue = Customer::where('tenant_id', $tenant->id)->sum('current_due');

        return view('customers.index', compact('customers', 'totalCustomerDue'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'type' => 'required|in:regular,vip,wholesale,corporate',
            'credit_limit' => 'nullable|numeric|min:0',
            'previous_due' => 'nullable|numeric|min:0',
        ]);

        $prevDue = (float) ($validated['previous_due'] ?? 0);

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'type' => $validated['type'],
            'credit_limit' => $validated['credit_limit'] ?? 0,
            'previous_due' => $prevDue,
            'current_due' => $prevDue,
            'loyalty_points' => 0,
            'status' => true,
        ]);

        if ($prevDue > 0) {
            CustomerLedger::create([
                'tenant_id' => $tenant->id,
                'customer_id' => $customer->id,
                'type' => 'opening',
                'debit' => $prevDue,
                'balance' => $prevDue,
                'notes' => 'Opening balance due',
            ]);
        }

        return back()->with('success', 'কাস্টমার সফলভাবে তৈরি হয়েছে!');
    }

    public function show(int $id): View
    {
        $tenant = app('currentTenant');
        $customer = Customer::where('tenant_id', $tenant->id)->with(['sales.items', 'ledgers' => fn($q) => $q->latest('id')])->findOrFail($id);

        return view('customers.show', compact('customer'));
    }

    public function collectDue(Request $request, int $id): RedirectResponse
    {
        $tenant = app('currentTenant');
        $customer = Customer::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $amount = (float) $validated['amount'];
        $customer->decrement('current_due', $amount);

        SalePayment::create([
            'tenant_id' => $tenant->id,
            'sale_id' => null,
            'customer_id' => $customer->id,
            'payment_no' => 'PAY-CUST-' . date('Ymd') . '-' . rand(100, 999),
            'method' => $validated['payment_method'],
            'amount' => $amount,
            'paid_at' => Carbon::now('Asia/Dhaka'),
            'notes' => $validated['notes'] ?? 'Customer due collection',
        ]);

        CustomerLedger::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'type' => 'payment',
            'credit' => $amount,
            'balance' => $customer->current_due,
            'notes' => 'Due collection: ৳' . number_format($amount, 2) . ' via ' . $validated['payment_method'],
        ]);

        return back()->with('success', 'কাস্টমার বকেয়া কালেকশন সফলভাবে সম্পন্ন হয়েছে!');
    }

    public function dues(): View
    {
        $tenant = app('currentTenant');
        $customersWithDue = Customer::where('tenant_id', $tenant->id)
            ->where('current_due', '>', 0)
            ->orderByDesc('current_due')
            ->paginate(20);

        $totalDue = Customer::where('tenant_id', $tenant->id)->sum('current_due');

        return view('customers.dues', compact('customersWithDue', 'totalDue'));
    }
}
