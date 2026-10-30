@extends('layouts.app')

@section('page-title', 'Assignments')

@section('content')
<div class="content-panel">

    <div class="mb-4">
        <h1 class="page-title">Assignments</h1>
        <p class="text-muted">Work assigned to your class</p>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if ($assignments->count())
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th style="width:90px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignments as $a)
                            @php $sub = $a->submissions->first(); @endphp
                            <tr>
                                <td><strong>{{ $a->title }}</strong></td>
                                <td>{{ $a->subject->name ?? '-' }}</td>
                                <td>{{ $a->teacher->name ?? '-' }}</td>
                                <td>{{ $a->due_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    @if ($sub)
                                        @if ($sub->isLateFor($a))
                                            <span class="badge bg-warning text-dark">Submitted late</span>
                                        @else
                                            <span class="badge bg-success">Submitted on time</span>
                                        @endif
                                    @elseif ($a->due_at->isPast())
                                        <span class="badge bg-danger">Not submitted</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('student.assignment.show', $a->id) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding:16px 20px;">{{ $assignments->links() }}</div>
            @else
                <div style="padding:40px 20px;text-align:center;color:#64748B;">
                    No assignments for your class yet.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection