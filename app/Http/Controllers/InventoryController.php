<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request): View
    {
        $tenant = app('currentTenant');
        $activeBranch = app('activeBranch');

        $branchId = $request->query('branch_id', $activeBranch?->id);

        $stocksQuery = BranchProductStock::where('tenant_id', $tenant->id)
            ->with(['product.category', 'product.unit', 'branch', 'warehouse']);

        if ($branchId && $branchId !== 'all') {
            $stocksQuery->where('branch_id', $branchId);
        }

        $stocks = $stocksQuery->paginate(20);
        $branches = $tenant->branches()->where('status', 'active')->get();

        $lowStockCount = Product::where('tenant_id', $tenant->id)
            ->whereHas('branchStocks', function ($q) use ($branchId) {
                if ($branchId && $branchId !== 'all') {
                    $q->where('branch_id', $branchId);
                }
                $q->whereColumn('quantity', '<=', 'alert_quantity');
            })->count();

        return view('inventory.index', compact('stocks', 'branches', 'branchId', 'lowStockCount'));
    }

    public function movements(Request $request): View
    {
        $tenant = app('currentTenant');
        $activeBranch = app('activeBranch');

        $movements = StockMovement::where('tenant_id', $tenant->id)
            ->with(['product', 'branch'])
            ->latest('id')
            ->paginate(25);

        return view('inventory.movements', compact('movements'));
    }

    public function transfers(): View
    {
        $tenant = app('currentTenant');
        $transfers = StockTransfer::where('tenant_id', $tenant->id)
            ->with(['fromBranch', 'toBranch', 'items.product'])
            ->latest('id')
            ->paginate(15);

        return view('inventory.transfers', compact('transfers'));
    }

    public function createTransfer(): View
    {
        $tenant = app('currentTenant');
        $branches = $tenant->branches()->where('status', 'active')->get();
        $products = Product::where('tenant_id', $tenant->id)->where('status', true)->get();

        return view('inventory.create_transfer', compact('branches', 'products'));
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        $validated = $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->inventoryService->transferStock(
                $tenant,
                $validated['from_branch_id'],
                $validated['to_branch_id'],
                $validated['items'],
                auth()->id(),
                $validated['notes'] ?? null
            );

            return redirect()->route('inventory.transfers')->with('success', 'ব্রাঞ্চ টু ব্রাঞ্চ স্টক সফলভাবে ট্রান্সফার করা হয়েছে!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function adjustments(): View
    {
        $tenant = app('currentTenant');
        $adjustments = StockAdjustment::where('tenant_id', $tenant->id)
            ->with(['branch', 'items.product'])
            ->latest('id')
            ->paginate(15);

        return view('inventory.adjustments', compact('adjustments'));
    }

    public function storeAdjustment(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');
        $branch = app('activeBranch');

        $validated = $request->validate([
            'type' => 'required|in:addition,subtraction,damage',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.5',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->inventoryService->adjustStock(
                $tenant,
                $branch->id,
                $validated['type'],
                [['product_id' => $validated['product_id'], 'quantity' => $validated['quantity']]],
                auth()->id(),
                $validated['notes'] ?? null
            );

            return back()->with('success', 'স্টক অ্যাডজাস্টমেন্ট রেকর্ড সম্পন্ন হয়েছে!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
