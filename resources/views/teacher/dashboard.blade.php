@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $barTone = fn ($r) => $r === null ? 'bg-secondary' : ($r >= $threshold ? 'bg-success' : ($r >= $threshold - 15 ? 'bg-warning' : 'bg-danger'));
@endphp

<div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
        <div class="eyebrow">Overview</div>
        <h1>{{ $greeting }}</h1>
        <div class="text-muted small mt-1">{{ now()->format('l, d F Y') }}</div>
    </div>
    <a href="{{ route('teacher.attendance.index') }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i> Mark attendance
    </a>
</div>

@if($isHoliday)
    <div class="alert alert-info py-2">
        <i class="bi bi-cup-hot me-1"></i> Today is a weekly holiday ({{ $holidayLabel }}). No attendance is expected.
    </div>
@endif

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-users"><i class="bi bi-journal-bookmark-fill"></i></div></div>
        <div class="stat-value">{{ $subjects->count() }}</div>
        <div class="stat-label">My subjects</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-students"><i class="bi bi-people-fill"></i></div></div>
        <div class="stat-value">{{ $studentCount }}</div>
        <div class="stat-label">Students in my classes</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-teachers"><i class="bi bi-calendar2-week-fill"></i></div></div>
        <div class="stat-value">{{ $sessionsThisWeek }}</div>
        <div class="stat-label">Sessions saved this week</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon {{ ($rate30 ?? 100) >= $threshold ? 'bg-students' : 'bg-attendance' }}"><i class="bi bi-graph-up-arrow"></i></div>
        </div>
        <div class="stat-value">{{ $rate30 !== null ? $rate30 . '%' : '—' }}</div>
        <div class="stat-label">Attendance rate, last 30 days</div>
        <div class="small text-muted mt-1">
            {{ $stats['attended'] }} of {{ $stats['marked'] }} records · minimum {{ $threshold }}% · late counts as attended
        </div>
    </div>
</div>

<div class="chart-grid mb-4">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Attendance Trend</h5>
                <div class="chart-subtitle">Daily rate in your classes, last 14 working days</div>
            </div>
        </div>
        <canvas id="trendChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Not marked today</h5>
                <div class="chart-subtitle">Subjects with no saved attendance for {{ now()->format('d M Y') }}</div>
            </div>
        </div>

        @if($isHoliday)
            <p class="text-muted mb-0">Weekly holiday ({{ $holidayLabel }}).</p>
        @else
            @forelse($unmarkedToday as $subject)
                <div class="d-flex justify-content-between align-items-center gap-2 py-2 border-bottom">
                    <div>
                        <div class="fw-semibold">{{ $subject->name }}</div>
                        <div class="small text-muted">
                            {{ $subject->classroom?->faculty?->name ?? '—' }} · Semester {{ $subject->classroom?->semester ?? '—' }}
                        </div>
                    </div>
                    <a class="btn btn-sm btn-primary text-nowrap"
                       href="{{ route('teacher.attendance.index', ['subject_id' => $subject->id, 'date' => now()->toDateString()]) }}">
                        Mark
                    </a>
                </div>
            @empty
                <p class="text-muted mb-0">
                    @if($subjects->isEmpty())
                        No subjects are assigned to your account yet.
                    @else
                        All your subjects are marked for today.
                    @endif
                </p>
            @endforelse
        @endif
    </div>
</div>

<div class="chart-grid mb-4">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Students below {{ $threshold }}%</h5>
                <div class="chart-subtitle">In your subjects, last 30 days · after at least {{ $minSessions }} recorded classes</div>
            </div>
            @if($atRiskCount > 0)
                <span class="badge text-bg-danger">{{ $atRiskCount }}</span>
            @endif
        </div>

        @forelse($atRisk as $row)
            <div class="d-flex justify-content-between align-items-center gap-3 py-2 border-bottom">
                <div>
                    <div class="fw-semibold">{{ $row['name'] }}</div>
                    <div class="small text-muted">{{ $row['class'] }}</div>
                </div>
                <div class="text-end text-nowrap">
                    <div class="fw-semibold text-danger">{{ $row['rate'] }}%</div>
                    <div class="small text-muted">{{ $row['attended'] }} of {{ $row['marked'] }} attended</div>
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">No student is below the minimum in your subjects.</p>
        @endforelse

        @if($atRiskCount > $atRisk->count())
            <div class="small text-muted pt-2">+ {{ $atRiskCount - $atRisk->count() }} more students below {{ $threshold }}%</div>
        @endif
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">My subjects</h5>
                <div class="chart-subtitle">Attendance rate, last 30 days</div>
            </div>
        </div>

        @forelse($bySubject as $row)
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-baseline">
                    <span class="fw-semibold">{{ $row['name'] }}</span>
                    <span class="fw-semibold">{{ $row['rate'] !== null ? $row['rate'] . '%' : '—' }}</span>
                </div>
                <div class="progress" style="height: 6px;" role="img" aria-label="{{ $row['name'] }} attendance {{ $row['rate'] ?? 0 }} percent">
                    <div class="progress-bar {{ $barTone($row['rate']) }}" style="width: {{ $row['rate'] ?? 0 }}%"></div>
                </div>
                <div class="small text-muted mt-1">
                    {{ $row['class'] }} · {{ $row['sessions'] }} {{ \Illuminate\Support\Str::plural('session', $row['sessions']) }}
                </div>
            </div>
        @empty
            <p class="text-muted mb-0">No subjects are assigned to your account yet.</p>
        @endforelse
    </div>
</div>

<div class="chart-card">
    <div class="chart-header">
        <div>
            <h5 class="chart-title">Latest sessions</h5>
            <div class="chart-subtitle">Your six most recent saved sessions</div>
        </div>
        <a href="{{ route('teacher.attendance.reports') }}" class="btn btn-sm btn-outline-secondary">All sessions</a>
    </div>

    @forelse($recent as $row)
        <div class="d-flex justify-content-between align-items-center gap-3 py-2 border-bottom">
            <div>
                <div class="fw-semibold">{{ $row['subject'] }}</div>
                <div class="small text-muted">{{ $row['class'] }}</div>
            </div>
            <div class="text-end text-nowrap">
                <div class="fw-semibold">{{ $row['rate'] !== null ? $row['rate'] . '%' : '—' }}</div>
                <div class="small text-muted">
                    {{ $row['counts']['present'] }} P · {{ $row['counts']['late'] }} L · {{ $row['counts']['absent'] }} A
                    · {{ $row['date']->format('d M Y') }}
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted mb-0">You haven't saved any attendance in the last 30 days.</p>
    @endforelse
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    var trend = document.getElementById('trendChart');
    if (!trend) return;

    new Chart(trend, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Attendance rate (%)',
                data: @json($trendValues),
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.08)',
                fill: true,
                tension: 0.3,
                spanGaps: true,
                pointRadius: 3
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { min: 0, max: 100, ticks: { callback: function (v) { return v + '%'; } } } }
        }
    });
})();
</script>
@endpush