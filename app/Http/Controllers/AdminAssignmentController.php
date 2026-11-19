<?php

namespace App\Http\Controllers;
use App\Models\ClassModel;
use App\Models\Faculty;
use App\Models\Subject;
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
    $facultyId = $request->input('faculty_id');
    $semester = $request->input('semester');
    $subjectId = $request->input('subject_id');
    $search = $request->input('search');

    // Filter options.
    $faculties = Faculty::orderBy('name')->get();

    $academicClasses = ClassModel::with('faculty')->get();

    $semesterMap = $academicClasses
        ->groupBy('faculty_id')
        ->map(fn ($classes) => $classes
            ->pluck('semester')
            ->filter()
            ->unique()
            ->sort()
            ->values()
        );

    $subjects = Subject::with('classroom.faculty')
        ->orderBy('name')
        ->get();

    // Admin can see assignments across all faculties.
    $assignments = Assignment::with([
            'subject',
            'classroom.faculty',
            'teacher',
        ])
        ->withCount('submissions')

        ->when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%");
        })

        ->when($facultyId, function ($query) use ($facultyId) {
            $query->whereHas('classroom', function ($classQuery) use ($facultyId) {
                $classQuery->where('faculty_id', $facultyId);
            });
        })

        ->when($semester, function ($query) use ($semester) {
            $query->whereHas('classroom', function ($classQuery) use ($semester) {
                $classQuery->where('semester', $semester);
            });
        })

        ->when($subjectId, function ($query) use ($subjectId) {
            $query->where('subject_id', $subjectId);
        })

        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();

    return view('assignments.list', [
        'assignments' => $assignments,
        'mode' => 'admin',
        'faculties' => $faculties,
        'semesterMap' => $semesterMap,
        'subjects' => $subjects,
        'facultyId' => $facultyId,
        'semester' => $semester,
        'subjectId' => $subjectId,
        'search' => $search,
    ]);
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