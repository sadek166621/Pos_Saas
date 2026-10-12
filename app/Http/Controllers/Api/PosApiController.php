<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\PosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class PosApiController extends Controller
{
    public function __construct(
        protected PosService $posService
    ) {}

    public function products(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $branchId = $request->query('branch_id', $user->branch_id);

        if (!$branchId && $tenant) {
            $branchId = $tenant->branches()->first()?->id;
        }

        $products = Product::where('tenant_id', $tenant->id)
            ->where('status', true)
            ->with(['unit'])
            ->get()
            ->map(function ($p) use ($branchId) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku,
                    'barcode' => $p->barcode,
                    'category_id' => $p->category_id,
                    'price' => $branchId ? $p->getPriceForBranch($branchId) : (float) $p->selling_price,
                    'stock' => $branchId ? $p->getStockForBranch($branchId) : 0,
                    'unit' => $p->unit?->code ?? 'pcs',
                    'vat_percent' => (float) $p->vat_percent,
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'products' => $products,
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $branchId = $request->input('branch_id', $user->branch_id);
        $branch = Branch::where('tenant_id', $tenant->id)->findOrFail($branchId);

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
                $user->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Sale processed successfully',
                'sale' => $sale,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
