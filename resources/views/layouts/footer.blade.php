
<footer class="app-footer">
    <span>&copy; {{ date('Y') }} LTU Academic. All rights reserved.</span>
    <span>v1.0.0</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle / mobile offcanvas
    (function () {
        const sidebar = document.getElementById('appSidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (!sidebar || !toggleBtn || !backdrop) return;

        function isMobile() {
            return window.matchMedia('(max-width: 991.98px)').matches;
        }

        toggleBtn.addEventListener('click', function () {
            if (isMobile()) {
                const isOpen = sidebar.classList.toggle('mobile-open');

                backdrop.classList.toggle('show', isOpen);
                toggleBtn.setAttribute('aria-expanded', String(isOpen));
            } else {
                const isCollapsed = sidebar.classList.toggle('collapsed');

                toggleBtn.setAttribute(
                    'aria-expanded',
                    String(!isCollapsed)
                );
            }
        });

        function closeMobileSidebar() {
            sidebar.classList.remove('mobile-open');
            backdrop.classList.remove('show');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }

        backdrop.addEventListener('click', closeMobileSidebar);

        window.addEventListener('resize', function () {
            if (!isMobile()) {
                closeMobileSidebar();
            }
        });
    })();

    // Dark mode toggle
    (function () {
        const root = document.documentElement;
        const toggleBtn = document.getElementById('themeToggleBtn');
        const toggleIcon = document.getElementById('themeToggleIcon');

        function setIcon(theme) {
            if (!toggleIcon) return;

            toggleIcon.className =
                theme === 'dark'
                    ? 'bi bi-sun'
                    : 'bi bi-moon-stars';
        }

        // Reflect the theme applied by the early head script
        const currentTheme =
            root.getAttribute('data-theme') === 'dark'
                ? 'dark'
                : 'light';

        setIcon(currentTheme);

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                const isDark =
                    root.getAttribute('data-theme') === 'dark';

                const nextTheme = isDark ? 'light' : 'dark';

                if (nextTheme === 'dark') {
                    root.setAttribute('data-theme', 'dark');
                    root.setAttribute('data-bs-theme', 'dark');
                } else {
                    root.removeAttribute('data-theme');
                    root.removeAttribute('data-bs-theme');
                }

                try {
                    localStorage.setItem('theme', nextTheme);
                } catch (error) {
                    console.warn('Unable to save theme preference:', error);
                }

                setIcon(nextTheme);
            });
        }
    })();
</script>

{{-- Page-specific scripts (e.g. Chart.js setup) --}}
@stack('scripts')
