@extends('layouts.app')

@section('page-title', 'Edit Notice')

@section('content')
<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Edit Notice</h1>
            <p class="text-muted mb-0">Update your notice</p>
        </div>
        <a href="{{ route('teacher.notice.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to My Notices
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header py-3">
            <h3 class="card-title mb-0">Notice Details</h3>
        </div>
        <div class="card-body p-4">
            @include('notices._form', [
                'mode'     => 'teacher',
                'action'   => route('teacher.notice.update', $notice->id),
                'notice'   => $notice,
                'classes'  => $classes,
                'cancel'   => route('teacher.notice.list'),
                'showFile' => true,
            ])
        </div>
    </div>

</div>
@endsection