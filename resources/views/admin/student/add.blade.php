
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

            <form action="{{ route('student.insert') }}" method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone') }}"
                               placeholder="98XXXXXXXX" required>
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Enrolled Year</label>
                        <select name="enrolled_year" class="form-control" required>
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
                        <input type="password" name="password_confirmation"
                               class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Faculty &amp; Semester</label>
                        <select name="class_id"
                                class="form-select @error('class_id') is-invalid @enderror"
                                required>
                            <option value="">Select Faculty &amp; Semester</option>

                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->faculty->name ?? 'No Faculty' }}
                                    - Semester {{ $class->semester }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" id="profile_image"
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
                    <a href="{{ route('student.list') }}" class="btn btn-secondary">
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

@endsection
