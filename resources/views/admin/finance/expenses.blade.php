@extends('admin.layouts.app')

@section('page-title', 'Expenses')
@section('page-subtitle', 'Capture operating spend and monitor approval or payment readiness.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>People &amp; Finance</span>
            <span>/</span>
            <span class="text-white">Expenses</span>
        </nav>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Expenses</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['count'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Approved</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['approved'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Pending</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['pending'] }}</p></div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-fuchsia-300">Value</p><p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['total'], 2) }}</p></div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.05fr_1.35fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Record Expense</p>
                <form method="POST" action="{{ route('admin.finance.expenses.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm text-slate-300">Expense Number<input name="expense_number" value="{{ old('expense_number') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Category<input name="category" value="{{ old('category') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Vendor<input name="vendor_name" value="{{ old('vendor_name') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Status<select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="pending">Pending</option><option value="approved">Approved</option><option value="paid">Paid</option><option value="rejected">Rejected</option></select></label>
                        <label class="block text-sm text-slate-300">Expense Date<input type="date" name="expense_date" value="{{ old('expense_date') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Amount<input type="number" step="0.01" min="0" name="amount" value="{{ old('amount') }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                    </div>
                    <label class="block text-sm text-slate-300">Notes<textarea name="notes" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white">{{ old('notes') }}</textarea></label>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save expense</button>
                </form>
            </section>

            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <div class="flex items-center justify-between gap-4">
                    <div><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Expense Register</p><h2 class="mt-2 text-2xl font-semibold text-white">Recorded expenses</h2></div>
                    <a href="{{ route('admin.finance.reports') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-slate-100 transition hover:bg-white/10">Open reports</a>
                </div>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Expense</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Amount</th></tr></thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($expenses as $expense)
                                <tr>
                                    <td class="px-4 py-4"><p class="font-medium text-white">{{ $expense->expense_number }}</p><p class="mt-1 text-xs text-slate-500">{{ $expense->vendor_name }}</p></td>
                                    <td class="px-4 py-4">{{ optional($expense->expense_date)->format('d M Y') ?: 'Pending' }}</td>
                                    <td class="px-4 py-4">{{ $expense->category ?: 'General' }}</td>
                                    <td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ in_array($expense->status, ['approved','paid'], true) ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200' }}">{{ ucfirst($expense->status) }}</span></td>
                                    <td class="px-4 py-4">{{ number_format((float) $expense->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">No expenses have been recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
