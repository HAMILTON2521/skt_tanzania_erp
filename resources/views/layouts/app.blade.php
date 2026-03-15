<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/skt-branding.css') }}">

    <!-- Scripts -->
    @vite(['resources/css/bootstrap-app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div id="app" class="auth-shell min-vh-100 d-flex flex-column">
        <header class="auth-topbar border-bottom border-light border-opacity-10">
            <div class="container auth-topbar-inner d-flex align-items-center justify-content-between py-3 py-lg-4">
                <a class="navbar-brand skt-brand-lockup text-decoration-none" href="{{ url('/') }}">
                    <img src="{{ asset('images/skt-logo.svg') }}" alt="SKT Tanzania logo" class="skt-brand-logo skt-brand-logo--sm brand-image">
                    <span class="auth-brand-text">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <nav class="d-flex align-items-center gap-2">
                    @guest
                        @if (Route::has('login'))
                            <a class="btn btn-auth-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        @endif

                        @if (Route::has('register'))
                            <a class="btn btn-auth-outline" href="{{ route('register') }}">{{ __('Create account') }}</a>
                        @endif
                    @else
                        <div class="dropdown">
                            <a id="navbarDropdown" class="btn btn-auth-outline dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end auth-dropdown" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </nav>
            </div>
        </header>

        <main class="flex-grow-1 d-flex align-items-stretch">
            @yield('content')
        </main>
    </div>
</body>
</html>
