<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Customer;
use App\Models\Sales\Quotation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index(): View
    {
        $quotations = Quotation::query()
            ->with('customer:id,name,customer_code')
            ->latest('issue_date')
            ->latest('id')
            ->get();

        $customers = Customer::query()->orderBy('name')->get(['id', 'name', 'customer_code']);

        return view('admin.sales.quotations', [
            'navigation' => config('admin.navigation', []),
            'quotations' => $quotations,
            'customers' => $customers,
            'summary' => [
                'count' => $quotations->count(),
                'approved' => $quotations->where('status', 'approved')->count(),
                'draft' => $quotations->where('status', 'draft')->count(),
                'value' => $quotations->sum('amount'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quotation_number' => ['required', 'string', 'max:50', 'unique:quotations,quotation_number'],
            'customer_id' => ['required', 'exists:customers,id'],
            'issue_date' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:issue_date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:draft,sent,approved,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Quotation::query()->create($validated);

        return redirect()->route('admin.sales.quotations.index')
            ->with('status', 'Quotation created successfully.');
    }
}
