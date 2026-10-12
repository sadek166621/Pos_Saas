<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchProductPrice;
use App\Models\BranchProductStock;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function index(Request $request): View
    {
        $tenant = app('currentTenant');
        $branch = app('activeBranch');

        $query = Product::where('tenant_id', $tenant->id)->with(['category', 'brand', 'unit']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('barcode', $q)
                    ->orWhere('sku', 'LIKE', "%{$q}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->latest('id')->paginate(15);
        $categories = Category::where('tenant_id', $tenant->id)->get();
        $brands = Brand::where('tenant_id', $tenant->id)->get();

        return view('products.index', compact('products', 'categories', 'brands', 'branch'));
    }

    public function create(): View|RedirectResponse
    {
        $tenant = app('currentTenant');
        $quota = $this->subscriptionService->checkQuota($tenant, 'products');

        if (!$quota['allowed']) {
            return redirect()->route('products.index')->with('warning', "আপনার প্ল্যানের সর্বোচ্চ প্রোডাক্ট লিমিট ({$quota['max']}) পূর্ণ হয়ে গেছে। অনুগ্রহ করে প্ল্যান আপগ্রেড করুন।");
        }

        $categories = Category::where('tenant_id', $tenant->id)->get();
        $brands = Brand::where('tenant_id', $tenant->id)->get();
        $units = Unit::where('tenant_id', $tenant->id)->get();
        $branches = $tenant->branches()->where('status', 'active')->get();

        return view('products.create', compact('categories', 'brands', 'units', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');
        $quota = $this->subscriptionService->checkQuota($tenant, 'products');

        if (!$quota['allowed']) {
            return redirect()->route('products.index')->with('warning', 'Product limit reached. Please upgrade.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'vat_percent' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'opening_stocks' => 'nullable|array',
            'branch_prices' => 'nullable|array',
        ]);

        $barcode = $validated['barcode'] ?: (string) rand(100000000000, 999999999999);
        $sku = $validated['sku'] ?: 'SKU-' . strtoupper(Str::random(6));

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'unit_id' => $validated['unit_id'],
            'sku' => $sku,
            'barcode' => $barcode,
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
            'wholesale_price' => $validated['wholesale_price'] ?? null,
            'min_stock_alert' => $validated['min_stock_alert'] ?? 5,
            'vat_percent' => $validated['vat_percent'] ?? 0,
            'description' => $validated['description'] ?? null,
            'status' => true,
        ]);

        // Branch Opening Stock
        if (!empty($validated['opening_stocks'])) {
            foreach ($validated['opening_stocks'] as $bId => $qty) {
                if ($qty > 0) {
                    BranchProductStock::create([
                        'tenant_id' => $tenant->id,
                        'branch_id' => $bId,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'alert_quantity' => $product->min_stock_alert,
                    ]);
                }
            }
        }

        // Branch Price Overrides
        if (!empty($validated['branch_prices'])) {
            foreach ($validated['branch_prices'] as $bId => $price) {
                if ($price > 0 && $price != $product->selling_price) {
                    BranchProductPrice::create([
                        'tenant_id' => $tenant->id,
                        'branch_id' => $bId,
                        'product_id' => $product->id,
                        'selling_price' => $price,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'প্রোডাক্ট সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function edit(int $id): View
    {
        $tenant = app('currentTenant');
        $product = Product::where('tenant_id', $tenant->id)->with(['branchPrices', 'branchStocks'])->findOrFail($id);
        $categories = Category::where('tenant_id', $tenant->id)->get();
        $brands = Brand::where('tenant_id', $tenant->id)->get();
        $units = Unit::where('tenant_id', $tenant->id)->get();
        $branches = $tenant->branches()->where('status', 'active')->get();

        return view('products.edit', compact('product', 'categories', 'brands', 'units', 'branches'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $tenant = app('currentTenant');
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'sku' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'min_stock_alert' => 'nullable|integer|min:0',
            'vat_percent' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'branch_prices' => 'nullable|array',
        ]);

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'unit_id' => $validated['unit_id'],
            'sku' => $validated['sku'] ?: $product->sku,
            'barcode' => $validated['barcode'] ?: $product->barcode,
            'purchase_price' => $validated['purchase_price'],
            'selling_price' => $validated['selling_price'],
            'wholesale_price' => $validated['wholesale_price'] ?? null,
            'min_stock_alert' => $validated['min_stock_alert'] ?? 5,
            'vat_percent' => $validated['vat_percent'] ?? 0,
            'description' => $validated['description'] ?? null,
        ]);

        // Update branch price overrides
        if (isset($validated['branch_prices'])) {
            foreach ($validated['branch_prices'] as $bId => $price) {
                if ($price > 0) {
                    BranchProductPrice::updateOrCreate(
                        ['tenant_id' => $tenant->id, 'branch_id' => $bId, 'product_id' => $product->id],
                        ['selling_price' => $price]
                    );
                } else {
                    BranchProductPrice::where('tenant_id', $tenant->id)->where('branch_id', $bId)->where('product_id', $product->id)->delete();
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'প্রোডাক্ট সফলভাবে আপডেট করা হয়েছে!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $tenant = app('currentTenant');
        $product = Product::where('tenant_id', $tenant->id)->findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'প্রোডাক্ট ডিলিট করা হয়েছে!');
    }

    public function barcodes(Request $request): View
    {
        $tenant = app('currentTenant');
        $products = Product::where('tenant_id', $tenant->id)->where('status', true)->get();
        return view('products.barcodes', compact('products'));
    }

    public function branchPricing(): View
    {
        $tenant = app('currentTenant');
        $branches = $tenant->branches()->where('status', 'active')->get();
        $products = Product::where('tenant_id', $tenant->id)->with('branchPrices')->get();

        return view('products.branch_pricing', compact('branches', 'products', 'tenant'));
    }
}
