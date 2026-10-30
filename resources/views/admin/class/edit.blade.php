@extends('layouts.app')

@section('title', 'Edit Class')

@section('content')

    <div class="page-heading">
        <div class="eyebrow">Academic Management</div>
        <h1>Edit Class</h1>
    </div>

    <div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Class Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('class.update', $class->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('admin.class._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary" {{ $faculties->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-check-circle"></i> Update Class
                        </button>
                        <a href="{{ route('class.list') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
