<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | Admin</title>

    <link rel="stylesheet" href="{{ asset('css/skt-branding.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="admin-shell relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[linear-gradient(rgba(148,163,184,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.08)_1px,transparent_1px)] bg-[size:28px_28px] opacity-20"></div>
        <div class="relative flex min-h-screen">
            @include('admin.partials.sidebar', ['navigation' => config('admin.navigation', [])])

            <div class="flex min-h-screen flex-1 flex-col">
                <header class="admin-header border-b px-6 py-4 backdrop-blur xl:px-10">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="admin-page-kicker text-xs font-semibold uppercase tracking-[0.3em]">Admin Workspace</p>
                            <h1 class="admin-page-title mt-1 text-2xl font-semibold">@yield('page-title', 'Dashboard')</h1>
                            <p class="mt-1 text-sm text-slate-300">@yield('page-subtitle', 'ERP operations, approvals and controls in one place.')</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.notifications.index') }}" class="rounded-2xl border border-white/10 px-4 py-3 text-sm text-slate-200 transition hover:border-cyan-400/40 hover:text-white">
                                Notifications
                                @if (auth()->user()->unreadNotifications()->count() > 0)
                                    <span class="ml-2 rounded-full bg-cyan-400/20 px-2 py-1 text-xs text-cyan-100">{{ auth()->user()->unreadNotifications()->count() }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.profile.edit') }}" class="admin-profile-card flex items-center gap-3 rounded-2xl border px-4 py-3 text-sm text-slate-200 shadow-lg transition hover:border-cyan-400/40">
                                <div class="admin-profile-avatar flex h-10 w-10 items-center justify-center rounded-full font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="admin-profile-name font-medium">{{ auth()->user()->name }}</p>
                                    <p class="admin-profile-role text-xs uppercase tracking-[0.2em]">{{ auth()->user()->getRoleNames()->implode(', ') ?: 'Authenticated User' }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-6 py-8 xl:px-10">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</body>
</html>
