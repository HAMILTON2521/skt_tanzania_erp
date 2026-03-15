@extends('admin.layouts.app')

@section('page-title', 'Permissions')
@section('page-subtitle', 'Action-level access map currently registered in the system.')

@section('content')
    <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><div class="overflow-x-auto"><table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300"><thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Permission</th><th class="px-4 py-3">Guard</th><th class="px-4 py-3">Roles</th></tr></thead><tbody class="divide-y divide-white/5">@forelse ($permissions as $permission)<tr><td class="px-4 py-4 font-medium text-white">{{ $permission->name }}</td><td class="px-4 py-4">{{ $permission->guard_name }}</td><td class="px-4 py-4">{{ $permission->roles_count }}</td></tr>@empty<tr><td colspan="3" class="px-4 py-10 text-center text-slate-400">No permissions found.</td></tr>@endforelse</tbody></table></div></section>
@endsection
