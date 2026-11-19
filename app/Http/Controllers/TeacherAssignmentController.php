<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TeacherAssignmentController extends Controller
{
    private function subjects()
    {
        return Subject::with('classroom.faculty')
            ->where('teacher_id', Auth::id())
            ->orderBy('name')
            ->get();
    }

    private function rules(): array
    {
        return [
            'subject_id'   => ['required', Rule::exists('subject', 'id')->where('teacher_id', Auth::id())],
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'due_date'     => 'required|date_format:Y-m-d',
            'due_hour'     => ['required', 'regex:/^(0[1-9]|1[0-2])$/'],
            'due_minute'   => ['required', 'regex:/^[0-5][0-9]$/'],
            'due_meridiem' => ['required', Rule::in(['AM', 'PM'])],
            'attachment'   => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip|max:10240',
        ];
    }

    private function messages(): array
    {
        return [
            'subject_id.required'   => 'Please choose a subject.',
            'subject_id.exists'     => 'You can only create assignments for subjects you teach.',
            'due_date.required'     => 'Please set a due date.',
            'due_date.date_format'  => 'Please choose a valid due date.',
            'due_hour.required'     => 'Please choose the hour.',
            'due_hour.regex'        => 'Please choose a valid hour.',
            'due_minute.required'   => 'Please choose the minute.',
            'due_minute.regex'      => 'Please choose a valid minute.',
            'due_meridiem.required' => 'Please choose AM or PM.',
            'attachment.max'        => 'The file must be 10 MB or smaller.',
        ];
    }

    private function dueAt(array $data): Carbon
    {
        return Carbon::createFromFormat(
            'Y-m-d h:i A',
            $data['due_date'] . ' ' . $data['due_hour'] . ':' . $data['due_minute'] . ' ' . $data['due_meridiem']
        );
    }

    private function mine($id): Assignment
    {
        return Assignment::where('teacher_id', Auth::id())->findOrFail($id);
    }

   
public function list(Request $request)
{
    $teacherId = Auth::id();

    // Only subjects assigned to this teacher.
    $subjects = $this->subjects();

    $subjectId = $request->input('subject_id');
    $search = $request->input('search');

    $assignments = Assignment::with([
            'subject',
            'classroom.faculty',
        ])
        ->withCount('submissions')

        // Strict ownership restriction.
        ->where('teacher_id', $teacherId)

        ->when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%");
        })

        // Subject filter is limited to the teacher's own subjects.
        ->when($subjectId, function ($query) use ($subjectId, $subjects) {
            $allowedSubjectIds = $subjects->pluck('id');

            if ($allowedSubjectIds->contains((int) $subjectId)) {
                $query->where('subject_id', $subjectId);
            } else {
                $query->whereRaw('1 = 0');
            }
        })

        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();

    return view('assignments.list', [
        'assignments' => $assignments,
        'mode' => 'teacher',
        'subjects' => $subjects,
        'subjectId' => $subjectId,
        'search' => $search,
    ]);
}


    public function add()
    {
        return view('assignments.add', ['subjects' => $this->subjects()]);
    }

    public function store(Request $request)
    {
        $data    = $request->validate($this->rules(), $this->messages());
        $subject = Subject::findOrFail($data['subject_id']);

        $payload = [
            'subject_id'  => $subject->id,
            'class_id'    => $subject->class_id,
            'teacher_id'  => Auth::id(),
            'title'       => trim($data['title']),
            'description' => isset($data['description']) ? trim($data['description']) : null,
            'due_at'      => $this->dueAt($data),
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $payload['attachment']      = $file->store('assignments/files', 'public');
            $payload['attachment_name'] = $file->getClientOriginalName();
        }

        Assignment::create($payload);

        return redirect()->route('teacher.assignment.list')->with('success', 'Assignment created successfully.');
    }

    public function edit($id)
    {
        return view('assignments.edit', [
            'assignment' => $this->mine($id),
            'subjects'   => $this->subjects(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $assignment = $this->mine($id);
        $data       = $request->validate($this->rules(), $this->messages());
        $subject    = Subject::findOrFail($data['subject_id']);

        $payload = [
            'subject_id'  => $subject->id,
            'class_id'    => $subject->class_id,
            'title'       => trim($data['title']),
            'description' => isset($data['description']) ? trim($data['description']) : null,
            'due_at'      => $this->dueAt($data),
        ];

        if ($request->hasFile('attachment')) {
            if ($assignment->attachment) {
                Storage::disk('public')->delete($assignment->attachment);
            }
            $file = $request->file('attachment');
            $payload['attachment']      = $file->store('assignments/files', 'public');
            $payload['attachment_name'] = $file->getClientOriginalName();
        } elseif ($request->boolean('remove_attachment')) {
            if ($assignment->attachment) {
                Storage::disk('public')->delete($assignment->attachment);
            }
            $payload['attachment']      = null;
            $payload['attachment_name'] = null;
        }

        $assignment->update($payload);

        return redirect()->route('teacher.assignment.list')->with('success', 'Assignment updated successfully.');
    }

    public function delete($id)
    {
        AdminAssignmentController::destroyWithFiles($this->mine($id));

        return redirect()->route('teacher.assignment.list')->with('success', 'Assignment deleted successfully.');
    }

    public function submissions($id)
    {
        $assignment = $this->mine($id)->load(['subject', 'classroom.faculty']);

        return view('assignments.submissions', AdminAssignmentController::submissionData($assignment) + ['mode' => 'teacher']);
    }

    /** Teacher marks a submission as On time / Late, or back to Auto */
    public function updateStatus(Request $request, $id)
    {
        $submission = AssignmentSubmission::with('assignment')->findOrFail($id);

        abort_unless($submission->assignment && (int) $submission->assignment->teacher_id === (int) Auth::id(), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(['auto', 'on_time', 'late'])],
        ]);

        $submission->update([
            'status_override' => $data['status'] === 'auto' ? null : $data['status'],
        ]);

        return back()->with('success', 'Submission status updated.');
    }
}