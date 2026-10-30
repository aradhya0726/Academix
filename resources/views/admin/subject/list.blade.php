@extends('layouts.app')

@section('title', 'Subject List')

@section('content')

    <div class="page-heading" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;">
        <div>
            <div class="eyebrow">Academic Management</div>
            <h1>Subject List</h1>
        </div>
        <div>
            <a href="{{ route('subject.add') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#2563EB;color:#ffffff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.1);transition:all 0.2s ease;text-decoration:none;" onmouseover="this.style.background='#1D4ED8'; this.style.boxShadow='0 2px 6px rgba(37,99,235,0.35)'" onmouseout="this.style.background='#2563EB'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                <span style="font-size:17px;line-height:1;font-weight:400;">+</span> Add New Subject
            </a>
        </div>
    </div>

    @if (session('success'))
        <div style="background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-wrapper">

        <form method="GET" action="{{ route('subject.list') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="faculty_id" class="form-select">
                        <option value="">All Faculties</option>
                        @foreach ($faculties as $f)
                            <option value="{{ $f->id }}" {{ request('faculty_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="semester" class="form-select">
                        <option value="">All Semesters</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem }}" {{ request('semester') == $sem ? 'selected' : '' }}>
                                Semester {{ $sem }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by name or code..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                        Search
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('subject.list') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Subjects</h3>
            </div>

            <div class="card-body p-0">

                @if ($subjects->count())
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Subject</th>
                                <th>Code</th>
                                <th>Faculty</th>
                                <th>Semester</th>
                                <th>Teacher</th>
                                <th>Status</th>
                                <th style="width: 140px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                                <tr>
                                    <td>{{ $loop->iteration + ($subjects->currentPage() - 1) * $subjects->perPage() }}.</td>
                                    <td>{{ $subject->name }}</td>
                                    <td>{{ $subject->code ?? '—' }}</td>
                                    <td>{{ $subject->classroom?->faculty?->name ?? '—' }}</td>
                                    <td>
                                        @if ($subject->classroom)
                                            Semester {{ $subject->classroom->semester }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $subject->teacher?->name ?? '—' }}</td>
                                    <td>
                                        @if ($subject->status == 0)
                                            <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;background:rgba(22,163,74,0.1);color:#16A34A;">Active</span>
                                        @else
                                            <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;background:rgba(220,38,38,0.1);color:#DC2626;">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('subject.edit', $subject->id) }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;background:var(--primary-soft);color:var(--primary);text-decoration:none;margin-right:6px;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('subject.delete', $subject->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this subject? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;background:rgba(220,38,38,0.08);color:#DC2626;border:none;cursor:pointer;">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div style="padding:16px 20px;">
                        {{ $subjects->links() }}
                    </div>
                @else
                    <div style="padding:40px 20px;text-align:center;color:var(--text-secondary,#64748B);">
                        No subjects found. <a href="{{ route('subject.add') }}" style="color:var(--primary,#2563EB);font-weight:600;">Add a subject</a>.
                    </div>
                @endif

            </div>
        </div>

    </div>

@endsection