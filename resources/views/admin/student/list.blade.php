
@extends('layouts.app')

@section('title', 'Student Management')

@section('content')

<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="eyebrow">Academic Management</div>
        <h1>Student Management</h1>
        <p class="text-muted mb-0">
            Manage students and filter them by their academic programme.
        </p>
    </div>

    <a href="{{ route('student.add') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>
        Add New Student
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mt-3">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="content-wrapper">

    {{-- FILTER SECTION --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-funnel text-primary fs-5"></i>
                <div>
                    <h5 class="mb-0">Filter Students</h5>
                    <small class="text-muted">
                        Narrow down students by faculty, semester or search.
                    </small>
                </div>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('student.list') }}" id="studentFilterForm">

                <div class="row g-3">

                    {{-- FACULTY --}}
                    <div class="col-lg-3 col-md-6">
                        <label for="faculty_id" class="form-label fw-semibold">
                            Faculty
                        </label>

                        <select
                            name="faculty_id"
                            id="faculty_id"
                            class="form-select"
                        >
                            <option value="">All faculties</option>

                            @foreach($faculties as $faculty)
                                <option
                                    value="{{ $faculty->id }}"
                                    @selected((string) $facultyId === (string) $faculty->id)
                                >
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SEMESTER --}}
                    <div class="col-lg-3 col-md-6">
                        <label for="semester" class="form-label fw-semibold">
                            Semester
                        </label>

                        <select
                            name="semester"
                            id="semester"
                            class="form-select"
                        >
                            <option value="">All semesters</option>

                            @foreach($semesterOptions as $option)
                                <option
                                    value="{{ $option }}"
                                    @selected((string) $semester === (string) $option)
                                >
                                    Semester {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SEARCH --}}
                    <div class="col-lg-4 col-md-8">
                        <label for="search" class="form-label fw-semibold">
                            Search students
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search text-muted"></i>
                            </span>

                            <input
                                type="text"
                                name="search"
                                id="search"
                                class="form-control"
                                placeholder="Name, email, phone or enrolled year"
                                value="{{ $search }}"
                            >
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="col-lg-2 col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel me-1"></i>
                            Apply
                        </button>

                        <a
                            href="{{ route('student.list') }}"
                            class="btn btn-outline-secondary"
                            title="Reset filters"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- STUDENT TABLE --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <h5 class="mb-1">Student Records</h5>
                    <small class="text-muted">
                        @if($students->total() > 0)
                            Showing {{ $students->firstItem() }}–{{ $students->lastItem() }}
                            of {{ $students->total() }} students
                        @else
                            No matching students
                        @endif
                    </small>
                </div>

                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                    {{ number_format($students->total()) }} results
                </span>

            </div>
        </div>

        <div class="card-body p-0">

            @if($students->count())

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Faculty</th>
                                <th>Semester</th>
                                <th>Enrolled Year</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($students as $student)

                                <tr>

                                    <td class="ps-3 text-muted">
                                        {{ $students->firstItem() + $loop->index }}
                                    </td>

                                    {{-- STUDENT --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">

                                            @if($student->photo)
                                                <img
                                                    src="{{ asset('uploads/profile/' . $student->photo) }}"
                                                    alt="{{ $student->name }}"
                                                    width="40"
                                                    height="40"
                                                    class="rounded-circle"
                                                    style="object-fit:cover;"
                                                >
                                            @else
                                                <div
                                                    class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold"
                                                    style="width:40px;height:40px;flex-shrink:0;"
                                                >
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                            @endif

                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $student->name }}
                                                </div>

                                                <small class="text-muted">
                                                    Student
                                                </small>
                                            </div>

                                        </div>
                                    </td>

                                    {{-- EMAIL --}}
                                    <td>
                                        <span>{{ $student->email }}</span>
                                    </td>

                                    {{-- PHONE --}}
                                    <td>
                                        {{ $student->phone_number ?? '—' }}
                                    </td>

                                    {{-- FACULTY --}}
                                    <td>
                                        @if($student->classroom)
                                            {{ $student->classroom->faculty->name ?? '—' }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>

                                    {{-- SEMESTER --}}
                                    <td>
                                        @if($student->classroom)
                                            <span class="badge bg-light text-dark border">
                                                Semester {{ $student->classroom->semester }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- ENROLLED YEAR --}}
                                    <td>
                                        {{ $student->enrolled_year ?? '—' }}
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="text-end pe-3">

                                        <div class="d-inline-flex gap-1">

                                            <a
                                                href="{{ route('student.edit', $student->id) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Edit student"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form
                                                action="{{ route('student.delete', $student->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete student"
                                                >
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

                {{-- PAGINATION --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3">

                    <small class="text-muted">
                        Page {{ $students->currentPage() }} of {{ $students->lastPage() }}
                    </small>

                    {{ $students->links('pagination::bootstrap-5') }}

                </div>

            @else

                <div class="text-center py-5 px-3">

                    <div class="mb-3">
                        <i class="bi bi-people text-muted" style="font-size:42px;"></i>
                    </div>

                    <h5>No students found</h5>

                    <p class="text-muted mb-3">
                        No students match your selected filters or search.
                    </p>

                    <a
                        href="{{ route('student.list') }}"
                        class="btn btn-outline-primary btn-sm"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Clear filters
                    </a>

                </div>

            @endif

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const facultySelect = document.getElementById('faculty_id');
    const semesterSelect = document.getElementById('semester');

    const semesterMap = @json($semesterMap);
    const selectedSemester = @json((string) ($semester ?? ''));

    function populateSemesters(selectedValue = '') {

        const facultyId = facultySelect.value;

        semesterSelect.innerHTML = '';

        semesterSelect.add(
            new Option('All semesters', '')
        );

        let semesters = [];

        if (facultyId) {
            semesters = semesterMap[facultyId] || [];
        } else {
            const all = Object.values(semesterMap).flat();

            semesters = [...new Set(all)]
                .sort((a, b) => Number(a) - Number(b));
        }

        semesters.forEach(function (semester) {

            const option = new Option(
                'Semester ' + semester,
                semester
            );

            if (String(semester) === String(selectedValue)) {
                option.selected = true;
            }

            semesterSelect.add(option);
        });
    }

    facultySelect.addEventListener('change', function () {

        // A semester from the previous faculty must not remain selected.
        populateSemesters('');

    });

    populateSemesters(selectedSemester);

});
</script>
@endpush
