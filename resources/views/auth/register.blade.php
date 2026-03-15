@extends('layouts.app')

@section('content')
    <section class="auth-login-page container-fluid px-0">
        <div class="row g-0 min-vh-auth-page">
            <div class="col-lg-7 auth-showcase d-none d-lg-flex">
                <div class="auth-showcase-overlay"></div>
                <div class="auth-showcase-content position-relative z-1 w-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="auth-kicker">Access Provisioning</span>
                        <h1 class="auth-title mt-4">Create a secure account for controlled access to the SKT Tanzania ERP workspace.</h1>
                        <p class="auth-subtitle mt-4">Set up your account once, then move directly into quotations, finance operations, HR workflows, procurement approvals and reporting.</p>
                    </div>

                    <div class="auth-feature-grid row g-3">
                        <div class="col-md-4">
                            <div class="auth-feature-card h-100">
                                <p class="auth-feature-value">Protected Access</p>
                                <p class="auth-feature-copy">Role-based entry into the modules you are authorized to use.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="auth-feature-card h-100">
                                <p class="auth-feature-value">One Workspace</p>
                                <p class="auth-feature-copy">Finance, sales, inventory, HR and procurement in one place.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="auth-feature-card h-100">
                                <p class="auth-feature-value">Audit Ready</p>
                                <p class="auth-feature-copy">Tracked activity, access control and operational visibility.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 auth-panel-wrap d-flex align-items-center justify-content-center">
                <div class="auth-panel card border-0 shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center text-lg-start">
                            <span class="auth-form-kicker">Account Setup</span>
                            <h2 class="auth-form-title mt-3">Create your account</h2>
                            <p class="auth-form-copy mt-2">Register with your name, email and password to access the system.</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="mt-4 mt-md-5">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="form-label auth-label">Full Name</label>
                                <input id="name" type="text" class="form-control auth-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter your full name">

                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label auth-label">Email Address</label>
                                <input id="email" type="email" class="form-control auth-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@company.com">

                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label auth-label">Password</label>
                                <input id="password" type="password" class="form-control auth-input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Create a strong password">

                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password-confirm" class="form-label auth-label">Confirm Password</label>
                                <input id="password-confirm" type="password" class="form-control auth-input" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter your password">
                            </div>

                            <button type="submit" class="btn auth-submit-btn w-100">
                                {{ __('Create Account') }}
                            </button>
                        </form>

                        <div class="auth-footer-note mt-4 pt-4 d-flex flex-column gap-2 flex-sm-row align-items-sm-center justify-content-sm-between">
                            <p class="mb-0">Already have an account?</p>
                            <a href="{{ route('login') }}" class="auth-inline-link">Return to login</a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </section>
</div>
@endsection
