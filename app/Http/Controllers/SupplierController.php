<?php

namespace App\Http\Controllers;

use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $tenant = app('currentTenant');
        $suppliers = Supplier::where('tenant_id', $tenant->id)->latest('id')->paginate(15);
        $totalSupplierDue = Supplier::where('tenant_id', $tenant->id)->sum('current_due');

        return view('suppliers.index', compact('suppliers', 'totalSupplierDue'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'previous_due' => 'nullable|numeric|min:0',
        ]);

        $prevDue = (float) ($validated['previous_due'] ?? 0);

        $supplier = Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'previous_due' => $prevDue,
            'current_due' => $prevDue,
            'status' => true,
        ]);

        if ($prevDue > 0) {
            SupplierLedger::create([
                'tenant_id' => $tenant->id,
                'supplier_id' => $supplier->id,
                'type' => 'opening',
                'credit' => $prevDue,
                'balance' => $prevDue,
                'notes' => 'Opening balance due',
            ]);
        }

        return back()->with('success', 'সাপ্লায়ার সফলভাবে তৈরি হয়েছে!');
    }

    public function show(int $id): View
    {
        $tenant = app('currentTenant');
        $supplier = Supplier::where('tenant_id', $tenant->id)->with(['purchases', 'ledgers' => fn($q) => $q->latest('id')])->findOrFail($id);

        return view('suppliers.show', compact('supplier'));
    }

    public function payDue(Request $request, int $id): RedirectResponse
    {
        $tenant = app('currentTenant');
        $supplier = Supplier::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $amount = (float) $validated['amount'];
        $supplier->decrement('current_due', $amount);

        PurchasePayment::create([
            'tenant_id' => $tenant->id,
            'supplier_id' => $supplier->id,
            'payment_no' => 'PAY-SUP-' . date('Ymd') . '-' . rand(100, 999),
            'amount' => $amount,
            'payment_method' => $validated['payment_method'],
            'paid_at' => Carbon::now('Asia/Dhaka'),
            'notes' => $validated['notes'] ?? 'Supplier due payment',
        ]);

        SupplierLedger::create([
            'tenant_id' => $tenant->id,
            'supplier_id' => $supplier->id,
            'type' => 'payment',
            'debit' => $amount,
            'balance' => $supplier->current_due,
            'notes' => 'Due payment: ৳' . number_format($amount, 2) . ' via ' . $validated['payment_method'],
        ]);

        return back()->with('success', 'সাপ্লায়ার বকেয়া পরিশোধ সফলভাবে সম্পন্ন হয়েছে!');
    }
}
