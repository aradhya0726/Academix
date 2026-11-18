
@extends('layouts.app')

@section('page-title', 'Edit Teacher')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Edit Teacher</h1>
            <p class="text-muted">Update teacher information</p>
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

            <form action="{{ route('teacher.update', $teacher->id) }}"
                  method="POST" enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $teacher->name) }}" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $teacher->email) }}" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $teacher->phone_number) }}"
                               placeholder="98XXXXXXXX" required>
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Change Profile Image</label>

                        <input type="file" name="profile_image" id="profile_image"
                               class="form-control"
                               accept=".jpg,.jpeg,.png,.webp">

                        <small class="text-muted">
                            Leave empty to keep the current image.
                        </small>

                        @error('profile_image')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                @include('admin.partials.profile-image-cropper', [
                    'initialPhotoUrl' => $teacher->photo
                        ? asset('uploads/profile/'.$teacher->photo)
                        : null,
                    'initialPhotoAlt' => $teacher->name
                ])

                <div class="text-end mt-4">
                    <a href="{{ route('teacher.list') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Teacher
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection
