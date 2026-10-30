@extends('layouts.app')

@section('page-title', 'Create Assignment')

@section('content')
<div class="content-panel">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Create Assignment</h1>
            <p class="text-muted">Students of the subject's class will see it</p>
        </div>
        <a href="{{ route('teacher.assignment.list') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> My Assignments
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @include('assignments._form', ['assignment' => null])
        </div>
    </div>
</div>
@endsection