@extends('admin.layouts.app')

@section('page-title', 'My Profile')
@section('page-subtitle', 'Update your administrator identity and account credentials.')

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
        <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
            @if (session('status'))<div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">{{ session('status') }}</div>@endif
            <form method="POST" action="{{ route('admin.profile.update') }}" class="mt-6 space-y-4">@csrf @method('PUT')<label class="block text-sm text-slate-300">Name<input name="name" value="{{ old('name', $user->name) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label><label class="block text-sm text-slate-300">Email<input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white" required></label><div class="grid gap-4 md:grid-cols-2"><label class="block text-sm text-slate-300">Current Password<input type="password" name="current_password" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label><label class="block text-sm text-slate-300">New Password<input type="password" name="password" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label></div><label class="block text-sm text-slate-300">Confirm New Password<input type="password" name="password_confirmation" class="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white"></label><button class="rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-5 py-3 text-sm font-medium text-cyan-100 transition hover:bg-cyan-400/20">Update profile</button></form>
        </section>
        <aside class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><p class="text-xs uppercase tracking-[0.3em] text-cyan-300">Account</p><h2 class="mt-3 text-2xl font-semibold text-white">{{ $user->name }}</h2><p class="mt-2 text-sm text-slate-300">{{ $user->email }}</p><p class="mt-2 text-xs uppercase tracking-[0.2em] text-slate-500">{{ $user->getRoleNames()->implode(', ') ?: 'Authenticated User' }}</p></aside>
    </div>
@endsection
