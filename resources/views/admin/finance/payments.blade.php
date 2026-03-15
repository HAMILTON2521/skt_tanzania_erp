@extends('admin.layouts.app')

@section('page-title', 'Payments')
@section('page-subtitle', 'Record collections and reconcile settlement status against invoices.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>People &amp; Finance</span>
            <span>/</span>
            <span class="text-white">Payments</span>
        </nav>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Payments</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['count'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Completed</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['completed'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Pending</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['pending'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-fuchsia-300">Value</p><p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['total'], 2) }}</p></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.05fr_1.35fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Record Payment</p>
                <form method="POST" action="{{ route('admin.finance.payments.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm text-slate-300">Payment Number<input name="payment_number" value="{{ old('payment_number') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Invoice<select name="invoice_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required><option value="">Select invoice</option>@foreach ($invoices as $invoice)<option value="{{ $invoice->id }}" @selected(old('invoice_id') == $invoice->id)>{{ $invoice->invoice_number }} | {{ $invoice->customer_name }} | {{ number_format((float) $invoice->total_amount, 2) }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300">Payment Date<input type="date" name="payment_date" value="{{ old('payment_date') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Amount<input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Method<select name="method" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="bank_transfer">Bank Transfer</option><option value="cash">Cash</option><option value="mobile_money">Mobile Money</option><option value="cheque">Cheque</option></select></label>
                        <label class="block text-sm text-slate-300">Status<select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="pending">Pending</option><option value="completed">Completed</option><option value="failed">Failed</option><option value="reversed">Reversed</option></select></label>
                        <label class="block text-sm text-slate-300">Bank Account<select name="bank_account_id" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="">Select bank account</option>@foreach ($bankAccounts as $bankAccount)<option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }} | {{ $bankAccount->account_name }} | {{ $bankAccount->account_number }}</option>@endforeach</select></label>
                        <label class="block text-sm text-slate-300 md:col-span-2">Reference<input name="reference" value="{{ old('reference') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label>
                    </div>
                    <label class="block text-sm text-slate-300">Notes<textarea name="notes" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('notes') }}</textarea></label>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save payment</button>
                </form>
            </section>

            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex items-center justify-between gap-4">
                    <div><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Collections Register</p><h2 class="mt-2 text-2xl font-semibold text-white">Recorded payments</h2></div>
                    <a href="{{ route('admin.finance.expenses.index') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-slate-100 transition hover:bg-white/10">Open expenses</a>
                </div>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Payment</th><th class="px-4 py-3">Invoice</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Amount</th></tr></thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($payments as $payment)
                                <tr>
                                    <td class="px-4 py-4"><p class="font-medium text-white">{{ $payment->payment_number }}</p><p class="mt-1 text-xs text-slate-500">{{ $payment->method ?: 'Method pending' }}{{ $payment->reference ? ' | '.$payment->reference : '' }}</p><p class="mt-1 text-xs text-slate-500">{{ $payment->bankAccount?->bank_name ? $payment->bankAccount->bank_name.' | '.$payment->bankAccount->account_number : 'No bank account linked' }}</p></td>
                                    <td class="px-4 py-4">{{ $payment->invoice?->invoice_number ?: 'Unlinked' }}<p class="mt-1 text-xs text-slate-500">{{ $payment->invoice?->customer_name }}</p></td>
                                    <td class="px-4 py-4">{{ optional($payment->payment_date)->format('d M Y') ?: 'Pending' }}</td>
                                    <td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $payment->status === 'completed' ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200' }}">{{ ucfirst($payment->status) }}</span></td>
                                    <td class="px-4 py-4">{{ number_format((float) $payment->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">No payments have been recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
