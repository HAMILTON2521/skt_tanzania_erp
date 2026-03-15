<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Customer;
use App\Models\Sales\Quotation;
use App\Models\Sales\SalesOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index(): View
    {
        $orders = SalesOrder::query()
            ->with('customer:id,name,customer_code', 'quotation:id,quotation_number')
            ->latest('order_date')
            ->latest('id')
            ->get();

        $customers = Customer::query()->orderBy('name')->get(['id', 'name', 'customer_code']);
        $quotations = Quotation::query()->orderBy('quotation_number')->get(['id', 'quotation_number']);

        return view('admin.sales.orders', [
            'navigation' => config('admin.navigation', []),
            'orders' => $orders,
            'customers' => $customers,
            'quotations' => $quotations,
            'summary' => [
                'count' => $orders->count(),
                'confirmed' => $orders->where('status', 'confirmed')->count(),
                'processing' => $orders->where('status', 'processing')->count(),
                'value' => $orders->sum('total_amount'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:50', 'unique:sales_orders,order_number'],
            'customer_id' => ['required', 'exists:customers,id'],
            'quotation_id' => ['nullable', 'exists:quotations,id'],
            'order_date' => ['required', 'date'],
            'expected_delivery_date' => ['required', 'date', 'after_or_equal:order_date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:draft,confirmed,processing,fulfilled,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        SalesOrder::query()->create($validated);

        return redirect()->route('admin.sales.orders.index')
            ->with('status', 'Sales order created successfully.');
    }
}
