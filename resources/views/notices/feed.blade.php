@extends('layouts.app')

@section('page-title', 'Notices')

@section('content')
@php
    $role      = auth()->user()->role;
    $viewRoute = $role === 'teacher' ? 'teacher.notice.view' : 'student.notice.view';
    $showRoute = $role === 'teacher' ? 'teacher.notice.show' : 'student.notice.show';
    $audiences = ['all' => 'Everyone', 'teachers' => 'All Teachers', 'students' => 'All Students'];
@endphp

<div class="content-panel">

    <div class="mb-4">
        <h1 class="page-title">Notices</h1>
        <p class="text-muted mb-0">Every notice in the system</p>
    </div>

    <form method="GET" action="{{ route($viewRoute) }}" class="row g-2 mb-4">
        <div class="col">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search notices...">
        </div>
        <div class="col-auto"><button type="submit" class="btn btn-primary">Search</button></div>
        <div class="col-auto"><a href="{{ route($viewRoute) }}" class="btn btn-secondary">Clear</a></div>
    </form>

    @forelse ($notices as $notice)
        <a href="{{ route($showRoute, $notice->id) }}" class="text-decoration-none text-reset">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body d-flex gap-3">
                    @if ($notice->image_url)
                        <img src="{{ $notice->image_url }}" alt=""
                             class="rounded border flex-shrink-0" style="width:96px;height:96px;object-fit:cover;">
                    @endif

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h5 class="mb-1">{{ $notice->title }}</h5>
                            <small class="text-muted text-nowrap">{{ $notice->created_at?->format('d M Y') }}</small>
                        </div>

                        <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($notice->message, 160) }}</p>

                        <div class="small text-muted d-flex flex-wrap gap-3">
                            <span><i class="bi bi-person"></i> {{ $notice->author?->name ?? 'Unknown' }}</span>
                            <span>
                                <i class="bi bi-people"></i>
                                @if ($notice->audience === 'class')
                                    {{ $notice->classroom?->faculty?->name ?? 'Class' }} – Semester {{ $notice->classroom?->semester }}
                                @else
                                    {{ $audiences[$notice->audience] ?? ucfirst($notice->audience) }}
                                @endif
                            </span>
                            @if ($notice->link)
                                <span><i class="bi bi-link-45deg"></i> Link</span>
                            @endif
                            @if ($notice->attachment)
                                <span><i class="bi bi-paperclip"></i> File</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">No notices found.</div>
        </div>
    @endforelse

    <div class="mt-3">
        {{ $notices->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection