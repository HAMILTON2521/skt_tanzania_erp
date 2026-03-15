@extends('admin.layouts.app')

@section('page-title', 'Company Settings')
@section('page-subtitle', 'Environment identity and top-level organizational context.')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-3"><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs uppercase tracking-[0.3em] text-cyan-300">Employees</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['employees'] }}</p></div><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs uppercase tracking-[0.3em] text-emerald-300">Customers</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['customers'] }}</p></div><div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5"><p class="text-xs uppercase tracking-[0.3em] text-amber-300">Products</p><p class="mt-3 text-3xl font-semibold text-white">{{ $summary['products'] }}</p></div></section>
        <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">@foreach ($company as $label => $value)<div class="rounded-2xl border border-white/10 bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ str_replace('_', ' ', $label) }}</p><p class="mt-2 text-sm font-medium text-white">{{ is_array($value) ? implode(', ', $value) : $value }}</p></div>@endforeach</div></section>
    </div>
@endsection
