<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') | LTU Academic</title>

<script>
    // Runs before first paint so the page never flashes the wrong theme
    (function () {
        var savedTheme = localStorage.getItem('theme');
               if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    })();
</script>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --sidebar-bg: #111827;
        --sidebar-bg-hover: #1F2937;
        --page-bg: #F8FAFC;
        --card-bg: #FFFFFF;
        --primary: #2563EB;
        --primary-soft: rgba(37, 99, 235, 0.12);
        --border: #E5E7EB;
        --success: #16A34A;
        --warning: #F59E0B;
        --danger: #DC2626;
        --text-primary: #0F172A;
        --text-secondary: #64748B;
        --text-on-dark: #E5E7EB;
        --text-on-dark-muted: #9CA3AF;
        --sidebar-width-expanded: 270px;
        --sidebar-width-collapsed: 80px;
        --navbar-height: 72px;
        --radius-sm: 8px;
        --radius-md: 12px;
        --shadow-subtle: 0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06);
    }

    html[data-theme="dark"] {
        --page-bg: #0B1220;
        --card-bg: #111827;
        --border: #1F2937;
        --text-primary: #E5E7EB;
        --text-secondary: #94A3B8;
        --primary-soft: rgba(37, 99, 235, 0.18);
        --shadow-subtle: 0 1px 2px rgba(0, 0, 0, 0.35), 0 1px 3px rgba(0, 0, 0, 0.45);
    }

    html[data-theme="dark"] body { background: var(--page-bg); }

    * { box-sizing: border-box; }
    html, body { height: 100%; }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--page-bg);
        color: var(--text-primary);
        font-size: 14.5px;
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
    }

    a { text-decoration: none; }

    /* SIDEBAR */
    .app-sidebar {
        position: fixed;
        top: 0; left: 0; bottom: 0;
        width: var(--sidebar-width-expanded);
        background: var(--sidebar-bg);
        z-index: 1030;
        display: flex;
        flex-direction: column;
        transition: width 0.25s ease;
        overflow: hidden;
    }

    .app-sidebar.collapsed { width: var(--sidebar-width-collapsed); }

    .sidebar-brand {
        height: var(--navbar-height);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0 1.5rem;
        flex-shrink: 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        
    }

        .sidebar-brand .mark {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: #4F46E5;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 800; font-size: 0.77rem;
        letter-spacing: 0.04em;
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.25);
        flex-shrink: 0;
    }

    .sidebar-brand .brand-text {
        color: #fff; font-weight: 800; font-size: 1rem;
        letter-spacing: -0.03em;
        white-space: nowrap;
    }

    .app-sidebar.collapsed .brand-text,
    .app-sidebar.collapsed .nav-link-text,
    .app-sidebar.collapsed .nav-chevron {
        opacity: 0; pointer-events: none; position: absolute;
    }

    .sidebar-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 0.75rem 1.5rem;
    }

    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

    .nav-section-label {
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-on-dark-muted);
        padding: 1.1rem 0.75rem 0.4rem;
        white-space: nowrap;
    }

    .nav-item-flat, .nav-item-toggle {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.6rem 0.75rem;
        border-radius: var(--radius-sm);
        color: var(--text-on-dark);
        font-size: 0.87rem;
        font-weight: 500;
        margin-bottom: 0.15rem;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
        position: relative;
        white-space: nowrap;
    }

    .nav-item-flat:hover, .nav-item-toggle:hover {
        background: var(--sidebar-bg-hover);
        color: #fff;
    }

    .nav-item-flat.active { background: var(--primary-soft); color: #fff; }

    .nav-item-flat.active::before {
        content: "";
        position: absolute;
        left: -0.75rem; top: 0.3rem; bottom: 0.3rem;
        width: 3px;
        border-radius: 0 4px 4px 0;
        background: var(--primary);
    }

    .nav-item-flat i, .nav-item-toggle i.nav-icon {
        font-size: 1.05rem;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
        color: var(--text-on-dark-muted);
    }

    .nav-item-flat.active i, .nav-item-flat:hover i, .nav-item-toggle:hover i.nav-icon { color: #fff; }

    .nav-item-toggle.active {
        background: var(--primary-soft);
        color: #fff;
    }

    .nav-item-toggle.active i.nav-icon { color: #fff; }

    .nav-chevron {
        margin-left: auto;
        font-size: 0.75rem;
        transition: transform 0.2s ease;
        color: var(--text-on-dark-muted);
    }

    .nav-item-toggle[aria-expanded="true"] .nav-chevron { transform: rotate(90deg); }

    .nav-submenu { padding-left: 2.4rem; margin-top: 0.1rem; margin-bottom: 0.35rem; }

    .nav-submenu .nav-link-sub {
        display: block;
        padding: 0.5rem 0.6rem;
        border-radius: var(--radius-sm);
        color: var(--text-on-dark-muted);
        font-size: 0.83rem;
        font-weight: 500;
        margin-bottom: 0.1rem;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .nav-submenu .nav-link-sub:hover { background: var(--sidebar-bg-hover); color: #fff; }
    .nav-submenu .nav-link-sub.active { color: #fff; font-weight: 600; }

    .app-sidebar.collapsed .sidebar-brand { justify-content: center; padding: 0; }
    .app-sidebar.collapsed .nav-item-flat,
    .app-sidebar.collapsed .nav-item-toggle { justify-content: center; padding: 0.65rem; }
    .app-sidebar.collapsed .nav-section-label,
    .app-sidebar.collapsed .nav-submenu { display: none; }

    /* TOP NAVBAR */
    .app-navbar {
        position: fixed;
        top: 0; right: 0;
        left: var(--sidebar-width-expanded);
        height: var(--navbar-height);
        background: var(--card-bg);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.75rem;
        z-index: 1020;
        transition: left 0.25s ease;
    }

    .app-sidebar.collapsed ~ .app-navbar { left: var(--sidebar-width-collapsed); }

    .navbar-left { display: flex; align-items: center; gap: 1rem; flex: 1; }

    .sidebar-toggle-btn {
        border: none; background: transparent;
        color: var(--text-secondary);
        width: 36px; height: 36px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        transition: background 0.15s ease;
    }

    .sidebar-toggle-btn:hover { background: var(--page-bg); }

    .navbar-breadcrumb .breadcrumb {
        margin: 0;
        background: transparent;
        padding: 0;
        font-size: 0.83rem;
    }

    .navbar-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: var(--text-secondary);
        padding: 0 0.5rem;
    }

    .navbar-breadcrumb .breadcrumb-item a {
        color: var(--text-secondary);
        font-weight: 500;
        transition: color 0.15s ease;
    }

    .navbar-breadcrumb .breadcrumb-item a:hover { color: var(--primary); }

    .navbar-breadcrumb .breadcrumb-item.active {
        color: var(--text-primary);
        font-weight: 600;
    }

    .navbar-right { display: flex; align-items: center; gap: 0.4rem; flex-shrink: 0; }

    .session-badge {
        display: flex; align-items: center; gap: 0.4rem;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 0.78rem; font-weight: 600;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        margin-right: 0.5rem;
        white-space: nowrap;
    }

    .icon-btn {
        position: relative;
        border: none; background: transparent;
        width: 40px; height: 40px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        color: var(--text-secondary);
        font-size: 1.15rem;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .icon-btn:hover { background: var(--page-bg); color: var(--text-primary); }

    .navbar-divider { width: 1px; height: 28px; background: var(--border); margin: 0 0.5rem; }

    .profile-menu-btn {
        display: flex; align-items: center; gap: 0.65rem;
        border: none; background: transparent;
        padding: 0.35rem 0.5rem 0.35rem 0.35rem;
        border-radius: var(--radius-sm);
        transition: background 0.15s ease;
    }

    .profile-menu-btn:hover { background: var(--page-bg); }

    .profile-avatar {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: var(--primary);
        color: #fff; font-weight: 600; font-size: 0.8rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .profile-meta { text-align: left; line-height: 1.25; }
    .profile-meta .name { font-size: 0.83rem; font-weight: 600; color: var(--text-primary); display: block; }
    .profile-meta .role { font-size: 0.74rem; color: var(--text-secondary); display: block; }

    /* MAIN CONTENT */
    .app-main {
        margin-left: var(--sidebar-width-expanded);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: margin-left 0.25s ease;
    }

    .app-sidebar.collapsed ~ .app-main { margin-left: var(--sidebar-width-collapsed); }

    .content-wrapper {
        flex: 1;
        padding: 2.25rem 2.5rem;
        margin-top: var(--navbar-height);
        max-width: 1400px;
        width: 100%;
    }

    .page-heading { margin-bottom: 1.75rem; }

    .page-heading .eyebrow {
        font-size: 0.78rem; font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.35rem;
    }

    .page-heading h1 {
        font-size: 1.65rem; font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.01em;
    }

    /* KPI STAT CARDS */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-subtle);
        padding: 1.5rem;
    }

    .stat-card .stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-card .stat-icon {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .stat-icon.bg-users      { background: rgba(37, 99, 235, 0.1); color: var(--primary); }
    .stat-icon.bg-students   { background: rgba(22, 163, 74, 0.1); color: var(--success); }
    .stat-icon.bg-teachers   { background: rgba(245, 158, 11, 0.12); color: var(--warning); }
    .stat-icon.bg-attendance { background: rgba(220, 38, 38, 0.08); color: var(--danger); }

    .stat-card .stat-delta {
        font-size: 0.72rem; font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        display: inline-flex; align-items: center; gap: 0.25rem;
    }

    .stat-delta.up   { background: rgba(22, 163, 74, 0.1); color: var(--success); }
    .stat-delta.down { background: rgba(220, 38, 38, 0.1); color: var(--danger); }

    .stat-card .stat-value {
        font-size: 1.75rem; font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.01em;
        line-height: 1.2;
    }

    .stat-card .stat-label {
        font-size: 0.82rem;
        color: var(--text-secondary);
        font-weight: 500;
        margin-top: 0.15rem;
    }

    /* CHARTS */
    .chart-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 1.25rem;
    }

    .chart-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-subtle);
        padding: 1.5rem;
    }

    .chart-card .chart-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .chart-card .chart-title { font-size: 0.98rem; font-weight: 700; color: var(--text-primary); margin: 0; }
    .chart-card .chart-subtitle { font-size: 0.78rem; color: var(--text-secondary); margin-top: 0.15rem; }
    .chart-card canvas { max-height: 280px; }

    /* FOOTER */
    .app-footer {
        border-top: 1px solid var(--border);
        padding: 1.1rem 2.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.78rem;
        color: var(--text-secondary);
    }

    /* RESPONSIVE / MOBILE OFFCANVAS SIDEBAR */
    @media (max-width: 991.98px) {
        .app-sidebar {
            left: calc(-1 * var(--sidebar-width-expanded));
            width: var(--sidebar-width-expanded);
            transition: left 0.25s ease;
        }
        .app-sidebar.mobile-open { left: 0; box-shadow: 0 0 40px rgba(0,0,0,0.25); }
        .app-sidebar.collapsed { width: var(--sidebar-width-expanded); }
        .app-sidebar.collapsed .brand-text,
        .app-sidebar.collapsed .nav-link-text,
        .app-sidebar.collapsed .nav-chevron,
        .app-sidebar.collapsed .nav-section-label { opacity: 1; position: static; pointer-events: auto; display: block; }
        .app-sidebar.collapsed .nav-item-flat,
        .app-sidebar.collapsed .nav-item-toggle { justify-content: flex-start; padding: 0.6rem 0.75rem; }

        .app-navbar { left: 0; }
        .app-main { margin-left: 0; }

        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .chart-grid { grid-template-columns: 1fr; }

        .sidebar-backdrop {
            display: none;
            position: fixed; inset: 0;
            background: rgba(15, 23, 42, 0.4);
            z-index: 1025;
        }
        .sidebar-backdrop.show { display: block; }
    }

    @media (max-width: 575.98px) {
        .content-wrapper { padding: 1.25rem; }
        .app-footer { padding: 1rem 1.25rem; flex-direction: column; gap: 0.35rem; text-align: center; }
        .session-badge { display: none; }
        .stat-grid { grid-template-columns: 1fr; }
    }

    a:focus-visible, button:focus-visible, input:focus-visible {
        outline: 2px solid var(--primary);
        outline-offset: 2px;
    }


        /* PROFILE AVATAR + DROPDOWN */
    .profile-avatar { overflow: hidden; }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .profile-dropdown { min-width: 260px; padding: 0.5rem; border-radius: var(--radius-md); }
    .profile-dropdown .dropdown-item { border-radius: var(--radius-sm); padding: 0.55rem 0.75rem; font-size: 0.87rem; }

    .profile-dropdown-user { display: flex; align-items: center; gap: 0.75rem; padding: 0.45rem 0.6rem 0.55rem; }
    .profile-dropdown-user .profile-avatar { width: 44px; height: 44px; font-size: 0.9rem; }
    .profile-dropdown-user .name { display: block; font-size: 0.88rem; font-weight: 600; color: var(--text-primary); }
    .profile-dropdown-user .email {
        display: block; font-size: 0.76rem; color: var(--text-secondary);
        max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

        a.sidebar-brand { text-decoration: none; cursor: pointer; }
    a.sidebar-brand:hover .mark { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4); }
    a.sidebar-brand .mark { transition: transform 0.2s ease, box-shadow 0.2s ease; }
</style>

@stack('styles')
</head>
<body>

@include('layouts.header')

<div class="app-main">
    <div class="content-wrapper">
        @yield('content')
    </div>

    @include('layouts.footer')
</div>

</body>
</html>