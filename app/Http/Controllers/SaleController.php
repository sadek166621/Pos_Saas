<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $tenant = app('currentTenant');
        $activeBranch = app('activeBranch');

        $query = Sale::where('tenant_id', $tenant->id)->with(['customer', 'branch', 'cashier']);

        if ($request->filled('branch_id') && $request->input('branch_id') !== 'all') {
            $query->where('branch_id', $request->input('branch_id'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('invoice_no')) {
            $query->where('invoice_no', 'LIKE', '%' . $request->input('invoice_no') . '%');
        }

        $sales = $query->latest('id')->paginate(15);
        $branches = $tenant->branches()->where('status', 'active')->get();

        return view('sales.index', compact('sales', 'branches'));
    }

    public function show(int $id): View
    {
        $tenant = app('currentTenant');
        $sale = Sale::where('tenant_id', $tenant->id)
            ->with(['items.product', 'payments', 'customer', 'branch', 'cashier'])
            ->findOrFail($id);

        return view('sales.show', compact('sale'));
    }

    public function invoice(Request $request, int $id): View
    {
        $tenant = app('currentTenant');
        $sale = Sale::where('tenant_id', $tenant->id)
            ->with(['items.product', 'payments', 'customer', 'branch', 'cashier'])
            ->findOrFail($id);

        $format = $request->query('format', '80mm'); // 58mm, 80mm, a4

        return view('sales.invoice', compact('sale', 'format', 'tenant'));
    }

    public function returnSale(Request $request, int $id): RedirectResponse
    {
        $tenant = app('currentTenant');
        $sale = Sale::where('tenant_id', $tenant->id)->with('items')->findOrFail($id);

        $validated = $request->validate([
            'reason' => 'required|string',
            'refund_type' => 'required|in:cash,store_credit,due_deduct',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($tenant, $sale, $validated) {
            $totalRefund = 0;
            $returnItemsData = [];

            foreach ($validated['items'] as $itemData) {
                $saleItem = SaleItem::where('sale_id', $sale->id)->findOrFail($itemData['sale_item_id']);
                $qty = (float) $itemData['quantity'];
                $lineRefund = $qty * (float) $saleItem->unit_price;
                $totalRefund += $lineRefund;

                $returnItemsData[] = [
                    'sale_item' => $saleItem,
                    'quantity' => $qty,
                    'refund_price' => (float) $saleItem->unit_price,
                    'subtotal' => $lineRefund,
                ];
            }

            $returnNo = 'RET-' . date('Ymd') . '-' . rand(100, 999);

            $saleReturn = SaleReturn::create([
                'tenant_id' => $tenant->id,
                'branch_id' => $sale->branch_id,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'return_no' => $returnNo,
                'refund_amount' => $totalRefund,
                'refund_type' => $validated['refund_type'],
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'reason' => $validated['reason'],
            ]);

            foreach ($returnItemsData as $rItem) {
                SaleReturnItem::create([
                    'sale_return_id' => $saleReturn->id,
                    'sale_item_id' => $rItem['sale_item']->id,
                    'product_id' => $rItem['sale_item']->product_id,
                    'quantity' => $rItem['quantity'],
                    'refund_price' => $rItem['refund_price'],
                    'subtotal' => $rItem['subtotal'],
                ]);

                // Restore stock in branch
                $stock = BranchProductStock::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $sale->branch_id,
                    'product_id' => $rItem['sale_item']->product_id,
                ], ['quantity' => 0]);

                $stock->increment('quantity', $rItem['quantity']);

                // Record movement
                StockMovement::create([
                    'tenant_id' => $tenant->id,
                    'branch_id' => $sale->branch_id,
                    'product_id' => $rItem['sale_item']->product_id,
                    'type' => 'return',
                    'quantity' => $rItem['quantity'],
                    'unit_cost' => (float) $rItem['sale_item']->cost_price,
                    'reference_type' => SaleReturn::class,
                    'reference_id' => $saleReturn->id,
                    'notes' => 'Sale return: ' . $returnNo,
                    'created_by' => auth()->id(),
                ]);
            }
        });

        return back()->with('success', 'পণ্য রিটার্ন এবং স্টক রিস্টোর সফলভাবে সম্পন্ন হয়েছে!');
    }

    public function returns(): View
    {
        $tenant = app('currentTenant');
        $returns = SaleReturn::where('tenant_id', $tenant->id)
            ->with(['sale', 'customer', 'branch', 'items.product'])
            ->latest('id')
            ->paginate(15);

        return view('sales.returns', compact('returns'));
    }
}
