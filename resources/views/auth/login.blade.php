@extends('layouts.app')

@section('content')
    <section class="auth-login-page container-fluid px-0">
        <div class="row g-0 min-vh-auth-page">
            <div class="col-lg-7 auth-showcase d-none d-lg-flex">
                <div class="auth-showcase-overlay"></div>
                <div class="auth-showcase-content position-relative z-1 w-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="auth-kicker">Enterprise Resource Platform</span>
                        <h1 class="auth-title mt-4">Professional control for finance, sales, inventory, HR and procurement.</h1>
                        <p class="auth-subtitle mt-4">A focused administrative workspace built for day-to-day operations, approvals and reporting without scattered tools.</p>
                    </div>

                    <div class="auth-visual-stage">
                        <div class="auth-orb auth-orb-one"></div>
                        <div class="auth-orb auth-orb-two"></div>
                        <div class="auth-preview-shell">
                            <div class="auth-preview-topbar">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="auth-preview-grid">
                                <div class="auth-preview-primary">
                                    <p class="auth-preview-label">Control Center</p>
                                    <h3>Operational Snapshot</h3>
                                    <div class="auth-preview-metrics">
                                        <div>
                                            <span>Revenue</span>
                                            <strong>TZS 48.2M</strong>
                                        </div>
                                        <div>
                                            <span>Orders</span>
                                            <strong>186</strong>
                                        </div>
                                        <div>
                                            <span>Staff</span>
                                            <strong>72</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="auth-preview-side">
                                    <div class="auth-preview-chip">Sales</div>
                                    <div class="auth-preview-chip">Finance</div>
                                    <div class="auth-preview-chip">Inventory</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-feature-grid row g-3 mt-1">
                        <div class="col-md-4">
                            <div class="auth-feature-card h-100">
                                <p class="auth-feature-value">Sales</p>
                                <p class="auth-feature-copy">Quotations, invoicing and receipt tracking in one controlled flow.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="auth-feature-card h-100">
                                <p class="auth-feature-value">Finance</p>
                                <p class="auth-feature-copy">Accounts, payments, expenses and reporting from the same workspace.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="auth-feature-card h-100">
                                <p class="auth-feature-value">People</p>
                                <p class="auth-feature-copy">Employees, leave, attendance and payroll with clearer oversight.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 auth-panel-wrap d-flex align-items-center justify-content-center">
                <div class="auth-panel card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <div class="auth-mobile-intro d-lg-none mb-4">
                            <span class="auth-kicker">SKT Tanzania ERP</span>
                            <h1 class="auth-mobile-title mt-3">Sign in to the operations workspace.</h1>
                            <p class="auth-form-copy mt-2">Access sales, finance, inventory, HR and procurement from one administrative surface.</p>
                        </div>

                        <div class="text-center text-lg-start">
                            <span class="auth-form-kicker">Secure Sign In</span>
                            <h2 class="auth-form-title mt-3">Welcome back</h2>
                            <p class="auth-form-copy mt-2">Sign in to access the SKT Tanzania ERP workspace.</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="mt-4 mt-md-5">
                            @csrf

                            <div class="mb-4">
                                <label for="email" class="form-label auth-label">Email Address</label>
                                <input id="email" type="email" class="form-control auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="you@company.com">

                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <label for="password" class="form-label auth-label mb-0">Password</label>
                                    @if (Route::has('password.request'))
                                        <a class="auth-inline-link" href="{{ route('password.request') }}">Forgot password?</a>
                                    @endif
                                </div>
                                <input id="password" type="password" class="form-control auth-input @error('password') is-invalid @enderror mt-2" name="password" required autocomplete="current-password" placeholder="Enter your password">

                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                                <div class="form-check auth-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                <span class="auth-helper-text">Authorized staff only</span>
                            </div>

                            <button type="submit" class="btn auth-submit-btn w-100">
                                {{ __('Login to Dashboard') }}
                            </button>
                        </form>

                        <div class="auth-footer-note mt-4 pt-4">
                            <p class="mb-0">Need an account? Contact your system administrator for authorized access.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
