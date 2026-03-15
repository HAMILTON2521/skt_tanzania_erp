@extends('admin.layouts.app')

@section('page-title', 'Finance Invoices')
@section('page-subtitle', 'Create invoices, monitor due dates and track outstanding exposure.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>People &amp; Finance</span>
            <span>/</span>
            <span class="text-white">Finance Invoices</span>
        </nav>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Invoices</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['count'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Sent</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['sent'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Overdue</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['overdue'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-fuchsia-300">Value</p><p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['value'], 2) }}</p></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.05fr_1.35fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Create Invoice</p>
                <form method="POST" action="{{ route('admin.finance.invoices.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm text-slate-300">Invoice Number<input name="invoice_number" value="{{ old('invoice_number') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Customer Name<input name="customer_name" value="{{ old('customer_name') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Customer Email<input type="email" name="customer_email" value="{{ old('customer_email') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                        <label class="block text-sm text-slate-300">Status<select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="draft">Draft</option><option value="sent">Sent</option><option value="paid">Paid</option><option value="overdue">Overdue</option><option value="cancelled">Cancelled</option></select></label>
                        <label class="block text-sm text-slate-300">Issue Date<input type="date" name="issue_date" value="{{ old('issue_date') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Due Date<input type="date" name="due_date" value="{{ old('due_date') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Subtotal<input type="number" step="0.01" min="0" name="subtotal" value="{{ old('subtotal') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Tax Rate<select name="tax_rate_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="">Manual / none</option>@foreach ($taxRates as $taxRate)<option value="{{ $taxRate->id }}" @selected(old('tax_rate_id') == $taxRate->id)>{{ $taxRate->code }} | {{ $taxRate->name }} | {{ number_format((float) $taxRate->rate, 2) }}%</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Manual Tax Amount<input type="number" step="0.01" min="0" name="tax_amount" value="{{ old('tax_amount', 0) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                    </div>
                    <label class="block text-sm text-slate-300">Notes<textarea name="notes" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('notes') }}</textarea></label>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save invoice</button>
                </form>
            </section>

            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Invoice Register</p>
                        <h2 class="mt-2 text-2xl font-semibold text-white">Recent finance invoices</h2>
                    </div>
                    <a href="{{ route('admin.finance.payments.index') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-slate-100 transition hover:bg-white/10">Open payments</a>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Invoice</th><th class="px-4 py-3">Dates</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Outstanding</th></tr></thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td class="px-4 py-4"><p class="font-medium text-white">{{ $invoice->invoice_number }}</p><p class="mt-1 text-xs text-slate-500">{{ $invoice->customer_name }}</p></td>
                                    <td class="px-4 py-4"><p>{{ optional($invoice->issue_date)->format('d M Y') ?: 'Pending' }}</p><p class="mt-1 text-xs text-slate-500">Due {{ optional($invoice->due_date)->format('d M Y') ?: 'Pending' }}</p><p class="mt-1 text-xs text-slate-500">{{ $invoice->taxRate?->code ? 'Tax '.$invoice->taxRate->code : 'Manual tax' }}</p></td>
                                    <td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ in_array($invoice->status, ['paid','sent'], true) ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200' }}">{{ ucfirst($invoice->status) }}</span></td>
                                    <td class="px-4 py-4">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                                    <td class="px-4 py-4">{{ number_format((float) $invoice->total_amount - (float) ($invoice->payments_sum_amount ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">No finance invoices have been created yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
