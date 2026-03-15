@extends('admin.layouts.app')

@section('page-title', $purchaseOrder->po_number ?: 'Purchase Order')
@section('page-subtitle', 'Supplier commitment details, receipt status and value tracking.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.procurement.purchase-orders.index') }}" class="hover:text-white">Purchase Orders</a>
            <span>/</span>
            <span class="text-white">{{ $purchaseOrder->po_number ?: 'Order' }}</span>
        </nav>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">{{ session('error') }}</div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Order Profile</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">{{ $purchaseOrder->po_number ?: 'Purchase Order' }}</h2>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200">{{ $statuses[$purchaseOrder->status] ?? ucfirst((string) $purchaseOrder->status) }}</span>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Supplier</p><p class="mt-2 font-medium text-white">{{ $purchaseOrder->supplier?->name ?: 'Not assigned' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Purchase Request</p><p class="mt-2 font-medium text-white">{{ $purchaseOrder->purchaseRequest?->pr_number ?: 'Direct order' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Order Date</p><p class="mt-2 font-medium text-white">{{ optional($purchaseOrder->order_date)->format('Y-m-d') ?: 'Not set' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Expected Delivery</p><p class="mt-2 font-medium text-white">{{ optional($purchaseOrder->expected_delivery_date)->format('Y-m-d') ?: 'Not set' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Subtotal</p><p class="mt-2 font-medium text-white">{{ number_format((float) $purchaseOrder->subtotal, 2) }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Total Amount</p><p class="mt-2 font-medium text-white">{{ number_format((float) $purchaseOrder->total_amount, 2) }}</p></div>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Shipping Address</p>
                    <p class="mt-2 text-slate-200">{{ $purchaseOrder->shipping_address ?: 'No shipping address recorded.' }}</p>
                </div>

                <div class="mt-6 overflow-x-auto rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Order Lines</p>
                    <table class="mt-4 min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase tracking-[0.25em] text-slate-500">
                            <tr>
                                <th class="px-3 py-2">Description</th>
                                <th class="px-3 py-2">Qty</th>
                                <th class="px-3 py-2">Unit Price</th>
                                <th class="px-3 py-2">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($purchaseOrder->items as $item)
                                <tr>
                                    <td class="px-3 py-3">{{ $item->description }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->quantity, 2) }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-slate-400">No order lines attached to this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Update Order</p>
                    <form method="POST" action="{{ route('admin.procurement.purchase-orders.update', $purchaseOrder) }}" class="mt-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <label class="block text-sm text-slate-300">PO Number<input name="po_number" value="{{ old('po_number', $purchaseOrder->po_number) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Order Date<input type="date" name="order_date" value="{{ old('order_date', optional($purchaseOrder->order_date)->format('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Supplier<select name="supplier_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required>@foreach (\App\Models\Inventory\Supplier::query()->orderBy('name')->get(['id', 'name']) as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Purchase Request<select name="purchase_request_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="">Direct order</option>@foreach (\App\Models\Procurement\PurchaseRequest::query()->orderByDesc('request_date')->get(['id', 'pr_number']) as $linkedRequest)<option value="{{ $linkedRequest->id }}" @selected(old('purchase_request_id', $purchaseOrder->purchase_request_id) == $linkedRequest->id)>{{ $linkedRequest->pr_number }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Expected Delivery<input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date', optional($purchaseOrder->expected_delivery_date)->format('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Status<select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">@foreach ($statuses as $statusValue => $statusLabel)<option value="{{ $statusValue }}" @selected(old('status', $purchaseOrder->status) === $statusValue)>{{ $statusLabel }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Shipping Address<textarea name="shipping_address" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('shipping_address', $purchaseOrder->shipping_address) }}</textarea></label>
                        <div class="grid gap-4 md:grid-cols-3">
                            <label class="block text-sm text-slate-300">Subtotal<input type="number" step="0.01" min="0" name="subtotal" value="{{ old('subtotal', $purchaseOrder->subtotal) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                            <label class="block text-sm text-slate-300">Tax Amount<input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', $purchaseOrder->tax_amount) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                            <label class="block text-sm text-slate-300">Total Amount<input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount', $purchaseOrder->total_amount) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        </div>
                        <label class="block text-sm text-slate-300">Notes<textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('notes', $purchaseOrder->notes) }}</textarea></label>
                        <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Update order</button>
                    </form>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Goods Receipts</p>
                    <div class="mt-6 space-y-4 text-sm text-slate-300">
                        @forelse ($purchaseOrder->receipts as $receipt)
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="font-medium text-white">{{ $receipt->receipt_number }}</p>
                                <p class="mt-2 text-slate-400">{{ optional($receipt->receipt_date)->format('Y-m-d') ?: 'No receipt date' }} · {{ ucfirst((string) $receipt->status) }}</p>
                            </div>
                        @empty
                            <p>No goods receipts have been recorded for this order yet.</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('admin.procurement.purchase-orders.destroy', $purchaseOrder) }}" class="mt-6">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-2xl border border-rose-400/30 bg-rose-400/10 px-5 py-3 text-sm font-medium text-rose-100 transition hover:bg-rose-400/20">Delete purchase order</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
