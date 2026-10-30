@extends('layouts.app')

@section('page-title', 'Create Notice')

@section('content')

<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Create Notice</h1>
            <p class="text-muted">Send a notice to one of your classes</p>
        </div>

        <a href="{{ route('teacher.notice.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> My Notices
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Notice Details</h3>
        </div>
        <div class="card-body">
            @include('notices._form', [
                'mode'    => 'teacher',
                'action'  => route('teacher.notice.store'),
                'notice'  => null,
                'classes' => $classes,
                'submit'  => 'Post Notice',
                'cancel'  => route('teacher.notice.list'),
            ])
        </div>
    </div>

</div>

@endsection