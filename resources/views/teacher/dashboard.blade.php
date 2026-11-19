
@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
@php
    $classCount = $assignedClasses->count();
    $assignmentCount = $assignments->total();
    $noticeCount = $notices->count();
    $sessionCount = $recentSessions->count();
@endphp

<style>
    /* Teacher dashboard only */
    .td-page {
        --td-line: var(--border);
        --td-muted: var(--text-secondary);
        color: var(--text-primary);
    }

    .td-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        padding: 1.65rem 1.8rem;
        margin-bottom: 1.35rem;
        border: 1px solid var(--td-line);
        border-radius: 18px;
        background: linear-gradient(115deg, var(--card-bg) 0%, var(--card-bg) 68%, var(--primary-soft) 100%);
        box-shadow: var(--shadow-subtle);
    }

    .td-kicker {
        color: var(--primary);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .td-hero h1 {
        margin: .25rem 0;
        font-size: clamp(1.45rem, 2.5vw, 2rem);
        font-weight: 750;
        letter-spacing: -.045em;
    }

    .td-hero p {
        margin: 0;
        color: var(--td-muted);
        font-size: .88rem;
    }

    .td-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        flex-shrink: 0;
    }

    .td-hero .btn {
        border-radius: 10px;
        padding: .65rem .95rem;
        font-weight: 650;
    }

    .td-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .9rem;
        margin-bottom: 1.35rem;
    }

    .td-metric {
        display: flex;
        align-items: center;
        gap: .85rem;
        min-width: 0;
        padding: 1rem 1.1rem;
        border: 1px solid var(--td-line);
        border-radius: 14px;
        background: var(--card-bg);
        box-shadow: var(--shadow-subtle);
    }

    .td-metric-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        flex: 0 0 42px;
        border-radius: 12px;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 1.1rem;
    }

    .td-metric strong {
        display: block;
        font-size: 1.35rem;
        line-height: 1.15;
        font-weight: 750;
        letter-spacing: -.04em;
    }

    .td-metric span {
        display: block;
        margin-top: .2rem;
        color: var(--td-muted);
        font-size: .75rem;
        font-weight: 550;
    }

    .td-section {
        margin-bottom: 1.25rem;
        padding: 1.35rem;
        border: 1px solid var(--td-line);
        border-radius: 16px;
        background: var(--card-bg);
        box-shadow: var(--shadow-subtle);
    }

    .td-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.05rem;
    }

    .td-section-title {
        display: flex;
        align-items: center;
        gap: .65rem;
        margin: 0;
        font-size: 1rem;
        font-weight: 750;
        letter-spacing: -.02em;
    }

    .td-section-title i {
        color: var(--primary);
        font-size: 1.05rem;
    }

    .td-section-subtitle {
        margin: .2rem 0 0 1.7rem;
        color: var(--td-muted);
        font-size: .78rem;
    }

    .td-count {
        white-space: nowrap;
        padding: .28rem .58rem;
        border: 1px solid var(--td-line);
        border-radius: 999px;
        color: var(--td-muted);
        font-size: .72rem;
        font-weight: 700;
    }

    /* Assigned classes */
    .td-class-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr));
        gap: .8rem;
    }

    .td-class {
        padding: 1rem;
        border: 1px solid var(--td-line);
        border-radius: 12px;
        background: var(--page-bg);
        min-width: 0;
    }

    .td-class-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .75rem;
    }

    .td-class-name {
        font-size: .9rem;
        font-weight: 750;
        line-height: 1.35;
    }

    .td-class-meta {
        margin-top: .25rem;
        color: var(--td-muted);
        font-size: .75rem;
    }

    .td-subjects {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin-top: .8rem;
    }

    .td-chip {
        display: inline-flex;
        align-items: center;
        padding: .27rem .55rem;
        border-radius: 7px;
        border: 1px solid var(--td-line);
        background: var(--card-bg);
        color: var(--text-primary);
        font-size: .7rem;
        font-weight: 650;
    }

    /* Assignments */
    .td-assignment-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }

    .td-assignment {
        min-width: 0;
        padding: 1rem;
        border: 1px solid var(--td-line);
        border-radius: 12px;
        transition: border-color .18s ease, transform .18s ease;
    }

    .td-assignment:hover {
        border-color: rgba(37, 99, 235, .45);
        transform: translateY(-1px);
    }

    .td-assignment-title {
        font-size: .9rem;
        font-weight: 750;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .td-assignment-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .35rem .7rem;
        margin-top: .45rem;
        color: var(--td-muted);
        font-size: .74rem;
    }

    .td-assignment-meta span {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }

    .td-assignment-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        padding-top: .8rem;
        margin-top: .8rem;
        border-top: 1px solid var(--td-line);
    }

    .td-due {
        font-size: .75rem;
        color: var(--td-muted);
    }

    .td-due strong {
        display: block;
        color: var(--text-primary);
        font-size: .78rem;
    }

    .td-status {
        display: inline-flex;
        align-items: center;
        padding: .2rem .48rem;
        border-radius: 999px;
        font-size: .66rem;
        font-weight: 750;
    }

    .td-status.overdue {
        color: #b91c1c;
        background: rgba(220, 38, 38, .1);
    }

    .td-status.upcoming {
        color: #047857;
        background: rgba(16, 185, 129, .12);
    }

    .td-status.no-date {
        color: var(--td-muted);
        background: var(--page-bg);
    }

    .td-submission {
        color: var(--td-muted);
        font-size: .73rem;
        white-space: nowrap;
    }

    .td-submission strong {
        color: var(--text-primary);
        font-size: .9rem;
    }

    /* Notices and attendance */
    .td-lower-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 1.25rem;
        align-items: start;
    }

    .td-feed {
        display: flex;
        flex-direction: column;
    }

    .td-feed-item {
        display: flex;
        gap: .8rem;
        padding: .85rem 0;
        border-bottom: 1px solid var(--td-line);
    }

    .td-feed-item:first-child {
        padding-top: .1rem;
    }

    .td-feed-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .td-feed-icon {
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--primary);
    }

    .td-feed-content {
        min-width: 0;
        flex: 1;
    }

    .td-feed-title {
        color: var(--text-primary);
        font-size: .82rem;
        font-weight: 700;
        line-height: 1.4;
    }

    .td-feed-title a {
        color: inherit;
    }

    .td-feed-title a:hover {
        color: var(--primary);
    }

    .td-feed-text {
        margin-top: .2rem;
        color: var(--td-muted);
        font-size: .74rem;
        line-height: 1.55;
        overflow-wrap: anywhere;
    }

    .td-feed-date {
        flex-shrink: 0;
        color: var(--td-muted);
        font-size: .68rem;
        white-space: nowrap;
    }

    .td-attendance-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .7rem;
        padding: .85rem 0;
        border-bottom: 1px solid var(--td-line);
    }

    .td-attendance-row:first-child {
        padding-top: .1rem;
    }

    .td-attendance-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .td-attendance-name {
        font-size: .81rem;
        font-weight: 700;
    }

    .td-attendance-meta {
        margin-top: .2rem;
        color: var(--td-muted);
        font-size: .72rem;
    }

    .td-tallies {
        display: flex;
        align-items: center;
        gap: .35rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .td-tally {
        padding: .22rem .42rem;
        border-radius: 7px;
        font-size: .68rem;
        font-weight: 750;
        white-space: nowrap;
    }

    .td-tally.present {
        color: #047857;
        background: rgba(16, 185, 129, .12);
    }

    .td-tally.absent {
        color: #b91c1c;
        background: rgba(220, 38, 38, .1);
    }

    .td-tally.late {
        color: #b45309;
        background: rgba(245, 158, 11, .14);
    }

    /* Empty states and links */
    .td-empty {
        padding: 1.35rem 1rem;
        border: 1px dashed var(--td-line);
        border-radius: 11px;
        text-align: center;
        color: var(--td-muted);
        font-size: .8rem;
    }

    .td-empty i {
        display: block;
        margin-bottom: .45rem;
        font-size: 1.35rem;
        opacity: .65;
    }

    .td-link {
        color: var(--primary);
        font-size: .76rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .td-link:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    .td-pagination {
        margin-top: 1rem;
    }

    .td-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .td-pagination .pagination {
        margin-bottom: 0;
    }

    /* Responsive */
    @media (max-width: 1100px) {
        .td-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .td-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 1.2rem;
        }

        .td-hero-actions {
            width: 100%;
        }

        .td-hero-actions .btn {
            flex: 1;
        }

        .td-section {
            padding: 1rem;
        }

        .td-assignment-list,
        .td-lower-grid {
            grid-template-columns: 1fr;
        }

        .td-section-head {
            align-items: flex-start;
        }
    }

    @media (max-width: 420px) {
        .td-metrics {
            gap: .55rem;
        }

        .td-metric {
            padding: .75rem;
            gap: .55rem;
        }

        .td-metric-icon {
            width: 35px;
            height: 35px;
            flex-basis: 35px;
        }

        .td-metric strong {
            font-size: 1.1rem;
        }

        .td-metric span {
            font-size: .68rem;
        }
    }
</style>

<div class="td-page">

    {{-- Welcome section --}}
    <section class="td-hero">
        <div>
            <div class="td-kicker">Teacher workspace</div>
            <h1>{{ $greeting }}</h1>
            <p>
                {{ now()->format('l, d F Y') }}
                <span class="mx-1">·</span>
                Your teaching activity at a glance
            </p>
        </div>

        <div class="td-hero-actions">
            <a href="{{ route('teacher.assignment.add') }}"
               class="btn btn-outline-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Create assignment
            </a>

            <a href="{{ route('teacher.attendance.index') }}"
               class="btn btn-primary">
                <i class="bi bi-clipboard-check me-1"></i>
                Mark attendance
            </a>
        </div>
    </section>

    {{-- Dashboard summary --}}
    <section class="td-metrics" aria-label="Dashboard summary">

        <div class="td-metric">
            <div class="td-metric-icon">
                <i class="bi bi-mortarboard"></i>
            </div>
            <div>
                <strong>{{ $classCount }}</strong>
                <span>Assigned classes</span>
            </div>
        </div>

        <div class="td-metric">
            <div class="td-metric-icon">
                <i class="bi bi-journal-text"></i>
            </div>
            <div>
                <strong>{{ $assignmentCount }}</strong>
                <span>Assignments created</span>
            </div>
        </div>

        <div class="td-metric">
            <div class="td-metric-icon">
                <i class="bi bi-megaphone"></i>
            </div>
            <div>
                <strong>{{ $noticeCount }}</strong>
                <span>Recent notices</span>
            </div>
        </div>

        <div class="td-metric">
            <div class="td-metric-icon">
                <i class="bi bi-calendar2-check"></i>
            </div>
            <div>
                <strong>{{ $sessionCount }}</strong>
                <span>Recent attendance sessions</span>
            </div>
        </div>

    </section>

    {{-- A. Assigned classes --}}
    <section class="td-section">

        <div class="td-section-head">
            <div>
                <h2 class="td-section-title">
                    <i class="bi bi-collection"></i>
                    Assigned classes
                </h2>

                <p class="td-section-subtitle">
                    Classes and subjects assigned to your account across faculties
                </p>
            </div>

            <span class="td-count">
                {{ $classCount }}
                {{ \Illuminate\Support\Str::plural('class', $classCount) }}
            </span>
        </div>

        @if($assignedClasses->isNotEmpty())

            <div class="td-class-grid">

                @foreach($assignedClasses as $class)

                    <article class="td-class">

                        <div class="td-class-top">
                            <div>
                                <div class="td-class-name">
                                    {{ $class['faculty'] }}
                                </div>

                                <div class="td-class-meta">
                                    Semester {{ $class['semester'] ?? '—' }}
                                    <span class="mx-1">·</span>
                                    Class #{{ $class['class_id'] }}
                                </div>
                            </div>

                            <span class="td-count">
                                {{ $class['subjects']->count() }}
                                {{ \Illuminate\Support\Str::plural('subject', $class['subjects']->count()) }}
                            </span>
                        </div>

                        <div class="td-subjects">
                            @foreach($class['subjects'] as $subject)
                                <span class="td-chip">{{ $subject }}</span>
                            @endforeach
                        </div>

                    </article>

                @endforeach

            </div>

        @else

            <div class="td-empty">
                <i class="bi bi-inbox"></i>
                No classes or subjects have been assigned to your account yet.
            </div>

        @endif

    </section>

    {{-- B. Assignments --}}
    <section class="td-section">

        <div class="td-section-head">
            <div>
                <h2 class="td-section-title">
                    <i class="bi bi-journal-check"></i>
                    Assignments
                </h2>

                <p class="td-section-subtitle">
                    Your latest assignments, due dates and student submissions
                </p>
            </div>

            <a href="{{ route('teacher.assignment.list') }}" class="td-link">
                View all
                <i class="bi bi-arrow-up-right ms-1"></i>
            </a>
        </div>

        @if($assignments->count())

            <div class="td-assignment-list">

                @foreach($assignments as $assignment)

                    @php
                        $due = $assignment->due_at;
                        $isOverdue = $due && $due->isPast();
                    @endphp

                    <article class="td-assignment">

                        <div class="td-assignment-title">
                            {{ $assignment->title }}
                        </div>

                        <div class="td-assignment-meta">

                            <span>
                                <i class="bi bi-book"></i>
                                {{ $assignment->subject?->name ?? 'Subject unavailable' }}
                            </span>

                            <span>
                                <i class="bi bi-building"></i>
                                {{ $assignment->classroom?->faculty?->name ?? 'Faculty —' }}
                            </span>

                            <span>
                                <i class="bi bi-mortarboard"></i>
                                Sem {{ $assignment->classroom?->semester ?? '—' }}
                                · Class #{{ $assignment->class_id }}
                            </span>

                        </div>

                        <div class="td-assignment-bottom">

                            <div class="td-due">

                                @if($due)

                                    <strong>
                                        {{ $due->format('d M Y · h:i A') }}
                                    </strong>

                                    <span class="td-status {{ $isOverdue ? 'overdue' : 'upcoming' }}">
                                        {{ $isOverdue ? 'Past due' : 'Upcoming' }}
                                    </span>

                                @else

                                    <strong>No due date</strong>

                                    <span class="td-status no-date">
                                        Not set
                                    </span>

                                @endif

                            </div>

                            <div class="text-end">

                                <div class="td-submission mb-1">
                                    <strong>{{ $assignment->submissions_count }}</strong>
                                    submitted
                                </div>

                                <a href="{{ route('teacher.assignment.submissions', $assignment->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-inbox me-1"></i>
                                    Submissions
                                </a>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>

            @if($assignments->hasPages())
                <div class="td-pagination">
                    {{ $assignments->links('pagination::bootstrap-5') }}
                </div>
            @endif

        @else

            <div class="td-empty">
                <i class="bi bi-journal-plus"></i>
                You have not created any assignments yet.
                Create your first assignment to get started.
            </div>

        @endif

    </section>

    {{-- C and D. Notices and attendance --}}
    <div class="td-lower-grid">

        {{-- Notices --}}
        <section class="td-section mb-0">

            <div class="td-section-head">
                <div>
                    <h2 class="td-section-title">
                        <i class="bi bi-megaphone"></i>
                        Notices
                    </h2>

                    <p class="td-section-subtitle">
                        Latest announcements visible to you
                    </p>
                </div>

                <a href="{{ route('teacher.notice.view') }}" class="td-link">
                    All notices
                    <i class="bi bi-arrow-up-right ms-1"></i>
                </a>
            </div>

            @forelse($notices as $notice)

                <article class="td-feed-item">

                    <div class="td-feed-icon">
                        <i class="bi bi-bell"></i>
                    </div>

                    <div class="td-feed-content">

                        <div class="td-feed-title">
                            <a href="{{ route('teacher.notice.show', $notice->id) }}">
                                {{ $notice->title }}
                            </a>
                        </div>

                        <div class="td-feed-text">
                            {{ \Illuminate\Support\Str::limit(strip_tags($notice->message), 145) }}
                        </div>

                    </div>

                    <time class="td-feed-date"
                          datetime="{{ $notice->created_at?->toDateString() }}">
                        {{ $notice->created_at?->format('d M') }}
                    </time>

                </article>

            @empty

                <div class="td-empty">
                    <i class="bi bi-chat-square-text"></i>
                    No notices are available right now.
                </div>

            @endforelse

        </section>

        {{-- Latest attendance --}}
        <section class="td-section mb-0">

            <div class="td-section-head">
                <div>
                    <h2 class="td-section-title">
                        <i class="bi bi-calendar2-week"></i>
                        Latest attendance
                    </h2>

                    <p class="td-section-subtitle">
                        Your six most recently saved attendance sessions
                    </p>
                </div>

                <a href="{{ route('teacher.attendance.reports') }}" class="td-link">
                    View reports
                    <i class="bi bi-arrow-up-right ms-1"></i>
                </a>
            </div>

            @forelse($recentSessions as $session)

                <article class="td-attendance-row">

                    <div>

                        <div class="td-attendance-name">
                            {{ $session->subject?->name ?? 'Subject unavailable' }}
                        </div>

                        <div class="td-attendance-meta">
                            {{ $session->classroom?->faculty?->name ?? 'Faculty —' }}
                            <span class="mx-1">·</span>
                            Sem {{ $session->classroom?->semester ?? '—' }}
                            <span class="mx-1">·</span>
                            Class #{{ $session->class_id }}
                        </div>

                        <div class="td-attendance-meta">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $session->attendance_date?->format('d M Y') ?? 'Date unavailable' }}
                        </div>

                    </div>

                    <div class="td-tallies" aria-label="Attendance totals">

                        <span class="td-tally present" title="Present">
                            P {{ $session->present_count }}
                        </span>

                        <span class="td-tally absent" title="Absent">
                            A {{ $session->absent_count }}
                        </span>

                        <span class="td-tally late" title="Late">
                            L {{ $session->late_count }}
                        </span>

                    </div>

                </article>

            @empty

                <div class="td-empty">
                    <i class="bi bi-calendar-x"></i>
                    No attendance sessions have been saved yet.
                </div>

            @endforelse

        </section>

    </div>

</div>
@endsection
