@extends('layouts.app')

@section('title', 'Attendance Reports')

@section('content')
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $isAdmin     = Auth::user()->role === 'admin';
    $reportsUrl  = route($isAdmin ? 'admin.attendance.reports' : 'teacher.attendance.reports');
    $exportRoute = $isAdmin ? 'admin.attendance.export' : 'teacher.attendance.export';

    $rows = method_exists($sessions, 'getCollection') ? $sessions->getCollection() : collect($sessions);

    $tally = $rows->map(function ($s) {
        $c = $s->records->countBy('status');
        return [
            'present' => (int) ($c['present'] ?? 0),
            'late'    => (int) ($c['late'] ?? 0),
            'absent'  => (int) ($c['absent'] ?? 0),
        ];
    });

    $totalSessions = method_exists($sessions, 'total') ? $sessions->total() : $rows->count();
    $isPaginated   = method_exists($sessions, 'hasPages') && $sessions->hasPages();

    // Quick ranges
    $today = Carbon::today();
    $presets = [
        'Today'        => [$today->toDateString(), $today->toDateString()],
        'Last 7 days'  => [$today->copy()->subDays(6)->toDateString(), $today->toDateString()],
        'This month'   => [$today->copy()->startOfMonth()->toDateString(), $today->toDateString()],
        'Last 30 days' => [$today->copy()->subDays(29)->toDateString(), $today->toDateString()],
    ];
    $activePreset = collect($presets)->filter(fn ($r) => request('from') === $r[0] && request('to') === $r[1])->keys()->first();
    $hasFilter = request()->filled('from') || request()->filled('to');
@endphp

@include('attendance._styles')

<style>
    .rpt-btn-dl { display: inline-flex; align-items: center; gap: .4rem; white-space: nowrap; }
    .rpt-main   { font-weight: 600; line-height: 1.3; }
    .rpt-meta   { display: block; font-size: .8125rem; color: #6b7280; line-height: 1.35; }
    .rpt-rate   { font-weight: 600; font-variant-numeric: tabular-nums; }
    .rpt-bar    { min-width: 150px; }
    .rpt-date   { white-space: nowrap; }
</style>

<div class="container-fluid py-4 att-page">

    <div class="att-head">
        <div>
            <h2 class="page-heading">Attendance Reports</h2>
            <p>Filter saved attendance sessions and download the full class list.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="att-card att-card-pad mb-4">
        <form method="GET" action="{{ $reportsUrl }}" class="row g-3 align-items-end">
            <div class="col-sm-6 col-lg-3">
                <label class="form-label" for="att-from">From date</label>
                <input id="att-from" class="form-control" type="date" name="from" value="{{ request('from') }}">
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label" for="att-to">To date</label>
                <input id="att-to" class="form-control" type="date" name="to" value="{{ request('to') }}">
            </div>
            <div class="col-lg-6 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Apply filter
                </button>
                @if($hasFilter)
                    <a class="btn btn-outline-secondary" href="{{ $reportsUrl }}">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear
                    </a>
                @endif
            </div>
        </form>

        <div class="att-chips mt-3">
            @foreach($presets as $label => [$f, $t])
                <a class="att-chip {{ $activePreset === $label ? 'is-active' : '' }}"
                   href="{{ $reportsUrl }}?from={{ $f }}&to={{ $t }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="att-card overflow-hidden">
        <div class="att-card-pad border-bottom small text-muted">
            {{ $totalSessions }} {{ Str::plural('session', $totalSessions) }} found
        </div>

        <div class="table-responsive">
            <table class="table att-table align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject &amp; class</th>
                        <th>Teacher</th>
                        <th>Attendance</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($rows as $i => $item)
                        @php
                            $t     = $tally[$i];
                            $total = $t['present'] + $t['late'] + $t['absent'];
                            $sum   = max(1, $total);
                            $rate  = $total > 0 ? round((($t['present'] + $t['late']) / $total) * 100) : null;
                        @endphp
                        <tr>
                            <td class="rpt-date">{{ $item->attendance_date->format('d M Y') }}</td>
                            <td>
                                <span class="rpt-main">{{ $item->subject?->name ?? '—' }}</span>
                                <span class="rpt-meta">
                                    {{ $item->classroom?->faculty?->name ?? '—' }} · Semester {{ $item->classroom?->semester ?? '—' }}
                                </span>
                            </td>
                            <td>{{ $item->teacher?->name ?? '—' }}</td>
                            <td class="rpt-bar">
                                <span class="rpt-rate">{{ $rate !== null ? $rate . '%' : '—' }}</span>
                                <div class="att-split mt-1" role="img"
                                     aria-label="{{ $t['present'] }} present, {{ $t['late'] }} late, {{ $t['absent'] }} absent">
                                    <i class="p" style="width: {{ $t['present'] / $sum * 100 }}%"></i>
                                    <i class="l" style="width: {{ $t['late'] / $sum * 100 }}%"></i>
                                    <i class="a" style="width: {{ $t['absent'] / $sum * 100 }}%"></i>
                                </div>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-success rpt-btn-dl"
                                   href="{{ route($exportRoute, $item->id) }}"
                                   title="Download full class list as CSV">
                                    <i class="bi bi-download"></i><span>Download</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="att-empty">
                                    <i class="bi bi-inbox"></i>
                                    <strong>No attendance sessions found</strong>
                                    {{ $hasFilter ? 'Try a wider date range or clear the filter.' : 'Sessions appear here once a teacher saves attendance.' }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($isPaginated)
            <div class="att-card-pad border-top">
                {{ $sessions->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection