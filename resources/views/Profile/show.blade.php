@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
<style>
    .pf-avatar-lg {
        width: 120px; height: 120px; border-radius: 50%; overflow: hidden;
        margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;
        background: var(--primary); color: #fff; font-size: 2.2rem; font-weight: 700;
        border: 4px solid var(--card-bg); box-shadow: 0 0 0 2px var(--border);
    }
    .pf-avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
    .pf-row { display: flex; justify-content: space-between; gap: 1rem; padding: 0.85rem 0; border-bottom: 1px solid var(--border); }
    .pf-row:last-child { border-bottom: 0; }
    .pf-label { color: var(--text-secondary); font-size: 0.84rem; }
    .pf-value { font-weight: 600; text-align: right; word-break: break-word; }
</style>
@endpush

@section('content')
<div class="page-heading d-flex flex-wrap justify-content-between align-items-end gap-2">
    <div>
        <div class="eyebrow">Account</div>
        <h1>My Profile</h1>
    </div>
    <a href="{{ route('account.settings') }}" class="btn btn-primary">
        <i class="bi bi-gear me-1"></i> Account settings
    </a>
</div>

@if (session('profile_success'))
    <div class="alert alert-success">{{ session('profile_success') }}</div>
@endif

<div class="row g-4">

    <div class="col-lg-4">
        <div class="chart-card text-center">
            <div class="pf-avatar-lg">
                @if ($user->photoUrl())
                    <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}">
                @else
                    {{ $user->initials() }}
                @endif
            </div>
            <h5 class="mb-1" style="font-weight:700;">{{ $user->name }}</h5>
            <span class="badge text-bg-primary text-capitalize">{{ $user->role }}</span>
            <div class="small text-muted mt-2">{{ $user->email }}</div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h5 class="chart-title">Account details</h5>
                    <div class="chart-subtitle">To change your email or role, contact an administrator.</div>
                </div>
            </div>

            <div class="pf-row"><span class="pf-label">Full name</span><span class="pf-value">{{ $user->name }}</span></div>
            <div class="pf-row"><span class="pf-label">Email</span><span class="pf-value">{{ $user->email }}</span></div>
            <div class="pf-row"><span class="pf-label">Phone</span><span class="pf-value">{{ $user->phone_number ?: '—' }}</span></div>
            <div class="pf-row"><span class="pf-label">Role</span><span class="pf-value text-capitalize">{{ $user->role }}</span></div>

            @if ($user->role === 'student')
                <div class="pf-row">
                    <span class="pf-label">Class</span>
                    <span class="pf-value">
                        @if ($user->classroom)
                            {{ $user->classroom->faculty->name ?? 'Unknown' }} · Semester {{ $user->classroom->semester }}
                        @else
                            Not assigned
                        @endif
                    </span>
                </div>
                <div class="pf-row"><span class="pf-label">Enrolled year</span><span class="pf-value">{{ $user->enrolled_year ?? '—' }}</span></div>
            @endif

            <div class="pf-row"><span class="pf-label">Member since</span><span class="pf-value">{{ $user->created_at?->format('d M Y') ?? '—' }}</span></div>
        </div>
    </div>

</div>
@endsection