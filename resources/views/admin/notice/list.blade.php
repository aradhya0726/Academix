@extends('layouts.app')

@section('page-title', request()->boolean('mine') ? 'My Notices' : 'All Notices')

@section('content')
@php $mine = request()->boolean('mine'); @endphp

<div class="content-panel">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">{{ $mine ? 'My Notices' : 'All Notices' }}</h1>
            <p class="text-muted mb-0">
                {{ $mine ? 'Notices you have posted' : 'Every notice in the system' }}
            </p>
        </div>
        <a href="{{ route('admin.notice.add') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Notice
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.notice.list') }}" class="row g-2 mb-4">
        @if ($mine)
            <input type="hidden" name="mine" value="1">
        @endif
        <div class="col">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search...">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.notice.list', $mine ? ['mine' => 1] : []) }}" class="btn btn-secondary">Clear</a>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="">
                    <tr>
                        <th>Notice</th>
                        <th>Sent To</th>
                        <th>Posted By</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notices as $notice)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($notice->image_url)
                                        <img src="{{ $notice->image_url }}" alt=""
                                             class="rounded border" style="width:56px;height:56px;object-fit:cover;">
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $notice->title }}</div>
                                        <div class="text-muted small">
                                            {{ \Illuminate\Support\Str::limit($notice->message, 80) }}
                                        </div>
                                        <div class="small mt-1">
                                            @if ($notice->link)
                                                <a href="{{ $notice->link }}" target="_blank" rel="noopener" class="me-2">
                                                    <i class="bi bi-link-45deg"></i> Link
                                                </a>
                                            @endif
                                            @if ($notice->attachment_url)
                                                <a href="{{ $notice->attachment_url }}" target="_blank">
                                                    <i class="bi bi-paperclip"></i> {{ $notice->attachment_name ?? 'File' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($notice->audience === 'class')
                                    {{ $notice->classroom?->faculty?->name ?? 'Unknown' }} – Semester {{ $notice->classroom?->semester }}
                                @else
                                    {{ ['all' => 'Everyone', 'teachers' => 'All Teachers', 'students' => 'All Students'][$notice->audience] ?? ucfirst($notice->audience) }}
                                @endif
                            </td>
                            <td>{{ $notice->author?->name ?? '—' }}</td>
                            <td class="text-muted small">{{ $notice->created_at?->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.notice.edit', $notice->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.notice.delete', $notice->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this notice?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                {{ $mine ? "You haven't posted any notices yet." : 'No notices found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $notices->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection