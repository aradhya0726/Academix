@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    $today     = Carbon::today();
    $fromLabel = Carbon::parse($from)->format('d M Y');
    $toLabel   = Carbon::parse($to)->format('d M Y');
    $attended  = $present + $late;
    $rate      = (float) $percentage;
    $tone = fn ($r) => $r >= $minimum ? '' : ($r >= $minimum - 15 ? 'is-warn' : 'is-low');
    $ringClass = $tone($rate);
    $share = fn ($n) => $marked > 0 ? round($n / $marked * 100) : 0;

    $indexUrl = route('student.attendance.index');
    $presets = [
        'This month'   => [$today->copy()->startOfMonth()->toDateString(), $today->toDateString()],
        'Last 30 days' => [$today->copy()->subDays(29)->toDateString(), $today->toDateString()],
        'Last 90 days' => [$today->copy()->subDays(89)->toDateString(), $today->toDateString()],
    ];
    $activePreset = collect($presets)->filter(fn ($r) => $from === $r[0] && $to === $r[1])->keys()->first();

    // Per-subject breakdown (only sessions that have a record for this student)
    $items = $sessions instanceof \Illuminate\Pagination\AbstractPaginator
        ? $sessions->getCollection()
        : collect($sessions);

    // Show the Remarks column only when at least one row has a remark
    $hasRemarks = $items->contains(fn ($s) => filled($s->records->first()?->remarks));
    $colCount   = $hasRemarks ? 4 : 3;

    $bySubject = $items
        ->groupBy(fn ($s) => $s->subject?->name ?? 'Unknown subject')
        ->map(function ($group) {
            $marked = 0;
            $attendedCount = 0;
            foreach ($group as $s) {
                $st = $s->records->first()?->status;
                if (! $st) { continue; }
                $marked++;
                if (in_array($st, ['present', 'late'], true)) { $attendedCount++; }
            }
            return [
                'attended' => $attendedCount,
                'marked'   => $marked,
                'rate'     => $marked > 0 ? round($attendedCount / $marked * 100) : null,
            ];
        })
        ->sortKeys();
@endphp

@include('attendance._styles')

