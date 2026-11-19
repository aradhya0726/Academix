
@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $indexUrl  = route('teacher.attendance.index');
    $dateLabel = Carbon::parse($date)->format('d M Y');
    $today     = Carbon::today()->toDateString();
    $yesterday = Carbon::yesterday()->toDateString();
    $subjectId = request('subject_id');

    $recent = (method_exists($sessions, 'getCollection')
        ? $sessions->getCollection()
        : collect($sessions))->take(8);

    $initials = fn ($name) => collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($w) => Str::upper(Str::substr($w, 0, 1)))
        ->implode('');
@endphp

@include('attendance._styles')

<style>
    .rpt-main { font-weight: 600; line-height: 1.3; }
    .rpt-meta { display: block; font-size: .8125rem; color: #6b7280; line-height: 1.35; }
    .rpt-counts { white-space: nowrap; font-variant-numeric: tabular-nums; }

    .att-history-notice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px 18px;
        border: 1px solid #e7c978;
        border-radius: 12px;
        background: #fff9e8;
        color: #795b13;
        margin: 18px;
    }

    .att-history-notice i {
        font-size: 1.25rem;
        margin-top: 2px;
    }

    .att-history-notice strong {
        display: block;
        margin-bottom: 3px;
    }

    .att-readonly-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .att-readonly-status.present {
        background: #dcfce7;
        color: #166534;
    }

    .att-readonly-status.late {
        background: #fef3c7;
        color: #92400e;
    }

    .att-readonly-status.absent {
        background: #fee2e2;
        color: #991b1b;
    }

    .att-previous-label {
        display: inline-block;
        margin-top: 4px;
        padding: 2px 7px;
        border-radius: 5px;
        background: #fef3c7;
        color: #92400e;
        font-size: .7rem;
        font-weight: 600;
    }

    .att-readonly-remarks {
        color: #6b7280;
        font-size: .875rem;
    }
</style>

