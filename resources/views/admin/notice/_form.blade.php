@php
    $mode    = $mode ?? 'teacher';
    $notice  = $notice ?? null;
    $classes = $classes ?? collect();
    $submit  = $submit ?? ($notice ? 'Update Notice' : 'Post Notice');
    $audienceValue = old('audience', $notice->audience ?? 'all');
@endphp

@if ($mode === 'teacher' && $classes->isEmpty())
    <div class="alert alert-warning">
        You have no classes yet. Ask the admin to assign you a subject that belongs to a class.
    </div>
@endif

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">

        <div class="col-md-12 mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $notice->title ?? '') }}" required>
            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        @if ($mode === 'admin')
            <div class="col-md-6 mb-3">
                <label class="form-label">Send To</label>
                <select name="audience" id="audience" class="form-control" required>
                    <option value="all" {{ $audienceValue === 'all' ? 'selected' : '' }}>Everyone</option>
                    <option value="teachers" {{ $audienceValue === 'teachers' ? 'selected' : '' }}>All Teachers</option>
                    <option value="students" {{ $audienceValue === 'students' ? 'selected' : '' }}>All Students</option>
                    <option value="class" {{ $audienceValue === 'class' ? 'selected' : '' }}>Specific Faculty &amp; Semester</option>
                </select>
                @error('audience') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6 mb-3" id="classBox" style="display:none;">
                <label class="form-label">Faculty &amp; Semester</label>
                <select name="class_id" class="form-control">
                    <option value="">Select faculty and semester</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id', $notice->class_id ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->faculty->name ?? 'Unknown' }} – Semester {{ $c->semester }}
                        </option>
                    @endforeach
                </select>
                @error('class_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        @else
            <div class="col-md-6 mb-3">
                <label class="form-label">Send To (your class)</label>
                <select name="class_id" class="form-control" required>
                    <option value="">Select faculty and semester</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id', $notice->class_id ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->faculty->name ?? 'Unknown' }} – Semester {{ $c->semester }}
                        </option>
                    @endforeach
                </select>
                @error('class_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        @endif

        <div class="col-md-12 mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" rows="6" class="form-control" required>{{ old('message', $notice->message ?? '') }}</textarea>
            @error('message') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label">Link (optional)</label>
            <input type="url" name="link" class="form-control" placeholder="https://" value="{{ old('link', $notice->link ?? '') }}">
            @error('link') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Photo (optional, max 4 MB)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if ($notice && $notice->image_url)
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                    <label class="form-check-label" for="remove_image">Remove current photo</label>
                </div>
            @endif
            @error('image') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">File (optional, max 10 MB)</label>
            <input type="file" name="attachment" class="form-control">
            @if ($notice && $notice->attachment)
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_attachment" value="1" id="remove_attachment">
                    <label class="form-check-label" for="remove_attachment">Remove current file ({{ $notice->attachment_name ?? 'attached' }})</label>
                </div>
            @endif
            @error('attachment') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

    </div>

    <div class="text-end mt-4">
        <a href="{{ $cancel ?? url()->previous() }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">{{ $submit }}</button>
    </div>
</form>

@if ($mode === 'admin')
<script>
    (function () {
        var audience = document.getElementById('audience');
        var box = document.getElementById('classBox');
        function toggle() { box.style.display = audience.value === 'class' ? 'block' : 'none'; }
        audience.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endif