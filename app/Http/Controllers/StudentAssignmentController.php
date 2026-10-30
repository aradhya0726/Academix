<?php

namespace App\Http\Controllers;

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

    public function index()
    {
        $me = Auth::user();

        $assignments = Assignment::with([
                'subject',
                'teacher',
                'submissions' => fn ($q) => $q->where('student_id', $me->id),
            ])
            ->where('class_id', $me->class_id)
            ->orderByDesc('due_at')
            ->paginate(10);

        return view('assignments.student_list', compact('assignments'));
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
            'file.max'      => 'The file must be 10 MB or smaller.',
        ]);

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->file);
        }

        $file = $request->file('file');

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => Auth::id()],
            [
                'file'            => $file->store('assignments/submissions', 'public'),
                'file_name'       => $file->getClientOriginalName(),
                'note'            => $request->input('note'),
                'submitted_at'    => now(),
                'status_override' => null,
            ]
        );

        return redirect()->route('student.assignment.show', $assignment->id)
            ->with('success', 'Your work was submitted.');
    }
}