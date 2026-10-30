<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Faculty Name</label>
        <input type="text" name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $faculty->name ?? '') }}"
               placeholder="e.g. BCA, BIT, BBA">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="0" {{ old('status', $faculty->status ?? 0) == 0 ? 'selected' : '' }}>Active</option>
            <option value="1" {{ old('status', $faculty->status ?? 0) == 1 ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
