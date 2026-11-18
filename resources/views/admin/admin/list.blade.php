
@extends('layouts.app')

@section('title', 'Admin List')

@section('content')

    <div class="page-heading" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;">
        <div>
            <div class="eyebrow">Overview</div>
            <h1>Admin List</h1>
        </div>
        <div>
            <a href="{{ route('admin.add') }}" style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;background:#2563EB;color:#ffffff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.1);transition:all 0.2s ease;text-decoration:none;" onmouseover="this.style.background='#1D4ED8'; this.style.boxShadow='0 2px 6px rgba(37,99,235,0.35)'" onmouseout="this.style.background='#2563EB'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                <span style="font-size:17px;line-height:1;font-weight:400;">+</span> Add New Admin
            </a>
        </div>
    </div>

    @if (session('success'))
        <div style="background:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-wrapper">

        <form method="GET" action="{{ route('admin.list') }}" class="mb-4">
            <div class="row g-2">

                <div class="col-md-5">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search admin by name, email or phone..."
                        value="{{ request('search') }}"
                        aria-label="Search admins"
                        style="color:#334155;"
                    >
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                        Search
                    </button>
                </div>

                <div class="col-md-2">
                    <a href="{{ route('admin.list') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-x-circle"></i>
                        Clear
                    </a>
                </div>

            </div>
        </form>

        <style>
            input[name="search"]::placeholder {
                color: #94A3B8 !important;
                opacity: 1 !important;
            }

            input[name="search"]::-webkit-input-placeholder {
                color: #94A3B8 !important;
                opacity: 1 !important;
            }

            input[name="search"]::-moz-placeholder {
                color: #94A3B8 !important;
                opacity: 1 !important;
            }

            input[name="search"]:-ms-input-placeholder {
                color: #94A3B8 !important;
                opacity: 1 !important;
            }
        </style>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Admins</h3>
            </div>

            <div class="card-body p-0">

                @if(request('search'))
                    <div class="alert alert-info">
                        Showing results for:
                        <strong>{{ request('search') }}</strong>
                    </div>
                @endif

                @if ($admins->count())

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th style="width: 90px">Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th style="width: 140px">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($admins as $admin)
                                <tr>
                                    <td>
                                        {{ $loop->iteration + ($admins->currentPage() - 1) * $admins->perPage() }}.
                                    </td>

                                    <td>
                                        @if ($admin->photo)
                                            <img
                                                src="{{ asset('uploads/profile/' . $admin->photo) }}"
                                                alt="{{ $admin->name }}"
                                                style="width:38px;height:38px;border-radius:50%;object-fit:cover;"
                                            >
                                        @else
                                            <span style="font-size:12px;color:var(--text-secondary,#64748B);">
                                                No photo
                                            </span>
                                        @endif
                                    </td>

                                    <td>{{ $admin->name }}</td>

                                    <td>{{ $admin->email }}</td>

                                    <td>{{ $admin->phone_number ?? '—' }}</td>

                                    <td>
                                        <a
                                            href="{{ route('admin.edit', $admin->id) }}"
                                            title="Edit Admin"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;background:var(--primary-soft);color:var(--primary);text-decoration:none;margin-right:6px;"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form
                                            action="{{ route('admin.delete', $admin->id) }}"
                                            method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Delete this admin? This cannot be undone.');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Delete Admin"
                                                style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;background:rgba(220,38,38,0.08);color:#DC2626;border:none;cursor:pointer;"
                                            >
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div style="padding:16px 20px;">
                        {{ $admins->links() }}
                    </div>

                @else

                    <div style="padding:40px 20px;text-align:center;color:var(--text-secondary,#64748B);">
                        No admins found yet.
                        <a href="{{ route('admin.add') }}" style="color:var(--primary,#2563EB);font-weight:600;">
                            Add your first admin
                        </a>.
                    </div>

                @endif

            </div>
        </div>

    </div>

@endsection
