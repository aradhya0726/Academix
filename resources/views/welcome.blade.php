
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LTU Academic | Academic Management System</title>

    <meta name="description" content="LTU Academic is a centralized academic management system for managing users, faculty, classes, subjects, attendance, assignments and notices.">
    <meta name="theme-color" content="#101828">

    <meta property="og:title" content="LTU Academic | Academic Management System">
    <meta property="og:description" content="A centralized workspace for academic administration, attendance, assignments and communication.">
    <meta property="og:type" content="website">

    <link rel="icon" href="https://ltu.edu.np/favicon.ico">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Fonts -->
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
            --radius: 18px;
            --shadow: 0 12px 35px rgba(16, 24, 40, .07);
            --shadow-hover: 0 20px 45px rgba(16, 24, 40, .12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px;
        }

        body {
            margin: 0;
            font-family: 'DM Sans', sans-serif;
            color: var(--navy);
            background: var(--white);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
        }

        button:focus-visible,
        a:focus-visible {
            outline: 3px solid #A5B4FC;
            outline-offset: 4px;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .section {
            padding: 100px 0;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: var(--primary);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .13em;
            text-transform: uppercase;
            margin-bottom: 17px;
        }

        .section-label::before {
            content: "";
            width: 24px;
            height: 2px;
            background: var(--primary);
        }

        .section-heading {
            max-width: 680px;
            margin: 0 auto 52px;
            text-align: center;
        }

        .section-heading .section-label {
            justify-content: center;
        }

        .section-heading .section-label::before {
            display: none;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.8rem);
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: -.025em;
            margin-bottom: 17px;
        }

        .section-description {
            color: var(--muted);
            font-size: .96rem;
            line-height: 1.9;
            margin: 0;
        }

        /* NAVIGATION */

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

        .brand:hover {
            color: var(--navy);
        }

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

        .sas-navbar.scrolled .brand {
            color: #fff;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-links a {
            padding: 10px 12px;
            color: #475467;
            font-size: .84rem;
            font-weight: 700;
            transition: .2s;
        }

        .sas-navbar.scrolled .nav-links a {
            color: #CBD5E1;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .sas-navbar.scrolled .nav-links a:hover {
            color: #fff;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

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

        .btn-login:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-2px);
        }

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

        .btn-university:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        .sas-navbar.scrolled .btn-university {
            color: #fff;
            border-color: rgba(255,255,255,.25);
        }

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

        .sas-navbar.scrolled .nav-toggle {
            color: #fff;
            border-color: rgba(255,255,255,.2);
        }

        /* HERO */

        .hero {
            position: relative;
            padding: 165px 0 105px;
            background:
                radial-gradient(circle at 85% 18%, rgba(79,70,229,.12), transparent 32%),
                radial-gradient(circle at 5% 85%, rgba(99,102,241,.06), transparent 30%),
                linear-gradient(180deg,#F8FAFF 0%,#FFFFFF 100%);
            overflow: hidden;
        }

        .hero::before {
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

        .hero-badge i {
            font-size: .95rem;
        }

        .hero-title {
            max-width: 650px;
            font-size: clamp(2.8rem, 5.5vw, 4.5rem);
            font-weight: 600;
            line-height: 1.12;
            letter-spacing: -.045em;
            margin-bottom: 24px;
        }

        .hero-title span {
            color: var(--primary);
            font-style: italic;
        }

        .hero-description {
            max-width: 550px;
            color: var(--muted);
            font-size: 1.02rem;
            line-height: 1.95;
            margin-bottom: 32px;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

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

        .hero-note {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-top: 25px;
            color: var(--muted);
            font-size: .79rem;
        }

        .hero-note i {
            color: var(--success);
        }

        /* DASHBOARD PREVIEW */

        .hero-visual {
            position: relative;
            min-height: 440px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-decoration {
            position: absolute;
            width: 340px;
            height: 340px;
            border: 1px solid rgba(79,70,229,.12);
            border-radius: 50%;
        }

        .preview-decoration::before,
        .preview-decoration::after {
            content: "";
            position: absolute;
            inset: 25px;
            border: 1px solid rgba(79,70,229,.1);
            border-radius: 50%;
        }

        .preview-decoration::after {
            inset: 55px;
        }

        .dashboard-preview {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 465px;
            padding: 25px;
            border: 1px solid rgba(230,233,240,.9);
            border-radius: 21px;
            background: rgba(255,255,255,.96);
            box-shadow: 0 25px 70px rgba(16,24,40,.13);
        }

        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 19px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .preview-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-logo {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 11px;
            font-size: 1.1rem;
        }

        .preview-heading {
            font-size: .91rem;
            font-weight: 800;
        }

        .preview-caption {
            color: var(--muted);
            font-size: .7rem;
            margin-top: 3px;
        }

        .preview-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 9px;
            background: #ECFDF3;
            color: #027A48;
            border-radius: 20px;
            font-size: .67rem;
            font-weight: 800;
        }

        .preview-status::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #12B76A;
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

        .preview-item:last-child {
            margin-bottom: 0;
        }

        .preview-item:hover {
            background: #FAFAFF;
            border-color: #C7D2FE;
        }

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

        .preview-item strong {
            display: block;
            font-size: .81rem;
            font-weight: 800;
        }

        .preview-item small {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: .69rem;
        }

        .preview-arrow {
            margin-left: auto;
            color: #98A2B3;
        }

        /* FEATURE CARDS */

        .features {
            background: #fff;
        }

        .feature-card {
            height: 100%;
            padding: 29px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: #fff;
            transition: .25s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: #C7D2FE;
            box-shadow: var(--shadow-hover);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            display: grid;
            place-items: center;
            margin-bottom: 21px;
            border-radius: 14px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 1.35rem;
        }

        .feature-card h3 {
            font-family: 'DM Sans', sans-serif;
            font-size: 1.04rem;
            font-weight: 800;
            margin-bottom: 11px;
        }

        .feature-card p {
            color: var(--muted);
            font-size: .85rem;
            line-height: 1.85;
            margin: 0;
        }

        /* MODULES */

        .modules {
            background: var(--surface);
        }

        .module-card {
            height: 100%;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 21px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            transition: .22s;
        }

        .module-card:hover {
            transform: translateY(-4px);
            border-color: #C7D2FE;
            box-shadow: var(--shadow);
        }

        .module-icon {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 12px;
            font-size: 1.12rem;
        }

        .module-card h3 {
            font-family: 'DM Sans', sans-serif;
            font-size: .88rem;
            font-weight: 800;
            margin: 2px 0 6px;
        }

        .module-card p {
            color: var(--muted);
            font-size: .77rem;
            line-height: 1.7;
            margin: 0;
        }

        /* USER ROLES */

        .roles {
            background: #fff;
        }

        .role-card {
            height: 100%;
            padding: 32px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: #fff;
            transition: .25s;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: #C7D2FE;
        }

        .role-icon {
            width: 57px;
            height: 57px;
            display: grid;
            place-items: center;
            margin-bottom: 22px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 16px;
            font-size: 1.5rem;
        }

        .role-card h3 {
            font-size: 1.28rem;
            margin-bottom: 12px;
        }

        .role-card > p {
            color: var(--muted);
            font-size: .85rem;
            line-height: 1.85;
            margin-bottom: 22px;
        }

        .role-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .role-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 13px;
            color: #344054;
            font-size: .82rem;
        }

        .role-list li i {
            color: var(--success);
            margin-top: 2px;
        }

        /* WORKFLOW */

        .workflow {
            background: var(--surface);
        }

        .workflow-card {
            height: 100%;
            padding: 25px 15px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 15px;
            text-align: center;
        }

        .workflow-number {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            margin: 0 auto 17px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            font-size: .83rem;
            font-weight: 800;
            box-shadow: 0 6px 15px rgba(79,70,229,.2);
        }

        .workflow-card h3 {
            font-family: 'DM Sans', sans-serif;
            font-size: .87rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .workflow-card p {
            color: var(--muted);
            font-size: .76rem;
            line-height: 1.7;
            margin: 0;
        }

        /* ABOUT */

        .about-panel {
            position: relative;
            overflow: hidden;
            padding: 55px;
            border-radius: 22px;
            background:
                radial-gradient(circle at 90% 10%, rgba(129,140,248,.22), transparent 30%),
                linear-gradient(120deg,#101828,#172554);
            color: #fff;
        }

        .about-panel::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            right: -120px;
            bottom: -170px;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 50%;
        }

        .about-panel .section-label {
            color: #A5B4FC;
        }

        .about-panel .section-label::before {
            background: #A5B4FC;
        }

        .about-panel h2 {
            max-width: 600px;
            font-size: clamp(1.8rem,3.5vw,2.5rem);
            margin-bottom: 18px;
        }

        .about-panel p {
            position: relative;
            z-index: 1;
            max-width: 750px;
            color: #CBD5E1;
            font-size: .92rem;
            line-height: 1.95;
            margin: 0;
        }

        .university-link {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-top: 25px;
            padding: 11px 16px;
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 10px;
            color: #fff;
            font-size: .83rem;
            font-weight: 700;
            transition: .2s;
        }

        .university-link:hover {
            background: #fff;
            color: var(--navy);
        }

        /* CTA */

        .cta {
            padding: 85px 0;
            background: var(--primary);
            color: #fff;
            text-align: center;
        }

        .cta h2 {
            font-size: clamp(1.9rem,4vw,2.6rem);
            margin-bottom: 15px;
        }

        .cta p {
            max-width: 590px;
            margin: 0 auto 28px;
            color: rgba(255,255,255,.83);
            font-size: .92rem;
            line-height: 1.85;
        }

        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 14px 23px;
            border-radius: 11px;
            background: #fff;
            color: var(--primary);
            font-size: .88rem;
            font-weight: 800;
            transition: .2s;
        }

        .btn-cta:hover {
            background: #EEF2FF;
            color: var(--primary-dark);
            transform: translateY(-3px);
        }

        /* FOOTER */

        .footer {
            padding: 60px 0 0;
            background: var(--navy-deep);
            color: #CBD5E1;
        }

        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            color: #fff;
            font-weight: 800;
            font-size: 1.08rem;
            margin-bottom: 17px;
        }

        .footer-description {
            max-width: 370px;
            color: #98A2B3;
            font-size: .84rem;
            line-height: 1.9;
        }

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

        .footer-link:hover {
            color: #fff;
            transform: translateX(3px);
        }

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

        .developer-credit strong {
            color: #fff;
            font-weight: 800;
        }

        /* ANIMATION */

        .fade-up {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .65s ease, transform .65s ease;
        }

        .fade-up.in-view {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }

            .fade-up {
                opacity: 1;
                transform: none;
            }
        }

        /* RESPONSIVE */

        @media (max-width: 991.98px) {
            .nav-toggle {
                display: inline-grid;
                place-items: center;
            }

            .nav-links {
                position: fixed;
                top: 0;
                right: 0;
                z-index: 1002;
                width: min(85vw,350px);
                height: 100dvh;
                padding: 100px 28px 30px;
                background: #fff;
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 5px;
                transform: translateX(100%);
                transition: transform .3s ease;
            }

            .nav-links.open {
                transform: translateX(0);
            }

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

            .nav-backdrop.open {
                opacity: 1;
                pointer-events: auto;
            }

            .hero {
                padding-top: 145px;
            }

            .hero-visual {
                min-height: 390px;
            }
        }

        @media (max-width: 767.98px) {
            .section {
                padding: 70px 0;
            }

            .hero-title {
                font-size: 2.8rem;
            }

            .hero-visual {
                min-height: 350px;
            }

            .about-panel {
                padding: 35px 28px;
            }

            .cta {
                padding: 65px 0;
            }
        }

        @media (max-width: 575.98px) {
            .hero {
                padding: 125px 0 65px;
            }

            .hero-title {
                font-size: 2.35rem;
            }

            .hero-description {
                font-size: .9rem;
            }

            .hero-visual {
                min-height: 310px;
            }

            .dashboard-preview {
                padding: 17px;
            }

            .preview-item {
                gap: 10px;
                padding: 11px 9px;
            }

            .preview-icon {
                width: 37px;
                height: 37px;
            }

            .btn-university {
                display: none;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<!-- ACCESSIBILITY -->
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
            <a href="#home" class="nav-link">Home</a>
            <a href="#features" class="nav-link">Features</a>
            <a href="#modules" class="nav-link">Modules</a>
            <a href="#roles" class="nav-link">User Roles</a>
            <a href="#about" class="nav-link">About</a>
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

            <a href="{{ route('login') }}" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                Sign In
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

    <!-- HERO -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-6">

                    <div class="hero-badge">
                        <i class="bi bi-mortarboard-fill"></i>
                        Academic Management Platform
                    </div>

                    <h1 class="hero-title">
                        Bringing Academic
                        <span>Management</span>
                        Together.
                    </h1>

                    <p class="hero-description">
                        LTU Academic provides a centralized workspace
                        for academic administration. Manage users,
                        organize faculties, classes and subjects,
                        record attendance, handle assignments and
                        share notices through one system.
                    </p>

                    <div class="hero-buttons">

                        <a href="{{ route('login') }}" class="btn-primary-custom">
                            Access Your Account
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <a href="#features" class="btn-secondary-custom">
                            Explore the System
                            <i class="bi bi-arrow-down"></i>
                        </a>

                    </div>

                    <div class="hero-note">
                        <i class="bi bi-shield-check"></i>
                        Separate access for administrators, teachers and students.
                    </div>

                </div>

                <div class="col-lg-6">

                    <div class="hero-visual">

                        <div class="preview-decoration"></div>

                        <div class="dashboard-preview">

                            <div class="preview-header">

                                <div class="preview-brand">
                                    <div class="preview-logo">
                                        <i class="bi bi-mortarboard-fill"></i>
                                    </div>

                                    <div>
                                        <div class="preview-heading">LTU Academic</div>
                                        <div class="preview-caption">Academic workspace</div>
                                    </div>
                                </div>

                                <span class="preview-status">Workspace</span>

                            </div>

                            <div class="preview-section-label">
                                System Overview
                            </div>

                            <div class="preview-item">
                                <div class="preview-icon">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <strong>User Management</strong>
                                    <small>Students, teachers and administrators</small>
                                </div>
                                <i class="bi bi-chevron-right preview-arrow"></i>
                            </div>

                            <div class="preview-item">
                                <div class="preview-icon">
                                    <i class="bi bi-calendar2-check-fill"></i>
                                </div>
                                <div>
                                    <strong>Attendance Management</strong>
                                    <small>Record and review attendance</small>
                                </div>
                                <i class="bi bi-chevron-right preview-arrow"></i>
                            </div>

                            <div class="preview-item">
                                <div class="preview-icon">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div>
                                    <strong>Assignments</strong>
                                    <small>Manage academic assignments</small>
                                </div>
                                <i class="bi bi-chevron-right preview-arrow"></i>
                            </div>

                            <div class="preview-item">
                                <div class="preview-icon">
                                    <i class="bi bi-megaphone-fill"></i>
                                </div>
                                <div>
                                    <strong>Notices</strong>
                                    <small>Academic announcements</small>
                                </div>
                                <i class="bi bi-chevron-right preview-arrow"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>


    <!-- FEATURES -->
    <section class="features section" id="features">
        <div class="container">

            <div class="section-heading">
                <span class="section-label">Core Features</span>

                <h2 class="section-title">
                    Essential Tools for Academic Administration
                </h2>

                <p class="section-description">
                    The system brings important academic management
                    activities together in a structured workspace.
                </p>
            </div>

            <div class="row g-4">

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up">
                        <div class="feature-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3>User Management</h3>
                        <p>
                            Manage administrator, teacher and student
                            accounts through the academic management system.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up">
                        <div class="feature-icon">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <h3>Academic Management</h3>
                        <p>
                            Organize faculty, classes and subjects
                            to maintain academic information.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up">
                        <div class="feature-icon">
                            <i class="bi bi-calendar2-check-fill"></i>
                        </div>
                        <h3>Attendance Management</h3>
                        <p>
                            Record student attendance and review
                            attendance information through reports.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up">
                        <div class="feature-icon">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h3>Assignment Management</h3>
                        <p>
                            Create and manage assignments and provide
                            access to relevant users.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up">
                        <div class="feature-icon">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        <h3>Notice Management</h3>
                        <p>
                            Create and manage notices to communicate
                            academic information to the intended audience.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card fade-up">
                        <div class="feature-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h3>Role-Based Access</h3>
                        <p>
                            Provide separate system access and navigation
                            for administrators, teachers and students.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- MODULES -->
    <section class="modules section" id="modules">
        <div class="container">

            <div class="section-heading">
                <span class="section-label">System Modules</span>

                <h2 class="section-title">
                    Academic Activities in One Place
                </h2>

                <p class="section-description">
                    Explore the management areas available within
                    the LTU Academic system.
                </p>
            </div>

            <div class="row g-3">

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-person-badge-fill"></i></div>
                        <div>
                            <h3>Administrator Management</h3>
                            <p>Manage administrator accounts and system access.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <h3>Student Management</h3>
                            <p>Maintain and manage student accounts.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-person-workspace"></i></div>
                        <div>
                            <h3>Teacher Management</h3>
                            <p>Manage teacher accounts and records.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-building"></i></div>
                        <div>
                            <h3>Faculty Management</h3>
                            <p>Organize faculty information.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-easel2-fill"></i></div>
                        <div>
                            <h3>Class Management</h3>
                            <p>Maintain academic class information.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-book-half"></i></div>
                        <div>
                            <h3>Subject Management</h3>
                            <p>Organize and manage academic subjects.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-calendar2-check-fill"></i></div>
                        <div>
                            <h3>Attendance Reports</h3>
                            <p>Review and export attendance information.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-journal-text"></i></div>
                        <div>
                            <h3>Assignment Management</h3>
                            <p>Manage assignment activities.</p>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-4">
                    <div class="module-card fade-up">
                        <div class="module-icon"><i class="bi bi-megaphone-fill"></i></div>
                        <div>
                            <h3>Notice Management</h3>
                            <p>Create and view academic notices.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- USER ROLES -->
    <section class="roles section" id="roles">
        <div class="container">

            <div class="section-heading">
                <span class="section-label">System Access</span>

                <h2 class="section-title">
                    Access Designed Around User Roles
                </h2>

                <p class="section-description">
                    Each user role has access to the relevant
                    academic tools and activities.
                </p>
            </div>

            <div class="row g-4">

                <div class="col-lg-4">
                    <div class="role-card fade-up">

                        <div class="role-icon">
                            <i class="bi bi-person-gear"></i>
                        </div>

                        <h3>Administrator</h3>

                        <p>
                            Administrators manage academic structures
                            and oversee system activities.
                        </p>

                        <ul class="role-list">
                            <li><i class="bi bi-check-circle-fill"></i> Manage users</li>
                            <li><i class="bi bi-check-circle-fill"></i> Manage faculty, classes and subjects</li>
                            <li><i class="bi bi-check-circle-fill"></i> Manage assignments and notices</li>
                            <li><i class="bi bi-check-circle-fill"></i> Review attendance reports</li>
                        </ul>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="role-card fade-up">

                        <div class="role-icon">
                            <i class="bi bi-person-workspace"></i>
                        </div>

                        <h3>Teacher</h3>

                        <p>
                            Teachers use the system to manage
                            attendance, assignments and notices.
                        </p>

                        <ul class="role-list">
                            <li><i class="bi bi-check-circle-fill"></i> Mark student attendance</li>
                            <li><i class="bi bi-check-circle-fill"></i> Review attendance reports</li>
                            <li><i class="bi bi-check-circle-fill"></i> Create and manage assignments</li>
                            <li><i class="bi bi-check-circle-fill"></i> Manage and view notices</li>
                        </ul>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="role-card fade-up">

                        <div class="role-icon">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>

                        <h3>Student</h3>

                        <p>
                            Students can access their available
                            academic information and activities.
                        </p>

                        <ul class="role-list">
                            <li><i class="bi bi-check-circle-fill"></i> View personal attendance</li>
                            <li><i class="bi bi-check-circle-fill"></i> Access assignments</li>
                            <li><i class="bi bi-check-circle-fill"></i> View academic notices</li>
                        </ul>

                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- WORKFLOW -->
    <section class="workflow section">
        <div class="container">

            <div class="section-heading">
                <span class="section-label">Academic Workflow</span>

                <h2 class="section-title">
                    A Connected Academic Workflow
                </h2>

                <p class="section-description">
                    Dedicated modules connect academic administration,
                    attendance, assignments and communication.
                </p>
            </div>

            <div class="row g-3">

                <div class="col-6 col-md-4 col-lg">
                    <div class="workflow-card fade-up">
                        <div class="workflow-number">01</div>
                        <h3>User Management</h3>
                        <p>Manage system users and roles.</p>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg">
                    <div class="workflow-card fade-up">
                        <div class="workflow-number">02</div>
                        <h3>Academic Setup</h3>
                        <p>Organize faculty, classes and subjects.</p>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg">
                    <div class="workflow-card fade-up">
                        <div class="workflow-number">03</div>
                        <h3>Attendance</h3>
                        <p>Record and review attendance.</p>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg">
                    <div class="workflow-card fade-up">
                        <div class="workflow-number">04</div>
                        <h3>Assignments</h3>
                        <p>Manage assignment activities.</p>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg">
                    <div class="workflow-card fade-up">
                        <div class="workflow-number">05</div>
                        <h3>Communication</h3>
                        <p>Share academic notices.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- ABOUT -->
    <section class="section" id="about">
        <div class="container">

            <div class="about-panel fade-up">

                <span class="section-label">About the System</span>

                <h2>
                    One Centralized Academic Workspace
                </h2>

                <p>
                    LTU Academic is a web-based academic management
                    system designed to bring essential academic
                    administration tasks together. It provides
                    separate access for administrators, teachers
                    and students, helping organize user management,
                    academic structures, attendance, assignments
                    and notices in one place.
                </p>

                <a href="https://ltu.edu.np/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="university-link">
                    Visit Lumbini Technological University
                    <i class="bi bi-box-arrow-up-right"></i>
                </a>

            </div>

        </div>
    </section>


    <!-- CTA -->
    <section class="cta">
        <div class="container">

            <h2>Access Your Academic Workspace</h2>

            <p>
                Sign in using your authorized account to access
                the features and academic activities available
                for your user role.
            </p>

            <a href="{{ route('login') }}" class="btn-cta">
                Sign In to LTU Academic
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>
    </section>

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

                <a href="#home" class="footer-link">Home</a>
                <a href="#features" class="footer-link">Features</a>
                <a href="#modules" class="footer-link">Modules</a>
                <a href="#roles" class="footer-link">User Roles</a>
                <a href="#about" class="footer-link">About</a>

            </div>

            <div class="col-6 col-lg-3">

                <h3>Account</h3>

                <a href="{{ route('login') }}" class="footer-link">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sign In
                </a>

                <a href="https://ltu.edu.np/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="footer-link">
                    <i class="bi bi-building"></i>
                    University Website
                </a>

            </div>

        </div>

        <div class="footer-bottom">

            <span>
                &copy; {{ date('Y') }} LTU Academic. All rights reserved.
            </span>

            <span class="developer-credit">
                Developed by <strong>Aradhya Dev Nepal & Co.</strong>
            </span>

        </div>

    </div>
</footer>


<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {

    const navbar = document.getElementById('sasNavbar');

    // Navbar scroll effect
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
            const isOpen = mobileNavLinks.classList.contains('open');

            if (isOpen) {
                closeMobileNav();
            } else {
                openMobileNav();
            }
        });

        navBackdrop.addEventListener('click', closeMobileNav);

        mobileNavLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', closeMobileNav);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMobileNav();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 991) {
                closeMobileNav();
            }
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

        }, {
            threshold: 0.12
        });

        fadeElements.forEach(function (element) {
            observer.observe(element);
        });

    } else {

        fadeElements.forEach(function (element) {
            element.classList.add('in-view');
        });

    }

})();
</script>

</body>
</html>