<div class="container-fluid py-4 att-page">

    <div class="att-head">
        <div>
            <h2 class="page-heading">Attendance</h2>
            <p>Take attendance for your subjects and review saved sessions.</p>
        </div>

        <a href="{{ route('teacher.attendance.reports') }}"
           class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>
            Attendance reports
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(\App\Models\AttendanceSession::isHoliday($date))
        <div class="alert alert-warning">
            <strong>{{ Carbon::parse($date)->format('l, d M Y') }} is a weekly holiday</strong>
            ({{ \App\Models\AttendanceSession::weekendLabel() }}).
            Attendance can't be saved for this date. Please choose a working day.
        </div>
    @endif

    {{-- STEP 1: Choose subject and date --}}
    @unless($selectedSubject)
        <div class="att-card att-card-pad mb-4">
            <h5 class="att-card-title">Choose a class and date</h5>
            <p class="att-card-sub">
                Pick one of your subjects, then load the student list.
            </p>

            <form method="GET" action="{{ $indexUrl }}"
                  class="row g-3 align-items-end">

                <div class="col-lg-6">
                    <label class="form-label" for="att-subject">Subject</label>

                    <select id="att-subject" class="form-select"
                            name="subject_id" required>
                        <option value="">Choose a subject</option>

                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                @selected((string) $subjectId === (string) $subject->id)>
                                {{ $subject->classroom?->faculty?->name ?? 'Faculty' }}
                                — Semester {{ $subject->classroom?->semester ?? '—' }}
                                — {{ $subject->name }}
                                {{ $subject->code ? '('.$subject->code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <label class="form-label" for="att-date">Date</label>
                    <input id="att-date" class="form-control"
                           type="date" name="date"
                           value="{{ $date }}" max="{{ $today }}" required>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="bi bi-people me-1"></i>
                        Load students
                    </button>
                </div>
            </form>

            @if($subjects->isEmpty())
                <div class="small text-muted mt-3">
                    No active subjects are assigned to your account.
                    Ask an admin to assign one.
                </div>
            @elseif($subjectId)
                <div class="att-chips mt-3">
                    <a class="att-chip {{ $date === $today ? 'is-active' : '' }}"
                       href="{{ $indexUrl }}?subject_id={{ $subjectId }}&date={{ $today }}">
                        Today
                    </a>

                    <a class="att-chip {{ $date === $yesterday ? 'is-active' : '' }}"
                       href="{{ $indexUrl }}?subject_id={{ $subjectId }}&date={{ $yesterday }}">
                        Yesterday
                    </a>
                </div>
            @endif
        </div>
    @endunless

    {{-- STEP 2: Attendance --}}
    @if($selectedSubject)
        <div class="att-card mb-4">

            <div class="att-card-pad d-flex flex-wrap justify-content-between align-items-start gap-2 border-bottom">
                <div>
                    <h5 class="att-card-title">
                        {{ $selectedSubject->name }}
                    </h5>

                    <div class="small text-muted">
                        {{ $selectedSubject->classroom?->faculty?->name }}
                        &nbsp;|&nbsp;
                        Semester {{ $selectedSubject->classroom?->semester }}
                        &nbsp;|&nbsp;
                        {{ $dateLabel }}
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">

                    @if($isHistoricalReadOnly)
                        <span class="att-pill absent">
                            <i class="bi bi-lock-fill me-1"></i>
                            Historical · Read only
                        </span>
                    @elseif($session)
                        <span class="att-pill present">
                            Saved session · Editable
                        </span>
                    @else
                        <span class="att-pill late">
                            New session · Not saved
                        </span>
                    @endif

                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ $indexUrl }}">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to sessions
                    </a>
                </div>
            </div>

            {{-- HISTORICAL SESSION: READ ONLY --}}
            @if($isHistoricalReadOnly)

                <div class="att-history-notice">
                    <i class="bi bi-shield-lock-fill"></i>
                    <div>
                        <strong>Previous semester attendance — Read only</strong>
                        The student roster has changed since this attendance
                        was recorded. These are the students and records
                        saved for this session.

                        Historical attendance cannot be edited, and no
                        new students can be added to this session.
                    </div>
                </div>

                @if($students->isEmpty())
                    <div class="att-empty">
                        <i class="bi bi-person-x"></i>
                        <strong>No saved student records</strong>
                        No student attendance records were found for this session.
                    </div>
                @else
                    @php
                        $historyCounts = $records->countBy('status');
                        $historyPresent = (int) ($historyCounts['present'] ?? 0);
                        $historyLate = (int) ($historyCounts['late'] ?? 0);
                        $historyAbsent = (int) ($historyCounts['absent'] ?? 0);
                    @endphp

                    <div class="att-card-pad d-flex flex-wrap align-items-center gap-3">
                        <span class="att-pill present">
                            {{ $historyPresent }} Present
                        </span>

                        <span class="att-pill late">
                            {{ $historyLate }} Late
                        </span>

                        <span class="att-pill absent">
                            {{ $historyAbsent }} Absent
                        </span>

                        <span class="small text-muted ms-auto">
                            {{ $students->count() }} recorded students
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table att-table align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 56px">#</th>
                                    <th>Student</th>
                                    <th>Status</th>
                                    <th style="min-width: 200px">Remarks</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($students as $i => $student)
                                    @php
                                        $record = $records->get($student->id);
                                    @endphp

                                    <tr>
                                        <td class="text-muted">{{ $i + 1 }}</td>

                                        <td>
                                            <div class="att-person">
                                                <span class="att-avatar" aria-hidden="true">
                                                    {{ $initials($student->name) }}
                                                </span>

                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ $student->name }}
                                                    </div>

                                                    <span class="att-sub">
                                                        {{ $student->email }}
                                                    </span>

                                                    <div>
                                                        <span class="att-previous-label">
                                                            <i class="bi bi-clock-history me-1"></i>
                                                            Historical record
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @if($record)
                                                <span class="att-readonly-status {{ $record->status }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            @else
                                                <span class="text-muted">Not recorded</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="att-readonly-remarks">
                                                {{ $record?->remarks ?: '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($session?->notes)
                    <div class="att-card-pad border-top">
                        <label class="form-label fw-semibold">Session notes</label>
                        <div class="text-muted">{{ $session->notes }}</div>
                    </div>
                @endif

                <div class="att-card-pad border-top">
                    <div class="small text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        This historical session is protected. No changes have
                        been made to its saved attendance records.
                    </div>
                </div>

            {{-- CURRENT ROSTER: NORMAL EDITABLE ATTENDANCE --}}
            @else

                @if($students->isEmpty())
                    <div class="att-empty">
                        <i class="bi bi-person-x"></i>
                        <strong>No students in this class</strong>
                        Ask an admin to assign students to this class.
                    </div>
                @else

                    <form method="POST"
                          action="{{ route('teacher.attendance.save') }}"
                          id="att-form">

                        @csrf

                        <input type="hidden" name="subject_id"
                               value="{{ $selectedSubject->id }}">

                        <input type="hidden" name="attendance_date"
                               value="{{ $date }}">

                        {{-- Toolbar --}}
                        <div class="att-card-pad d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <span class="att-pill present">
                                    <b id="cnt-present">0</b> Present
                                </span>

                                <span class="att-pill late">
                                    <b id="cnt-late">0</b> Late
                                </span>

                                <span class="att-pill absent">
                                    <b id="cnt-absent">0</b> Absent
                                </span>
                            </div>

                            <div class="d-flex flex-wrap gap-2">

                                <div class="input-group input-group-sm"
                                     style="width: 220px;">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>

                                    <input type="search" id="att-search"
                                           class="form-control"
                                           placeholder="Find a student"
                                           aria-label="Find a student">
                                </div>

                                <div class="btn-group btn-group-sm"
                                     role="group" aria-label="Mark everyone">

                                    <button type="button"
                                            class="btn btn-outline-success"
                                            data-set-all="present">
                                        All present
                                    </button>

                                    <button type="button"
                                            class="btn btn-outline-warning"
                                            data-set-all="late">
                                        All late
                                    </button>

                                    <button type="button"
                                            class="btn btn-outline-danger"
                                            data-set-all="absent">
                                        All absent
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table att-table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 56px">#</th>
                                        <th>Student</th>
                                        <th>Status</th>
                                        <th style="min-width: 200px">Remarks</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($students as $i => $student)
                                        @php
                                            $record = $records->get($student->id);

                                            $status = old(
                                                "attendance.{$student->id}.status",
                                                $record?->status ?? 'present'
                                            );
                                        @endphp

                                        <tr data-search="{{ Str::lower($student->name.' '.$student->email) }}">
                                            <td class="text-muted">
                                                {{ $i + 1 }}
                                            </td>

                                            <td>
                                                <div class="att-person">
                                                    <span class="att-avatar" aria-hidden="true">
                                                        {{ $initials($student->name) }}
                                                    </span>

                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ $student->name }}
                                                        </div>

                                                        <span class="att-sub">
                                                            {{ $student->email }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="att-seg"
                                                     role="radiogroup"
                                                     aria-label="Status for {{ $student->name }}">

                                                    @foreach([
                                                        'present' => 'Present',
                                                        'late' => 'Late',
                                                        'absent' => 'Absent'
                                                    ] as $value => $label)

                                                        <input type="radio"
                                                               id="st-{{ $student->id }}-{{ $value }}"
                                                               name="attendance[{{ $student->id }}][status]"
                                                               value="{{ $value }}"
                                                               @checked($status === $value)
                                                               required>

                                                        <label for="st-{{ $student->id }}-{{ $value }}">
                                                            {{ $label }}
                                                        </label>

                                                    @endforeach
                                                </div>
                                            </td>

                                            <td>
                                                <input class="form-control form-control-sm"
                                                       name="attendance[{{ $student->id }}][remarks]"
                                                       value="{{ old("attendance.{$student->id}.remarks", $record?->remarks) }}"
                                                       placeholder="Add a note"
                                                       aria-label="Remarks for {{ $student->name }}">
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr id="att-no-match" class="att-row-hidden">
                                        <td colspan="4"
                                            class="text-center text-muted py-4">
                                            No student matches your search.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="att-card-pad border-top">
                            <label class="form-label" for="att-notes">
                                Session notes (optional)
                            </label>

                            <textarea id="att-notes"
                                      name="notes"
                                      class="form-control"
                                      rows="2"
                                      maxlength="2000"
                                      placeholder="Anything worth recording about this class">{{ old('notes', $session?->notes) }}</textarea>
                        </div>

                        <div class="att-savebar">
                            <span class="small text-muted">
                                {{ $students->count() }}
                                {{ Str::plural('student', $students->count()) }}
                                in this class
                            </span>

                            <button type="submit"
                                    class="btn btn-primary"
                                    id="att-save">

                                <i class="bi bi-check2-circle me-1"></i>

                                {{ $session ? 'Update attendance' : 'Save attendance' }}
                            </button>
                        </div>

                    </form>
                @endif
            @endif
        </div>
    @endif

    {{-- RECENT SESSIONS --}}
    @unless($selectedSubject)
        <div class="att-card overflow-hidden">

            <div class="att-card-pad border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="att-card-title mb-0">Recent sessions</h5>
                    <p class="att-card-sub mb-0">
                        Open a saved session to review its attendance.
                    </p>
                </div>

                <a class="btn btn-sm btn-outline-secondary"
                   href="{{ route('teacher.attendance.reports') }}">
                    All sessions &amp; downloads
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table att-table align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Subject &amp; class</th>
                            <th>Attendance</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($recent as $item)
                            @php
                                $c = $item->records->countBy('status');
                                $p = (int) ($c['present'] ?? 0);
                                $l = (int) ($c['late'] ?? 0);
                                $a = (int) ($c['absent'] ?? 0);
                            @endphp

                            <tr>
                                <td class="text-nowrap">
                                    {{ $item->attendance_date->format('d M Y') }}
                                </td>

                                <td>
                                    <span class="rpt-main">
                                        {{ $item->subject?->name ?? '—' }}
                                    </span>

                                    <span class="rpt-meta">
                                        {{ $item->classroom?->faculty?->name ?? '—' }}
                                        · Semester {{ $item->classroom?->semester ?? '—' }}
                                    </span>
                                </td>

                                <td class="rpt-counts">
                                    {{ $p }} present ·
                                    {{ $l }} late ·
                                    {{ $a }} absent
                                </td>

                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="{{ route('teacher.attendance.index', [
                                           'subject_id' => $item->subject_id,
                                           'date' => $item->attendance_date->format('Y-m-d'),
                                       ]) }}">
                                        <i class="bi bi-eye me-1"></i>
                                        View session
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="att-empty">
                                        <i class="bi bi-inbox"></i>
                                        <strong>No sessions yet</strong>
                                        Choose a class above to take your first attendance.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endunless

