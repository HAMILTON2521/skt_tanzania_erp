@extends('admin.layouts.app')

@section('page-title', 'Financial Reports')
@section('page-subtitle', 'Ledger mix, recent posting activity and quick variance checks.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>People &amp; Finance</span>
            <span>/</span>
            <span class="text-white">Financial Reports</span>
        </nav>

        <div class="grid gap-4 md:grid-cols-6 xl:grid-cols-6">
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Accounts</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['accounts'] }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Recent Entries</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['entries'] }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-300">Bank Accounts</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['bank_accounts'] }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-lime-300">Tax Rates</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['tax_rates'] }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Debit</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['total_debit'], 2) }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-fuchsia-300">Credit</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['total_credit'], 2) }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-rose-300">Variance</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['variance'], 2) }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_1fr]">
            <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Account Mix</p>
                <h2 class="mt-2 text-2xl font-semibold text-white">Ledger composition</h2>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase tracking-[0.25em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Accounts</th>
                                <th class="px-4 py-3">Active</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($accountMix as $row)
                                <tr>
                                    <td class="px-4 py-4 font-medium text-white">{{ $row['type'] }}</td>
                                    <td class="px-4 py-4">{{ $row['total'] }}</td>
                                    <td class="px-4 py-4">{{ $row['active'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-10 text-center text-slate-400">No accounts are available for reporting yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-3xl border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/30">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Recent Activity</p>
                <div class="mt-4 space-y-3">
                    @forelse ($recentEntries as $entry)
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="font-medium text-white">{{ $entry->reference ?: 'Draft entry' }}</p>
                                    <p class="text-sm text-slate-400">{{ $entry->chartOfAccount?->name ?: 'Unmapped account' }}</p>
                                </div>
                                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ optional($entry->entry_date)->format('d M Y') ?: 'Pending' }}</p>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Debit {{ number_format((float) $entry->debit, 2) }} / Credit {{ number_format((float) $entry->credit, 2) }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-sm text-slate-400">
                            No journal activity has been posted yet.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
