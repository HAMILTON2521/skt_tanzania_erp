<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.inventory.categories', [
            'navigation' => config('admin.navigation', []),
            'categories' => $categories,
            'summary' => [
                'count' => $categories->count(),
                'active' => $categories->where('is_active', true)->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:categories,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        Category::query()->create($validated);

        return redirect()->route('admin.inventory.categories.index')
            ->with('status', 'Category created successfully.');
    }
}
