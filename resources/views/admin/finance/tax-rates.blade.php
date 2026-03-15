@extends('admin.layouts.app')

@section('page-title', 'Tax Rates')
@section('page-subtitle', 'Maintain VAT and charge percentages used on finance invoices.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400"><a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a><span>/</span><span>People &amp; Finance</span><span>/</span><span class="text-white">Tax Rates</span></nav>
        @if (session('status'))<div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>@endif
        <div class="grid gap-6 xl:grid-cols-[1fr_1.25fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Add Tax Rate</p>
                <form method="POST" action="{{ route('admin.finance.tax-rates.store') }}" class="mt-6 space-y-4">@csrf
                    <label class="block text-sm text-slate-300">Tax Name<input name="name" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                    <label class="block text-sm text-slate-300">Code<input name="code" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm text-slate-300">Rate (%)<input type="number" step="0.01" min="0" max="100" name="rate" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Status<select name="is_active" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="1">Active</option><option value="0">Inactive</option></select></label>
                    </div>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save tax rate</button>
                </form>
            </section>
            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Rate Register</p>
                <div class="mt-6 overflow-x-auto"><table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300"><thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Tax</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">Rate</th><th class="px-4 py-3">Status</th></tr></thead><tbody class="divide-y divide-white/5">@forelse ($taxRates as $taxRate)<tr><td class="px-4 py-4 font-medium text-white">{{ $taxRate->name }}</td><td class="px-4 py-4">{{ $taxRate->code }}</td><td class="px-4 py-4">{{ number_format((float) $taxRate->rate, 2) }}%</td><td class="px-4 py-4">{{ $taxRate->is_active ? 'Active' : 'Inactive' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">No tax rates have been configured yet.</td></tr>@endforelse</tbody></table></div>
            </section>
        </div>
    </div>
@endsection
