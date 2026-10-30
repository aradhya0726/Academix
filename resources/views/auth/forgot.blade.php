<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<meta name="description" content="Reset your password to sign back in to the Student Attendance &amp; Academic Record Management System.">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Fonts: Fraunces for display (editorial serif), Inter for body/UI — shared with the welcome/login pages -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ================================================================
   DESIGN TOKENS — shared with welcome.blade.php / login.blade.php
   ================================================================ */
:root {
    --indigo: #4F46E5;
    --indigo-soft: rgba(79, 70, 229, 0.10);
    --navy-deep: #101828;
    --navy: #0B1220;
    --gold: #4F46E5;
    --gold-light: #818CF8;
    --gold-soft: rgba(79, 70, 229, 0.10);
    --cream: #F3F5F9;
    --paper: #FFFFFF;
    --border: #E6E9F0;
    --border-navy: rgba(255,255,255,0.10);
    --text-dark: #101828;
    --text-muted: #667085;
    --text-faint: #98A2B3;
    --text-on-navy-muted: #8A93A6;

    --radius-lg: 20px;
    --radius-md: 14px;
    --radius-sm: 10px;
    --shadow-lift: 0 16px 40px rgba(16, 24, 40, 0.12);
}

* { box-sizing: border-box; }
html { scroll-behavior: smooth; }

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--text-dark);
    background: var(--paper);
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
}

::selection { background: var(--gold-soft); color: var(--navy-deep); }

a:focus-visible,
button:focus-visible,
input:focus-visible {
    outline: 2px solid var(--gold);
    outline-offset: 2px;
    border-radius: 2px;
}

h1, .display-font {
    font-family: 'Fraunces', 'Georgia', serif;
    font-optical-sizing: auto;
}

a { text-decoration: none; }

.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 1.1rem;
}

.eyebrow::before {
    content: "";
    display: block;
    width: 22px;
    height: 1px;
    background: var(--gold);
}

/* ================================================================
   NAVBAR — matches welcome.blade.php exactly
   ================================================================ */
.sas-navbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1000;
    padding: 1.35rem 0;
    background: transparent;
    transition: background 0.35s ease, box-shadow 0.35s ease, padding 0.35s ease;
}

.sas-navbar.scrolled {
    background: rgba(13, 24, 38, 0.92);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    padding: 0.95rem 0;
}

.sas-navbar .brand {
    font-family: 'Fraunces', serif;
    font-weight: 600;
    font-size: 1.05rem;
    letter-spacing: 0.01em;
    color: var(--navy-deep);
    display: flex;
    align-items: center;
    gap: 0.65rem;
    transition: color 0.35s ease;
}

