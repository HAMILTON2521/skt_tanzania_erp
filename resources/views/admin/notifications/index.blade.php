@extends('admin.layouts.app')

@section('page-title', 'Notifications')
@section('page-subtitle', 'Unread and historical alerts assigned to your account.')

@section('content')
    <div class="space-y-6">
        @if (session('status'))<div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>@endif
        <div class="flex items-center justify-between rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><div><p class="text-xs uppercase tracking-[0.3em] text-cyan-300">Unread</p><p class="mt-2 text-3xl font-semibold text-white">{{ $unreadCount }}</p></div><form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">@csrf<button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Mark all as read</button></form></div>
        <div class="space-y-4">@forelse ($notifications as $notification)<div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5 shadow-2xl shadow-slate-950/30"><div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between"><div><p class="font-medium text-white">{{ data_get($notification->data, 'title', class_basename($notification->type)) }}</p><p class="mt-2 text-sm text-slate-300">{{ data_get($notification->data, 'message', json_encode($notification->data)) }}</p><p class="mt-2 text-xs text-slate-500">{{ optional($notification->created_at)->diffForHumans() }}</p></div><div>@if (! $notification->read_at)<form method="POST" action="{{ route('admin.notifications.mark-read', $notification) }}">@csrf<button class="rounded-2xl border border-white/10 px-4 py-3 text-sm text-slate-200 transition hover:border-cyan-400/40 hover:text-white">Mark as read</button></form>@else<p class="text-sm text-emerald-300">Read {{ $notification->read_at->diffForHumans() }}</p>@endif</div></div></div>@empty<div class="rounded-3xl border border-white/10 bg-slate-950/50 px-4 py-10 text-center text-slate-400">No notifications available.</div>@endforelse</div>
        <div>{{ $notifications->links() }}</div>
    </div>
@endsection
