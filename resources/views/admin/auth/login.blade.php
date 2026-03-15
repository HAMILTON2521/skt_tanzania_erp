<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | Admin Login</title>

    <link rel="stylesheet" href="{{ asset('css/skt-branding.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-login-shell min-h-screen text-slate-100 antialiased">
    <div class="relative isolate min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[linear-gradient(rgba(148,163,184,0.07)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.07)_1px,transparent_1px)] bg-[size:30px_30px] opacity-20"></div>
        <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-6 py-10 lg:px-10">
            <div class="grid w-full gap-8 lg:grid-cols-[1.15fr_0.9fr] lg:items-stretch">
                <section class="admin-login-showcase hidden rounded-[2rem] border border-white/10 p-8 lg:flex lg:flex-col lg:justify-between xl:p-10">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-lime-100/80">SKT Tanzania ERP</p>
                        <h1 class="mt-5 max-w-xl text-4xl font-semibold leading-tight text-white xl:text-5xl">Admin access for the teams controlling finance, operations and compliance.</h1>
                        <p class="mt-5 max-w-2xl text-sm leading-7 text-slate-300">Use the secured admin gateway to reach the ERP control center, monitor activity and manage live workflows across departments.</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <article class="admin-login-metric rounded-[1.5rem] border border-white/10 p-5 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Control</p>
                            <p class="mt-3 text-3xl font-semibold text-white">24/7</p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Administrative access to the ERP workspace and decision points.</p>
                        </article>
                        <article class="admin-login-metric rounded-[1.5rem] border border-white/10 p-5 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Coverage</p>
                            <p class="mt-3 text-3xl font-semibold text-cyan-200">6</p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Connected domains including sales, finance, HR, inventory and procurement.</p>
                        </article>
                        <article class="admin-login-metric rounded-[1.5rem] border border-white/10 p-5 backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">Security</p>
                            <p class="mt-3 text-3xl font-semibold text-lime-200">Role-based</p>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Only accounts with admin authorization are allowed into this surface.</p>
                        </article>
                    </div>
                </section>

                <section class="flex items-center justify-center">
                    <div class="admin-login-card w-full rounded-[2rem] border border-white/10 p-6 backdrop-blur xl:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-lime-100/80">Admin Sign In</p>
                                <h2 class="mt-3 text-3xl font-semibold text-white">Login to the control center</h2>
                                <p class="mt-3 text-sm leading-7 text-slate-300">Enter your administrator credentials to access protected ERP modules and oversight tools.</p>
                            </div>
                            <a href="{{ route('login') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm text-slate-200 transition hover:border-lime-300/30 hover:text-white">Standard login</a>
                        </div>

                        <div class="mt-6 rounded-[1.5rem] border border-lime-300/10 bg-lime-300/5 px-4 py-4 text-sm text-slate-200">
                            Admin accounts are verified against the <span class="font-semibold text-lime-100">Admin</span> role before access is granted.
                        </div>

                        <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-8 space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Email Address</label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    placeholder="admin@company.com"
                                    class="admin-login-input w-full rounded-2xl border px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none @error('email') border-red-400/60 @enderror"
                                >
                                @error('email')
                                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <label for="password" class="block text-sm font-medium text-slate-200">Password</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-sm text-cyan-200 transition hover:text-cyan-100">Forgot password?</a>
                                    @endif
                                </div>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                    class="admin-login-input w-full rounded-2xl border px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none @error('password') border-red-400/60 @enderror"
                                >
                                @error('password')
                                    <p class="mt-2 text-sm text-red-300">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between gap-3 rounded-[1.5rem] border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                                <label for="remember" class="flex items-center gap-3">
                                    <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 rounded border-white/20 bg-slate-950 text-lime-400 focus:ring-lime-400/40">
                                    <span>Keep me signed in on this device</span>
                                </label>
                                <span class="text-xs uppercase tracking-[0.25em] text-slate-500">Restricted</span>
                            </div>

                            <button type="submit" class="admin-login-submit w-full rounded-2xl px-4 py-3 text-sm font-semibold transition">
                                Access Admin Workspace
                            </button>
                        </form>

                        <p class="mt-6 text-sm leading-6 text-slate-400">Need access? Contact the system administrator to assign the required admin role before signing in here.</p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
