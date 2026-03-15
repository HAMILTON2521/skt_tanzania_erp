@extends('admin.layouts.app')

@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Operational activity history across the admin workspace.')

@section('content')
    <div class="space-y-6">
        <form method="GET" class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><input name="search" value="{{ $filters['search'] }}" placeholder="Search description, log name or actor" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></form>
        <div class="space-y-4">@forelse ($activities as $activity)<div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5 shadow-2xl shadow-slate-950/30"><div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between"><div><p class="font-medium text-white">{{ $activity->description }}</p><p class="mt-1 text-sm text-slate-300">Actor: {{ $activity->causer?->name ?: 'System' }} | Log: {{ $activity->log_name ?: 'default' }}</p></div><p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ optional($activity->created_at)->diffForHumans() }}</p></div></div>@empty<div class="rounded-3xl border border-white/10 bg-slate-950/50 px-4 py-10 text-center text-slate-400">No audit logs found.</div>@endforelse</div>
        <div>{{ $activities->links() }}</div>
    </div>
@endsection
