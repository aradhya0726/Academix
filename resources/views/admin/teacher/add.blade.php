
@extends('layouts.app')

@section('page-title', 'Add New Teacher')

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
            <h1 class="page-title">Add New Teacher</h1>
            <p class="text-muted">Create a new teacher account</p>
        </div>

        <a href="{{ route('teacher.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Teacher Details</h3>
        </div>

        <div class="card-body">

            <form action="{{ route('teacher.insert') }}" method="POST"
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
                    'initialPhotoAlt' => 'Teacher Photo'
                ])

                <div class="text-end mt-4">
                    <a href="{{ route('teacher.list') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Teacher
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
