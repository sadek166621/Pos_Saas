<?php

namespace App\Http\Controllers;

use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\PosService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class PosController extends Controller
{
    public function __construct(
        protected PosService $posService
    ) {}

    public function index(): View
    {
        $tenant = app('currentTenant');
        $branch = app('activeBranch');

        // Check if branch exists
        if (!$branch) {
            $branch = $tenant->branches()->first();
            session(['active_branch_id' => $branch->id]);
        }

        // Active shift
        $activeShift = CashShift::where('tenant_id', $tenant->id)
            ->where('branch_id', $branch->id)
            ->where('status', 'open')
            ->latest('id')
            ->first();

        $categories = Category::where('tenant_id', $tenant->id)->where('status', true)->get();
        $customers = Customer::where('tenant_id', $tenant->id)->where('status', true)->orderBy('name')->get();

        // Load all active products with branch prices and stocks
        $products = Product::where('tenant_id', $tenant->id)
            ->where('status', true)
            ->with(['category', 'unit', 'branchPrices' => fn($q) => $q->where('branch_id', $branch->id), 'branchStocks' => fn($q) => $q->where('branch_id', $branch->id)])
            ->get()
            ->map(function ($p) use ($branch) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'barcode' => $p->barcode,
                    'sku' => $p->sku,
                    'category_id' => $p->category_id,
                    'price' => $p->getPriceForBranch($branch->id),
                    'stock' => $p->getStockForBranch($branch->id),
                    'unit' => $p->unit?->code ?? 'pcs',
                    'vat_percent' => (float) $p->vat_percent,
                    'image' => $p->image,
                ];
            });

        return view('pos.index', compact('tenant', 'branch', 'activeShift', 'categories', 'customers', 'products'));
    }

    /**
     * Search products via AJAX (Barcode scanner or typing)
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $tenant = app('currentTenant');
        $branch = app('activeBranch');
        $query = $request->input('q', '');
        $categoryId = $request->input('category_id');

        $productsQuery = Product::where('tenant_id', $tenant->id)
            ->where('status', true);

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('barcode', $query)
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            });
        }

        $products = $productsQuery->with(['unit'])->take(30)->get()->map(function ($p) use ($branch) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'barcode' => $p->barcode,
                'sku' => $p->sku,
                'price' => $p->getPriceForBranch($branch->id),
                'stock' => $p->getStockForBranch($branch->id),
                'unit' => $p->unit?->code ?? 'pcs',
                'vat_percent' => (float) $p->vat_percent,
            ];
        });

        return response()->json(['success' => true, 'products' => $products]);
    }

    /**
     * Process POS Checkout / Sale
     */
    public function checkout(Request $request): JsonResponse
    {
        $tenant = app('currentTenant');
        $branch = app('activeBranch');

        if (!$branch) {
            return response()->json(['success' => false, 'message' => 'Active branch not selected.'], 422);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        try {
            $sale = $this->posService->processSale(
                $tenant,
                $branch,
                $request->all(),
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'বিক্রি সফলভাবে সম্পন্ন হয়েছে! ইনভয়েস তৈরি হয়েছে।',
                'sale' => $sale,
                'invoice_url' => route('sales.invoice', $sale->id),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Open Cash Shift
     */
    public function openShift(Request $request): JsonResponse
    {
        $tenant = app('currentTenant');
        $branch = app('activeBranch');

        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $existing = CashShift::where('tenant_id', $tenant->id)
            ->where('branch_id', $branch->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'An active shift is already open for this branch.'], 422);
        }

        $shift = CashShift::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
            'user_id' => auth()->id(),
            'opening_cash' => $request->input('opening_cash'),
            'cash_sales' => 0,
            'cash_in' => 0,
            'cash_out' => 0,
            'expense_total' => 0,
            'status' => 'open',
            'opened_at' => Carbon::now('Asia/Dhaka'),
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ক্যাশ ড্রয়ার এবং শিফট সফলভাবে শুরু হয়েছে!',
            'shift' => $shift,
        ]);
    }

    /**
     * Close Cash Shift with Variance Calculation
     */
    public function closeShift(Request $request): JsonResponse
    {
        $tenant = app('currentTenant');
        $branch = app('activeBranch');

        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $shift = CashShift::where('tenant_id', $tenant->id)
            ->where('branch_id', $branch->id)
            ->where('status', 'open')
            ->latest('id')
            ->first();

        if (!$shift) {
            return response()->json(['success' => false, 'message' => 'No open shift found to close.'], 422);
        }

        $expectedCash = $shift->opening_cash + $shift->cash_sales + $shift->cash_in - $shift->cash_out - $shift->expense_total;
        $actualCash = (float) $request->input('actual_cash');
        $difference = $actualCash - $expectedCash;

        $shift->update([
            'closing_cash' => $actualCash,
            'expected_cash' => $expectedCash,
            'status' => 'closed',
            'closed_at' => Carbon::now('Asia/Dhaka'),
            'notes' => ($shift->notes ? $shift->notes . "\n" : '') . 'Closing Notes: ' . $request->input('notes') . " (Difference: ৳{$difference})",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'শিফট সফলভাবে বন্ধ করা হয়েছে!',
            'expected_cash' => $expectedCash,
            'actual_cash' => $actualCash,
            'difference' => $difference,
        ]);
    }
}
