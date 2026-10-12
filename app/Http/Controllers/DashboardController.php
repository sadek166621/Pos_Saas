<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $tenant = app('currentTenant');
        $activeBranch = app('activeBranch');

        // Optional query filter: ?branch=all or ?branch=1
        $filterBranchId = $request->query('branch', session('active_branch_id'));
        $isAllBranches = ($filterBranchId === 'all');

        $salesQuery = Sale::where('tenant_id', $tenant->id)->where('status', 'completed');
        $expenseQuery = Expense::where('tenant_id', $tenant->id);

        if (!$isAllBranches && $filterBranchId) {
            $salesQuery->where('branch_id', $filterBranchId);
            $expenseQuery->where('branch_id', $filterBranchId);
        }

        // Today metrics
        $today = Carbon::today('Asia/Dhaka');
        $todaySales = (clone $salesQuery)->whereDate('sale_date', $today)->sum('total');
        $todayCashCollected = (clone $salesQuery)->whereDate('sale_date', $today)->sum('paid');
        $todayExpenses = (clone $expenseQuery)->whereDate('date', $today)->sum('amount');

        // Calculate Today's Profit (Sale subtotal - item cost)
        $todaySaleIds = (clone $salesQuery)->whereDate('sale_date', $today)->pluck('id');
        $todayCost = SaleItem::whereIn('sale_id', $todaySaleIds)
            ->selectRaw('SUM(quantity * cost_price) as total_cost')
            ->value('total_cost') ?? 0;
        $todayProfit = max(0, $todaySales - $todayCost - $todayExpenses);

        // CRM Dues
        $totalCustomerDue = Customer::where('tenant_id', $tenant->id)->sum('current_due');
        $totalSupplierDue = Supplier::where('tenant_id', $tenant->id)->sum('current_due');

        // Products & Stock
        $totalProducts = Product::where('tenant_id', $tenant->id)->count();
        $lowStockCount = Product::where('tenant_id', $tenant->id)
            ->whereHas('branchStocks', function ($q) use ($filterBranchId, $isAllBranches) {
                if (!$isAllBranches && $filterBranchId) {
                    $q->where('branch_id', $filterBranchId);
                }
                $q->whereColumn('quantity', '<=', 'alert_quantity');
            })->count();

        // Recent 5 Sales
        $recentSales = (clone $salesQuery)->with(['customer', 'branch', 'cashier'])->latest('id')->take(6)->get();

        // 7-day Sales Chart
        $chartDates = [];
        $chartSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today('Asia/Dhaka')->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $dayTotal = (clone $salesQuery)->whereDate('sale_date', $dateStr)->sum('total');

            $chartDates[] = $date->format('d M');
            $chartSales[] = (float) $dayTotal;
        }

        // Top Selling Products
        $topProducts = SaleItem::whereHas('sale', function ($q) use ($tenant, $filterBranchId, $isAllBranches) {
            $q->where('tenant_id', $tenant->id);
            if (!$isAllBranches && $filterBranchId) {
                $q->where('branch_id', $filterBranchId);
            }
        })
        ->selectRaw('product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_amount')
        ->groupBy('product_name')
        ->orderByDesc('total_qty')
        ->take(5)
        ->get();

        return view('dashboard.index', compact(
            'tenant',
            'activeBranch',
            'filterBranchId',
            'isAllBranches',
            'todaySales',
            'todayProfit',
            'todayExpenses',
            'todayCashCollected',
            'totalCustomerDue',
            'totalSupplierDue',
            'totalProducts',
            'lowStockCount',
            'recentSales',
            'chartDates',
            'chartSales',
            'topProducts'
        ));
    }

    public function switchBranch(Request $request): RedirectResponse
    {
        $branchId = $request->input('branch_id');
        $tenant = app('currentTenant');

        if ($branchId === 'all') {
            session(['active_branch_id' => 'all']);
            return back()->with('info', 'Active view switched to: All Branches (সকল শাখা)');
        }

        $branch = $tenant->branches()->findOrFail($branchId);
        session(['active_branch_id' => $branch->id]);

        return back()->with('info', "Active branch switched to: {$branch->name}");
    }
}
