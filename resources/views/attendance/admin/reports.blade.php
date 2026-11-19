
@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')
@php
    $reportUrl = route(
        $isAdmin ? 'admin.attendance.reports' : 'teacher.attendance.reports'
    );

    $exportRoute = $isAdmin
        ? 'admin.attendance.export'
        : 'teacher.attendance.export';

    $threshold = \App\Models\AttendanceRecord::threshold();

    $rateClass = function ($rate) use ($threshold) {
        if ($rate === null) return 'text-muted';
        if ($rate < $threshold) return 'text-danger';
        return 'text-success';
    };

    $today = now()->toDateString();
@endphp

@include('attendance._styles')

<style>
    .attendance-report {
        --report-border: var(--bs-border-color, #e5e7eb);
    }

    .attendance-report .report-heading {
        margin: 0;
        font-size: clamp(1.45rem, 2vw, 1.9rem);
        font-weight: 760;
        letter-spacing: -.04em;
    }

    .attendance-report .report-subtitle {
        color: var(--bs-secondary-color);
        font-size: .85rem;
        margin-top: .35rem;
    }

    .attendance-report .report-card {
        border: 1px solid var(--report-border);
        border-radius: 15px;
        background: var(--bs-body-bg);
        box-shadow: 0 2px 10px rgba(15,23,42,.04);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .attendance-report .report-card-body {
        padding: 1.2rem;
    }

    .attendance-report .report-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: .75rem;
        padding: 1.15rem 1.2rem;
        border-bottom: 1px solid var(--report-border);
    }

    .attendance-report .report-title {
        margin: 0;
        font-size: .98rem;
        font-weight: 750;
    }

    .attendance-report .report-description {
        margin: .25rem 0 0;
        color: var(--bs-secondary-color);
        font-size: .76rem;
    }

    .attendance-report .report-label {
        display: block;
        margin-bottom: .4rem;
        font-size: .76rem;
        font-weight: 700;
    }

    .attendance-report .form-select,
    .attendance-report .form-control {
        min-height: 42px;
        border-radius: 9px;
        font-size: .83rem;
    }

    .attendance-report .btn {
        border-radius: 9px;
        font-size: .81rem;
        font-weight: 650;
        padding: .58rem .85rem;
    }

    .attendance-report .report-table {
        min-width: 850px;
        margin: 0;
        vertical-align: middle;
    }

    .attendance-report .report-table thead th {
        padding: .8rem .9rem;
        background: rgba(100,116,139,.045);
        border-bottom: 1px solid var(--report-border);
        color: var(--bs-secondary-color);
        font-size: .68rem;
        font-weight: 750;
        letter-spacing: .045em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .attendance-report .report-table tbody td {
        padding: .8rem .9rem;
        font-size: .8rem;
        border-bottom: 1px solid var(--report-border);
    }

    .attendance-report .report-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .attendance-report .student-name {
        font-weight: 700;
        font-size: .82rem;
    }

    .attendance-report .student-meta {
        color: var(--bs-secondary-color);
        font-size: .71rem;
        margin-top: .15rem;
    }

    .attendance-report .number {
        font-variant-numeric: tabular-nums;
        font-weight: 650;
    }

    .attendance-report .rate {
        font-weight: 800;
        font-size: .86rem;
        font-variant-numeric: tabular-nums;
    }

    .attendance-report .rate-track {
        width: 92px;
        height: 6px;
        overflow: hidden;
        border-radius: 99px;
        background: rgba(100,116,139,.16);
        margin-top: .35rem;
        margin-left: auto;
    }

    .attendance-report .rate-fill {
        height: 100%;
        border-radius: inherit;
        background: #16a34a;
    }

    .attendance-report .rate-fill.low {
        background: #dc2626;
    }

    .attendance-report .status-label {
        display: inline-flex;
        align-items: center;
        padding: .25rem .5rem;
        border-radius: 999px;
        font-size: .67rem;
        font-weight: 750;
        white-space: nowrap;
    }

    .attendance-report .status-good {
        color: #15803d;
        background: rgba(22,163,74,.11);
    }

    .attendance-report .status-low {
        color: #b91c1c;
        background: rgba(220,38,38,.1);
    }

    .attendance-report .status-none {
        color: var(--bs-secondary-color);
        background: rgba(100,116,139,.1);
    }

    .attendance-report .report-stat {
        border: 1px solid var(--report-border);
        border-radius: 12px;
        padding: .9rem 1rem;
        background: var(--bs-body-bg);
        height: 100%;
    }

    .attendance-report .report-stat-label {
        color: var(--bs-secondary-color);
        font-size: .72rem;
        font-weight: 650;
    }

    .attendance-report .report-stat-value {
        margin-top: .2rem;
        font-size: 1.45rem;
        font-weight: 780;
        letter-spacing: -.04em;
        font-variant-numeric: tabular-nums;
    }

    .attendance-report .report-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--bs-secondary-color);
        font-size: .82rem;
    }

    .attendance-report .report-empty i {
        display: block;
        font-size: 1.6rem;
        opacity: .55;
        margin-bottom: .5rem;
    }

    .attendance-report .report-empty strong {
        display: block;
        color: var(--bs-body-color);
        margin-bottom: .2rem;
    }

    .attendance-report .filter-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .attendance-report .table-responsive {
        border-radius: 0 0 15px 15px;
    }

    @media(max-width:767.98px) {
        .attendance-report .report-card-body {
            padding: 1rem;
        }

        .attendance-report .report-card-header {
            padding: 1rem;
        }

        .attendance-report .report-stat-value {
            font-size: 1.2rem;
        }
    }
