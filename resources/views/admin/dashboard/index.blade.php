@extends('admin.layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Live counts and quick access across the ERP.')

@section('content')
    <div class="space-y-8">
        <section class="dashboard-hero-panel overflow-hidden rounded-[2rem] border border-white/10 p-6 shadow-2xl shadow-slate-950/40 xl:p-8">
            <div class="grid gap-8 xl:grid-cols-[1.5fr_1fr] xl:items-start">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-cyan-100">
                        Admin Control Center
                    </div>
                    <h2 class="mt-5 max-w-3xl text-3xl font-semibold leading-tight text-white md:text-4xl">Run the ERP from one surface without jumping through placeholder screens.</h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-200">Dashboard, operations, finance, HR, procurement and system support are now available from live module routes. Start where work is queued, not where navigation happens.</p>

                    <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($quickActions as $action)
                            <a href="{{ $action['route'] }}" class="dashboard-action-card rounded-2xl border border-white/10 p-4 transition hover:-translate-y-0.5 hover:border-cyan-300/30">
                                <p class="text-sm font-semibold text-white">{{ $action['title'] }}</p>
                                <p class="mt-2 text-xs leading-6 text-slate-300">{{ $action['summary'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-[1.75rem] border border-white/10 bg-slate-950/45 p-5 backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Signed In</p>
                        <div class="mt-4 flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-400/15 text-lg font-semibold text-cyan-100">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
                            <div>
                                <p class="text-lg font-semibold text-white">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-slate-300">{{ auth()->user()->getRoleNames()->implode(', ') ?: 'Authenticated User' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($heroMetrics as $metric)
                            <div class="rounded-2xl border border-white/10 bg-slate-950/45 p-4 backdrop-blur">
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">{{ $metric['label'] }}</p>
                                <p class="mt-3 text-2xl font-semibold {{ $metric['tone'] }}">{{ $metric['value'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <article class="dashboard-stat-orb rounded-[1.75rem] border border-white/10 p-5 shadow-xl shadow-slate-950/25">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-4 text-4xl font-semibold {{ $stat['accent'] }}">{{ $stat['value'] }}</p>
                    <p class="mt-3 text-sm leading-6 text-slate-300">{{ $stat['description'] }}</p>
                </article>
            @endforeach
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.45fr_1fr]">
            <section class="rounded-[2rem] border border-white/10 bg-slate-950/45 p-6 shadow-2xl shadow-slate-950/35 xl:p-7">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Module Grid</p>
                        <h3 class="mt-2 text-2xl font-semibold text-white">Live areas of work</h3>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.25em] text-slate-400">{{ $systemSummary['linked_pages'] }} pages</span>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @foreach ($moduleCards as $card)
                        <article class="dashboard-module-card rounded-[1.6rem] border border-white/10 p-5 transition hover:-translate-y-1 hover:border-cyan-300/30">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-cyan-300/90">{{ $card['eyebrow'] }}</p>
                                    <p class="mt-2 text-xl font-semibold text-white">{{ $card['title'] }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 text-right">
                                    <p class="text-2xl font-semibold text-white">{{ $card['metric'] }}</p>
                                    <p class="text-xs uppercase tracking-[0.25em] text-slate-400">{{ $card['metric_label'] }}</p>
                                </div>
                            </div>
                            <p class="mt-4 text-sm leading-7 text-slate-300">{{ $card['description'] }}</p>
                            <div class="mt-5">
                                <a href="{{ $card['route'] }}" class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-100 transition hover:border-cyan-400/30 hover:text-cyan-100">Open module</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <aside class="space-y-6">
                <section class="rounded-[2rem] border border-white/10 bg-gradient-to-br from-cyan-400/15 via-slate-950/80 to-emerald-400/10 p-6 shadow-2xl shadow-cyan-950/25">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-100">System Picture</p>
                    <h3 class="mt-3 text-2xl font-semibold text-white">{{ $systemSummary['sections'] }} sections and {{ $systemSummary['module_groups'] }} top-level groups are connected.</h3>
                    <p class="mt-4 text-sm leading-7 text-slate-200">This admin workspace now routes directly into the modules that matter, including HR operations, procurement receipts, notifications and support settings.</p>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/35">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Health Signals</p>
                    <div class="mt-4 space-y-3">
                        @foreach ($healthCards as $item)
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="font-medium text-white">{{ $item['title'] }}</p>
                                    <p class="text-2xl font-semibold text-cyan-100">{{ $item['value'] }}</p>
                                </div>
                                <p class="mt-2 text-xs leading-6 text-slate-400">{{ $item['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/35">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Finance Snapshot</p>
                    <div class="mt-4 grid gap-3">
                        @foreach ($financeSnapshot as $item)
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="font-medium text-white">{{ $item['title'] }}</p>
                                    <p class="text-2xl font-semibold text-emerald-300">{{ $item['value'] }}</p>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">{{ $item['note'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/35">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Workflow Watch</p>
                    <div class="mt-4 space-y-3">
                        @foreach ($workflowCards as $workflow)
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="font-medium text-white">{{ $workflow['title'] }}</p>
                                    <p class="text-xl font-semibold text-amber-300">{{ $workflow['value'] }}</p>
                                </div>
                                <p class="mt-2 text-xs leading-6 text-slate-400">{{ $workflow['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-slate-950/50 p-6 shadow-2xl shadow-slate-950/35">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Recent Accounts</p>
                    <div class="mt-4 space-y-3">
                        @forelse ($recentUsers as $recentUser)
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="font-medium text-white">{{ $recentUser->name }}</p>
                                        <p class="text-sm text-slate-400">{{ $recentUser->email }}</p>
                                    </div>
                                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ optional($recentUser->created_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-4 text-sm text-slate-400">No users available yet.</div>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
