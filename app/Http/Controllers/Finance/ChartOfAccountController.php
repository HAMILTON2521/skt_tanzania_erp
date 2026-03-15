<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\ChartOfAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    public function index(Request $request): View
    {
        $query = ChartOfAccount::query()
            ->with('parent:id,code,name')
            ->withCount('journalEntries');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $accountTypes = ChartOfAccount::getTypes();
        $accounts = $query
            ->orderBy('code')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $summaryAccounts = ChartOfAccount::query()->get(['type', 'is_active']);

        return view('admin.finance.chart-of-accounts', [
            'navigation' => config('admin.navigation', []),
            'accounts' => $accounts,
            'accountTypes' => $accountTypes,
            'filters' => [
                'type' => $request->string('type')->toString(),
                'search' => $request->string('search')->toString(),
            ],
            'summary' => [
                'total_accounts' => $summaryAccounts->count(),
                'active_accounts' => $summaryAccounts->where('is_active', true)->count(),
                'posting_ready_accounts' => $summaryAccounts->whereIn('type', array_keys($accountTypes))->count(),
            ],
        ]);
    }

    public function create(): View
    {
        $parentAccounts = ChartOfAccount::query()
            ->whereNull('parent_id')
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return $this->index(request())->with([
            'accountTypes' => ChartOfAccount::getTypes(),
            'parentAccounts' => $parentAccounts,
            'pageMode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:chart_of_accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', array_keys(ChartOfAccount::getTypes()))],
            'category' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        try {
            $account = DB::transaction(fn () => ChartOfAccount::query()->create($validated));

            if (function_exists('activity')) {
                activity()
                    ->performedOn($account)
                    ->log('Created chart of account: '.$account->name);
            }

            return redirect()->route('admin.finance.chart-of-accounts')
                ->with('status', 'Account created successfully.');
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Error creating account: '.$exception->getMessage());
        }
    }

    public function show(ChartOfAccount $chartOfAccount): View
    {
        return $this->index(request())->with([
            'selectedAccount' => $chartOfAccount,
            'pageMode' => 'show',
        ]);
    }

    public function edit(ChartOfAccount $chartOfAccount): View
    {
        $parentAccounts = ChartOfAccount::query()
            ->whereNull('parent_id')
            ->whereKeyNot($chartOfAccount->id)
            ->orderBy('code')
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return $this->index(request())->with([
            'accountTypes' => ChartOfAccount::getTypes(),
            'parentAccounts' => $parentAccounts,
            'editingAccount' => $chartOfAccount,
            'pageMode' => 'edit',
        ]);
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:chart_of_accounts,code,'.$chartOfAccount->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', array_keys(ChartOfAccount::getTypes()))],
            'category' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id', 'not_in:'.$chartOfAccount->id],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        try {
            DB::transaction(fn () => $chartOfAccount->update($validated));

            if (function_exists('activity')) {
                activity()
                    ->performedOn($chartOfAccount)
                    ->log('Updated chart of account: '.$chartOfAccount->name);
            }

            return redirect()->route('admin.finance.chart-of-accounts')
                ->with('status', 'Account updated successfully.');
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Error updating account: '.$exception->getMessage());
        }
    }

    public function destroy(ChartOfAccount $chartOfAccount): RedirectResponse
    {
        if ($chartOfAccount->journalEntries()->exists()) {
            return back()->with('error', 'Cannot delete account with journal entries.');
        }

        try {
            $accountName = $chartOfAccount->name;

            DB::transaction(fn () => $chartOfAccount->delete());

            if (function_exists('activity')) {
                activity()->log('Deleted chart of account: '.$accountName);
            }

            return redirect()->route('admin.finance.chart-of-accounts')
                ->with('status', 'Account deleted successfully.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Error deleting account: '.$exception->getMessage());
        }
    }
}
