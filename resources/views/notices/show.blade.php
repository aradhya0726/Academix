@extends('layouts.app')

@section('page-title', $notice->title)

@section('content')
@php
    $role      = auth()->user()->role;
    $viewRoute = $role === 'teacher' ? 'teacher.notice.view' : 'student.notice.view';
    $audiences = ['all' => 'Everyone', 'teachers' => 'All Teachers', 'students' => 'All Students'];
@endphp

<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Notice</h1>
        <a href="{{ route($viewRoute) }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to Notices
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <h2 class="h3 mb-2">{{ $notice->title }}</h2>

            <div class="small text-muted d-flex flex-wrap gap-3 mb-4">
                <span><i class="bi bi-person"></i> {{ $notice->author?->name ?? 'Unknown' }}</span>
                <span><i class="bi bi-calendar3"></i> {{ $notice->created_at?->format('d M Y, h:i A') }}</span>
                <span>
                    <i class="bi bi-people"></i>
                    @if ($notice->audience === 'class')
                        {{ $notice->classroom?->faculty?->name ?? 'Class' }} – Semester {{ $notice->classroom?->semester }}
                    @else
                        {{ $audiences[$notice->audience] ?? ucfirst($notice->audience) }}
                    @endif
                </span>
            </div>

            @if ($notice->image_url)
                <img src="{{ $notice->image_url }}" alt="{{ $notice->title }}"
                     class="img-fluid rounded border mb-4" style="max-height:480px;object-fit:contain;">
            @endif

            <div class="mb-4" style="white-space:pre-line;">{{ $notice->message }}</div>

            @if ($notice->link || $notice->attachment_url)
                <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                    @if ($notice->link)
                        <a href="{{ $notice->link }}" target="_blank" rel="noopener" class="btn btn-primary">
                            <i class="bi bi-box-arrow-up-right"></i> Open Link
                        </a>
                    @endif

                    @if ($notice->attachment_url)
                        <a href="{{ $notice->attachment_url }}" target="_blank" class="btn btn-outline-secondary">
                            <i class="bi bi-paperclip"></i> {{ $notice->attachment_name ?? 'Download file' }}
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>

</div>
@endsection