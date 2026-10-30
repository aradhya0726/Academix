<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Sign In') | LTU Academic</title>
    <meta name="description" content="@yield('description', 'Sign in to LTU Academic to access attendance, assignments and notices for your role.')">
    <meta name="theme-color" content="#101828">

    <link rel="icon" href="https://ltu.edu.np/favicon.ico">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Fonts (same as welcome page) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #3730A3;
            --primary-light: #EEF2FF;
            --navy: #101828;
            --navy-deep: #0B1220;
            --white: #FFFFFF;
            --surface: #F7F8FC;
            --border: #E7EAF1;
            --muted: #667085;
            --success: #059669;
            --danger: #B42318;
            --radius: 18px;
            --shadow: 0 12px 35px rgba(16, 24, 40, .07);
            --shadow-hover: 0 20px 45px rgba(16, 24, 40, .12);
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; scroll-padding-top: 90px; }

        body {
            margin: 0;
            font-family: 'DM Sans', sans-serif;
            color: var(--navy);
            background: var(--white);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        a { text-decoration: none; }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline: 3px solid #A5B4FC;
            outline-offset: 4px;
        }

        h1, h2, h3, h4, h5 { font-family: 'Playfair Display', Georgia, serif; }

        /* ---------- NAVIGATION (same as welcome) ---------- */
        .sas-navbar {
            position: fixed;
            inset: 0 0 auto;
            z-index: 1000;
            padding: 18px 0;
            transition: .3s ease;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid transparent;
        }

        .sas-navbar.scrolled {
            padding: 11px 0;
            background: rgba(11,18,32,.96);
            border-bottom-color: rgba(255,255,255,.08);
            box-shadow: 0 8px 30px rgba(0,0,0,.1);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            color: var(--navy);
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: -.03em;
        }

        .brand:hover { color: var(--navy); }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            color: #fff;
            background: var(--primary);
            font-size: .77rem;
            font-weight: 800;
            letter-spacing: .04em;
            box-shadow: 0 5px 15px rgba(79,70,229,.25);
            flex-shrink: 0;
        }

        .sas-navbar.scrolled .brand { color: #fff; }

        .nav-links { display: flex; align-items: center; gap: 5px; }

        .nav-links a {
            padding: 10px 12px;
            color: #475467;
            font-size: .84rem;
            font-weight: 700;
            transition: .2s;
        }

        .sas-navbar.scrolled .nav-links a { color: #CBD5E1; }
        .nav-links a:hover { color: var(--primary); }
        .sas-navbar.scrolled .nav-links a:hover { color: #fff; }

        .nav-actions { display: flex; align-items: center; gap: 10px; }

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 19px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-size: .84rem;
            font-weight: 800;
            transition: .2s;
            border: 1px solid var(--primary);
        }

        .btn-login:hover { background: var(--primary-dark); color: #fff; transform: translateY(-2px); }

        .btn-university {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 13px;
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--navy);
            font-size: .8rem;
            font-weight: 700;
            transition: .2s;
        }

        .btn-university:hover { color: var(--primary); border-color: var(--primary); }
        .sas-navbar.scrolled .btn-university { color: #fff; border-color: rgba(255,255,255,.25); }

        .nav-toggle {
            display: none;
            width: 42px;
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: transparent;
            color: var(--navy);
            font-size: 1.25rem;
        }

        .sas-navbar.scrolled .nav-toggle { color: #fff; border-color: rgba(255,255,255,.2); }

        /* ---------- BUTTONS (same as welcome) ---------- */
        .btn-primary-custom,
        .btn-secondary-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 22px;
            border-radius: 11px;
            font-size: .9rem;
            font-weight: 800;
            transition: .25s;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary-custom {
            background: var(--primary);
            border: 1px solid var(--primary);
            color: #fff;
            box-shadow: 0 8px 20px rgba(79,70,229,.2);
        }

        .btn-primary-custom:hover {
            color: #fff;
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(79,70,229,.25);
        }

        .btn-primary-custom:disabled { opacity: .75; transform: none; cursor: not-allowed; }

        .btn-secondary-custom {
            background: #fff;
            color: var(--navy);
            border: 1px solid var(--border);
        }

        .btn-secondary-custom:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        /* ---------- AUTH HERO SECTION ---------- */
        .auth-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 140px 0 90px;
            background:
                radial-gradient(circle at 85% 18%, rgba(79,70,229,.12), transparent 32%),
                radial-gradient(circle at 5% 85%, rgba(99,102,241,.06), transparent 30%),
                linear-gradient(180deg,#F8FAFF 0%,#FFFFFF 100%);
            overflow: hidden;
        }

        .auth-section::before {
            content: "";
            position: absolute;
            width: 440px;
            height: 440px;
            border: 1px solid rgba(79,70,229,.09);
            border-radius: 50%;
            top: 70px;
            right: -180px;
            pointer-events: none;
        }

        .auth-section > .container { position: relative; z-index: 1; }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 9px 14px;
            border: 1px solid rgba(79,70,229,.15);
            border-radius: 30px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: .73rem;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
            margin-bottom: 25px;
        }

        .hero-title {
            max-width: 560px;
            font-size: clamp(2.4rem, 4.6vw, 3.6rem);
            font-weight: 600;
            line-height: 1.12;
            letter-spacing: -.045em;
            margin-bottom: 22px;
        }

        .hero-title span { color: var(--primary); font-style: italic; }

        .hero-description {
            max-width: 500px;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.9;
            margin-bottom: 30px;
        }

        .hero-note {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 22px;
            color: var(--muted);
            font-size: .79rem;
        }

        .hero-note i { color: var(--success); }

        /* Role preview list (styled like the welcome dashboard preview) */
        .role-preview {
            max-width: 480px;
            padding: 20px;
            border: 1px solid rgba(230,233,240,.9);
            border-radius: 21px;
            background: rgba(255,255,255,.96);
            box-shadow: 0 25px 70px rgba(16,24,40,.08);
        }

        .preview-section-label {
            margin-bottom: 12px;
            color: var(--muted);
            font-size: .7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .preview-item {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px;
            margin-bottom: 10px;
            border: 1px solid var(--border);
            border-radius: 13px;
            transition: .2s;
        }

        .preview-item:last-child { margin-bottom: 0; }
        .preview-item:hover { background: #FAFAFF; border-color: #C7D2FE; }

        .preview-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .preview-item strong { display: block; font-size: .81rem; font-weight: 800; }
        .preview-item small { display: block; margin-top: 3px; color: var(--muted); font-size: .69rem; }

        /* ---------- AUTH CARD ---------- */
        .auth-card {
            width: 100%;
            max-width: 460px;
            margin-left: auto;
            padding: 38px;
            border: 1px solid rgba(230,233,240,.9);
            border-radius: 21px;
            background: #fff;
            box-shadow: 0 25px 70px rgba(16,24,40,.13);
        }

        .auth-card-title {
            font-size: 1.85rem;
            font-weight: 600;
            letter-spacing: -.03em;
            margin-bottom: 8px;
        }

        .auth-card-subtitle {
            color: var(--muted);
            font-size: .88rem;
            line-height: 1.7;
            margin-bottom: 26px;
        }

        .auth-alert {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 13px 15px;
            margin-bottom: 22px;
            border-radius: 12px;
            font-size: .84rem;
            line-height: 1.6;
        }

        .auth-alert i { margin-top: 2px; font-size: 1rem; }

        .auth-alert.success { background: #ECFDF3; border: 1px solid #ABEFC6; color: #067647; }
        .auth-alert.error   { background: #FEF3F2; border: 1px solid #FECDCA; color: var(--danger); }

        .auth-card .form-label {
            font-size: .8rem;
            font-weight: 700;
            color: #344054;
            margin-bottom: 8px;
        }

        .input-icon-group { position: relative; }

        .input-icon-group .field-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #98A2B3;
            font-size: .95rem;
            pointer-events: none;
            z-index: 2;
        }

        .auth-card .form-control {
            height: 50px;
            padding-left: 2.75rem;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fff;
            font-size: .9rem;
            font-family: inherit;
            color: var(--navy);
            transition: border-color .2s, box-shadow .2s;
        }

        .auth-card .form-control::placeholder { color: #98A2B3; }
        .auth-card .form-control:hover { border-color: #C7D2FE; }

        .auth-card .form-control:focus,
        .auth-card .form-control:focus-visible {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79,70,229,.12);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            background: none;
            border: 0;
            border-radius: 9px;
            color: #98A2B3;
            transition: .2s;
            z-index: 2;
        }

        .toggle-password:hover { color: var(--primary); background: var(--primary-light); }

        .form-check-label { font-size: .84rem; color: #475467; }
        .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
        .form-check-input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79,70,229,.12); }

        .link-forgot { color: var(--primary); font-size: .84rem; font-weight: 800; transition: .2s; }
        .link-forgot:hover { color: var(--primary-dark); text-decoration: underline; }

        .spinner-sm { width: 1rem; height: 1rem; border-width: .14em; }

        /* ---------- FOOTER (same as welcome) ---------- */
        .footer { padding: 60px 0 0; background: var(--navy-deep); color: #CBD5E1; }

        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            color: #fff;
            font-weight: 800;
            font-size: 1.08rem;
            margin-bottom: 17px;
        }

        .footer-brand:hover { color: #fff; }

        .footer-description { max-width: 370px; color: #98A2B3; font-size: .84rem; line-height: 1.9; }

        .footer h3 {
            font-family: 'DM Sans', sans-serif;
            color: #fff;
            font-size: .87rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .footer-link {
            display: flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            margin-bottom: 13px;
            color: #98A2B3;
            font-size: .82rem;
            transition: .2s;
        }

        .footer-link:hover { color: #fff; transform: translateX(3px); }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 40px;
            padding: 22px 0;
            border-top: 1px solid rgba(255,255,255,.1);
            color: #98A2B3;
            font-size: .77rem;
        }

        .developer-credit strong { color: #fff; font-weight: 800; }

        /* ---------- ANIMATION ---------- */
        .fade-up {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .65s ease, transform .65s ease;
        }

        .fade-up.in-view { opacity: 1; transform: translateY(0); }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
            .fade-up { opacity: 1; transform: none; }
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 991.98px) {
            .nav-toggle { display: inline-grid; place-items: center; }

            .nav-links {
                position: fixed;
                top: 0;
                right: 0;
                z-index: 1002;
                width: min(85vw,350px);
                height: 100dvh;
                padding: 100px 28px 30px;
                background: #fff;
                flex-direction: column;
                align-items: stretch;
                gap: 5px;
                transform: translateX(100%);
                transition: transform .3s ease;
            }

            .nav-links.open { transform: translateX(0); }

            .nav-links a,
            .sas-navbar.scrolled .nav-links a {
                padding: 14px 5px;
                color: var(--navy);
                border-bottom: 1px solid var(--border);
            }

            .nav-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1001;
                background: rgba(16,24,40,.5);
                opacity: 0;
                pointer-events: none;
                transition: .3s;
            }

            .nav-backdrop.open { opacity: 1; pointer-events: auto; }

            .auth-section { padding-top: 125px; min-height: auto; }
            .auth-card { margin: 0 auto; }
            .role-preview { max-width: 100%; }
        }

        @media (max-width: 575.98px) {
            .auth-section { padding: 110px 0 60px; }
            .auth-card { padding: 28px 22px; }
            .hero-title { font-size: 2.2rem; }
            .btn-university { display: none; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
    </style>

    <noscript><style>.fade-up { opacity: 1; transform: none; }</style></noscript>

    @stack('styles')
</head>

<body>

<a href="#main-content" class="visually-hidden-focusable position-absolute top-0 start-0 p-3 bg-white">
    Skip to content
</a>

<!-- NAVIGATION -->
<nav class="sas-navbar" id="sasNavbar">
    <div class="container d-flex align-items-center justify-content-between">

        <a href="{{ url('/') }}" class="brand" aria-label="LTU Academic home">
            <span class="brand-mark">LTU</span>
            <span>LTU Academic</span>
        </a>

        <div class="nav-links" id="mobileNavLinks">
            <a href="{{ url('/') }}#home" class="nav-link">Home</a>
            <a href="{{ url('/') }}#features" class="nav-link">Features</a>
            <a href="{{ url('/') }}#modules" class="nav-link">Modules</a>
            <a href="{{ url('/') }}#roles" class="nav-link">User Roles</a>
            <a href="{{ url('/') }}#about" class="nav-link">About</a>
        </div>

        <div class="nav-actions">

            <a href="https://ltu.edu.np/"
               target="_blank"
               rel="noopener noreferrer"
               class="btn-university">
                <i class="bi bi-globe2"></i>
                University Website
                <i class="bi bi-box-arrow-up-right"></i>
            </a>

            <a href="{{ url('/') }}" class="btn-login">
                <i class="bi bi-arrow-left"></i>
                Back to Home
            </a>

            <button type="button"
                    class="nav-toggle"
                    id="navToggle"
                    aria-label="Open navigation menu"
                    aria-expanded="false"
                    aria-controls="mobileNavLinks">
                <i class="bi bi-list" id="navToggleIcon"></i>
            </button>

        </div>
    </div>

    <div class="nav-backdrop" id="navBackdrop"></div>
</nav>

<main id="main-content">
    @yield('content')
</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">

        <div class="row g-5">

            <div class="col-lg-5">

                <a href="{{ url('/') }}" class="footer-brand">
                    <span class="brand-mark">LTU</span>
                    LTU Academic
                </a>

                <p class="footer-description">
                    A centralized academic management platform
                    for managing users, academic structures,
                    attendance, assignments and notices.
                </p>

                <a href="https://ltu.edu.np/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="footer-link">
                    <i class="bi bi-globe2"></i>
                    Official University Website
                    <i class="bi bi-box-arrow-up-right"></i>
                </a>

            </div>

            <div class="col-6 col-lg-3">
                <h3>Explore</h3>
                <a href="{{ url('/') }}#home" class="footer-link">Home</a>
                <a href="{{ url('/') }}#features" class="footer-link">Features</a>
                <a href="{{ url('/') }}#modules" class="footer-link">Modules</a>
                <a href="{{ url('/') }}#roles" class="footer-link">User Roles</a>
                <a href="{{ url('/') }}#about" class="footer-link">About</a>
            </div>

            <div class="col-6 col-lg-3">
                <h3>Account</h3>

                <a href="{{ route('login') }}" class="footer-link">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </a>

                <a href="{{ route('password.request') }}" class="footer-link">
                    <i class="bi bi-key"></i>
                    Forgot Password
                </a>
            </div>

        </div>

        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} LTU Academic. All rights reserved.</span>
            <span class="developer-credit">Developed by <strong>Aradhya Dev Nepal &amp; Co.</strong></span>
        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
    const navbar = document.getElementById('sasNavbar');

    function updateNavbar() {
        navbar.classList.toggle('scrolled', window.scrollY > 25);
    }
    window.addEventListener('scroll', updateNavbar, { passive: true });
    updateNavbar();

    // Mobile navigation
    const navToggle = document.getElementById('navToggle');
    const navToggleIcon = document.getElementById('navToggleIcon');
    const mobileNavLinks = document.getElementById('mobileNavLinks');
    const navBackdrop = document.getElementById('navBackdrop');

    function closeMobileNav() {
        mobileNavLinks.classList.remove('open');
        navBackdrop.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.setAttribute('aria-label', 'Open navigation menu');
        navToggleIcon.classList.remove('bi-x-lg');
        navToggleIcon.classList.add('bi-list');
    }

    function openMobileNav() {
        mobileNavLinks.classList.add('open');
        navBackdrop.classList.add('open');
        navToggle.setAttribute('aria-expanded', 'true');
        navToggle.setAttribute('aria-label', 'Close navigation menu');
        navToggleIcon.classList.remove('bi-list');
        navToggleIcon.classList.add('bi-x-lg');
    }

    if (navToggle && mobileNavLinks && navBackdrop) {
        navToggle.addEventListener('click', function () {
            mobileNavLinks.classList.contains('open') ? closeMobileNav() : openMobileNav();
        });
        navBackdrop.addEventListener('click', closeMobileNav);
        mobileNavLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMobileNav);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMobileNav();
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 991) closeMobileNav();
        });
    }

    // Reveal animations
    const fadeElements = document.querySelectorAll('.fade-up');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        fadeElements.forEach(function (el) { observer.observe(el); });
    } else {
        fadeElements.forEach(function (el) { el.classList.add('in-view'); });
    }
})();
</script>

@stack('scripts')

</body>
</html>