</div>

<script>
(function () {
    var form = document.getElementById('att-form');

    // Historical sessions have no editable form.
    if (!form) return;

    function tally() {
        var c = { present: 0, late: 0, absent: 0 };

        form.querySelectorAll('.att-seg input:checked').forEach(function (input) {
            c[input.value]++;
        });

        Object.keys(c).forEach(function (key) {
            var counter = document.getElementById('cnt-' + key);

            if (counter) {
                counter.textContent = c[key];
            }
        });
    }

    form.addEventListener('change', function (event) {
        if (event.target.matches('.att-seg input')) {
            tally();
        }
    });

    // Mark everyone.
    form.querySelectorAll('[data-set-all]').forEach(function (button) {
        button.addEventListener('click', function () {
            var status = button.getAttribute('data-set-all');

            form.querySelectorAll(
                '.att-seg input[value="' + status + '"]'
            ).forEach(function (input) {
                input.checked = true;
            });

            tally();
        });
    });

    // Search students.
    var search = document.getElementById('att-search');
    var noMatch = document.getElementById('att-no-match');

    if (search && noMatch) {
        search.addEventListener('input', function () {
            var query = search.value.trim().toLowerCase();
            var shown = 0;

            form.querySelectorAll('tbody tr[data-search]').forEach(function (row) {
                var hit = row.getAttribute('data-search').indexOf(query) !== -1;

                row.classList.toggle('att-row-hidden', !hit);

                if (hit) shown++;
            });

            noMatch.classList.toggle('att-row-hidden', shown !== 0);
        });
    }

    // Prevent double submissions.
    form.addEventListener('submit', function () {
        var button = document.getElementById('att-save');

        if (button) {
            button.disabled = true;
            button.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Saving';
        }
    });

    tally();
})();
</script>
@endsection
