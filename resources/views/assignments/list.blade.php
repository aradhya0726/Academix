@extends('layouts.app')

@section('page-title', 'Assignments')

@section('content')

<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">{{ $mode === 'admin' ? 'All Assignments' : 'My Assignments' }}</h1>
            <p class="text-muted">{{ $mode === 'admin' ? 'Assignments created by teachers' : 'Assignments you have created' }}</p>
        </div>
        @if ($mode === 'teacher')
            <a href="{{ route('teacher.assignment.add') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Create Assignment
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route($mode . '.assignment.list') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search title..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route($mode . '.assignment.list') }}" class="btn btn-secondary w-100">Clear</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            @if ($assignments->count())
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Class</th>
                            @if ($mode === 'admin') <th>Teacher</th> @endif
                            <th>Due</th>
                            <th>Submissions</th>
                            <th style="width:150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignments as $a)
                            <tr>
                                <td>
                                    <strong>{{ $a->title }}</strong>
                                    @if ($a->attachment)
                                        <i class="bi bi-paperclip text-muted" title="Has attachment"></i>
                                    @endif
                                </td>
                                <td>{{ $a->subject->name ?? '-' }}</td>
                                <td>{{ $a->classroom->faculty->name ?? '-' }} / Sem {{ $a->classroom->semester ?? '-' }}</td>
                                @if ($mode === 'admin') <td>{{ $a->teacher->name ?? '-' }}</td> @endif
                                <td>
                                    {{ $a->due_at->format('d M Y, h:i A') }}
                                    @if ($a->due_at->isPast())
                                        <span class="badge bg-secondary">Closed</span>
                                    @endif
                                </td>
                                <td>{{ $a->submissions_count }}</td>
                                <td>
                                    <a href="{{ route($mode . '.assignment.submissions', $a->id) }}" class="btn btn-sm btn-outline-secondary" title="View submissions">
                                        <i class="bi bi-inboxes"></i>
                                    </a>
                                    @if ($mode === 'teacher')
                                        <a href="{{ route('teacher.assignment.edit', $a->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route($mode . '.assignment.delete', $a->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this assignment and all its submissions?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding:16px 20px;">{{ $assignments->links() }}</div>
            @else
                <div style="padding:40px 20px;text-align:center;color:#64748B;">
                    No assignments yet.
                </div>
            @endif
        </div>
    </div>

</div>

@endsection