<div class="container-fluid py-4 att-page">

    <div class="att-head">
        <div>
            <h2 class="page-heading">My Attendance</h2>
            <p>Track your attendance and download a report for any date range.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-warning">{{ session('error') }}</div>
    @endif

    {{-- Date range: filter and download --}}
    <div class="att-card att-card-pad mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h5 class="att-card-title mb-0">Date range</h5>
            <div class="att-chips">
                @foreach($presets as $label => [$f, $t])
                    <a class="att-chip {{ $activePreset === $label ? 'is-active' : '' }}"
                       href="{{ $indexUrl }}?from={{ $f }}&to={{ $t }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <form method="GET" action="{{ $indexUrl }}" class="att-toolbar">
            <div class="att-field">
                <label class="form-label" for="att-from">From</label>
                <input id="att-from" type="date" class="form-control" name="from" value="{{ $from }}" required>
            </div>
            <div class="att-field">
                <label class="form-label" for="att-to">To</label>
                <input id="att-to" type="date" class="form-control" name="to" value="{{ $to }}" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-arrow-repeat me-1"></i> Update
            </button>
            <a class="btn btn-outline-success text-nowrap"
               href="{{ route('student.attendance.export', ['from' => $from, 'to' => $to]) }}">
                <i class="bi bi-download me-1"></i> Download 
            </a>
        </form>

        <p class="small text-muted mt-3 mb-0">For a single day, set From and To to the same date.</p>
    </div>

    {{-- Statistics --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-6">
            <div class="att-card att-card-pad att-stat h-100">
                <div class="att-ring {{ $ringClass }}" style="--p: {{ min(100, max(0, $rate)) }}"
                     role="img" aria-label="Attendance rate {{ $percentage }} percent">
                    <span>{{ $percentage }}%</span>
                </div>
                <div>
                    <div class="att-stat-label">Attendance rate</div>
                    <div class="fw-semibold fs-5">
                       {{ $attended }} of {{ $marked }} {{ Str::plural('class', $marked) }} attended
                    </div>
                    <div class="small text-muted">Present and late both count as attended. Minimum required: {{ $minimum }}%.</div>
                </div>
            </div>
        </div>

        <div class="col-4 col-xl-2">
            <div class="att-card att-card-pad h-100">
                <div class="att-stat-label">Present</div>
                <div class="att-stat-value">{{ $present }}</div>
                <span class="att-sub">{{ $share($present) }}%</span>
            </div>
        </div>
        <div class="col-4 col-xl-2">
            <div class="att-card att-card-pad h-100">
                <div class="att-stat-label">Late</div>
                <div class="att-stat-value">{{ $late }}</div>
                <span class="att-sub">{{ $share($late) }}%</span>
            </div>
        </div>
        <div class="col-4 col-xl-2">
            <div class="att-card att-card-pad h-100">
                <div class="att-stat-label">Absent</div>
                <div class="att-stat-value">{{ $absent }}</div>
                <span class="att-sub">{{ $share($absent) }}%</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- History --}}
        <div class="col-xl-8">
            <div class="att-card overflow-hidden h-100">
                <div class="att-card-pad d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                    <h5 class="att-card-title mb-0">Attendance history</h5>
                    <span class="small text-muted">
                        {{ $fromLabel }} to {{ $toLabel }} &nbsp;|&nbsp; {{ $marked }} {{ Str::plural('class', $marked) }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table att-table align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Subject</th>
                                <th>Status</th>
                                @if($hasRemarks)
                                    <th>Remarks</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                @php
                                    $record = $item->records->first();
                                    $status = $record?->status ?? 'unmarked';
                                    $pill = match ($status) {
                                        'present' => ['present', 'Present'],
                                        'late'    => ['late', 'Late'],
                                        'absent'  => ['absent', 'Absent'],
                                        default   => ['none', 'Not recorded'],
                                    };
                                @endphp
                                <tr>
                                    <td class="text-nowrap">
                                        {{ $item->attendance_date->format('d M Y') }}
                                        <span class="att-sub">{{ $item->attendance_date->format('l') }}</span>
                                    </td>
                                    <td>{{ $item->subject?->name ?? '—' }}</td>
                                    <td><span class="att-pill {{ $pill[0] }}">{{ $pill[1] }}</span></td>
                                    @if($hasRemarks)
                                        <td class="text-muted">{{ $record?->remarks ?: '—' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $colCount }}">
                                        <div class="att-empty">
                                            <i class="bi bi-calendar2-x"></i>
                                            <strong>No classes in this range</strong>
                                            Pick a wider date range to see more of your attendance.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($unmarked > 0)
                    <div class="att-card-pad border-top small text-muted">
                        {{ $unmarked }} {{ Str::plural('class', $unmarked) }} in this range
                        {{ $unmarked === 1 ? 'has' : 'have' }} no attendance record for you yet.
                    </div>
                @endif
            </div>
        </div>

        {{-- By subject --}}
        <div class="col-xl-4">
            <div class="att-card overflow-hidden h-100">
                <div class="att-card-pad border-bottom">
                    <h5 class="att-card-title mb-0">By subject</h5>
                </div>

                @forelse($bySubject as $name => $s)
                    <div class="att-list-row">
                        <div class="d-flex justify-content-between align-items-baseline gap-2 mb-2">
                            <div class="fw-semibold">{{ $name }}</div>
                            <div class="fw-semibold" style="font-variant-numeric: tabular-nums;">
                                {{ $s['rate'] !== null ? $s['rate'].'%' : '—' }}
                            </div>
                        </div>
                        <div class="att-meter {{ $s['rate'] !== null ? $tone($s['rate']) : '' }}"
                             role="img" aria-label="{{ $name }} attendance {{ $s['rate'] ?? 0 }} percent">
                            <i style="width: {{ $s['rate'] ?? 0 }}%"></i>
                        </div>
                        <span class="att-sub mt-1">
                            {{ $s['attended'] }} of {{ $s['marked'] }} {{ Str::plural('class', $s['marked']) }} attended
                        </span>
                    </div>
                @empty
                    <div class="att-empty">
                        <i class="bi bi-journal-x"></i>
                        <strong>No subjects yet</strong>
                        Subject totals appear once attendance is recorded.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection