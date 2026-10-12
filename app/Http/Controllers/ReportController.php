<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $tenant = app('currentTenant');
        $branches = $tenant->branches()->where('status', 'active')->get();

        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());
        $branchId = $request->query('branch_id');

        $query = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['branch', 'customer', 'cashier']);

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        $sales = $query->latest('id')->paginate(20);
        $totalSales = (clone $query)->sum('total');
        $totalPaid = (clone $query)->sum('paid');
        $totalDue = (clone $query)->sum('due');

        return view('reports.sales', compact(
            'sales',
            'branches',
            'startDate',
            'endDate',
            'branchId',
            'totalSales',
            'totalPaid',
            'totalDue'
        ));
    }

    public function inventory(): View
    {
        $tenant = app('currentTenant');
        $branches = $tenant->branches()->where('status', 'active')->get();

        $stocks = BranchProductStock::where('tenant_id', $tenant->id)
            ->with(['product.unit', 'branch'])
            ->get();

        $totalValuationCost = $stocks->sum(function ($s) {
            return $s->quantity * ($s->product->purchase_price ?? 0);
        });

        $totalValuationRetail = $stocks->sum(function ($s) {
            return $s->quantity * ($s->product->selling_price ?? 0);
        });

        $potentialProfit = max(0, $totalValuationRetail - $totalValuationCost);

        return view('reports.inventory', compact(
            'stocks',
            'branches',
            'totalValuationCost',
            'totalValuationRetail',
            'potentialProfit'
        ));
    }

    public function customerDues(): View
    {
        $tenant = app('currentTenant');
        $customers = Customer::where('tenant_id', $tenant->id)
            ->where('current_due', '>', 0)
            ->orderByDesc('current_due')
            ->paginate(25);

        $totalCustomerDue = Customer::where('tenant_id', $tenant->id)->sum('current_due');

        return view('reports.customer_dues', compact('customers', 'totalCustomerDue'));
    }
}
