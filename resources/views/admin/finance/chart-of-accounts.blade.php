@extends('admin.layouts.app')

@section('page-title', 'Chart of Accounts')
@section('page-subtitle', 'Ledger account structure, activation state and posting coverage.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>People &amp; Finance</span>
            <span>/</span>
            <span class="text-white">Chart of Accounts</span>
        </nav>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Total Accounts</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['total_accounts'] }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Active Accounts</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['active_accounts'] }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Posting Ready</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['posting_ready_accounts'] }}</p>
            </div>
        </div>

        <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Finance Setup</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white">Chart of accounts</h2>
                </div>
                <a href="{{ route('admin.finance.journal-entries') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-slate-100 transition hover:bg-white/10">View journal entries</a>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase tracking-[0.25em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Account</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Entries</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($accounts as $account)
                            <tr>
                                <td class="px-4 py-4 font-medium text-white">{{ $account->code ?: 'Unassigned' }}</td>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-white">{{ $account->name ?: 'Untitled account' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $account->description ?: 'No description available.' }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $account->type ?: 'Unclassified' }}</td>
                                <td class="px-4 py-4">{{ $account->category ?: 'General' }}</td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $account->is_active ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200' }}">
                                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">{{ $account->journal_entries_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-400">No finance accounts have been configured yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
