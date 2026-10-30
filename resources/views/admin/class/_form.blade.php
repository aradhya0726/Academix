@if ($faculties->isEmpty())
    <div style="background:#FFFBEB;color:#92400E;border:1px solid #FDE68A;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;">
        You need at least one active
        <a href="{{ route('faculty.add') }}" style="color:#2563EB;font-weight:600;">faculty</a>
        before you can add a class.
    </div>
@endif

<div class="row g-3">
    <div class="col-md-5">
        <label class="form-label fw-semibold">Faculty</label>
        <select name="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror">
            <option value="">-- Select Faculty --</option>
            @foreach ($faculties as $faculty)
                <option value="{{ $faculty->id }}" {{ old('faculty_id', $class->faculty_id ?? '') == $faculty->id ? 'selected' : '' }}>
                    {{ $faculty->name }}
                </option>
            @endforeach
        </select>
        @error('faculty_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Semester</label>
        <select name="semester" class="form-select @error('semester') is-invalid @enderror">
            <option value="">-- Select Semester --</option>
            @foreach ($semesters as $sem)
                <option value="{{ $sem }}" {{ old('semester', $class->semester ?? '') == $sem ? 'selected' : '' }}>
                    Semester {{ $sem }}
                </option>
            @endforeach
        </select>
        @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="0" {{ old('status', $class->status ?? 0) == 0 ? 'selected' : '' }}>Active</option>
            <option value="1" {{ old('status', $class->status ?? 0) == 1 ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
