@php
    $editing = isset($assignment) && $assignment;

    $selHour = old('due_hour', $editing ? $assignment->due_at->format('h') : '');
    $selMin  = old('due_minute', $editing ? $assignment->due_at->format('i') : '');
    $selAmPm = old('due_meridiem', $editing ? $assignment->due_at->format('A') : '');
@endphp

<form action="{{ $editing ? route('teacher.assignment.update', $assignment->id) : route('teacher.assignment.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Subject</label>
        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror">
            <option value="">Choose subject...</option>
            @foreach ($subjects as $s)
                <option value="{{ $s->id }}" {{ old('subject_id', $assignment->subject_id ?? '') == $s->id ? 'selected' : '' }}>
                    {{ $s->name }} ({{ $s->classroom->faculty->name ?? '' }} / Sem {{ $s->classroom->semester ?? '' }})
                </option>
            @endforeach
        </select>
        @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $assignment->title ?? '') }}">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $assignment->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <div class="col-md-5 mb-3">
            <label class="form-label">Due date</label>
            <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                   value="{{ old('due_date', $editing ? $assignment->due_at->format('Y-m-d') : '') }}">
            @error('due_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-7 mb-3">
            <label class="form-label">Due time</label>
            <div class="input-group">
                <select name="due_hour" class="form-select @error('due_hour') is-invalid @enderror">
                    <option value="">Hour</option>
                    @for ($h = 1; $h <= 12; $h++)
                        @php $hv = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $hv }}" {{ (string) $selHour === $hv ? 'selected' : '' }}>{{ $hv }}</option>
                    @endfor
                </select>

                <select name="due_minute" class="form-select @error('due_minute') is-invalid @enderror">
                    <option value="">Min</option>
                    @for ($m = 0; $m <= 59; $m++)
                        @php $mv = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $mv }}" {{ (string) $selMin === $mv ? 'selected' : '' }}>{{ $mv }}</option>
                    @endfor
                </select>

                <select name="due_meridiem" class="form-select @error('due_meridiem') is-invalid @enderror">
                    <option value="">AM/PM</option>
                    <option value="AM" {{ $selAmPm === 'AM' ? 'selected' : '' }}>AM</option>
                    <option value="PM" {{ $selAmPm === 'PM' ? 'selected' : '' }}>PM</option>
                </select>
            </div>
            @if ($errors->has('due_hour') || $errors->has('due_minute') || $errors->has('due_meridiem'))
                <div class="text-danger" style="font-size:13px;margin-top:4px;">Please choose the hour, minute and AM/PM.</div>
            @endif
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Attachment (optional)</label>
        @if ($editing && $assignment->attachment)
            <div class="mb-2">
                <a href="{{ route('assignment.attachment.download', $assignment->id) }}">
                    <i class="bi bi-download"></i> {{ $assignment->attachment_name }}
                </a>
                @if (\App\Http\Controllers\AssignmentDownloadController::canPreview($assignment->attachment_name))
                    <a href="{{ route('assignment.attachment.preview', $assignment->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary ms-2">
                        <i class="bi bi-eye"></i> Preview
                    </a>
                @endif
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="remove_attachment" value="1" id="removeAtt">
                    <label class="form-check-label" for="removeAtt">Remove current file</label>
                </div>
            </div>
        @endif
        <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
        @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">PDF, Word, Excel, PowerPoint, image or ZIP. Max 10 MB.</div>
    </div>

    <button type="submit" class="btn btn-primary">{{ $editing ? 'Update Assignment' : 'Create Assignment' }}</button>
    <a href="{{ route('teacher.assignment.list') }}" class="btn btn-secondary">Cancel</a>
</form>