@extends('admin.layouts.app')

@section('page-title', 'Users')
@section('page-subtitle', 'Review application users and assigned access roles.')

@section('content')
    <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30"><div class="overflow-x-auto"><table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300"><thead class="text-xs uppercase tracking-[0.25em] text-slate-500"><tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Roles</th><th class="px-4 py-3">Joined</th></tr></thead><tbody class="divide-y divide-white/5">@forelse ($users as $user)<tr><td class="px-4 py-4 font-medium text-white">{{ $user->name }}</td><td class="px-4 py-4">{{ $user->email }}</td><td class="px-4 py-4">{{ $user->roles->pluck('name')->implode(', ') ?: 'None' }}</td><td class="px-4 py-4">{{ optional($user->created_at)->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">No users found.</td></tr>@endforelse</tbody></table></div><div class="mt-4">{{ $users->links() }}</div></section>
@endsection
