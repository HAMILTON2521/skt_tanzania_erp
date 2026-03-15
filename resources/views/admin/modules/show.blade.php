@extends('admin.layouts.app')

@section('page-title', $page['title'])
@section('page-subtitle', $page['summary'])

@section('content')
    <div class="space-y-6">
        <nav class="flex items-center gap-3 text-sm text-slate-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            <span>/</span>
            <span>{{ $sectionTitle }}</span>
            <span>/</span>
            <span class="text-white">{{ $page['title'] }}</span>
        </nav>

        <section class="rounded-3xl border border-white/10 bg-white/6 p-8 shadow-2xl shadow-slate-950/30 backdrop-blur">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">{{ $sectionTitle }}</p>
                <h2 class="mt-3 text-3xl font-semibold text-white">{{ $page['title'] }}</h2>
                <p class="mt-4 text-sm leading-7 text-slate-300">{{ $page['summary'] }}</p>
                <div class="mt-8 rounded-2xl border border-dashed border-cyan-300/30 bg-slate-950/40 p-5 text-sm text-slate-300">
                    This linked placeholder exists so the sidebar, breadcrumbs and admin navigation work end to end before the full CRUD and Vue module screens are generated.
                </div>
            </div>
        </section>
    </div>
@endsection
