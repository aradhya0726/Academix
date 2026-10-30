@extends('layouts.app')

@section('title', 'Edit Faculty')

@section('content')

    <div class="page-heading">
        <div class="eyebrow">Academic Management</div>
        <h1>Edit Faculty</h1>
    </div>

    <div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Faculty Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.update', $faculty->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include('admin.faculty._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Faculty
                        </button>
                        <a href="{{ route('faculty.list') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
