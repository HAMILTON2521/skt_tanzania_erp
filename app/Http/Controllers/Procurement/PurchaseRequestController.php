<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\HR\Department;
use App\Models\Procurement\PurchaseRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = PurchaseRequest::query()
            ->with(['requester:id,name', 'department:id,name,code', 'purchaseOrder:id,purchase_request_id,po_number,status'])
            ->withCount('items');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($builder) use ($search): void {
                $builder->where('pr_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('requester', fn ($requesterQuery) => $requesterQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('department', fn ($departmentQuery) => $departmentQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->integer('department'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        $purchaseRequests = $query
            ->orderByDesc('request_date')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $departments = Department::query()->orderBy('name')->get(['id', 'name', 'code']);
        $summaryRequests = PurchaseRequest::query()->get(['status', 'total_amount']);

        return view('admin.procurement.purchase-requests.index', [
            'navigation' => config('admin.navigation', []),
            'purchaseRequests' => $purchaseRequests,
            'departments' => $departments,
            'statuses' => PurchaseRequest::getStatuses(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'department' => $request->string('department')->toString(),
                'status' => $request->string('status')->toString(),
            ],
            'summary' => [
                'count' => $summaryRequests->count(),
                'pending' => $summaryRequests->where('status', PurchaseRequest::STATUS_PENDING)->count(),
                'approved' => $summaryRequests->where('status', PurchaseRequest::STATUS_APPROVED)->count(),
                'value' => $summaryRequests->sum('total_amount'),
            ],
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('admin.procurement.purchase-requests.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pr_number' => ['required', 'string', 'max:50', 'unique:purchase_requests,pr_number'],
            'request_date' => ['required', 'date'],
            'department_id' => ['required', 'exists:departments,id'],
            'description' => ['required', 'string', 'max:2000'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:'.implode(',', array_keys(PurchaseRequest::getStatuses()))],
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

        $payload['requester_id'] = $request->user()?->id;
        $payload['status'] ??= PurchaseRequest::STATUS_DRAFT;
        $payload['total_amount'] = $itemPayload->isNotEmpty()
            ? $itemPayload->sum('total_amount')
            : (float) ($payload['total_amount'] ?? 0);

        try {
            DB::transaction(function () use ($payload, $itemPayload): void {
                $purchaseRequest = PurchaseRequest::query()->create($payload);

                if ($itemPayload->isNotEmpty()) {
                    $purchaseRequest->items()->createMany($itemPayload->all());
                }

                if (function_exists('activity')) {
                    activity()
                        ->performedOn($purchaseRequest)
                        ->log('Created purchase request: '.$purchaseRequest->pr_number);
                }
            });

            return redirect()->route('admin.procurement.purchase-requests.index')
                ->with('status', 'Purchase request created successfully.');
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Error creating purchase request: '.$exception->getMessage());
        }
    }

    public function show(PurchaseRequest $purchaseRequest): View
    {
        $purchaseRequest->load([
            'requester:id,name,email',
            'department:id,name,code',
            'approver:id,name',
            'items',
            'purchaseOrder.supplier:id,name',
        ]);

        return view('admin.procurement.purchase-requests.show', [
            'navigation' => config('admin.navigation', []),
            'purchaseRequest' => $purchaseRequest,
            'statuses' => PurchaseRequest::getStatuses(),
        ]);
    }

    public function edit(PurchaseRequest $purchaseRequest): RedirectResponse
    {
        return redirect()->route('admin.procurement.purchase-requests.show', $purchaseRequest);
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest): RedirectResponse
    {
        $validated = $request->validate([
            'pr_number' => ['required', 'string', 'max:50', 'unique:purchase_requests,pr_number,'.$purchaseRequest->id],
            'request_date' => ['required', 'date'],
            'department_id' => ['required', 'exists:departments,id'],
            'description' => ['required', 'string', 'max:2000'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:'.implode(',', array_keys(PurchaseRequest::getStatuses()))],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        if ($validated['status'] === PurchaseRequest::STATUS_APPROVED && ! $purchaseRequest->approved_by) {
            $validated['approved_by'] = $request->user()?->id;
            $validated['approved_at'] = now();
        }

        try {
            DB::transaction(fn () => $purchaseRequest->update($validated));

            if (function_exists('activity')) {
                activity()
                    ->performedOn($purchaseRequest)
                    ->log('Updated purchase request: '.$purchaseRequest->pr_number);
            }

            return redirect()->route('admin.procurement.purchase-requests.show', $purchaseRequest)
                ->with('status', 'Purchase request updated successfully.');
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Error updating purchase request: '.$exception->getMessage());
        }
    }

    public function destroy(PurchaseRequest $purchaseRequest): RedirectResponse
    {
        try {
            $requestNumber = $purchaseRequest->pr_number;

            DB::transaction(fn () => $purchaseRequest->delete());

            if (function_exists('activity')) {
                activity()->log('Deleted purchase request: '.$requestNumber);
            }

            return redirect()->route('admin.procurement.purchase-requests.index')
                ->with('status', 'Purchase request deleted successfully.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Error deleting purchase request: '.$exception->getMessage());
        }
    }
}
