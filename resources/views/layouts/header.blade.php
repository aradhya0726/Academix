<!-- SIDEBAR -->
<aside class="app-sidebar" id="appSidebar">
       <a href="{{ route(auth()->user()->dashboardRoute() ?? 'login') }}"
       class="sidebar-brand"
       aria-label="LTU Academic dashboard"
       title="Go to dashboard">
        <div class="mark">LTU</div>
        <span class="brand-text">LTU Academic</span>
    </a>

    <nav class="sidebar-scroll" id="sidebarNav">

        @if(Auth::user()->role == 'admin')

            @php
                $academicOpen = request()->routeIs('faculty.*')
                    || request()->routeIs('class.*')
                    || request()->routeIs('subject.*')
                    || request()->routeIs('admin.assignment.*');

                $adminActive = request()->routeIs(
                    'admin.list',
                    'admin.add',
                    'admin.insert',
                    'admin.edit',
                    'admin.update',
                    'admin.delete'
                );

                $studentActive = request()->routeIs(
                    'student.list',
                    'student.add',
                    'student.insert',
                    'student.edit',
                    'student.update',
                    'student.delete'
                );

                $teacherActive = request()->routeIs(
                    'teacher.list',
                    'teacher.add',
                    'teacher.insert',
                    'teacher.edit',
                    'teacher.update',
                    'teacher.delete'
                );

                $usersOpen = $adminActive || $studentActive || $teacherActive;

                $attendanceOpen = request()->routeIs('admin.attendance.*');
                $noticesOpen = request()->routeIs('admin.notice.*');
            @endphp

            <a href="{{ url('admin/dashboard') }}"
               class="nav-item-flat {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span class="nav-link-text">Dashboard</span>
            </a>

            <div class="nav-section-label">Academics</div>

            <div class="nav-item-toggle {{ $usersOpen ? 'active' : '' }}"
                 data-bs-toggle="collapse"
                 data-bs-target="#menuUsers"
                 aria-expanded="{{ $usersOpen ? 'true' : 'false' }}"
                 aria-controls="menuUsers"
                 role="button">

                <i class="bi bi-people-fill nav-icon"></i>
                <span class="nav-link-text">User Management</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </div>

            <div class="collapse nav-submenu {{ $usersOpen ? 'show' : '' }}"
                 id="menuUsers"
                 data-bs-parent="#sidebarNav">

                <a href="{{ route('admin.list') }}"
                   class="nav-link-sub {{ $adminActive ? 'active' : '' }}">
                    Admin
                </a>
                  <a href="{{ route('teacher.list') }}"
                   class="nav-link-sub {{ $teacherActive ? 'active' : '' }}">
                    Teachers
                </a>
                <a href="{{ route('student.list') }}"
                   class="nav-link-sub {{ $studentActive ? 'active' : '' }}">
                    Students
                </a>

              
            </div>

            <div class="nav-item-toggle {{ $academicOpen ? 'active' : '' }}"
                 data-bs-toggle="collapse"
                 data-bs-target="#menuAcademic"
                 aria-expanded="{{ $academicOpen ? 'true' : 'false' }}"
                 aria-controls="menuAcademic"
                 role="button">

                <i class="bi bi-mortarboard-fill nav-icon"></i>
                <span class="nav-link-text">Academic Management</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </div>

            <div class="collapse nav-submenu {{ $academicOpen ? 'show' : '' }}"
                 id="menuAcademic"
                 data-bs-parent="#sidebarNav">

                <a href="{{ route('faculty.list') }}"
                   class="nav-link-sub {{ request()->routeIs('faculty.*') ? 'active' : '' }}">
                    Faculty
                </a>

                <a href="{{ route('class.list') }}"
                   class="nav-link-sub {{ request()->routeIs('class.*') ? 'active' : '' }}">
                    Class
                </a>

                <a href="{{ route('subject.list') }}"
                   class="nav-link-sub {{ request()->routeIs('subject.*') ? 'active' : '' }}">
                    Subject
                </a>

                <a href="{{ route('admin.assignment.list') }}"
                   class="nav-link-sub {{ request()->routeIs('admin.assignment.*') ? 'active' : '' }}">
                    Assignments
                </a>
            </div>

            <div class="nav-section-label">Attendance</div>

            <div class="nav-item-toggle {{ $attendanceOpen ? 'active' : '' }}"
                 data-bs-toggle="collapse"
                 data-bs-target="#menuAttendance"
                 aria-expanded="{{ $attendanceOpen ? 'true' : 'false' }}"
                 aria-controls="menuAttendance"
                 role="button">

                <i class="bi bi-calendar2-check-fill nav-icon"></i>
                <span class="nav-link-text">Attendance Management</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </div>

            <div class="collapse nav-submenu {{ $attendanceOpen ? 'show' : '' }}"
                 id="menuAttendance"
                 data-bs-parent="#sidebarNav">

                <a href="{{ route('admin.attendance.reports') }}"
                   class="nav-link-sub {{ request()->routeIs('admin.attendance.reports') ? 'active' : '' }}">
                    Attendance Reports
                </a>
            </div>

            <div class="nav-section-label">Communication</div>

            <div class="nav-item-toggle {{ $noticesOpen ? 'active' : '' }}"
                 data-bs-toggle="collapse"
                 data-bs-target="#menuNotices"
                 aria-expanded="{{ $noticesOpen ? 'true' : 'false' }}"
                 aria-controls="menuNotices"
                 role="button">

                <i class="bi bi-megaphone-fill nav-icon"></i>
                <span class="nav-link-text">Notice Management</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </div>

            <div class="collapse nav-submenu {{ $noticesOpen ? 'show' : '' }}"
                 id="menuNotices"
                 data-bs-parent="#sidebarNav">

                <a href="{{ route('admin.notice.add') }}"
                   class="nav-link-sub {{ request()->routeIs('admin.notice.add', 'admin.notice.store') ? 'active' : '' }}">
                    Create Notice
                </a>

                <a href="{{ route('admin.notice.list', ['mine' => 1]) }}"
                   class="nav-link-sub {{ request()->routeIs('admin.notice.list') && request('mine') ? 'active' : '' }}">
                    My Notices
                </a>

                <a href="{{ route('admin.notice.list') }}"
                   class="nav-link-sub {{ (request()->routeIs('admin.notice.list') && !request('mine')) || request()->routeIs('admin.notice.edit', 'admin.notice.update') ? 'active' : '' }}">
                    View Notices
                </a>
            </div>

            <div class="nav-section-label">Insights</div>

            <a href="#" class="nav-item-flat">
                <i class="bi bi-bar-chart-line-fill"></i>
                <span class="nav-link-text">Reports</span>
            </a>

            <div class="nav-section-label">System</div>

            <a href="#" class="nav-item-flat">
                <i class="bi bi-gear-fill"></i>
                <span class="nav-link-text">Settings</span>
            </a>

        @elseif(Auth::user()->role == 'teacher')

            @php
                $teacherAttendanceOpen = request()->routeIs('teacher.attendance.*');
                $teacherAssignmentsOpen = request()->routeIs('teacher.assignment.*');
                $teacherNoticesOpen = request()->routeIs('teacher.notice.*');
            @endphp

            <a href="{{ url('teacher/dashboard') }}"
               class="nav-item-flat {{ request()->is('teacher/dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span class="nav-link-text">Dashboard</span>
            </a>

            <div class="nav-section-label">Attendance</div>

            <div class="nav-item-toggle {{ $teacherAttendanceOpen ? 'active' : '' }}"
                 data-bs-toggle="collapse"
                 data-bs-target="#menuTeacherAttendance"
                 aria-expanded="{{ $teacherAttendanceOpen ? 'true' : 'false' }}"
                 aria-controls="menuTeacherAttendance"
                 role="button">

                <i class="bi bi-calendar2-check-fill nav-icon"></i>
                <span class="nav-link-text">Attendance</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </div>

            <div class="collapse nav-submenu {{ $teacherAttendanceOpen ? 'show' : '' }}"
                 id="menuTeacherAttendance"
                 data-bs-parent="#sidebarNav">

                <a href="{{ route('teacher.attendance.index') }}"
                   class="nav-link-sub {{ request()->routeIs('teacher.attendance.index') ? 'active' : '' }}">
                    Mark Attendance
                </a>

                <a href="{{ route('teacher.attendance.reports') }}"
                   class="nav-link-sub {{ request()->routeIs('teacher.attendance.reports') ? 'active' : '' }}">
                    Attendance Reports
                </a>
            </div>

            <div class="nav-section-label">Academics</div>

            <div class="nav-item-toggle {{ $teacherAssignmentsOpen ? 'active' : '' }}"
                 data-bs-toggle="collapse"
                 data-bs-target="#menuTeacherAssignments"
                 aria-expanded="{{ $teacherAssignmentsOpen ? 'true' : 'false' }}"
                 aria-controls="menuTeacherAssignments"
                 role="button">

                <i class="bi bi-journal-text nav-icon"></i>
                <span class="nav-link-text">Assignments</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </div>

            <div class="collapse nav-submenu {{ $teacherAssignmentsOpen ? 'show' : '' }}"
                 id="menuTeacherAssignments"
                 data-bs-parent="#sidebarNav">

                <a href="{{ route('teacher.assignment.add') }}"
                   class="nav-link-sub {{ request()->routeIs('teacher.assignment.add', 'teacher.assignment.store') ? 'active' : '' }}">
                    Create Assignment
                </a>

                <a href="{{ route('teacher.assignment.list') }}"
                   class="nav-link-sub {{ request()->routeIs('teacher.assignment.list', 'teacher.assignment.edit', 'teacher.assignment.update', 'teacher.assignment.submissions') ? 'active' : '' }}">
                    My Assignments
                </a>
            </div>

            <div class="nav-section-label">Communication</div>

            <div class="nav-item-toggle {{ $teacherNoticesOpen ? 'active' : '' }}"
                 data-bs-toggle="collapse"
                 data-bs-target="#menuTeacherNotices"
                 aria-expanded="{{ $teacherNoticesOpen ? 'true' : 'false' }}"
                 aria-controls="menuTeacherNotices"
                 role="button">

                <i class="bi bi-megaphone-fill nav-icon"></i>
                <span class="nav-link-text">Notice Management</span>
                <i class="bi bi-chevron-right nav-chevron"></i>
            </div>

            <div class="collapse nav-submenu {{ $teacherNoticesOpen ? 'show' : '' }}"
                 id="menuTeacherNotices"
                 data-bs-parent="#sidebarNav">

                <a href="{{ route('teacher.notice.add') }}"
                   class="nav-link-sub {{ request()->routeIs('teacher.notice.add', 'teacher.notice.store') ? 'active' : '' }}">
                    Create Notice
                </a>

                <a href="{{ route('teacher.notice.list') }}"
                   class="nav-link-sub {{ request()->routeIs('teacher.notice.list', 'teacher.notice.edit', 'teacher.notice.update') ? 'active' : '' }}">
                    My Notices
                </a>

                <a href="{{ route('teacher.notice.view') }}"
                   class="nav-link-sub {{ request()->routeIs('teacher.notice.view', 'teacher.notice.show') ? 'active' : '' }}">
                    View Notices
                </a>
            </div>

        @elseif(Auth::user()->role == 'student')

            <a href="{{ url('student/dashboard') }}"
               class="nav-item-flat {{ request()->is('student/dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span class="nav-link-text">Dashboard</span>
            </a>

            <div class="nav-section-label">Attendance</div>

            <a href="{{ route('student.attendance.index') }}"
               class="nav-item-flat {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check-fill"></i>
                <span class="nav-link-text">My Attendance</span>
            </a>

            <div class="nav-section-label">Academics</div>

            <a href="{{ route('student.assignment.list') }}"
               class="nav-item-flat {{ request()->routeIs('student.assignment.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span class="nav-link-text">Assignments</span>
            </a>

            <div class="nav-section-label">Communication</div>

            <a href="{{ route('student.notice.view') }}"
               class="nav-item-flat {{ request()->routeIs('student.notice.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone-fill"></i>
                <span class="nav-link-text">Notices</span>
            </a>

        @endif

        <!-- SIDEBAR LOGOUT BUTTON -->
        <button type="submit"
                form="logout-form"
                class="nav-item-flat border-0 w-100 text-start"
                style="background: transparent;">

            <i class="bi bi-box-arrow-right"></i>
            <span class="nav-link-text">Logout</span>
        </button>

    </nav>
