<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()->latest('id')->get();

        return view('admin.sales.customers', [
            'navigation' => config('admin.navigation', []),
            'customers' => $customers,
            'summary' => [
                'count' => $customers->count(),
                'active' => $customers->where('status', 'active')->count(),
                'inactive' => $customers->where('status', 'inactive')->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_code' => ['required', 'string', 'max:50', 'unique:customers,customer_code'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Customer::query()->create($validated);

        return redirect()->route('admin.sales.customers.index')
            ->with('status', 'Customer added successfully.');
    }
}
