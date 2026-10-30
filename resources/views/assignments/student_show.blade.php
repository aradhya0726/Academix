@extends('layouts.app')

@section('page-title', 'Assignment')

@section('content')
<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">{{ $assignment->title }}</h1>
            <p class="text-muted">
                {{ $assignment->subject->name ?? '' }} &middot; {{ $assignment->teacher->name ?? '' }} &middot;
                Due {{ $assignment->due_at->format('d M Y, h:i A') }}
                @if ($assignment->due_at->isPast()) <span class="badge bg-danger">Past due</span> @endif
            </p>
        </div>
        <a href="{{ route('student.assignment.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Assignments
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><h3 class="card-title">Instructions</h3></div>
        <div class="card-body">
            @if ($assignment->description)
                <p style="white-space:pre-line;">{{ $assignment->description }}</p>
            @else
                <p class="text-muted">No description provided.</p>
            @endif

            @if ($assignment->attachment)
                @if (\App\Http\Controllers\AssignmentDownloadController::canPreview($assignment->attachment_name))
                    <a href="{{ route('assignment.attachment.preview', $assignment->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-eye"></i> Preview
                    </a>
                @endif
                <a href="{{ route('assignment.attachment.download', $assignment->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download"></i> Download {{ $assignment->attachment_name }}
                </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Your Submission</h3>

            @if ($submission)
                @if ($submission->isLateFor($assignment))
                    <span class="badge bg-warning text-dark">Submitted late</span>
                @else
                    <span class="badge bg-success">Submitted on time</span>
                @endif
            @elseif ($assignment->due_at->isPast())
                <span class="badge bg-danger">Not submitted</span>
            @else
                <span class="badge bg-secondary">Pending</span>
            @endif
        </div>
        <div class="card-body">

            @if ($submission)
                <div class="mb-3">
                    <a href="{{ route('assignment.submission.download', $submission->id) }}">
                        <i class="bi bi-download"></i> {{ $submission->file_name }}
                    </a>
                    @if (\App\Http\Controllers\AssignmentDownloadController::canPreview($submission->file_name))
                        <a href="{{ route('assignment.submission.preview', $submission->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="bi bi-eye"></i> Preview
                        </a>
                    @endif
                    <div class="text-muted" style="font-size:13px;">
                        Submitted {{ $submission->submitted_at->format('d M Y, h:i A') }}
                    </div>
                    @if ($submission->note)
                        <div class="mt-1">Note: {{ $submission->note }}</div>
                    @endif
                </div>
                <hr>
                <p class="text-muted mb-2">You can replace your submission by uploading a new file.</p>
            @elseif ($assignment->due_at->isPast())
                <div class="alert alert-warning">
                    The deadline has passed. You can still submit, but it will be marked as late.
                </div>
            @endif

            <form action="{{ route('student.assignment.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">File</label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                    @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">PDF, Word, Excel, PowerPoint, image or ZIP. Max 10 MB.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Note to teacher (optional)</label>
                    <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                    @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    {{ $submission ? 'Replace Submission' : 'Submit Work' }}
                </button>
            </form>
        </div>
    </div>

</div>
@endsection