</aside>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>


<!-- TOP NAVBAR -->
<header class="app-navbar">

    <div class="navbar-left">
        <button class="sidebar-toggle-btn d-lg-none"
                id="sidebarToggle"
                type="button"
                aria-label="Toggle sidebar"
                aria-expanded="false"
                aria-controls="appSidebar">

            <i class="bi bi-layout-sidebar"></i>
        </button>
    </div>

    <div class="navbar-right">

        <span class="session-badge" title="Current date and time">
            <i class="bi bi-calendar3"></i>

            <span id="liveDate">{{ now()->format('D, d M Y') }}</span>

            <span aria-hidden="true">·</span>

            <i class="bi bi-clock"></i>

            <span id="liveTime" style="font-variant-numeric: tabular-nums;">
                {{ now()->format('h:i:s A') }}
            </span>
        </span>

        <button class="icon-btn"
                id="themeToggleBtn"
                type="button"
                aria-label="Toggle dark mode">

            <i class="bi bi-moon-stars" id="themeToggleIcon"></i>
        </button>

        <div class="navbar-divider"></div>

        <div class="dropdown">

            <button class="profile-menu-btn dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">

                             <div class="profile-avatar">
                    @if (auth()->user()->photoUrl())
                        <img src="{{ auth()->user()->photoUrl() }}" alt="{{ auth()->user()->name }}">
                    @else
                        {{ auth()->user()->initials() }}
                    @endif
                </div>

                <div class="profile-meta d-none d-md-block">
                    <span class="name">{{ auth()->user()->name }}</span>
                    <span class="role">{{ auth()->user()->role }}</span>
                </div>
            </button>

                        <ul class="dropdown-menu dropdown-menu-end mt-2 profile-dropdown">

                <li class="profile-dropdown-user">
                    <div class="profile-avatar">
                        @if (auth()->user()->photoUrl())
                            <img src="{{ auth()->user()->photoUrl() }}" alt="{{ auth()->user()->name }}">
                        @else
                            {{ auth()->user()->initials() }}
                        @endif
                    </div>
                    <div>
                        <span class="name">{{ auth()->user()->name }}</span>
                        <span class="email">{{ auth()->user()->email }}</span>
                    </div>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                        <i class="bi bi-person me-2"></i>
                        My Profile
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="{{ route('account.settings') }}">
                        <i class="bi bi-gear me-2"></i>
                        Account Settings
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <!-- PROFILE DROPDOWN LOGOUT BUTTON -->
                <li>
                    <button type="submit"
                            form="logout-form"
                            class="dropdown-item">

                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </button>
                </li>

            </ul>
        </div>
    </div>
</header>


<!-- SINGLE LOGOUT FORM FOR BOTH BUTTONS -->
<form id="logout-form"
      action="{{ route('logout') }}"
      method="POST"
      class="d-none">

    @csrf
</form>


<!-- LIVE DATE AND TIME -->
<script>
(function () {
    var d = document.getElementById('liveDate');
    var t = document.getElementById('liveTime');

    if (!d || !t) {
        return;
    }

    function tick() {
        var now = new Date();

        d.textContent = now.toLocaleDateString('en-GB', {
            timeZone: 'Asia/Kathmandu',
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }).replace(',', '');

        t.textContent = now.toLocaleTimeString('en-US', {
            timeZone: 'Asia/Kathmandu',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    tick();

    setInterval(tick, 1000);
})();
</script>