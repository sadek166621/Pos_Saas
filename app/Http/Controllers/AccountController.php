<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $tenant = app('currentTenant');
        $accounts = Account::where('tenant_id', $tenant->id)->with('transactions')->get();
        $totalBalance = $accounts->sum('balance');

        return view('accounting.accounts', compact('accounts', 'totalBalance'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,mobile_banking',
            'account_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $account = Account::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'account_number' => $validated['account_number'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'balance' => $validated['opening_balance'] ?? 0,
        ]);

        return back()->with('success', 'অ্যাকাউন্ট সফলভাবে তৈরি হয়েছে!');
    }

    public function profitLoss(Request $request): View
    {
        $tenant = app('currentTenant');
        $activeBranch = app('activeBranch');

        $startDate = $request->query('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::now()->endOfMonth()->toDateString());

        $salesQuery = Sale::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $expenseQuery = Expense::where('tenant_id', $tenant->id)
            ->whereBetween('date', [$startDate, $endDate]);

        $totalSales = (clone $salesQuery)->sum('total');
        $saleIds = (clone $salesQuery)->pluck('id');

        $totalCogs = SaleItem::whereIn('sale_id', $saleIds)
            ->selectRaw('SUM(quantity * cost_price) as cogs')
            ->value('cogs') ?? 0;

        $grossProfit = max(0, $totalSales - $totalCogs);
        $totalExpenses = (clone $expenseQuery)->sum('amount');
        $netProfit = $grossProfit - $totalExpenses;

        $expenseBreakdown = Expense::where('tenant_id', $tenant->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as cat_total')
            ->groupBy('category_id')
            ->get();

        return view('accounting.profit_loss', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalCogs',
            'grossProfit',
            'totalExpenses',
            'netProfit',
            'expenseBreakdown'
        ));
    }
}
