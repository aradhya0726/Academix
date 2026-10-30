@extends('layouts.app')

@section('page-title', 'Submissions')

@section('content')
<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">{{ $assignment->title }}</h1>
            <p class="text-muted">
                {{ $assignment->subject->name ?? '' }} &middot;
                {{ $assignment->classroom->faculty->name ?? '' }} / Sem {{ $assignment->classroom->semester ?? '' }} &middot;
                Due {{ $assignment->due_at->format('d M Y, h:i A') }}
            </p>
        </div>
        <a href="{{ route($mode . '.assignment.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            @if ($students->count())
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>File</th>
                            <th>Note</th>
                            @if ($mode === 'teacher') <th style="min-width:220px">Change status</th> @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php $sub = $submissions->get($student->id); @endphp
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>
                                    @if ($sub)
                                        @if ($sub->isLateFor($assignment))
                                            <span class="badge bg-warning text-dark">Late</span>
                                        @else
                                            <span class="badge bg-success">On time</span>
                                        @endif
                                        @if ($sub->status_override)
                                            <div class="text-muted" style="font-size:11px;">set by teacher</div>
                                        @endif
                                    @elseif ($assignment->due_at->isPast())
                                        <span class="badge bg-danger">Not submitted</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $sub ? $sub->submitted_at->format('d M Y, h:i A') : '-' }}</td>
                                <td>
                                    @if ($sub)
                                        @if (\App\Http\Controllers\AssignmentDownloadController::canPreview($sub->file_name))
                                            <a href="{{ route('assignment.submission.preview', $sub->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye"></i> Preview
                                            </a>
                                        @endif
                                        <a href="{{ route('assignment.submission.download', $sub->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                        <div class="text-muted" style="font-size:12px;">{{ $sub->file_name }}</div>
                                    @else - @endif
                                </td>
                                <td>{{ $sub->note ?? '' }}</td>

                                @if ($mode === 'teacher')
                                    <td>
                                        @if ($sub)
                                            <form action="{{ route('teacher.assignment.submission.status', $sub->id) }}" method="POST" class="d-flex gap-1">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="auto"    {{ !$sub->status_override ? 'selected' : '' }}>Auto (by time)</option>
                                                    <option value="on_time" {{ $sub->status_override === 'on_time' ? 'selected' : '' }}>On time</option>
                                                    <option value="late"    {{ $sub->status_override === 'late' ? 'selected' : '' }}>Late</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                            </form>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="padding:40px 20px;text-align:center;color:#64748B;">
                    No students are enrolled in this class yet.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection