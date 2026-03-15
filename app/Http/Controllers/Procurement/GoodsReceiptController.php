<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\Procurement\GoodsReceipt;
use App\Models\Procurement\PurchaseOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsReceiptController extends Controller
{
    public function index(Request $request): View
    {
        $query = GoodsReceipt::query()
            ->with(['purchaseOrder:id,po_number,supplier_id,status,total_amount', 'purchaseOrder.supplier:id,name'])
            ->with('purchaseOrder.purchaseRequest:id,pr_number');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search): void {
                $builder->where('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas('purchaseOrder', fn ($orderQuery) => $orderQuery->where('po_number', 'like', "%{$search}%"));
            });
        }

        $receipts = $query->orderByDesc('receipt_date')->orderByDesc('id')->paginate(12)->withQueryString();
        $summaryReceipts = GoodsReceipt::query()->get(['status']);

        return view('admin.procurement.goods-receipts.index', [
            'navigation' => config('admin.navigation', []),
            'receipts' => $receipts,
            'purchaseOrders' => PurchaseOrder::query()
                ->with(['supplier:id,name', 'purchaseRequest:id,pr_number'])
                ->whereIn('status', [
                    PurchaseOrder::STATUS_APPROVED,
                    PurchaseOrder::STATUS_SENT,
                    PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
                ])
                ->orderByDesc('order_date')
                ->get(['id', 'po_number', 'supplier_id', 'purchase_request_id', 'status', 'total_amount']),
            'statuses' => $this->statuses(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
            ],
            'summary' => [
                'count' => $summaryReceipts->count(),
                'received' => $summaryReceipts->where('status', 'received')->count(),
                'pending' => $summaryReceipts->where('status', 'pending')->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_order_id' => ['required', 'exists:purchase_orders,id'],
            'receipt_number' => ['required', 'string', 'max:50', 'unique:goods_receipts,receipt_number'],
            'receipt_date' => ['required', 'date'],
            'status' => ['required', 'in:'.implode(',', array_keys($this->statuses()))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($validated, $request): void {
                $receipt = GoodsReceipt::query()->create([
                    ...$validated,
                    'received_by' => $request->user()?->id,
                ]);

                $purchaseOrder = PurchaseOrder::query()->findOrFail($validated['purchase_order_id']);
                $purchaseOrder->update([
                    'status' => $validated['status'] === 'received'
                        ? PurchaseOrder::STATUS_RECEIVED
                        : PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
                ]);

                if (function_exists('activity')) {
                    activity()->performedOn($receipt)->log('Created goods receipt: '.$receipt->receipt_number);
                }
            });

            return redirect()->route('admin.procurement.goods-receipts.index')
                ->with('status', 'Goods receipt created successfully.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'Error creating goods receipt: '.$exception->getMessage());
        }
    }

    public function show(GoodsReceipt $goodsReceipt): View
    {
        $goodsReceipt->load(['purchaseOrder.supplier:id,name,email,phone', 'purchaseOrder.purchaseRequest:id,pr_number,description']);

        return view('admin.procurement.goods-receipts.show', [
            'navigation' => config('admin.navigation', []),
            'goodsReceipt' => $goodsReceipt,
        ]);
    }

    private function statuses(): array
    {
        return [
            'pending' => 'Pending',
            'partial' => 'Partial',
            'received' => 'Received',
        ];
    }
}