.sas-navbar.scrolled .brand { color: #fff; }

.brand-mark {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1px solid currentColor;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Fraunces', serif;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    flex-shrink: 0;
}

.btn-nav-home {
    background: transparent;
    color: var(--navy-deep);
    border: 1px solid rgba(13, 24, 38, 0.25);
    border-radius: var(--radius-sm);
    padding: 0.5rem 1.4rem;
    font-weight: 600;
    font-size: 0.82rem;
    letter-spacing: 0.01em;
    transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}

.sas-navbar.scrolled .btn-nav-home { color: #fff; border-color: var(--border-navy); }

.btn-nav-home:hover { background: var(--gold); border-color: var(--gold); color: #fff; }

/* ================================================================
   AUTH SHELL — welcome intro (left) + form (right)
   ================================================================ */
.auth-shell {
    position: relative;
    display: flex;
    align-items: stretch;
    min-height: 100vh;
    overflow: hidden;
}

.auth-left {
    flex: 1 1 54%;
    background: linear-gradient(180deg, #FBFCFE 0%, #FFFFFF 100%);
    background-image:
        linear-gradient(180deg, #FBFCFE 0%, #FFFFFF 100%),
        radial-gradient(circle at 12% 8%, rgba(79,70,229,0.06) 0%, transparent 45%);
    color: var(--text-dark);
    padding: 7.5rem 5.5vw 4rem 6vw;
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    border-right: 1px solid var(--border);
}

.auth-right {
    flex: 1 1 46%;
    background: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rem 3rem;
    position: relative;
    z-index: 1;
}

.crest {
    width: 56px; height: 56px;
    border-radius: 50%;
    border: 1px solid rgba(79, 70, 229, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Fraunces', serif;
    font-weight: 600;
    font-size: 0.95rem;
    letter-spacing: 0.03em;
    color: var(--indigo);
    margin-bottom: 2.25rem;
}

.auth-left h1 {
    font-weight: 500;
    font-size: 3rem;
    line-height: 1.16;
    letter-spacing: -0.01em;
    color: var(--text-dark);
    margin-bottom: 1.25rem;
}

.auth-left h1 em { font-style: italic; color: var(--indigo); }

.auth-left .lead {
    font-size: 1.02rem;
    color: var(--text-muted);
    line-height: 1.8;
    max-width: 440px;
    margin-bottom: 3rem;
    font-weight: 400;
}

.ledger { max-width: 480px; border-top: 1px solid var(--border); }

.ledger-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.1rem 0.1rem;
    border-bottom: 1px solid var(--border);
    transition: padding-left 0.25s ease, border-color 0.25s ease;
}

.ledger-row:hover { padding-left: 0.6rem; border-color: rgba(79, 70, 229, 0.3); }

.ledger-row .ledger-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.86rem;
    color: var(--text-dark);
    font-weight: 500;
}

.ledger-row .ledger-label i { color: var(--indigo); font-size: 0.95rem; width: 18px; text-align: center; }

.ledger-row .ledger-value {
    font-family: 'Fraunces', serif;
    font-size: 0.95rem;
    color: var(--indigo);
    font-weight: 500;
    letter-spacing: 0.01em;
    text-align: right;
    white-space: nowrap;
}

.ledger-row .ledger-sub {
    display: block;
    font-family: 'Inter', sans-serif;
    font-size: 0.68rem;
    color: var(--text-muted);
    font-weight: 400;
    margin-top: 0.1rem;
}

/* ---- Card (login / forgot-password / reset-password) ---- */
.login-card {
    width: 100%;
    max-width: 420px;
    background: var(--paper);
    border: 1px solid var(--border);
    border-top: 2px solid var(--gold);
    border-radius: var(--radius-lg);
    padding: 3rem 2.75rem;
    box-shadow: var(--shadow-lift);
    animation: card-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes card-in {
    from { opacity: 0; transform: translateY(14px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (prefers-reduced-motion: reduce) {
    .login-card { animation: none; }
}

.login-success-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
    padding: 0.95rem 1.05rem;
    margin-bottom: 1.6rem;
    background: #F2F7F3;
    border: 1px solid #CFE0D2;
    border-left: 3px solid #4F7A58;
    border-radius: var(--radius-sm);
}

.success-icon { color: #4F7A58; font-size: 1.05rem; margin-top: 0.1rem; }

.login-title {
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 1.65rem;
    color: var(--navy-deep);
    margin-bottom: 0.4rem;
}

.login-subtitle {
    color: var(--text-muted);
    font-size: 0.88rem;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.login-card .form-label {
    font-weight: 600;
    font-size: 0.76rem;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.input-icon-group { position: relative; }

.input-icon-group .field-icon {
    position: absolute;
    top: 50%;
    left: 0.95rem;
    transform: translateY(-50%);
    color: var(--text-faint);
    font-size: 0.92rem;
    pointer-events: none;
}

.form-control {
    height: 48px;
    padding-left: 2.6rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--paper);
    font-size: 0.9rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:hover { border-color: #D2CBB4; }

.form-control:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 3px var(--gold-soft);
}

.input-icon-group .toggle-password {
    position: absolute;
    top: 50%;
    right: 0.85rem;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-faint);
    padding: 0.25rem;
    line-height: 1;
    transition: color 0.15s ease;
}

.input-icon-group .toggle-password:hover { color: var(--navy-deep); }

.link-forgot {
    color: var(--navy-deep);
    font-size: 0.82rem;
    font-weight: 600;
    border-bottom: 1px solid transparent;
}

.link-forgot:hover { border-bottom-color: var(--gold); color: var(--navy-deep); }

.form-check-label { font-size: 0.85rem; color: var(--text-muted); }
.form-check-input:checked { background-color: var(--indigo); border-color: var(--indigo); }
.form-check-input:focus { box-shadow: 0 0 0 3px var(--gold-soft); border-color: var(--gold); }

.btn-signin {
    background: linear-gradient(135deg, #6366F1, #4F46E5);
    color: #fff;
    border: 1px solid transparent;
    height: 50px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.88rem;
    letter-spacing: 0.02em;
    transition: box-shadow 0.25s ease, transform 0.15s ease;
}

.btn-signin:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(79, 70, 229, 0.28);
}

.btn-signin:disabled { opacity: 0.7; transform: none; }

.spinner-border-sm-custom { width: 1rem; height: 1rem; border-width: 0.14em; }

.btn-back-home {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text-dark);
    height: 44px;
    border-radius: var(--radius-sm);
    font-weight: 600;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease, border-color 0.2s ease;
}

.btn-back-home:hover {
    background: var(--paper);
    border-color: var(--navy-deep);
    color: var(--navy-deep);
}

@media (max-width: 991.98px) {
    .auth-shell { flex-direction: column; min-height: auto; }
    .auth-left { padding: 7.5rem 6vw 3rem; }
    .auth-right { padding: 3rem 6vw; }
    .auth-left h1 { font-size: 2.25rem; }
    .ledger { max-width: 100%; }
}

@media (max-width: 575.98px) {
    .auth-left h1 { font-size: 1.95rem; }
    .login-card { padding: 2.25rem 1.75rem; }
}

/* ================================================================
   MINIMAL FOOTER
   ================================================================ */
.sas-footer-minimal {
    background: var(--navy);
    color: var(--text-on-navy-muted);
    padding: 1.5rem 0;
    border-top: 1px solid var(--border-navy);
    font-size: 0.8rem;
    text-align: center;
}

.sas-footer-minimal a { color: var(--text-on-navy-muted); }
.sas-footer-minimal a:hover { color: var(--gold-light); }
</style>
</head>
<body>

<!-- ============================================================
     NAVIGATION BAR — matches welcome.blade.php exactly
     ============================================================ -->
<nav class="sas-navbar" id="sasNavbar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="{{ url('/') }}" class="brand">
            <span class="brand-mark">LTU</span>
            <span class="d-none d-sm-inline">Student Attendance &amp; Records</span>
        </a>

        <a href="{{ url('/') }}" class="btn-nav-home">
            <i class="bi bi-arrow-left me-1"></i>Back to Home
        </a>
    </div>
</nav>

<!-- ============================================================
     FORGOT PASSWORD — welcome panel (left) + request form (right)
     ============================================================ -->
<div class="auth-shell">

    <!-- ============================== LEFT: WELCOME + LEDGER ============================== -->
    <section class="auth-left">
        <div class="crest">LTU</div>

        <span class="eyebrow">Secure University Portal</span>

        <h1>Locked out?<br><em>Let's fix that.</em></h1>
        <p class="lead">
            Enter the email address linked to your account and we'll send you a secure link to reset your password.
        </p>

        <div class="ledger">
            <div class="ledger-row">
                <span class="ledger-label"><i class="bi bi-shield-check"></i>Reset Link Validity</span>
                <span class="ledger-value">60 min<span class="ledger-sub">Expires automatically</span></span>
            </div>
            <div class="ledger-row">
                <span class="ledger-label"><i class="bi bi-envelope-check"></i>Delivery</span>
                <span class="ledger-value">Email<span class="ledger-sub">Sent to your registered address</span></span>
            </div>
            <div class="ledger-row">
                <span class="ledger-label"><i class="bi bi-lock"></i>Account Security</span>
                <span class="ledger-value">Encrypted<span class="ledger-sub">Tokens are single-use</span></span>
            </div>
        </div>
    </section>

    <!-- ============================== RIGHT: FORGOT PASSWORD FORM ============================== -->
    <section class="auth-right">
        <div class="login-card">

            @if (session('status'))
                <div class="login-success-alert">
                    <i class="bi bi-check-circle-fill success-icon"></i>
                    <div>
                        <div class="fw-bold" style="font-size:0.9rem;">Reset Link Sent</div>
                        <small class="text-muted">{{ session('status') }}</small>
                    </div>
                </div>
            @endif

            <h2 class="login-title">Forgot Password</h2>
            <p class="login-subtitle">No worries — enter your email and we'll send a password reset link.</p>

            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 small" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger py-2 px-3 small" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form id="forgotForm" action="" method="POST" novalidate>
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-icon-group">
                        <i class="bi bi-envelope field-icon"></i>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@ltu.edu"
                            required
                            autofocus
                            autocomplete="username"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-signin w-100 text-white d-flex align-items-center justify-content-center gap-2 mt-3" id="forgotBtn">
                    <span class="spinner-border spinner-border-sm-custom d-none" id="forgotSpinner" role="status" aria-hidden="true"></span>
                    <span id="forgotBtnText">Send Reset Link</span>
                </button>

                <a href="{{ url('login') }}" class="btn btn-back-home w-100 mt-2 text-center d-block">
                    Back to Sign In
                </a>
            </form>

        </div>
    </section>

</div>

<!-- ============================================================
     MINIMAL FOOTER
     ============================================================ -->
<footer class="sas-footer-minimal">
    <div class="container">
        &copy; {{ date('Y') }} Student Attendance &amp; Academic Record Management System &middot; Lumbini Technological University
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    // ------------------------------------------------------------
    // Navbar: transparent at top, deep navy + blurred once scrolled
    // ------------------------------------------------------------
    const navbar = document.getElementById('sasNavbar');
    function updateNavbar() {
        if (window.scrollY > 24) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
    window.addEventListener('scroll', updateNavbar, { passive: true });
    updateNavbar();

    // ------------------------------------------------------------
    // Submit button loading state
    // ------------------------------------------------------------
    const forgotForm = document.getElementById('forgotForm');
    const forgotBtn = document.getElementById('forgotBtn');
    const forgotSpinner = document.getElementById('forgotSpinner');
    const forgotBtnText = document.getElementById('forgotBtnText');
    if (forgotForm && forgotBtn) {
        forgotForm.addEventListener('submit', () => {
            forgotBtn.setAttribute('disabled', 'disabled');
            if (forgotSpinner) forgotSpinner.classList.remove('d-none');
            if (forgotBtnText) forgotBtnText.textContent = 'Sending link…';
        });
    }

    // ------------------------------------------------------------
    // Auto-dismiss success alerts
    // ------------------------------------------------------------
    setTimeout(() => {
        document.querySelectorAll('.login-success-alert').forEach((alertEl) => {
            alertEl.style.transition = 'opacity 0.4s ease';
            alertEl.style.opacity = '0';
            setTimeout(() => alertEl.remove(), 400);
        });
    }, 4500);
})();
</script>

</body>
</html>