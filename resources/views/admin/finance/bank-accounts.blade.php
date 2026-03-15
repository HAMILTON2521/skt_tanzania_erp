@extends('admin.layouts.app')

@section('page-title', 'Bank Accounts')
@section('page-subtitle', 'Settlement accounts used to receive customer payments and finance collections.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400"><a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a><span>/</span><span>People &amp; Finance</span><span>/</span><span class="text-white">Bank Accounts</span></nav>
        @if (session('status'))<div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>@endif
        <div class="grid gap-6 xl:grid-cols-[1fr_1.25fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Add Bank Account</p>
                <form method="POST" action="{{ route('admin.finance.bank-accounts.store') }}" class="mt-6 space-y-4">@csrf
                    <label class="block text-sm text-slate-300">Account Name<input name="account_name" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                    <label class="block text-sm text-slate-300">Bank Name<input name="bank_name" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                    <label class="block text-sm text-slate-300">Account Number<input name="account_number" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm text-slate-300">Currency<input name="currency" value="TZS" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label>
                        <label class="block text-sm text-slate-300">Status<select name="is_active" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="1">Active</option><option value="0">Inactive</option></select></label>
                    </div>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save bank account</button>
                </form>
            </section>
            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Bank Register</p>
                <div class="mt-6 overflow-x-auto"><table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300"><thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Account</th><th class="px-4 py-3">Bank</th><th class="px-4 py-3">Currency</th><th class="px-4 py-3">Status</th></tr></thead><tbody class="divide-y divide-white/5">@forelse ($accounts as $account)<tr><td class="px-4 py-4"><p class="font-medium text-white">{{ $account->account_name }}</p><p class="mt-1 text-xs text-slate-500">{{ $account->account_number }}</p></td><td class="px-4 py-4">{{ $account->bank_name }}</td><td class="px-4 py-4">{{ $account->currency }}</td><td class="px-4 py-4">{{ $account->is_active ? 'Active' : 'Inactive' }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">No bank accounts have been configured yet.</td></tr>@endforelse</tbody></table></div>
            </section>
        </div>
    </div>
@endsection
