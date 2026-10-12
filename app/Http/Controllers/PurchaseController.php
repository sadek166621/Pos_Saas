<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $tenant = app('currentTenant');
        $purchases = Purchase::where('tenant_id', $tenant->id)
            ->with(['supplier', 'branch', 'items.product'])
            ->latest('id')
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $tenant = app('currentTenant');
        $suppliers = Supplier::where('tenant_id', $tenant->id)->where('status', true)->get();
        $branches = $tenant->branches()->where('status', 'active')->get();
        $products = Product::where('tenant_id', $tenant->id)->where('status', true)->get();
        $warehouses = Warehouse::where('tenant_id', $tenant->id)->where('status', 'active')->get();

        return view('purchases.create', compact('suppliers', 'branches', 'products', 'warehouses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'branch_id' => 'required|exists:branches,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($tenant, $validated) {
            $purchaseNo = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += ($item['quantity'] * $item['unit_price']);
            }

            $paid = (float) $validated['paid_amount'];
            $due = max(0, $subtotal - $paid);
            $paymentStatus = $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due');

            $purchase = Purchase::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $validated['branch_id'],
                'supplier_id' => $validated['supplier_id'],
                'warehouse_id' => $validated['warehouse_id'] ?? null,
                'purchase_no' => $purchaseNo,
                'purchase_date' => $validated['purchase_date'],
                'subtotal' => $subtotal,
                'discount' => 0,
                'vat' => 0,
                'shipping_charge' => 0,
                'total' => $subtotal,
                'paid' => $paid,
                'due' => $due,
                'payment_status' => $paymentStatus,
                'status' => 'received',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Items & Stock update
            foreach ($validated['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $lineTotal,
                ]);

                // Increment stock in branch
                $stock = BranchProductStock::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $validated['branch_id'],
                    'product_id' => $item['product_id'],
                ], ['quantity' => 0]);

                $stock->increment('quantity', $item['quantity']);

                // Record stock movement
                StockMovement::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $validated['branch_id'],
                    'product_id' => $item['product_id'],
                    'type' => 'purchase',
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_price'],
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'notes' => 'Purchase goods received: ' . $purchaseNo,
                    'created_by' => auth()->id(),
                ]);
            }

            // Record Payment
            if ($paid > 0) {
                PurchasePayment::create([
                    'tenant_id' => $tenant->id,
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $validated['supplier_id'],
                    'payment_no' => 'PAY-PO-' . $purchase->id,
                    'amount' => $paid,
                    'payment_method' => $validated['payment_method'],
                    'paid_at' => Carbon::now('Asia/Dhaka'),
                    'notes' => 'Initial payment for ' . $purchaseNo,
                ]);
            }

            // Update Supplier Ledger & Due
            $supplier = Supplier::findOrFail($validated['supplier_id']);
            if ($due > 0) {
                $supplier->increment('current_due', $due);
            }

            SupplierLedger::create([
                'tenant_id' => $tenant->id,
                'supplier_id' => $supplier->id,
                'type' => 'purchase',
                'debit' => $paid,
                'credit' => $subtotal,
                'balance' => $supplier->current_due,
                'reference_no' => $purchaseNo,
                'notes' => "Purchase #{$purchaseNo} - Total: ৳{$subtotal}, Paid: ৳{$paid}, Due: ৳{$due}",
            ]);
        });

        return redirect()->route('purchases.index')->with('success', 'পণ্য ক্রয় এবং স্টক ইন সফলভাবে সম্পন্ন হয়েছে!');
    }

    public function show(int $id): View
    {
        $tenant = app('currentTenant');
        $purchase = Purchase::where('tenant_id', $tenant->id)
            ->with(['supplier', 'branch', 'items.product', 'payments'])
            ->findOrFail($id);

        return view('purchases.show', compact('purchase'));
    }
}
