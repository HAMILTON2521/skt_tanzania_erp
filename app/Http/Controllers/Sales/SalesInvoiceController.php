<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Customer;
use App\Models\Sales\Quotation;
use App\Models\Sales\SalesInvoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalesInvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = SalesInvoice::query()
            ->with('customer:id,name,customer_code', 'quotation:id,quotation_number')
            ->latest('issue_date')
            ->latest('id')
            ->get();

        $customers = Customer::query()->orderBy('name')->get(['id', 'name', 'customer_code']);
        $quotations = Quotation::query()->orderBy('quotation_number')->get(['id', 'quotation_number']);

        return view('admin.sales.invoices', [
            'navigation' => config('admin.navigation', []),
            'invoices' => $invoices,
            'customers' => $customers,
            'quotations' => $quotations,
            'summary' => [
                'count' => $invoices->count(),
                'sent' => $invoices->where('status', 'sent')->count(),
                'paid' => $invoices->where('status', 'paid')->count(),
                'value' => $invoices->sum('total_amount'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_number' => ['required', 'string', 'max:50', 'unique:sales_invoices,invoice_number'],
            'customer_id' => ['required', 'exists:customers,id'],
            'quotation_id' => ['nullable', 'exists:quotations,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:draft,sent,paid,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        SalesInvoice::query()->create($validated);

        return redirect()->route('admin.sales.invoices.index')
            ->with('status', 'Sales invoice created successfully.');
    }
}
