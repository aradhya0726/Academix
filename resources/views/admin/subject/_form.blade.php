
@if ($faculties->isEmpty())
    <div class="alert alert-warning">
        You need at least one active faculty and a class before adding a subject.

        <a href="{{ route('faculty.add') }}">Add Faculty</a>
        or
        <a href="{{ route('class.add') }}">Add Class</a>.
    </div>
@endif

@if ($teachers->isEmpty())
    <div class="alert alert-warning">
        No teachers are available.

        <a href="{{ route('teacher.add') }}">Add a teacher</a> first.
    </div>
@endif

<div class="row g-3">

    {{-- Faculty --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Faculty</label>

        <select name="faculty_id"
                id="faculty_id"
                class="form-select @error('faculty_id') is-invalid @enderror">

            <option value="">-- Select Faculty --</option>

            @foreach ($faculties as $f)
                <option value="{{ $f->id }}"
                    {{ old('faculty_id', $selectedFaculty ?? '') == $f->id ? 'selected' : '' }}>
                    {{ $f->name }}
                </option>
            @endforeach

        </select>

        @error('faculty_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Semester --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Semester</label>

        <select name="semester"
                id="semester"
                class="form-select @error('semester') is-invalid @enderror">

            <option value="">-- Select Faculty first --</option>

        </select>

        @error('semester')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div id="semester_hint"
             class="text-muted"
             style="font-size:12px;margin-top:4px;">
        </div>
    </div>

    {{-- Teacher --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Teacher</label>

        <select name="teacher_id"
                class="form-select @error('teacher_id') is-invalid @enderror">

            <option value="">-- Select Teacher --</option>

            @foreach ($teachers as $t)
                <option value="{{ $t->id }}"
                    {{ old('teacher_id', $subject->teacher_id ?? '') == $t->id ? 'selected' : '' }}>
                    {{ $t->name }}
                </option>
            @endforeach

        </select>

        @error('teacher_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Subject Name --}}
    <div class="col-md-5">
        <label class="form-label fw-semibold">Subject Name</label>

        <input type="text"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $subject->name ?? '') }}"
               placeholder="e.g. Database Management">

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Subject Code --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">
            Code <span class="text-muted fw-normal">(optional)</span>
        </label>

        <input type="text"
               name="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $subject->code ?? '') }}"
               placeholder="e.g. CSC-301">

        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">Status</label>

        <select name="status"
                class="form-select @error('status') is-invalid @enderror">

            <option value="0"
                {{ old('status', $subject->status ?? 0) == 0 ? 'selected' : '' }}>
                Active
            </option>

            <option value="1"
                {{ old('status', $subject->status ?? 0) == 1 ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

@push('scripts')
<script>
(function () {
    const semesterMap = @json($semesterMap);
    let selectedSemester = @json(old('semester', $selectedSemester ?? ''));

    const facultySelect = document.getElementById('faculty_id');
    const semesterSelect = document.getElementById('semester');
    const hint = document.getElementById('semester_hint');

    if (!facultySelect || !semesterSelect || !hint) {
        return;
    }

    function fillSemesters() {
        const list = semesterMap[facultySelect.value] || [];

        semesterSelect.innerHTML = '';
        hint.textContent = '';

        if (!facultySelect.value) {
            semesterSelect.add(
                new Option('-- Select Faculty first --', '')
            );
            return;
        }

        if (list.length === 0) {
            semesterSelect.add(
                new Option('-- No semester available --', '')
            );

            hint.textContent =
                'This faculty has no class yet. Add a class first.';

            return;
        }

        semesterSelect.add(
            new Option('-- Select Semester --', '')
        );

        list.forEach(function (semester) {
            const option = new Option(
                'Semester ' + semester,
                semester
            );

            if (String(semester) === String(selectedSemester)) {
                option.selected = true;
            }

            semesterSelect.add(option);
        });
    }

    facultySelect.addEventListener('change', function () {
        selectedSemester = '';
        fillSemesters();
    });

    fillSemesters();
})();
</script>
@endpush
