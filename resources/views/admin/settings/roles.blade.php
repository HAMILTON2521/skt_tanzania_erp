@extends('admin.layouts.app')

@section('page-title', 'Roles')
@section('page-subtitle', 'Access role catalogue and attached permission counts.')

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">@forelse ($roles as $role)<div class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><p class="text-xs uppercase tracking-[0.3em] text-cyan-300">Role</p><h2 class="mt-3 text-2xl font-semibold text-white">{{ $role->name }}</h2><p class="mt-3 text-sm text-slate-300">{{ $role->permissions_count }} permission bindings</p></div>@empty<div class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 text-slate-400">No roles found.</div>@endforelse</section>
@endsection
