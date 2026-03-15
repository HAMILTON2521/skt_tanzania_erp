<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Supplier;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = PurchaseOrder::query()
            ->with(['purchaseRequest:id,pr_number,department_id,requester_id', 'supplier:id,name', 'receipts'])
            ->withCount(['items', 'receipts']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search): void {
                $builder->where('po_number', 'like', "%{$search}%")
                    ->orWhere('shipping_address', 'like', "%{$search}%")
                    ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('purchaseRequest', fn ($requestQuery) => $requestQuery->where('pr_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->integer('supplier'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $purchaseOrders = $query
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $suppliers = Supplier::query()->orderBy('name')->get(['id', 'name']);
        $purchaseRequests = PurchaseRequest::query()->orderByDesc('request_date')->get(['id', 'pr_number']);
        $summaryOrders = PurchaseOrder::query()->get(['status', 'total_amount']);

        return view('admin.procurement.purchase-orders.index', [
            'navigation' => config('admin.navigation', []),
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers,
            'purchaseRequests' => $purchaseRequests,
            'statuses' => PurchaseOrder::getStatuses(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'supplier' => $request->string('supplier')->toString(),
                'status' => $request->string('status')->toString(),
            ],
            'summary' => [
                'count' => $summaryOrders->count(),
                'pending' => $summaryOrders->where('status', PurchaseOrder::STATUS_PENDING)->count(),
                'received' => $summaryOrders->where('status', PurchaseOrder::STATUS_RECEIVED)->count(),
                'value' => $summaryOrders->sum('total_amount'),
            ],
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.procurement.purchase-orders.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'po_number' => ['required', 'string', 'max:50', 'unique:purchase_orders,po_number'],
            'order_date' => ['required', 'date'],
            'purchase_request_id' => ['nullable', 'exists:purchase_requests,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'expected_delivery_date' => ['nullable', 'date'],
            'shipping_address' => ['nullable', 'string', 'max:2000'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:'.implode(',', array_keys(PurchaseOrder::getStatuses()))],
            'notes' => ['nullable', 'string', 'max:4000'],
            'items' => ['nullable', 'array'],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $itemPayload = collect($validated['items'] ?? [])
            ->filter(fn (array $item): bool => filled($item['description'] ?? null))
            ->map(function (array $item): array {
                $quantity = (float) ($item['quantity'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                return [
                    'description' => $item['description'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => round($quantity * $unitPrice, 2),
                    'notes' => $item['notes'] ?? null,
                ];
            })
            ->values();

        $payload = collect($validated)
            ->except(['items'])
            ->all();

        $payload['status'] ??= PurchaseOrder::STATUS_DRAFT;
        $payload['tax_amount'] = (float) ($payload['tax_amount'] ?? 0);
        $payload['subtotal'] = $itemPayload->isNotEmpty()
            ? $itemPayload->sum('total_amount')
            : (float) ($payload['subtotal'] ?? 0);
        $payload['total_amount'] = $payload['subtotal'] + $payload['tax_amount'];

        try {
            DB::transaction(function () use ($payload, $itemPayload): void {
                $purchaseOrder = PurchaseOrder::query()->create($payload);

                if ($itemPayload->isNotEmpty()) {
                    $purchaseOrder->items()->createMany($itemPayload->all());
                }

                if (function_exists('activity')) {
                    activity()
                        ->performedOn($purchaseOrder)
                        ->log('Created purchase order: '.$purchaseOrder->po_number);
                }
            });

            return redirect()->route('admin.procurement.purchase-orders.index')
                ->with('status', 'Purchase order created successfully.');
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Error creating purchase order: '.$exception->getMessage());
        }
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load([
            'purchaseRequest.department:id,name,code',
            'purchaseRequest.requester:id,name',
            'supplier:id,name,email,phone',
            'approver:id,name',
            'items',
            'receipts',
        ]);

        return view('admin.procurement.purchase-orders.show', [
            'navigation' => config('admin.navigation', []),
            'purchaseOrder' => $purchaseOrder,
            'statuses' => PurchaseOrder::getStatuses(),
        ]);
    }

    public function edit(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        return redirect()->route('admin.procurement.purchase-orders.show', $purchaseOrder);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $validated = $request->validate([
            'po_number' => ['required', 'string', 'max:50', 'unique:purchase_orders,po_number,'.$purchaseOrder->id],
            'order_date' => ['required', 'date'],
            'purchase_request_id' => ['nullable', 'exists:purchase_requests,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'expected_delivery_date' => ['nullable', 'date'],
            'shipping_address' => ['nullable', 'string', 'max:2000'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['required', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', array_keys(PurchaseOrder::getStatuses()))],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        if ($validated['status'] === PurchaseOrder::STATUS_APPROVED && ! $purchaseOrder->approved_by) {
            $validated['approved_by'] = $request->user()?->id;
            $validated['approved_at'] = now();
        }

        try {
            DB::transaction(fn () => $purchaseOrder->update($validated));

            if (function_exists('activity')) {
                activity()
                    ->performedOn($purchaseOrder)
                    ->log('Updated purchase order: '.$purchaseOrder->po_number);
            }

            return redirect()->route('admin.procurement.purchase-orders.show', $purchaseOrder)
                ->with('status', 'Purchase order updated successfully.');
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Error updating purchase order: '.$exception->getMessage());
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $orderNumber = $purchaseOrder->po_number;

            DB::transaction(fn () => $purchaseOrder->delete());

            if (function_exists('activity')) {
                activity()->log('Deleted purchase order: '.$orderNumber);
            }

            return redirect()->route('admin.procurement.purchase-orders.index')
                ->with('status', 'Purchase order deleted successfully.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Error deleting purchase order: '.$exception->getMessage());
        }
    }
}
