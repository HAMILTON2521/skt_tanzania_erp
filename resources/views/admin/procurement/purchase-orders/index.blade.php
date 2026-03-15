@extends('admin.layouts.app')

@section('page-title', 'Purchase Orders')
@section('page-subtitle', 'Issue supplier commitments, monitor receipt status and control procurement value.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>Operations</span>
            <span>/</span>
            <span class="text-white">Purchase Orders</span>
        </nav>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">{{ session('error') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Orders</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['count'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Pending</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['pending'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Received</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['received'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-fuchsia-300">Order Value</p><p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['value'], 2) }}</p></div>
        </div>

        <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Filter Orders</p>
            <form method="GET" action="{{ route('admin.procurement.purchase-orders.index') }}" class="mt-6 grid gap-4 md:grid-cols-4">
                <label class="block text-sm text-slate-300">Search
                    <input name="search" value="{{ $filters['search'] }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" placeholder="PO number, supplier or PR reference">
                </label>
                <label class="block text-sm text-slate-300">Supplier
                    <select name="supplier" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">
                        <option value="">All suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected($filters['supplier'] == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm text-slate-300">Status
                    <select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">
                        <option value="">All statuses</option>
                        @foreach ($statuses as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" @selected($filters['status'] === $statusValue)>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex items-end gap-3">
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Apply filters</button>
                    <a href="{{ route('admin.procurement.purchase-orders.index') }}" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-medium text-slate-200 transition hover:bg-white/10">Reset</a>
                </div>
            </form>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1fr_1.35fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">New Purchase Order</p>
                <form method="POST" action="{{ route('admin.procurement.purchase-orders.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm text-slate-300">PO Number<input name="po_number" value="{{ old('po_number') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Order Date<input type="date" name="order_date" value="{{ old('order_date', now()->format('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Purchase Request<select name="purchase_request_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="">Direct order</option>@foreach ($purchaseRequests as $purchaseRequest)<option value="{{ $purchaseRequest->id }}" @selected(old('purchase_request_id') == $purchaseRequest->id)>{{ $purchaseRequest->pr_number }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Supplier<select name="supplier_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required><option value="">Select supplier</option>@foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Expected Delivery<input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Status<select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">@foreach ($statuses as $statusValue => $statusLabel)<option value="{{ $statusValue }}" @selected(old('status', 'draft') === $statusValue)>{{ $statusLabel }}</option>@endforeach</select></label>
                    </div>
                    <label class="block text-sm text-slate-300">Shipping Address<textarea name="shipping_address" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('shipping_address') }}</textarea></label>
                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="block text-sm text-slate-300">Subtotal<input type="number" step="0.01" min="0" name="subtotal" value="{{ old('subtotal') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" placeholder="Optional if item lines are used"></label>
                        <label class="block text-sm text-slate-300">Tax Amount<input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', 0) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Notes<input name="notes" value="{{ old('notes') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Optional item lines</p>
                        <div class="mt-4 space-y-3">
                            @for ($index = 0; $index < 2; $index++)
                                <div class="grid gap-3 md:grid-cols-[1.4fr_0.6fr_0.7fr]">
                                    <input name="items[{{ $index }}][description]" value="{{ old('items.'.$index.'.description') }}" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" placeholder="Item description">
                                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]" value="{{ old('items.'.$index.'.quantity') }}" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" placeholder="Qty">
                                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" value="{{ old('items.'.$index.'.unit_price') }}" class="rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" placeholder="Unit price">
                                </div>
                            @endfor
                        </div>
                    </div>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save purchase order</button>
                </form>
            </section>

            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Order Register</p>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase tracking-[0.25em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Order</th>
                                <th class="px-4 py-3">Supplier</th>
                                <th class="px-4 py-3">Request</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($purchaseOrders as $purchaseOrder)
                                <tr>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('admin.procurement.purchase-orders.show', $purchaseOrder) }}" class="font-medium text-white hover:text-cyan-200">{{ $purchaseOrder->po_number ?: 'Draft order' }}</a>
                                        <p class="mt-1 text-xs text-slate-500">{{ optional($purchaseOrder->order_date)->format('Y-m-d') ?: 'No date' }} · {{ number_format((float) $purchaseOrder->total_amount, 2) }}</p>
                                    </td>
                                    <td class="px-4 py-4">{{ $purchaseOrder->supplier?->name ?: 'Unassigned' }}</td>
                                    <td class="px-4 py-4">{{ $purchaseOrder->purchaseRequest?->pr_number ?: 'Direct order' }}</td>
                                    <td class="px-4 py-4">{{ $statuses[$purchaseOrder->status] ?? ucfirst((string) $purchaseOrder->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-slate-400">No purchase orders have been issued yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">{{ $purchaseOrders->links() }}</div>
            </section>
        </div>
    </div>
@endsection
