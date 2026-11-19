<?php

namespace App\Http\Controllers;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentAssignmentController extends Controller
{
    private function forMyClass($id): Assignment
    {
        return Assignment::with(['subject', 'teacher'])
            ->where('class_id', Auth::user()->class_id)
            ->findOrFail($id);
    }

    
public function index(Request $request)
{
    $me = Auth::user();

    // A student without a class cannot access class assignments.
    if (!$me->class_id) {
        return view('assignments.student_list', [
            'assignments' => Assignment::whereRaw('1 = 0')->paginate(10),
            'subjects' => collect(),
            'subjectId' => null,
        ]);
    }

    $subjects = Subject::where('class_id', $me->class_id)
        ->whereIn('id', Assignment::where('class_id', $me->class_id)
            ->select('subject_id'))
        ->orderBy('name')
        ->get();

    $subjectId = $request->input('subject_id');

    $assignments = Assignment::with([
            'subject',
            'teacher',
            'submissions' => fn ($query) => $query
                ->where('student_id', $me->id),
        ])
        ->where('class_id', $me->class_id)
        ->when($subjectId, function ($query) use ($subjectId, $subjects) {
            if ($subjects->contains('id', (int) $subjectId)) {
                $query->where('subject_id', $subjectId);
            } else {
                $query->whereRaw('1 = 0');
            }
        })
        ->orderByDesc('due_at')
        ->paginate(10)
        ->withQueryString();

    return view('assignments.student_list', compact(
        'assignments',
        'subjects',
        'subjectId'
    ));
}

    public function show($id)
    {
        $assignment = $this->forMyClass($id);
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();

        return view('assignments.student_show', compact('assignment', 'submission'));
    }

   
public function submit(Request $request, $id)
{
    $assignment = $this->forMyClass($id);

    $request->validate([
        'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip|max:10240',
        'note' => 'nullable|string|max:1000',
    ], [
        'file.required' => 'Please choose a file to submit.',
        'file.max' => 'The file must be 10 MB or smaller.',
    ]);

    $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
        ->where('student_id', Auth::id())
        ->first();

    $file = $request->file('file');
    $newPath = $file->store('assignments/submissions', 'public');

    // Save or replace the student's submission.
    AssignmentSubmission::updateOrCreate(
        [
            'assignment_id' => $assignment->id,
            'student_id' => Auth::id(),
        ],
        [
            'file' => $newPath,
            'file_name' => $file->getClientOriginalName(),
            'note' => $request->input('note'),
            'submitted_at' => now(),
            'status_override' => null,
        ]
    );

    // Delete the previous file only after the new submission is saved.
    if ($existing && $existing->file && $existing->file !== $newPath) {
        Storage::disk('public')->delete($existing->file);
    }

    $message = $existing
        ? 'Your assignment submission has been updated successfully.'
        : 'Your assignment has been submitted successfully.';

    return redirect()
        ->route('student.assignment.list', [
            'subject_id' => $assignment->subject_id,
        ])
        ->with('success', $message);
}

}