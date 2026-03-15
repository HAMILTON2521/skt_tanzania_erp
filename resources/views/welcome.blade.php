<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SKT Tanzania ERP') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-950 text-stone-100 antialiased">
        <div class="relative isolate overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.25),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.2),_transparent_28%),linear-gradient(135deg,_#0c0a09,_#1c1917_55%,_#111827)]"></div>
            <div class="absolute inset-y-0 left-0 w-1/3 bg-[linear-gradient(180deg,transparent,rgba(251,191,36,0.08),transparent)]"></div>

            <main class="relative mx-auto flex min-h-screen max-w-7xl flex-col justify-center px-6 py-12 lg:px-10">
                <div class="grid gap-8 lg:grid-cols-[1.3fr_0.9fr] lg:items-center">
                    <section class="space-y-8">
                        <div class="inline-flex items-center gap-3 rounded-full border border-amber-300/20 bg-amber-300/10 px-4 py-2 text-sm font-medium text-amber-100">
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                            SKT Tanzania ERP Portal
                        </div>

                        <div class="space-y-5">
                            <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
                                Sign in to the admin system from one clear entry point.
                            </h1>
                            <p class="max-w-2xl text-lg leading-8 text-stone-300">
                                The login route already exists, but the old landing page hid it in a small corner link. This page exposes the admin sign-in directly and gives users a clearer starting point.
                            </p>
                        </div>

                        <div class="flex flex-col gap-4 sm:flex-row">
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center justify-center rounded-2xl bg-amber-400 px-6 py-3 text-base font-semibold text-stone-950 shadow-lg shadow-amber-400/25 transition hover:bg-amber-300"
                            >
                                Admin Sign In
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center justify-center rounded-2xl border border-stone-600 bg-white/5 px-6 py-3 text-base font-semibold text-white transition hover:border-stone-400 hover:bg-white/10"
                                >
                                    Create User Account
                                </a>
                            @endif
                        </div>

                        <div class="grid gap-4 text-sm text-stone-300 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                                <p class="text-xs uppercase tracking-[0.2em] text-stone-400">Entry URL</p>
                                <p class="mt-2 font-semibold text-white">/login</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                                <p class="text-xs uppercase tracking-[0.2em] text-stone-400">Admin Area</p>
                                <p class="mt-2 font-semibold text-white">/admin</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                                <p class="text-xs uppercase tracking-[0.2em] text-stone-400">Access Rule</p>
                                <p class="mt-2 font-semibold text-white">Admin role required</p>
                            </div>
                        </div>
                    </section>

                    <aside class="rounded-[2rem] border border-white/10 bg-white/8 p-6 shadow-2xl shadow-black/30 backdrop-blur-md sm:p-8">
                        <div class="space-y-6">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-amber-200/80">Access Guide</p>
                                <h2 class="mt-3 text-2xl font-semibold text-white">Admin login details</h2>
                                <p class="mt-3 text-sm leading-7 text-stone-300">
                                    Use the admin sign-in button to reach the authentication screen. After login, administrators are redirected to the ERP dashboard.
                                </p>
                            </div>

                            <div class="rounded-2xl border border-emerald-300/20 bg-emerald-300/10 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-100">Local setup</p>
                                <div class="mt-4 space-y-3 text-sm text-emerald-50">
                                    <div>
                                        <p class="text-emerald-100/70">Email</p>
                                        <p class="font-semibold">admin@skt.co.tz</p>
                                    </div>
                                    <div>
                                        <p class="text-emerald-100/70">Password</p>
                                        <p class="font-semibold">Admin@123456</p>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-black/20 p-5 text-sm text-stone-300">
                                <p class="font-semibold text-white">If login does not work</p>
                                <ol class="mt-3 list-decimal space-y-2 pl-5">
                                    <li>Run the database seeders so the admin user exists.</li>
                                    <li>Open the login screen directly from the button above or by visiting /login.</li>
                                    <li>Use an account with the Admin role for /admin access.</li>
                                </ol>
                            </div>
                        </div>
                    </aside>
                </div>
            </main>
        </div>
    </body>
</html>