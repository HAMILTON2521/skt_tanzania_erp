<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Expense;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::query()
            ->latest('expense_date')
            ->latest('id')
            ->get();

        return view('admin.finance.expenses', [
            'navigation' => config('admin.navigation', []),
            'expenses' => $expenses,
            'summary' => [
                'count' => $expenses->count(),
                'approved' => $expenses->where('status', 'approved')->count(),
                'pending' => $expenses->where('status', 'pending')->count(),
                'total' => $expenses->sum('amount'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_number' => ['required', 'string', 'max:50', 'unique:expenses,expense_number'],
            'expense_date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:100'],
            'vendor_name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:pending,approved,paid,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Expense::query()->create($validated);

        return redirect()
            ->route('admin.finance.expenses.index')
            ->with('status', 'Expense recorded successfully.');
    }
}
