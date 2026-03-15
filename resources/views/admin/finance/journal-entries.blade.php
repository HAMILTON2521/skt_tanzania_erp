@extends('admin.layouts.app')

@section('page-title', 'Journal Entries')
@section('page-subtitle', 'Posting activity, approval status and balancing checks.')

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>People &amp; Finance</span>
            <span>/</span>
            <span class="text-white">Journal Entries</span>
        </nav>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Entries</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['total_entries'] }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-emerald-300">Posted</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ $summary['posted_entries'] }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300">Total Debit</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['total_debit'], 2) }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-fuchsia-300">Total Credit</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format((float) $summary['total_credit'], 2) }}</p>
            </div>
        </div>

        <section class="rounded-3xl border border-white/10 bg-white/6 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Posting Register</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white">Journal entries</h2>
                </div>
                <a href="{{ route('admin.finance.reports') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-slate-100 transition hover:bg-white/10">Open reports</a>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase tracking-[0.25em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Reference</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Account</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Debit</th>
                            <th class="px-4 py-3">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($entries as $entry)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-white">{{ $entry->reference ?: 'Draft entry' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $entry->description ?: 'No narration provided.' }}</p>
                                </td>
                                <td class="px-4 py-4">{{ optional($entry->entry_date)->format('d M Y') ?: 'Pending' }}</td>
                                <td class="px-4 py-4">{{ $entry->chartOfAccount?->code ? $entry->chartOfAccount->code.' - '.$entry->chartOfAccount->name : 'Unmapped' }}</td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $entry->status === 'posted' ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200' }}">
                                        {{ ucfirst($entry->status ?: 'draft') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">{{ number_format((float) $entry->debit, 2) }}</td>
                                <td class="px-4 py-4">{{ number_format((float) $entry->credit, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-400">No journal entries have been captured yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
