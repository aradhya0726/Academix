@php
    $mode     = $mode ?? 'teacher';
    $notice   = $notice ?? null;
    $classes  = $classes ?? collect();
    $showFile = $showFile ?? true;
    $submit   = $submit ?? ($notice ? 'Update Notice' : 'Post Notice');
    $audienceValue = old('audience', $notice->audience ?? 'all');
@endphp

@if ($mode === 'teacher' && $classes->isEmpty())
    <div class="alert alert-warning">
        You have no classes yet. Ask the admin to assign you a subject that belongs to a class.
    </div>
@endif

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">

        {{-- Title --}}
        <div class="col-md-12">
            <label class="form-label fw-semibold">Title</label>
            <input type="text" name="title" class="form-control form-control-lg"
                   placeholder="e.g. Mid-term exam schedule"
                   value="{{ old('title', $notice->title ?? '') }}" required>
            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Audience --}}
        @if ($mode === 'admin')
            <div class="col-md-6">
                <label class="form-label fw-semibold">Send To</label>
                <select name="audience" id="audience" class="form-select" required>
                    <option value="all" {{ $audienceValue === 'all' ? 'selected' : '' }}>Everyone</option>
                    <option value="teachers" {{ $audienceValue === 'teachers' ? 'selected' : '' }}>All Teachers</option>
                    <option value="students" {{ $audienceValue === 'students' ? 'selected' : '' }}>All Students</option>
                    <option value="class" {{ $audienceValue === 'class' ? 'selected' : '' }}>Specific Faculty &amp; Semester</option>
                </select>
                @error('audience') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6" id="classBox" style="display:none;">
                <label class="form-label fw-semibold">Faculty &amp; Semester</label>
                <select name="class_id" class="form-select">
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
            <div class="col-md-6">
                <label class="form-label fw-semibold">Send To (your class)</label>
                <select name="class_id" class="form-select" required>
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

        {{-- Message --}}
        <div class="col-md-12">
            <label class="form-label fw-semibold">Message</label>
            <textarea name="message" rows="6" class="form-control"
                      placeholder="Write your notice here..." required>{{ old('message', $notice->message ?? '') }}</textarea>
            @error('message') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- Link --}}
        <div class="col-md-12">
            <label class="form-label fw-semibold">Link <span class="text-muted fw-normal">(optional)</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                <input type="url" name="link" class="form-control" placeholder="https://example.com/resource"
                       value="{{ old('link', $notice->link ?? '') }}">
            </div>
            <small class="text-muted">Paste a link to a form, document, meeting or website.</small>
            @error('link') <small class="text-danger d-block">{{ $message }}</small> @enderror
        </div>

        {{-- Photo --}}
        <div class="{{ $showFile ? 'col-md-6' : 'col-md-12' }}">
            <label class="form-label fw-semibold">Photo <span class="text-muted fw-normal">(optional)</span></label>
            <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">

            <div id="imagePreviewWrap" class="mt-2" style="display:none;">
                <img id="imagePreview" src="" alt="Preview"
                     class="img-fluid rounded border" style="max-height:220px; object-fit:cover;">
            </div>

            @if ($notice && $notice->image_url)
                <div class="mt-2">
                    <img src="{{ $notice->image_url }}" alt="Current photo"
                         class="img-fluid rounded border" style="max-height:220px; object-fit:cover;">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                        <label class="form-check-label" for="remove_image">Remove current photo</label>
                    </div>
                </div>
            @endif
            @error('image') <small class="text-danger d-block">{{ $message }}</small> @enderror
        </div>

        {{-- Attachment --}}
        @if ($showFile)
            <div class="col-md-6">
                <label class="form-label fw-semibold">Attachment <span class="text-muted fw-normal">(optional)</span></label>
                <input type="file" name="attachment" class="form-control">
                @if ($notice && $notice->attachment)
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_attachment" value="1" id="remove_attachment">
                        <label class="form-check-label" for="remove_attachment">
                            Remove current file ({{ $notice->attachment_name ?? 'attached' }})
                        </label>
                    </div>
                @endif
                @error('attachment') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>
        @endif

    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="{{ $cancel ?? url()->previous() }}" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">{{ $submit }}</button>
    </div>
</form>

<script>
    (function () {
        var input = document.getElementById('imageInput');
        var wrap  = document.getElementById('imagePreviewWrap');
        var img   = document.getElementById('imagePreview');
        if (input) {
            input.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    img.src = URL.createObjectURL(this.files[0]);
                    wrap.style.display = 'block';
                } else {
                    wrap.style.display = 'none';
                }
            });
        }

        @if ($mode === 'admin')
        var audience = document.getElementById('audience');
        var box = document.getElementById('classBox');
        function toggle() { box.style.display = audience.value === 'class' ? 'block' : 'none'; }
        audience.addEventListener('change', toggle);
        toggle();
        @endif
    })();
</script>