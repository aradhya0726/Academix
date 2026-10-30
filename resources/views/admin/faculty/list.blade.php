@extends('layouts.app')

@section('title', 'Faculty List')

@section('content')

    <div class="page-heading" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;">
        <div>
            <div class="eyebrow">Academic Management</div>
            <h1>Faculty List</h1>
        </div>
        <div>
            <a href="{{ route('faculty.add') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#2563EB;color:#ffffff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.1);transition:all 0.2s ease;text-decoration:none;" onmouseover="this.style.background='#1D4ED8'; this.style.boxShadow='0 2px 6px rgba(37,99,235,0.35)'" onmouseout="this.style.background='#2563EB'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                <span style="font-size:17px;line-height:1;font-weight:400;">+</span> Add New Faculty
            </a>
        </div>
    </div>

    @if (session('success'))
        <div style="background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background:#FEF2F2;color:#B91C1C;border:1px solid #FECACA;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="content-wrapper">
        <form method="GET" action="{{ route('faculty.list') }}" class="mb-4">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search faculty by name..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Active</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('faculty.list') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Faculties</h3>
            </div>

            <div class="card-body p-0">
                @if (request('search'))
                    <div class="alert alert-info">
                        Showing results for: <strong>{{ request('search') }}</strong>
                    </div>
                @endif

                @if ($faculties->count())
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Faculty Name</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th style="width: 140px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($faculties as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($faculties->currentPage() - 1) * $faculties->perPage() }}.</td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        @if ($item->status == 0)
                                            <span style="display:inline-block;padding:3px 10px;border-radius:999px;background:#F0FDF4;color:#15803D;font-size:12px;font-weight:600;">Active</span>
                                        @else
                                            <span style="display:inline-block;padding:3px 10px;border-radius:999px;background:rgba(220,38,38,0.08);color:#DC2626;font-size:12px;font-weight:600;">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_by ?? '—' }}</td>
                                    <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '—' }}</td>
                                    <td>
                                        <a href="{{ route('faculty.edit', $item->id) }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;background:var(--primary-soft);color:var(--primary);text-decoration:none;margin-right:6px;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('faculty.delete', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this faculty? This cannot be undone.');">
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
                        {{ $faculties->links() }}
                    </div>
                @else
                    <div style="padding:40px 20px;text-align:center;color:var(--text-secondary,#64748B);">
                        No faculties found yet. <a href="{{ route('faculty.add') }}" style="color:var(--primary,#2563EB);font-weight:600;">Add your first faculty</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection
