@extends('layouts.app')

@section('page-title', 'My Notices')

@section('content')

<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">My Notices</h1>
            <p class="text-muted">Notices you have posted</p>
        </div>
        <a href="{{ route('teacher.notice.add') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Notice
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('teacher.notice.list') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('teacher.notice.list') }}" class="btn btn-secondary w-100">Clear</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            @if ($notices->count())
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Sent To</th>
                            <th>Date</th>
                            <th style="width:120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notices as $notice)
                            <tr>
                                <td>
                                    <strong>{{ $notice->title }}</strong>
                                    @if ($notice->image || $notice->attachment || $notice->link)
                                        <i class="bi bi-paperclip text-muted" title="Has attachments"></i>
                                    @endif
                                    <div style="font-size:12px;color:#64748B;">
                                        {{ \Illuminate\Support\Str::limit($notice->message, 70) }}
                                    </div>
                                </td>
                          
<td>
    @if ($notice->classroom)
        {{ $notice->classroom->faculty->name ?? 'Unknown Faculty' }}
        — Semester {{ $notice->classroom->semester }}
    @else
        <span class="text-muted">Class unavailable</span>
    @endif
</td>

                                <td>{{ $notice->created_at?->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('teacher.notice.edit', $notice->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('teacher.notice.delete', $notice->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this notice?');">
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

                <div style="padding:16px 20px;">{{ $notices->links() }}</div>
            @else
                <div style="padding:40px 20px;text-align:center;color:#64748B;">
                    You haven't posted any notices yet.
                </div>
            @endif
        </div>
    </div>

</div>

@endsection