@extends('layouts.auth')

@section('title', 'Sign In')
@section('description', 'Sign in to LTU Academic to access attendance, assignments and notices for your role.')

@section('content')
<section class="auth-section">
    <div class="container">
        <div class="row align-items-center g-5">

            {{-- LEFT: intro --}}
            <div class="col-lg-6">

                <div class="hero-badge">
                    <i class="bi bi-shield-lock-fill"></i>
                    Secure Sign In
                </div>

                <h1 class="hero-title">
                    Welcome to
                    <span>LTU Academic</span>
                </h1>

                <p class="hero-description">
                    Sign in with your authorized account to continue to the
                    workspace for your role: attendance, assignments, notices
                    and academic management in one place.
                </p>

                <div class="role-preview d-none d-md-block">
                    <div class="preview-section-label">Access by role</div>

                    <div class="preview-item">
                        <div class="preview-icon"><i class="bi bi-person-gear"></i></div>
                        <div>
                            <strong>Administrator</strong>
                            <small>Users, academic structure, reports and notices</small>
                        </div>
                    </div>

                    <div class="preview-item">
                        <div class="preview-icon"><i class="bi bi-person-workspace"></i></div>
                        <div>
                            <strong>Teacher</strong>
                            <small>Attendance, assignments and class notices</small>
                        </div>
                    </div>

                    <div class="preview-item">
                        <div class="preview-icon"><i class="bi bi-mortarboard-fill"></i></div>
                        <div>
                            <strong>Student</strong>
                            <small>Your attendance, assignments and notices</small>
                        </div>
                    </div>
                </div>

                <div class="hero-note">
                    <i class="bi bi-shield-check"></i>
                    You are taken to the dashboard that matches your role.
                </div>

            </div>

            {{-- RIGHT: login card --}}
            <div class="col-lg-6">
                <div class="auth-card fade-up">

                    @if (session('success'))
                        <div class="auth-alert success" id="loginSuccessAlert" role="status">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="auth-alert error" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    <h2 class="auth-card-title">Sign In</h2>
                    <p class="auth-card-subtitle">Enter your credentials to continue to your dashboard.</p>

                    <form id="loginForm" action="{{ route('login.post') }}" method="POST" novalidate>
                        @csrf

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-icon-group">
                                <i class="bi bi-envelope field-icon"></i>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="you@ltu.edu"
                                       required
                                       autofocus
                                       autocomplete="username">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-icon-group">
                                <i class="bi bi-lock field-icon"></i>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       placeholder="Enter your password"
                                       required
                                       autocomplete="current-password"
                                       style="padding-right: 3rem;">
                                <button type="button"
                                        class="toggle-password"
                                        id="togglePassword"
                                        aria-label="Show password"
                                        aria-pressed="false">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember me / Forgot password --}}
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="remember"
                                       name="remember"
                                       value="1"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="link-forgot">Forgot password?</a>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                class="btn-primary-custom w-100"
                                id="loginBtn">
                            <span class="spinner-border spinner-sm d-none" id="loginSpinner" role="status" aria-hidden="true"></span>
                            <span id="loginBtnText">Sign In</span>
                            <i class="bi bi-arrow-right" id="loginBtnArrow"></i>
                        </button>

                        <a href="{{ url('/') }}" class="btn-secondary-custom w-100 mt-2">
                            Back to Home
                        </a>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    // Password show/hide
    const toggle = document.getElementById('togglePassword');
    const field  = document.getElementById('password');
    const icon   = document.getElementById('toggleIcon');

    if (toggle && field && icon) {
        toggle.addEventListener('click', function () {
            const hidden = field.type === 'password';
            field.type = hidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !hidden);
            icon.classList.toggle('bi-eye-slash', hidden);
            toggle.setAttribute('aria-pressed', String(hidden));
            toggle.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
        });
    }

    // Loading state on submit (reset if the page is restored from back/forward cache)
    const form    = document.getElementById('loginForm');
    const btn     = document.getElementById('loginBtn');
    const spinner = document.getElementById('loginSpinner');
    const text    = document.getElementById('loginBtnText');
    const arrow   = document.getElementById('loginBtnArrow');

    function resetButton() {
        if (!btn) return;
        btn.removeAttribute('disabled');
        spinner.classList.add('d-none');
        arrow.classList.remove('d-none');
        text.textContent = 'Sign In';
    }

    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.setAttribute('disabled', 'disabled');
            spinner.classList.remove('d-none');
            arrow.classList.add('d-none');
            text.textContent = 'Signing in…';
        });
        window.addEventListener('pageshow', function (e) { if (e.persisted) resetButton(); });
    }

    // Auto-dismiss the logout / reset success message
    const ok = document.getElementById('loginSuccessAlert');
    if (ok) {
        setTimeout(function () {
            ok.style.transition = 'opacity .4s ease';
            ok.style.opacity = '0';
            setTimeout(function () { ok.remove(); }, 400);
        }, 4500);
    }
})();
</script>
@endpush