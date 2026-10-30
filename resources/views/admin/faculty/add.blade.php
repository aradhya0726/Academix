@extends('layouts.app')

@section('title', 'Add Faculty')

@section('content')

    <div class="page-heading">
        <div class="eyebrow">Academic Management</div>
        <h1>Add New Faculty</h1>
    </div>

    <div class="content-wrapper">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Faculty Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('faculty.store') }}" method="POST">
                    @csrf

                    @include('admin.faculty._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Faculty
                        </button>
                        <a href="{{ route('faculty.list') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
