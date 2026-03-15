<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Category;
use App\Models\Inventory\Product;
use App\Models\Inventory\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category:id,name,code', 'supplier:id,name,supplier_code')
            ->orderBy('name')
            ->get();

        return view('admin.inventory.products', [
            'navigation' => config('admin.navigation', []),
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'code']),
            'suppliers' => Supplier::query()->orderBy('name')->get(['id', 'name', 'supplier_code']),
            'summary' => [
                'count' => $products->count(),
                'low_stock' => $products->filter(fn (Product $product) => $product->stock_on_hand <= $product->reorder_level)->count(),
                'inventory_value' => $products->sum(fn (Product $product) => (float) $product->unit_price * (int) $product->stock_on_hand),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'stock_on_hand' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive,discontinued'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Product::query()->create($validated);

        return redirect()->route('admin.inventory.products.index')
            ->with('status', 'Product created successfully.');
    }
}
