@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $plural = fn ($n, $word) => $n . ' ' . \Illuminate\Support\Str::plural($word, $n);
    $tone   = fn ($r) => $r >= $threshold ? 'bg-success' : ($r >= $threshold - 15 ? 'bg-warning' : 'bg-danger');
    $pills  = [
        'present'  => ['success', 'Present'],
        'late'     => ['warning', 'Late'],
        'absent'   => ['danger', 'Absent'],
        'unmarked' => ['secondary', 'Not recorded'],
    ];
@endphp

<div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
        <div class="eyebrow">Overview</div>
        <h1>{{ $greeting }}</h1>
        <div class="text-muted small mt-1">{{ now()->format('l, d F Y') }}</div>
    </div>
    <a href="{{ route('student.attendance.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-calendar2-check me-1"></i> My attendance
    </a>
</div>

{{-- Subject-wise attendance alerts --}}
@php
    $subjectsAtRisk = $bySubject->filter(
        fn ($s) => $s['rate'] !== null && $s['rate'] < $threshold
    );
@endphp

@if($subjectsAtRisk->isNotEmpty())
    @foreach($subjectsAtRisk as $name => $s)
        <div class="alert alert-danger d-flex gap-2 align-items-start">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <div>
                <strong>
                    Your {{ $name }} attendance is {{ $s['rate'] }}%,
                    below the required {{ $threshold }}%.
                </strong>
                <div>
                    Attend your next {{ $s['needed'] }}
                    {{ \Illuminate\Support\Str::plural('class', $s['needed']) }}
                    in {{ $name }} without missing any to reach the required percentage.
                </div>
            </div>
        </div>
    @endforeach
@elseif($bySubject->isNotEmpty())
    <div class="alert alert-success py-2">
        <i class="bi bi-check-circle-fill me-1"></i>
        Your attendance is meeting the required percentage in every subject.
    </div>
@endif


<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-users"><i class="bi bi-graph-up-arrow"></i></div></div>
        <div class="stat-value">{{ $rate30 !== null ? $rate30 . '%' : '—' }}</div>
        <div class="stat-label">My attendance, last 30 days</div>
        <div class="small text-muted mt-1">{{ $stats['attended'] }} of {{ $stats['marked'] }} classes · minimum {{ $threshold }}%</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-students"><i class="bi bi-check-lg"></i></div></div>
        <div class="stat-value">{{ $present }}</div>
        <div class="stat-label">Present</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-teachers"><i class="bi bi-clock"></i></div></div>
        <div class="stat-value">{{ $late }}</div>
        <div class="stat-label">Late</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-attendance"><i class="bi bi-x-lg"></i></div></div>
        <div class="stat-value">{{ $absent }}</div>
        <div class="stat-label">Absent</div>
    </div>
</div>

<div class="chart-grid mb-4">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Latest sessions</h5>
                <div class="chart-subtitle">Your six most recent classes</div>
            </div>
            <a href="{{ route('student.attendance.index') }}" class="btn btn-sm btn-outline-secondary">Full history</a>
        </div>

        @forelse($recent as $row)
            @php [$colour, $label] = $pills[$row['status']] ?? $pills['unmarked']; @endphp
            <div class="d-flex justify-content-between align-items-center gap-2 py-2 border-bottom">
                <div>
                    <div class="fw-semibold">{{ $row['subject'] }}</div>
                    <div class="small text-muted">{{ $row['date']->format('d M Y') }} · {{ $row['date']->format('l') }}</div>
                </div>
                <span class="badge text-bg-{{ $colour }}">{{ $label }}</span>
            </div>
        @empty
            <p class="text-muted mb-0">No classes were recorded in the last 30 days.</p>
        @endforelse
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">By subject</h5>
                <div class="chart-subtitle">Last 30 days · green means {{ $threshold }}% or more</div>
            </div>
        </div>

        @forelse($bySubject as $name => $s)
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-baseline">
                    <span class="fw-semibold">{{ $name }}</span>
                    <span class="fw-semibold">{{ $s['rate'] }}%</span>
                </div>
                <div class="progress" style="height: 6px;" role="img" aria-label="{{ $name }} attendance {{ $s['rate'] }} percent">
                    <div class="progress-bar {{ $tone($s['rate']) }}" style="width: {{ $s['rate'] }}%"></div>
                </div>
                <div class="small text-muted mt-1">{{ $s['attended'] }} of {{ $s['marked'] }} attended</div>
            </div>
        @empty
            <p class="text-muted mb-0">Subject totals appear once attendance is recorded.</p>
        @endforelse
    </div>
</div>

<div class="chart-grid">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Upcoming assignments</h5>
                <div class="chart-subtitle">Not submitted yet</div>
            </div>
            <a href="{{ route('student.assignment.list') }}" class="btn btn-sm btn-outline-secondary">All</a>
        </div>

        @forelse($assignments as $a)
            <a href="{{ route('student.assignment.show', $a->id) }}" class="d-flex justify-content-between align-items-center gap-3 py-2 border-bottom text-reset">
                <div>
                    <div class="fw-semibold">{{ $a->title }}</div>
                    <div class="small text-muted">{{ $a->subject?->name ?? '—' }}</div>
                </div>
                <div class="text-end text-nowrap small">
                    <div class="fw-semibold">{{ $a->due_at->diffForHumans() }}</div>
                    <div class="text-muted">{{ $a->due_at->format('d M, h:i A') }}</div>
                </div>
            </a>
        @empty
            <p class="text-muted mb-0">Nothing is waiting for you.</p>
        @endforelse
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Latest notices</h5>
                <div class="chart-subtitle">Sent to you or your class</div>
            </div>
            <a href="{{ route('student.notice.view') }}" class="btn btn-sm btn-outline-secondary">All</a>
        </div>

        @forelse($notices as $n)
            <a href="{{ route('student.notice.show', $n->id) }}" class="d-block py-2 border-bottom text-reset">
                <div class="fw-semibold">{{ $n->title }}</div>
                <div class="small text-muted">{{ $n->author?->name ?? 'Unknown' }} · {{ $n->created_at?->format('d M Y') }}</div>
            </a>
        @empty
            <p class="text-muted mb-0">No notices yet.</p>
        @endforelse
    </div>
</div>
@endsection