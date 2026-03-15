@extends('admin.layouts.app')

@section('page-title', $purchaseRequest->pr_number ?: 'Purchase Request')
@section('page-subtitle', 'Request details, approval state and sourcing readiness.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.procurement.purchase-requests.index') }}" class="hover:text-white">Purchase Requests</a>
            <span>/</span>
            <span class="text-white">{{ $purchaseRequest->pr_number ?: 'Request' }}</span>
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
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Request Profile</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">{{ $purchaseRequest->pr_number ?: 'Purchase Request' }}</h2>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200">{{ $statuses[$purchaseRequest->status] ?? ucfirst((string) $purchaseRequest->status) }}</span>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Department</p><p class="mt-2 font-medium text-white">{{ $purchaseRequest->department?->name ?: 'Unassigned' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Requester</p><p class="mt-2 font-medium text-white">{{ $purchaseRequest->requester?->name ?: 'System' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Request Date</p><p class="mt-2 font-medium text-white">{{ optional($purchaseRequest->request_date)->format('Y-m-d') ?: 'Not set' }}</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">Total Amount</p><p class="mt-2 font-medium text-white">{{ number_format((float) $purchaseRequest->total_amount, 2) }}</p></div>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Description</p>
                    <p class="mt-2 text-slate-200">{{ $purchaseRequest->description ?: 'No description provided.' }}</p>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Notes</p>
                    <p class="mt-2 text-slate-200">{{ $purchaseRequest->notes ?: 'No notes recorded.' }}</p>
                </div>

                <div class="mt-6 overflow-x-auto rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Item Lines</p>
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
                            @forelse ($purchaseRequest->items as $item)
                                <tr>
                                    <td class="px-3 py-3">{{ $item->description }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->quantity, 2) }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-3 py-3">{{ number_format((float) $item->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-slate-400">No item lines attached to this request.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Update Request</p>
                    <form method="POST" action="{{ route('admin.procurement.purchase-requests.update', $purchaseRequest) }}" class="mt-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <label class="block text-sm text-slate-300">PR Number<input name="pr_number" value="{{ old('pr_number', $purchaseRequest->pr_number) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Request Date<input type="date" name="request_date" value="{{ old('request_date', optional($purchaseRequest->request_date)->format('Y-m-d')) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Department<select name="department_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required>@foreach (\App\Models\HR\Department::query()->orderBy('name')->get(['id', 'name']) as $department)<option value="{{ $department->id }}" @selected(old('department_id', $purchaseRequest->department_id) == $department->id)>{{ $department->name }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Status<select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">@foreach ($statuses as $statusValue => $statusLabel)<option value="{{ $statusValue }}" @selected(old('status', $purchaseRequest->status) === $statusValue)>{{ $statusLabel }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Total Amount<input type="number" step="0.01" min="0" name="total_amount" value="{{ old('total_amount', $purchaseRequest->total_amount) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Description<textarea name="description" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required>{{ old('description', $purchaseRequest->description) }}</textarea></label>
                        <label class="block text-sm text-slate-300">Notes<textarea name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('notes', $purchaseRequest->notes) }}</textarea></label>
                        <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Update request</button>
                    </form>
                </div>

                <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Order Linkage</p>
                    <div class="mt-6 text-sm text-slate-300">
                        @if ($purchaseRequest->purchaseOrder)
                            <p class="text-white">Linked Purchase Order</p>
                            <a href="{{ route('admin.procurement.purchase-orders.show', $purchaseRequest->purchaseOrder) }}" class="mt-2 inline-block text-cyan-200 hover:text-cyan-100">{{ $purchaseRequest->purchaseOrder->po_number ?: 'View purchase order' }}</a>
                            <p class="mt-2 text-slate-400">Supplier: {{ $purchaseRequest->purchaseOrder->supplier?->name ?: 'Not assigned' }}</p>
                        @else
                            <p>No purchase order has been issued from this request yet.</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.procurement.purchase-requests.destroy', $purchaseRequest) }}" class="mt-6">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-2xl border border-rose-400/30 bg-rose-400/10 px-5 py-3 text-sm font-medium text-rose-100 transition hover:bg-rose-400/20">Delete purchase request</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
