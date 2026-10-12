<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashShift;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $tenant = app('currentTenant');
        $activeBranch = app('activeBranch');

        $expenses = Expense::where('tenant_id', $tenant->id)
            ->with(['category', 'branch'])
            ->latest('date')
            ->paginate(15);

        $categories = ExpenseCategory::where('tenant_id', $tenant->id)->get();
        $branches = $tenant->branches()->where('status', 'active')->get();
        $totalExpense = Expense::where('tenant_id', $tenant->id)->sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'branches', 'totalExpense'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');
        $activeBranch = app('activeBranch');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:expense_categories,id',
            'branch_id' => 'nullable|exists:branches,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $branchId = $validated['branch_id'] ?? $activeBranch->id;
        $shift = CashShift::where('tenant_id', $tenant->id)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        $expenseNo = 'EXP-' . date('Ymd') . '-' . rand(100, 999);

        $expense = Expense::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branchId,
            'category_id' => $validated['category_id'],
            'shift_id' => $shift?->id,
            'expense_no' => $expenseNo,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        if ($shift && $validated['payment_method'] === 'cash') {
            $shift->increment('expense_total', (float) $validated['amount']);
        }

        return back()->with('success', 'খরচ সফলভাবে যুক্ত করা হয়েছে!');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'খরচের ক্যাটাগরি তৈরি হয়েছে!');
    }
}
