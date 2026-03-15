<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Receipt;
use App\Models\Sales\SalesInvoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(): View
    {
        $receipts = Receipt::query()
            ->with('salesInvoice:id,invoice_number')
            ->latest('receipt_date')
            ->latest('id')
            ->get();

        $salesInvoices = SalesInvoice::query()->orderBy('invoice_number')->get(['id', 'invoice_number', 'total_amount']);

        return view('admin.sales.receipts', [
            'navigation' => config('admin.navigation', []),
            'receipts' => $receipts,
            'salesInvoices' => $salesInvoices,
            'summary' => [
                'count' => $receipts->count(),
                'total' => $receipts->sum('amount'),
                'received' => $receipts->where('status', 'received')->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'receipt_number' => ['required', 'string', 'max:50', 'unique:receipts,receipt_number'],
            'sales_invoice_id' => ['required', 'exists:sales_invoices,id'],
            'receipt_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50'],
            'status' => ['required', 'in:received,pending,reversed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Receipt::query()->create($validated);

        return redirect()->route('admin.sales.receipts.index')
            ->with('status', 'Receipt recorded successfully.');
    }
}
