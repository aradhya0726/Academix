@extends('layouts.app')

@section('page-title', 'Create Notice')

@section('content')
<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Create Notice</h1>
            <p class="text-muted mb-0">Share an announcement with teachers, students or a specific class</p>
        </div>
        <a href="{{ route('admin.notice.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header py-3">
            <h3 class="card-title mb-0">Notice Details</h3>
        </div>
        <div class="card-body p-4">
            @include('notices._form', [
                'mode'     => 'admin',
                'action'   => route('admin.notice.store'),
                'notice'   => null,
                'classes'  => $classes,
                'cancel'   => route('admin.notice.list'),
                'showFile' => true,
            ])
        </div>
    </div>

</div>
@endsection