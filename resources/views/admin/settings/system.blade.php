@extends('admin.layouts.app')

@section('page-title', 'System Health')
@section('page-subtitle', 'Runtime details and cross-module data footprint overview.')

@section('content')
    <div class="grid gap-6 xl:grid-cols-2"><section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><p class="text-xs uppercase tracking-[0.3em] text-cyan-300">Runtime</p><div class="mt-4 grid gap-3 md:grid-cols-2">@foreach ($system as $label => $value)<div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ $label }}</p><p class="mt-2 text-sm font-medium text-white">{{ $value }}</p></div>@endforeach</div></section><section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><p class="text-xs uppercase tracking-[0.3em] text-emerald-300">Module Counts</p><div class="mt-4 grid gap-3 md:grid-cols-2">@foreach ($counts as $label => $value)<div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4"><p class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ str_replace('_', ' ', $label) }}</p><p class="mt-2 text-2xl font-semibold text-white">{{ $value }}</p></div>@endforeach</div></section></div>
@endsection
