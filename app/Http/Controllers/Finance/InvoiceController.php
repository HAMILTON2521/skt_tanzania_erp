<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Invoice;
use App\Models\Finance\TaxRate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = Invoice::query()
            ->with('taxRate:id,name,rate,code')
            ->withSum('payments', 'amount')
            ->latest('issue_date')
            ->latest('id')
            ->get();

        $taxRates = TaxRate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'rate']);

        return view('admin.finance.invoices', [
            'navigation' => config('admin.navigation', []),
            'invoices' => $invoices,
            'taxRates' => $taxRates,
            'summary' => [
                'count' => $invoices->count(),
                'sent' => $invoices->where('status', 'sent')->count(),
                'overdue' => $invoices->where('status', 'overdue')->count(),
                'value' => $invoices->sum('total_amount'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_number' => ['required', 'string', 'max:50', 'unique:invoices,invoice_number'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax_rate_id' => ['nullable', 'exists:tax_rates,id'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,sent,paid,overdue,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! empty($validated['tax_rate_id'])) {
            $taxRate = TaxRate::query()->find($validated['tax_rate_id']);
            $validated['tax_amount'] = round(((float) $validated['subtotal'] * (float) $taxRate->rate) / 100, 2);
        } else {
            $validated['tax_amount'] = (float) ($validated['tax_amount'] ?? 0);
        }

        $validated['total_amount'] = (float) $validated['subtotal'] + (float) $validated['tax_amount'];

        Invoice::query()->create($validated);

        return redirect()
            ->route('admin.finance.invoices.index')
            ->with('status', 'Invoice created successfully.');
    }
}
