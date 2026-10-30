@extends('layouts.app')

@section('title', 'Add Subject')

@section('content')

    <div class="page-heading">
        <div class="eyebrow">Academic Management</div>
        <h1>Add New Subject</h1>
    </div>

    <div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Subject Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('subject.store') }}" method="POST">
                    @csrf

                    @include('admin.subject._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Subject
                        </button>
                        <a href="{{ route('subject.list') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection