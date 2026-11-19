
@extends('layouts.app')

@section('page-title', 'Assignments')

@section('content')

<div class="content-panel">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <div class="eyebrow">Academic Management</div>

            <h1 class="page-title mb-1">
                {{ $mode === 'admin' ? 'Assignment Management' : 'My Assignments' }}
            </h1>

            <p class="text-muted mb-0">
                {{ $mode === 'admin'
                    ? 'Manage and review assignments across all academic programmes.'
                    : 'Manage assignments created for your assigned subjects.' }}
            </p>
        </div>

        @if($mode === 'teacher')
            <a href="{{ route('teacher.assignment.add') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Create Assignment
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTERS --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-1">
                <i class="bi bi-funnel text-primary me-2"></i>
                Filter Assignments
            </h5>
            <small class="text-muted">
                Find assignments using academic details or assignment title.
            </small>
        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route($mode . '.assignment.list') }}"
                  id="assignmentFilterForm">

                <div class="row g-3">

                    @if($mode === 'admin')

                        {{-- FACULTY --}}
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold" for="faculty_id">
                                Faculty
                            </label>

                            <select name="faculty_id"
                                    id="faculty_id"
                                    class="form-select">

                                <option value="">All faculties</option>

                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}"
                                        @selected((string)$facultyId === (string)$faculty->id)>
                                        {{ $faculty->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- SEMESTER --}}
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label fw-semibold" for="semester">
                                Semester
                            </label>

                            <select name="semester"
                                    id="semester"
                                    class="form-select">

                                <option value="">All semesters</option>

                                @foreach(($semesterMap[$facultyId] ?? collect()) as $item)
                                    <option value="{{ $item }}"
                                        @selected((string)$semester === (string)$item)>
                                        Semester {{ $item }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    @endif

                    {{-- SUBJECT --}}
                    <div class="{{ $mode === 'admin' ? 'col-lg-3 col-md-6' : 'col-lg-4 col-md-6' }}">
                        <label class="form-label fw-semibold" for="subject_id">
                            Subject
                        </label>

                        <select name="subject_id"
                                id="subject_id"
                                class="form-select">

                            <option value="">All subjects</option>

                            @foreach($subjects as $subject)
                                <option
                                    value="{{ $subject->id }}"
                                    @if($mode === 'admin')
                                        data-faculty="{{ $subject->classroom->faculty_id ?? '' }}"
                                        data-semester="{{ $subject->classroom->semester ?? '' }}"
                                    @endif
                                    @selected((string)$subjectId === (string)$subject->id)
                                >
                                    {{ $subject->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- SEARCH --}}
                    <div class="{{ $mode === 'admin' ? 'col-lg-3 col-md-6' : 'col-lg-5 col-md-6' }}">
                        <label class="form-label fw-semibold" for="search">
                            Search assignment
                        </label>

                        <input type="text"
                               name="search"
                               id="search"
                               class="form-control"
                               placeholder="Search by assignment title..."
                               value="{{ $search }}">
                    </div>

                </div>

                <div class="d-flex gap-2 mt-3">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>
                        Apply filters
                    </button>

                    <a href="{{ route($mode . '.assignment.list') }}"
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>

                </div>

            </form>
        </div>
    </div>

    {{-- ASSIGNMENT TABLE --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">Assignment Records</h5>
                    <small class="text-muted">
                        {{ number_format($assignments->total()) }} assignments found
                    </small>
                </div>

                <span class="badge bg-primary-subtle text-primary">
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
                                <th>Faculty / Semester</th>

                                @if($mode === 'admin')
                                    <th>Teacher</th>
                                @endif

                                <th>Due date</th>
                                <th>Submissions</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($assignments as $a)

                                <tr>

                                    <td class="ps-3">
                                        <div class="fw-semibold">
                                            {{ $a->title }}

                                            @if($a->attachment)
                                                <i class="bi bi-paperclip text-muted ms-1"
                                                   title="Attachment available"></i>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        {{ $a->subject->name ?? '—' }}
                                    </td>

                                    <td>
                                        <div>
                                            {{ $a->classroom->faculty->name ?? '—' }}
                                        </div>
                                        <small class="text-muted">
                                            Semester {{ $a->classroom->semester ?? '—' }}
                                        </small>
                                    </td>

                                    @if($mode === 'admin')
                                        <td>
                                            {{ $a->teacher->name ?? '—' }}
                                        </td>
                                    @endif

                                    <td>
                                        <div>{{ $a->due_at->format('d M Y') }}</div>
                                        <small class="text-muted">
                                            {{ $a->due_at->format('h:i A') }}
                                        </small>

                                        @if($a->due_at->isPast())
                                            <span class="badge bg-secondary ms-1">Closed</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $a->submissions_count }}
                                        </span>
                                    </td>

                                    <td class="text-end pe-3">

                                        <div class="d-inline-flex gap-1">

                                            <a href="{{ route($mode . '.assignment.submissions', $a->id) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="View submissions">
                                                <i class="bi bi-inboxes"></i>
                                            </a>

                                            @if($mode === 'teacher')
                                                <a href="{{ route('teacher.assignment.edit', $a->id) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Edit assignment">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endif

                                            <form action="{{ route($mode . '.assignment.delete', $a->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Delete this assignment and all its submissions?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Delete assignment">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>
                </div>

                <div class="p-3">
                    {{ $assignments->links('pagination::bootstrap-5') }}
                </div>

            @else

                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x text-muted"
                       style="font-size:42px;"></i>

                    <h5 class="mt-3">No assignments found</h5>

                    <p class="text-muted mb-3">
                        No assignments match your selected filters.
                    </p>

                    <a href="{{ route($mode . '.assignment.list') }}"
                       class="btn btn-outline-primary btn-sm">
                        Clear filters
                    </a>
                </div>

            @endif

        </div>
    </div>

</div>

@endsection

@if($mode === 'admin')
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const faculty = document.getElementById('faculty_id');
        const semester = document.getElementById('semester');
        const subject = document.getElementById('subject_id');

        const semesterMap = @json($semesterMap);
        const initialSemester = @json((string)($semester ?? ''));
        const initialSubject = @json((string)($subjectId ?? ''));

        function updateSemesters(selected = '') {
            const facultyId = faculty.value;

            semester.innerHTML = '';
            semester.add(new Option('All semesters', ''));

            let values = [];

            if (facultyId) {
                values = semesterMap[facultyId] || [];
            } else {
                values = [...new Set(
                    Object.values(semesterMap).flat()
                )].sort((a, b) => Number(a) - Number(b));
            }

            values.forEach(value => {
                const option = new Option('Semester ' + value, value);
                option.selected = String(value) === String(selected);
                semester.add(option);
            });
        }

        function updateSubjects(selected = '') {
            const facultyId = faculty.value;
            const semesterValue = semester.value;

            subject.innerHTML = '';
            subject.add(new Option('All subjects', ''));

            Array.from(subject.dataset.options ? [] : subject.querySelectorAll('option'));

           const allSubjects = @json($subjects);

            allSubjects.forEach(item => {
                if (facultyId && String(item.classroom?.faculty_id ?? '') !== facultyId) return;

if (semesterValue && String(item.classroom?.semester ?? '') !== semesterValue) return;

                const option = new Option(item.name, item.id);
                option.selected = String(item.id) === String(selected);
                subject.add(option);
            });
        }

        faculty.addEventListener('change', function () {
            updateSemesters('');
            updateSubjects('');
        });

        semester.addEventListener('change', function () {
            updateSubjects('');
        });

        updateSemesters(initialSemester);
        updateSubjects(initialSubject);
    });
    </script>
    @endpush
@endif