</style>

<div class="container-fluid py-4 att-page attendance-report">

    {{-- Page heading --}}
    <div class="mb-4">
        <h1 class="report-heading">
            {{ $isAdmin ? 'Attendance Management' : 'My Student Attendance Reports' }}
        </h1>

        <p class="report-subtitle">
            {{ $isAdmin
                ? 'Filter faculties, semesters and subjects to inspect individual student attendance.'
                : 'Review attendance performance of students enrolled in your assigned subjects.'
            }}
        </p>
    </div>

    {{-- Filters --}}
    <section class="report-card">

        <div class="report-card-header">
            <div>
                <h2 class="report-title">
                    <i class="bi bi-funnel me-1 text-primary"></i>
                    Report filters
                </h2>

                <p class="report-description">
                    Choose the academic scope and reporting period.
                </p>
            </div>

            @if(request()->query())
                <a href="{{ $reportUrl }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset filters
                </a>
            @endif
        </div>

        <div class="report-card-body">

            <form method="GET" action="{{ $reportUrl }}" id="attendance-report-filter-form">

                <div class="row g-3">

                    @if($isAdmin)

                        <div class="col-md-6 col-xl-3">
                            <label class="report-label" for="filter-faculty">
                                Faculty
                            </label>

                            <select
                                class="form-select"
                                name="faculty_id"
                                id="filter-faculty"
                            >
                                <option value="">All faculties</option>

                                @foreach($faculties as $faculty)
                                    <option
                                        value="{{ $faculty->id }}"
                                        @selected((string) request('faculty_id') === (string) $faculty->id)
                                    >
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 col-xl-2">
                            <label class="report-label" for="filter-semester">
                                Semester
                            </label>

                            <select
                                class="form-select"
                                name="semester"
                                id="filter-semester"
                            >
                                <option value="">All semesters</option>

                                @foreach($semesters as $semester)
                                    <option
                                        value="{{ $semester }}"
                                        @selected((string) request('semester') === (string) $semester)
                                    >
                                        Semester {{ $semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    @endif

                    <div class="{{ $isAdmin ? 'col-md-12 col-xl-4' : 'col-md-6 col-xl-5' }}">
                        <label class="report-label" for="filter-subject">
                            Subject
                        </label>

                        <select
                            class="form-select"
                            name="subject_id"
                            id="filter-subject"
                        >
                            <option value="">All subjects</option>

                            @if($subjectOptions->isEmpty())
                                <option value="" disabled>No subjects available for this selection</option>
                            @endif

                            @foreach($subjectOptions as $subject)
                                <option
                                    value="{{ $subject->id }}"
                                    @selected((string) request('subject_id') === (string) $subject->id)
                                >
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label class="report-label" for="filter-from">
                            From date
                        </label>

                        <input
                            class="form-control"
                            type="date"
                            name="from"
                            id="filter-from"
                            value="{{ request('from') }}"
                            max="{{ $today }}"
                        >
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label class="report-label" for="filter-to">
                            To date
                        </label>

                        <input
                            class="form-control"
                            type="date"
                            name="to"
                            id="filter-to"
                            value="{{ request('to') }}"
                            max="{{ $today }}"
                            min="{{ request('from') }}"
                        >
                    </div>

                </div>

                <div class="filter-actions mt-3">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>
                        Generate report
                    </button>

                    <a href="{{ $reportUrl }}" class="btn btn-outline-secondary">
                        Clear
                    </a>

                </div>

            </form>

            @if($isAdmin && request()->filled('subject_id') && !$selectedSubject)
                <div class="alert alert-info mt-3 mb-0 py-2 small" role="status">
                    That subject is not available under the selected faculty and semester.
                    Choose a subject from the updated list, or clear the subject filter to view all matching subjects.
                </div>
            @endif

        </div>

    </section>

    {{-- Subject summary --}}
    <section class="report-card">

        <div class="report-card-header">
            <div>
                <h2 class="report-title">Subject-wise summary</h2>

                <p class="report-description">
                    Each subject and class is reported independently.
                </p>
            </div>

            <span class="badge text-bg-light border">
                {{ $subjectSummaries->count() }}
                {{ \Illuminate\Support\Str::plural('subject', $subjectSummaries->count()) }}
            </span>
        </div>

        <div class="table-responsive">

            <table class="table report-table">

                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Faculty</th>
                        <th>Semester</th>
                        <th>Classes held</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($subjectSummaries as $item)

                        <tr>

                            <td>
                                <div class="student-name">
                                    {{ $item->subject_name }}
                                </div>

                                @if($item->subject_code)
                                    <div class="student-meta">
                                        {{ $item->subject_code }}
                                    </div>
                                @endif

                            </td>

                            <td>{{ $item->faculty_name ?? '—' }}</td>

                            <td>Semester {{ $item->semester }}</td>

                            <td class="number">
                                {{ $item->total_sessions }}
                            </td>


                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">
                                <div class="report-empty">
                                    <i class="bi bi-journal-x"></i>
                                    <strong>No subject attendance found</strong>
                                    Select a different date range or academic filter.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

    {{-- Student-wise attendance --}}
    <section class="report-card">

        <div class="report-card-header">
            <div>
                <h2 class="report-title">
                    <i class="bi bi-people me-1 text-primary"></i>
                    Student-wise attendance
                </h2>

                <p class="report-description">
                    Individual attendance performance for the selected subject and class.
                </p>
            </div>

            @if($selectedSubject)
                <span class="badge text-bg-primary">
                    {{ $studentAttendance->count() }}
                    {{ \Illuminate\Support\Str::plural('student', $studentAttendance->count()) }}
                </span>
            @endif
        </div>

        @if(!$selectedSubject)

            <div class="report-empty">
                <i class="bi bi-funnel"></i>

                <strong>Select a subject to view individual students</strong>

                @if($isAdmin)
                    Choose a faculty, semester and subject above.
                @else
                    Choose one of your assigned subjects above.
                @endif
            </div>

        @else

            <div class="report-card-body border-bottom">

                <div class="row g-3">

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="report-stat">
                            <div class="report-stat-label">Selected subject</div>
                            <div class="fw-bold mt-1">
                                {{ $selectedSubject->name }}
                            </div>
                            <div class="student-meta">
                                {{ $selectedSubject->classroom?->faculty?->name ?? '—' }}
                                · Semester {{ $selectedSubject->classroom?->semester ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="report-stat">
                            <div class="report-stat-label">Classes held</div>
                            <div class="report-stat-value">
                                {{ $classesConducted }}
                            </div>
                            <div class="student-meta">
                                Saved attendance sessions
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="report-stat">
                            <div class="report-stat-label">Students enrolled</div>
                            <div class="report-stat-value">
                                {{ $studentAttendance->count() }}
                            </div>
                            <div class="student-meta">
                                Students in this class
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-6 col-xl-3">
                        <div class="report-stat">
                            <div class="report-stat-label">Minimum attendance</div>
                            <div class="report-stat-value">
                                {{ $threshold }}%
                            </div>
                            <div class="student-meta">
                                Institutional threshold
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table report-table">

                    <thead>
                        <tr>
                            <th>Student</th>
                            <th class="text-end">Classes held</th>
                            <th class="text-end">Marked</th>
                            <th class="text-end">Present</th>
                            <th class="text-end">Late</th>
                            <th class="text-end">Absent</th>
                            <th class="text-end">Unrecorded</th>
                            <th class="text-end">Attendance %</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($studentAttendance as $student)

                            <tr>

                                <td>
                                    <div class="student-name">
                                        {{ $student->name }}
                                    </div>

                                    <div class="student-meta">
                                        {{ $student->email }}
                                    </div>
                                </td>

                                <td class="text-end number">
                                    {{ $classesConducted }}
                                </td>

                                <td class="text-end number">
                                    {{ $student->marked_count }}
                                </td>

                                <td class="text-end number text-success">
                                    {{ $student->present_count }}
                                </td>

                                <td class="text-end number text-warning">
                                    {{ $student->late_count }}
                                </td>

                                <td class="text-end number text-danger">
                                    {{ $student->absent_count }}
                                </td>

                                <td class="text-end number">
                                    {{ $student->unrecorded_count }}
                                </td>

                                <td class="text-end">

                                    @if($student->attendance_rate !== null)

                                        <div class="rate {{ $rateClass($student->attendance_rate) }}">
                                            {{ number_format($student->attendance_rate, 1) }}%
                                        </div>

                                        <div class="rate-track">
                                            <div
                                                class="rate-fill {{ $student->attendance_rate < $threshold ? 'low' : '' }}"
                                                style="width: {{ min($student->attendance_rate, 100) }}%"
                                            ></div>
                                        </div>

                                    @else

                                        <span class="text-muted">—</span>

                                    @endif

                                </td>

                                <td>

                                    @if($student->attendance_status === 'not_recorded')

                                        <span class="status-label status-none">
                                            Not recorded
                                        </span>

                                    @elseif($student->attendance_status === 'below')

                                        <span class="status-label status-low">
                                            Below {{ $threshold }}%
                                        </span>

                                    @else

                                        <span class="status-label status-good">
                                            Satisfactory
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="9">
                                    <div class="report-empty">
                                        <i class="bi bi-people"></i>
                                        <strong>No students enrolled in this class</strong>
                                        No student accounts are currently assigned to this class.
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="report-card-body border-top">

                <div class="small text-muted">
                    <strong>Calculation:</strong>
                    Attendance percentage = (Present + Late) ÷ recorded attendance × 100.
                    Unrecorded sessions are displayed separately and are not counted as absent.
                </div>

            </div>

        @endif

    </section>

    {{-- Detailed session history --}}
    <section class="report-card">

        <div class="report-card-header">
            <div>
                <h2 class="report-title">Attendance session history</h2>

                <p class="report-description">
                    Individual saved sessions matching your current filters.
                </p>
            </div>

            <span class="badge text-bg-light border">
                {{ number_format($totalSessions) }}
                {{ \Illuminate\Support\Str::plural('session', $totalSessions) }}
            </span>
        </div>

        <div class="table-responsive">

            <table class="table report-table">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Faculty</th>
                        <th>Semester</th>

                        @if($isAdmin)
                            <th>Teacher</th>
                        @endif

                        <th>Students marked</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($sessions as $session)

                        <tr>

                            <td class="text-nowrap">
                                {{ $session->attendance_date->format('d M Y') }}
                            </td>

                            <td>
                                <div class="student-name">
                                    {{ $session->subject?->name ?? '—' }}
                                </div>

                            </td>

                            <td>
                                {{ $session->classroom?->faculty?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $session->classroom?->semester ?? '—' }}
                            </td>

                            @if($isAdmin)
                                <td>
                                    {{ $session->teacher?->name ?? '—' }}
                                </td>
                            @endif

                            <td class="number">
                                {{ $session->records->count() }}
                            </td>

                            <td class="text-end">
                                <a
                                    href="{{ route($exportRoute, $session->id) }}"
                                    class="btn btn-sm btn-outline-success"
                                >
                                    <i class="bi bi-download me-1"></i>
                                    CSV
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="{{ $isAdmin ? 7 : 6 }}">
                                <div class="report-empty">
                                    <i class="bi bi-calendar-x"></i>
                                    <strong>No attendance sessions found</strong>
                                    Change your filters or select a different date range.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($sessions->hasPages())
            <div class="report-card-body border-top">
                {{ $sessions->withQueryString()->links() }}
            </div>
        @endif

    </section>

</div>

<script>
(function () {
    const form = document.getElementById('attendance-report-filter-form');
    if (!form) return;

    const faculty = document.getElementById('filter-faculty');
    const semester = document.getElementById('filter-semester');
    const subject = document.getElementById('filter-subject');

    if (faculty) {
        faculty.addEventListener('change', function () {
            if (semester) semester.value = '';
            if (subject) subject.value = '';
            form.requestSubmit();
        });
    }

    if (semester) {
        semester.addEventListener('change', function () {
            if (subject) subject.value = '';
            form.requestSubmit();
        });
    }
})();
</script>

@endsection
