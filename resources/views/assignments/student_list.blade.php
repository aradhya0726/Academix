
@extends('layouts.app')

@section('page-title', 'Assignments')

@section('content')

<div class="content-panel">

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <div class="eyebrow">Academic Work</div>
        <h1 class="page-title mb-1">My Assignments</h1>
        <p class="text-muted mb-0">
            View assignments given to your class and track your submissions.
        </p>
    </div>

    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-start gap-3 mb-4"
             role="alert">

            <i class="bi bi-check-circle-fill fs-5 mt-1"></i>

            <div>
                <strong>Submission successful!</strong>
                <div>{{ session('success') }}</div>
            </div>

            <button type="button"
                    class="btn-close ms-auto"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    {{-- ERROR ALERT --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-3 mb-4"
             role="alert">

            <i class="bi bi-exclamation-circle-fill fs-5 mt-1"></i>

            <div>
                <strong>Submission failed</strong>
                <div>{{ session('error') }}</div>
            </div>

            <button type="button"
                    class="btn-close ms-auto"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4"
             role="alert">

            <strong>Please check the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif

    {{-- SUBJECT FILTER --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-1">
                <i class="bi bi-funnel text-primary me-2"></i>
                Filter by subject
            </h5>

            <small class="text-muted">
                Select a subject to view its assignments.
            </small>
        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route('student.assignment.list') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-6 col-lg-5">

                        <label for="subject_id"
                               class="form-label fw-semibold">
                            Subject
                        </label>

                        <select name="subject_id"
                                id="subject_id"
                                class="form-select">

                            <option value="">All subjects</option>

                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                    @selected((string)$subjectId === (string)$subject->id)>
                                    {{ $subject->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-auto d-flex gap-2">

                        <button type="submit"
                                class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Apply
                        </button>

                        <a href="{{ route('student.assignment.list') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- ASSIGNMENT RECORDS --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <h5 class="mb-1">Assignment Records</h5>

                    <small class="text-muted">
                        {{ number_format($assignments->total()) }}
                        assignments found
                    </small>
                </div>

                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                    {{ $assignments->total() }} results
                </span>

            </div>

        </div>

        <div class="card-body p-0">

            @if($assignments->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Assignment</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Due date</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($assignments as $a)

                                @php
                                    $submission = $a->submissions->first();
                                @endphp

                                <tr>

                                    {{-- ASSIGNMENT --}}
                                    <td class="ps-3">

                                        <div class="fw-semibold">
                                            {{ $a->title }}
                                        </div>

                                        @if($a->attachment)
                                            <small class="text-muted">
                                                <i class="bi bi-paperclip me-1"></i>
                                                Attachment available
                                            </small>
                                        @endif

                                    </td>

                                    {{-- SUBJECT --}}
                                    <td>
                                        {{ $a->subject->name ?? '—' }}
                                    </td>

                                    {{-- TEACHER --}}
                                    <td>
                                        {{ $a->teacher->name ?? '—' }}
                                    </td>

                                    {{-- DUE DATE --}}
                                    <td>

                                        <div>
                                            {{ $a->due_at->format('d M Y') }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $a->due_at->format('h:i A') }}
                                        </small>

                                    </td>

                                    {{-- SUBMISSION STATUS --}}
                                    <td>

                                        @if($submission)

                                            @if($submission->isLateFor($a))

                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock-history me-1"></i>
                                                    Submitted late
                                                </span>

                                            @else

                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Submitted on time
                                                </span>

                                            @endif

                                        @elseif($a->due_at->isPast())

                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Not submitted
                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                <i class="bi bi-hourglass-split me-1"></i>
                                                Pending
                                            </span>

                                        @endif

                                    </td>

                                    {{-- ACTION --}}
                                    <td class="text-end pe-3">

                                        <a href="{{ route('student.assignment.show', $a->id) }}"
                                           class="btn btn-sm btn-outline-primary">

                                            Open
                                            <i class="bi bi-arrow-right ms-1"></i>

                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- PAGINATION --}}
                <div class="p-3">

                    {{ $assignments->links('pagination::bootstrap-5') }}

                </div>

            @else

                {{-- EMPTY STATE --}}
                <div class="text-center py-5 px-3">

                    <div class="mb-3">
                        <i class="bi bi-clipboard-x text-muted"
                           style="font-size:42px;"></i>
                    </div>

                    <h5>No assignments found</h5>

                    <p class="text-muted mb-3">
                        There are no assignments matching this subject.
                    </p>

                    <a href="{{ route('student.assignment.list') }}"
                       class="btn btn-outline-primary btn-sm">

                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        View all assignments

                    </a>

                </div>

            @endif

        </div>
    </div>

</div>

@endsection
