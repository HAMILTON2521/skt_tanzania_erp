@extends('admin.layouts.app')

@section('page-title', 'Customers')
@section('page-subtitle', 'Maintain customer records used across quotations and sales invoices.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400"><a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a><span>/</span><span>Operations</span><span>/</span><span class="text-white">Customers</span></nav>
        @if (session('status'))<div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>@endif
        <div class="grid gap-4 md:grid-cols-3"><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Customers</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['count'] }}</p></div><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Active</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['active'] }}</p></div><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Inactive</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['inactive'] }}</p></div></div>
        <div class="grid gap-6 xl:grid-cols-[1fr_1.25fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Add Customer</p>
                <form method="POST" action="{{ route('admin.sales.customers.store') }}" class="mt-6 space-y-4">@csrf
                    <div class="grid gap-4 md:grid-cols-2"><label class="block text-sm text-slate-300">Customer Code<input name="customer_code" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label><label class="block text-sm text-slate-300">Name<input name="name" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label><label class="block text-sm text-slate-300">Email<input type="email" name="email" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label><label class="block text-sm text-slate-300">Phone<input name="phone" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label></div>
                    <label class="block text-sm text-slate-300">Address<textarea name="address" rows="4" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></textarea></label>
                    <label class="block text-sm text-slate-300">Status<select name="status" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"><option value="active">Active</option><option value="inactive">Inactive</option></select></label>
                    <button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Save customer</button>
                </form>
            </section>
            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Customer Register</p><div class="mt-6 overflow-x-auto"><table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300"><thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Contact</th><th class="px-4 py-3">Status</th></tr></thead><tbody class="divide-y divide-white/5">@forelse ($customers as $customer)<tr><td class="px-4 py-4"><p class="font-medium text-white">{{ $customer->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $customer->customer_code }}</p></td><td class="px-4 py-4"><p>{{ $customer->email ?: 'No email' }}</p><p class="mt-1 text-xs text-slate-500">{{ $customer->phone ?: 'No phone' }}</p></td><td class="px-4 py-4">{{ ucfirst($customer->status) }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-10 text-center text-slate-400">No customers have been added yet.</td></tr>@endforelse</tbody></table></div></section>
        </div>
    </div>
@endsection
