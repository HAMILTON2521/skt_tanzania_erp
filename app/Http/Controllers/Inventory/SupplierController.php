<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('admin.inventory.suppliers', [
            'navigation' => config('admin.navigation', []),
            'suppliers' => $suppliers,
            'summary' => [
                'count' => $suppliers->count(),
                'active' => $suppliers->where('status', 'active')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_code' => ['required', 'string', 'max:50', 'unique:suppliers,supplier_code'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Supplier::query()->create($validated);

        return redirect()->route('admin.inventory.suppliers.index')
            ->with('status', 'Supplier created successfully.');
    }
}
