<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Product;
use App\Models\Inventory\StockMovement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(): View
    {
        $movements = StockMovement::query()
            ->with('product:id,sku,name')
            ->latest('id')
            ->get();

        $products = Product::query()->orderBy('name')->get(['id', 'sku', 'name', 'stock_on_hand']);

        return view('admin.inventory.stock', [
            'navigation' => config('admin.navigation', []),
            'movements' => $movements,
            'products' => $products,
            'summary' => [
                'count' => $movements->count(),
                'incoming' => $movements->where('movement_type', 'in')->sum('quantity'),
                'outgoing' => $movements->where('movement_type', 'out')->sum('quantity'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'movement_type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:posted,pending,void'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $movement = StockMovement::query()->create($validated);

        $product = Product::query()->findOrFail($validated['product_id']);

        if ($validated['movement_type'] === 'in') {
            $product->increment('stock_on_hand', $validated['quantity']);
        } elseif ($validated['movement_type'] === 'out') {
            $product->decrement('stock_on_hand', $validated['quantity']);
        }

        if ($validated['movement_type'] === 'adjustment') {
            $product->increment('stock_on_hand', $validated['quantity']);
        }

        return redirect()->route('admin.inventory.stock.index')
            ->with('status', 'Stock movement recorded successfully.');
    }
}
