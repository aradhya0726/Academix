<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAssignmentController extends Controller
{
    /** Students of the assignment's class, with their submission (if any) */
    public static function submissionData(Assignment $assignment): array
    {
        $students = User::where('role', 'student')
            ->where('class_id', $assignment->class_id)
            ->orderBy('name')
            ->get();

        $submissions = $assignment->submissions()->get()->keyBy('student_id');

        return compact('assignment', 'students', 'submissions');
    }

    /** Delete an assignment together with its attachment and all submitted files */
    public static function destroyWithFiles(Assignment $assignment): void
    {
        if ($assignment->attachment) {
            Storage::disk('public')->delete($assignment->attachment);
        }

        foreach ($assignment->submissions as $submission) {
            Storage::disk('public')->delete($submission->file);
        }

        $assignment->submissions()->delete();
        $assignment->delete();
    }

    public function list(Request $request)
    {
        $assignments = Assignment::with(['subject', 'classroom.faculty', 'teacher'])
            ->withCount('submissions')
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('assignments.list', ['assignments' => $assignments, 'mode' => 'admin']);
    }

    public function submissions($id)
    {
        $assignment = Assignment::with(['subject', 'classroom.faculty'])->findOrFail($id);

        return view('assignments.submissions', self::submissionData($assignment) + ['mode' => 'admin']);
    }

    public function delete($id)
    {
        self::destroyWithFiles(Assignment::findOrFail($id));

        return redirect()->route('admin.assignment.list')->with('success', 'Assignment deleted successfully.');
    }
}