<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance\BankAccount;
use App\Models\Finance\ChartOfAccount;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\TaxRate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function chartOfAccounts(): View
    {
        $accounts = ChartOfAccount::query()
            ->withCount('journalEntries')
            ->orderBy('code')
            ->orderBy('name')
            ->get();

        return view('admin.finance.chart-of-accounts', [
            'navigation' => config('admin.navigation', []),
            'accounts' => $accounts,
            'summary' => [
                'total_accounts' => $accounts->count(),
                'active_accounts' => $accounts->where('is_active', true)->count(),
                'posting_ready_accounts' => $accounts->whereIn('type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'])->count(),
            ],
        ]);
    }

    public function journalEntries(): View
    {
        $entries = JournalEntry::query()
            ->with('chartOfAccount:id,code,name')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->get();

        return view('admin.finance.journal-entries', [
            'navigation' => config('admin.navigation', []),
            'entries' => $entries,
            'summary' => [
                'total_entries' => $entries->count(),
                'posted_entries' => $entries->where('status', 'posted')->count(),
                'total_debit' => $entries->sum('debit'),
                'total_credit' => $entries->sum('credit'),
            ],
        ]);
    }

    public function reports(): View
    {
        $accounts = ChartOfAccount::query()->orderBy('type')->orderBy('code')->get();
        $entries = JournalEntry::query()
            ->with('chartOfAccount:id,code,name')
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $accountMix = $accounts
            ->groupBy(fn (ChartOfAccount $account) => $account->type ?: 'Unclassified')
            ->map(fn ($group, $type) => [
                'type' => $type,
                'total' => $group->count(),
                'active' => $group->where('is_active', true)->count(),
            ])
            ->values();

        return view('admin.finance.reports', [
            'navigation' => config('admin.navigation', []),
            'accountMix' => $accountMix,
            'recentEntries' => $entries,
            'summary' => [
                'accounts' => $accounts->count(),
                'bank_accounts' => BankAccount::query()->count(),
                'tax_rates' => TaxRate::query()->count(),
                'entries' => $entries->count(),
                'total_debit' => $entries->sum('debit'),
                'total_credit' => $entries->sum('credit'),
                'variance' => $entries->sum('debit') - $entries->sum('credit'),
            ],
        ]);
    }

    public function bankAccounts(): View
    {
        $accounts = BankAccount::query()->orderBy('bank_name')->orderBy('account_name')->get();

        return view('admin.finance.bank-accounts', [
            'navigation' => config('admin.navigation', []),
            'accounts' => $accounts,
        ]);
    }

    public function storeBankAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:255'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50', 'unique:bank_accounts,account_number'],
            'currency' => ['required', 'string', 'max:10'],
            'is_active' => ['required', 'boolean'],
        ]);

        BankAccount::query()->create($validated);

        return redirect()->route('admin.finance.bank-accounts.index')
            ->with('status', 'Bank account added successfully.');
    }

    public function taxRates(): View
    {
        $taxRates = TaxRate::query()->orderBy('name')->get();

        return view('admin.finance.tax-rates', [
            'navigation' => config('admin.navigation', []),
            'taxRates' => $taxRates,
        ]);
    }

    public function storeTaxRate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:30', 'unique:tax_rates,code'],
            'rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['required', 'boolean'],
        ]);

        TaxRate::query()->create($validated);

        return redirect()->route('admin.finance.tax-rates.index')
            ->with('status', 'Tax rate added successfully.');
    }
}
