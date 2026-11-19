
@extends('layouts.app')

@section('page-title', 'Add New Student')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Add New Student</h1>
            <p class="text-muted">Create a new student account</p>
        </div>

        <a href="{{ route('student.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Student Details</h3>
        </div>

        <div class="card-body">

            <form action="{{ route('student.insert') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name"
                               class="form-control"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email"
                               class="form-control"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone"
                               class="form-control"
                               value="{{ old('phone') }}"
                               placeholder="98XXXXXXXX" required>
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Enrolled Year</label>
                        <select name="enrolled_year"
                                class="form-control" required>
                            <option value="">Select year</option>

                            @for ($year = date('Y'); $year >= 2000; $year--)
                                <option value="{{ $year }}"
                                    {{ old('enrolled_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                        @error('enrolled_year')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password"
                               class="form-control" required>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control" required>
                    </div>

                    {{-- Faculty --}}
                    <div class="col-md-6 mb-3">
                        <label for="faculty_id" class="form-label">
                            Faculty
                        </label>

                        <select name="faculty_id" id="faculty_id"
                                class="form-select @error('faculty_id') is-invalid @enderror"
                                required>

                            <option value="">Select Faculty</option>

                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}"
                                    {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('faculty_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Semester --}}
                    <div class="col-md-6 mb-3">
                        <label for="semester" class="form-label">
                            Semester
                        </label>

                        <select name="semester" id="semester"
                                class="form-select @error('semester') is-invalid @enderror"
                                required disabled>

                            <option value="">Select Faculty First</option>

                        </select>

                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image"
                               id="profile_image"
                               class="form-control"
                               accept=".jpg,.jpeg,.png,.webp" required>

                        @error('profile_image')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                @include('admin.partials.profile-image-cropper', [
                    'initialPhotoUrl' => null,
                    'initialPhotoAlt' => 'Student Photo'
                ])

                <div class="text-end mt-4">
                    <a href="{{ route('student.list') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Student
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const facultySelect = document.getElementById('faculty_id');
    const semesterSelect = document.getElementById('semester');

    const semesterMap = @json($semesterMap);
    const oldSemester = @json(old('semester'));

    function loadSemesters(facultyId, selectedSemester = '') {

        semesterSelect.innerHTML = '';

        const semesters = semesterMap[facultyId] || [];

        if (!facultyId || semesters.length === 0) {
            semesterSelect.disabled = true;
            semesterSelect.add(new Option('No semesters available', ''));
            return;
        }

        semesterSelect.disabled = false;
        semesterSelect.add(new Option('Select Semester', ''));

        semesters.forEach(function (semester) {

            const option = new Option(
                'Semester ' + semester,
                semester
            );

            if (String(semester) === String(selectedSemester)) {
                option.selected = true;
            }

            semesterSelect.add(option);
        });
    }

    facultySelect.addEventListener('change', function () {
        loadSemesters(this.value);
    });

    // Restore selections after validation errors.
    if (facultySelect.value) {
        loadSemesters(facultySelect.value, oldSemester);
    }

});
</script>

@endsection
