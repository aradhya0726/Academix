```blade
@extends('layouts.app')

@section('title', 'Edit Subject')

@section('content')

    <div class="page-heading">
        <div class="eyebrow">Academic Management</div>
        <h1>Edit Subject</h1>
    </div>

    <div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Subject Details</h3>
            </div>

            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('subject.update', $subject->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('admin.subject._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Update Subject
                        </button>

                        <a href="{{ route('subject.list') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection
```