@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $plural  = fn ($n, $word) => $n . ' ' . \Illuminate\Support\Str::plural($word, $n);
    $barTone = fn ($r) => $r === null ? 'bg-secondary' : ($r >= $threshold ? 'bg-success' : ($r >= $threshold - 15 ? 'bg-warning' : 'bg-danger'));
@endphp

<div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
        <div class="eyebrow">Overview</div>
        <h1>{{ $greeting }}</h1>
        <div class="text-muted small mt-1">{{ now()->format('l, d F Y') }}</div>
    </div>
    <a href="{{ route('admin.attendance.reports') }}" class="btn btn-outline-primary">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Attendance reports
    </a>
</div>

@if($isHoliday)
    <div class="alert alert-info py-2">
        <i class="bi bi-cup-hot me-1"></i> Today is a weekly holiday ({{ $holidayLabel }}). No attendance is expected.
    </div>
@endif

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-users"><i class="bi bi-people-fill"></i></div></div>
        <div class="stat-value">{{ number_format($totalUsers) }}</div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-students"><i class="bi bi-mortarboard-fill"></i></div></div>
        <div class="stat-value">{{ number_format($totalStudents) }}</div>
        <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon bg-teachers"><i class="bi bi-person-workspace"></i></div></div>
        <div class="stat-value">{{ number_format($totalTeachers) }}</div>
        <div class="stat-label">Total Teachers</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon {{ ($rate30 ?? 100) >= $threshold ? 'bg-students' : 'bg-attendance' }}"><i class="bi bi-calendar2-check-fill"></i></div>
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
                <div class="chart-subtitle">
                    Daily rate across all classes, last 14 working days
                    · {{ $plural($sessionsToday, 'session') }} saved today
                </div>
            </div>
        </div>
        <canvas id="trendChart"></canvas>
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Students by Program</h5>
                <div class="chart-subtitle">Enrolled students per faculty</div>
            </div>
        </div>
        @if(array_sum($facultyValues) > 0)
            <canvas id="facultyChart"></canvas>
        @else
            <p class="text-muted mb-0">No students are enrolled in a class yet.</p>
        @endif
    </div>
</div>

<div class="chart-grid mb-4">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Students below {{ $threshold }}%</h5>
                <div class="chart-subtitle">Last 30 days · after at least {{ $minSessions }} recorded classes</div>
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
            <p class="text-muted mb-0">No student is below the minimum.</p>
        @endforelse

        @if($atRiskCount > $atRisk->count())
            <div class="small text-muted pt-2">+ {{ $atRiskCount - $atRisk->count() }} more students below {{ $threshold }}%</div>
        @endif
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Not marked yet today</h5>
                <div class="chart-subtitle">Active subjects with no saved attendance</div>
            </div>
            @if($pendingCount > 0)
                <span class="badge text-bg-warning">{{ $pendingCount }}</span>
            @endif
        </div>

        @if($isHoliday)
            <p class="text-muted mb-0">Weekly holiday ({{ $holidayLabel }}).</p>
        @else
            @forelse($pending as $row)
                <div class="py-2 border-bottom">
                    <div class="fw-semibold">{{ $row['subject'] }}</div>
                    <div class="small text-muted">{{ $row['class'] }} · {{ $row['teacher'] }}</div>
                </div>
            @empty
                <p class="text-muted mb-0">Every active subject has been marked today.</p>
            @endforelse

            @if($pendingCount > $pending->count())
                <div class="small text-muted pt-2">+ {{ $pendingCount - $pending->count() }} more</div>
            @endif
        @endif
    </div>
</div>

<div class="chart-grid mb-4">
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Attendance by class</h5>
                <div class="chart-subtitle">Last 30 days, lowest first · green means {{ $threshold }}% or more</div>
            </div>
        </div>

        @forelse($byClass as $row)
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-baseline">
                    <span class="fw-semibold">{{ $row['name'] }}</span>
                    <span class="fw-semibold">{{ $row['rate'] !== null ? $row['rate'] . '%' : '—' }}</span>
                </div>
                <div class="progress" style="height: 6px;" role="img" aria-label="{{ $row['name'] }} attendance {{ $row['rate'] ?? 0 }} percent">
                    <div class="progress-bar {{ $barTone($row['rate']) }}" style="width: {{ $row['rate'] ?? 0 }}%"></div>
                </div>
                <div class="small text-muted mt-1">{{ $row['attended'] }} of {{ $row['marked'] }} records attended</div>
            </div>
        @empty
            <p class="text-muted mb-0">No attendance has been recorded in the last 30 days.</p>
        @endforelse
    </div>

    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h5 class="chart-title">Attendance split</h5>
                <div class="chart-subtitle">Last 30 days</div>
            </div>
        </div>
        @if(array_sum($counts) > 0)
            <canvas id="splitChart"></canvas>
        @else
            <p class="text-muted mb-0">No attendance has been recorded in the last 30 days.</p>
        @endif
    </div>
</div>

<div class="chart-card">
    <div class="chart-header">
        <div>
            <h5 class="chart-title">Latest sessions</h5>
            <div class="chart-subtitle">The six most recently saved attendance sessions</div>
        </div>
    </div>

    @forelse($recent as $row)
        <div class="d-flex justify-content-between align-items-center gap-3 py-2 border-bottom">
            <div>
                <div class="fw-semibold">{{ $row['subject'] }}</div>
                <div class="small text-muted">{{ $row['class'] }} · {{ $row['teacher'] }}</div>
            </div>
            <div class="text-end text-nowrap">
                <div class="fw-semibold">{{ $row['rate'] !== null ? $row['rate'] . '%' : '—' }}</div>
                <div class="small text-muted">{{ $row['date']->format('d M Y') }}</div>
            </div>
        </div>
    @empty
        <p class="text-muted mb-0">No attendance has been saved yet.</p>
    @endforelse
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    var palette = ['#2563EB', '#16A34A', '#F59E0B', '#DC2626', '#7C3AED', '#0891B2', '#DB2777', '#64748B'];

    var trend = document.getElementById('trendChart');
    if (trend) {
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
    }

    var faculty = document.getElementById('facultyChart');
    if (faculty) {
        new Chart(faculty, {
            type: 'doughnut',
            data: {
                labels: @json($facultyLabels),
                datasets: [{ data: @json($facultyValues), backgroundColor: palette, borderWidth: 0 }]
            },
            options: { cutout: '65%', plugins: { legend: { position: 'bottom' } } }
        });
    }

    var split = document.getElementById('splitChart');
    if (split) {
        new Chart(split, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Late', 'Absent'],
                datasets: [{
                    data: [{{ $counts['present'] }}, {{ $counts['late'] }}, {{ $counts['absent'] }}],
                    backgroundColor: ['#16A34A', '#F59E0B', '#DC2626'],
                    borderWidth: 0
                }]
            },
            options: { cutout: '65%', plugins: { legend: { position: 'bottom' } } }
        });
    }
})();
</script>
@endpush