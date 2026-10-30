<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /** Data the add/edit form needs for its dropdowns */
    private function formData($includeClass = null)
    {
        $classes = ClassModel::where('status', 0)
            ->when($includeClass, fn ($q) => $q->orWhere('id', $includeClass->id))
            ->get();

        $faculties = Faculty::where('status', 0)
            ->when($includeClass, fn ($q) => $q->orWhere('id', $includeClass->faculty_id))
            ->orderBy('name')
            ->get();

        // [faculty_id => [1, 2, 3 ...]]  semesters that exist for that faculty
        $semesterMap = $classes
            ->groupBy('faculty_id')
            ->map(fn ($g) => $g->pluck('semester')->unique()->sort()->values())
            ->all();

        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return compact('faculties', 'semesterMap', 'teachers');
    }

    /** The class (faculty + semester) the admin chose, or null */
    private function chosenClass(Request $request)
    {
        return ClassModel::where('faculty_id', $request->faculty_id)
            ->where('semester', $request->semester)
            ->first();
    }

    private function rules($classId, $ignoreId = null): array
    {
        return [
            'faculty_id' => 'required|integer|exists:faculty,id',
            'semester'   => 'required|integer',
            'name'       => [
                'required', 'string', 'max:255',
                Rule::unique('subject', 'name')->where('class_id', $classId)->ignore($ignoreId),
            ],
            'code'       => 'nullable|string|max:50',
            'teacher_id' => ['required', Rule::exists('users', 'id')->where('role', 'teacher')],
            'status'     => 'required|in:0,1',
        ];
    }

    private function messages(): array
    {
        return [
            'faculty_id.required' => 'Please choose a faculty.',
            'semester.required'   => 'Please choose a semester.',
            'teacher_id.required' => 'Please choose a teacher for this subject.',
            'name.unique'         => 'This faculty and semester already has a subject with that name.',
        ];
    }

    public function list(Request $request)
    {
        $subjects = Subject::with(['classroom.faculty', 'teacher'])
            ->when($request->filled('faculty_id'), function ($q) use ($request) {
                $q->whereHas('classroom', fn ($c) => $c->where('faculty_id', $request->faculty_id));
            })
            ->when($request->filled('semester'), function ($q) use ($request) {
                $q->whereHas('classroom', fn ($c) => $c->where('semester', $request->semester));
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('class_id')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $faculties = Faculty::orderBy('name')->get();
        $semesters = ClassModel::SEMESTERS;

        return view('admin.subject.list', compact('subjects', 'faculties', 'semesters'));
    }

    public function add()
    {
        return view('admin.subject.add', $this->formData() + [
            'selectedFaculty'  => null,
            'selectedSemester' => null,
        ]);
    }

    public function store(Request $request)
    {
        $class = $this->chosenClass($request);

        $data = $request->validate($this->rules($class?->id), $this->messages());

        if (! $class) {
            return back()->withInput()->withErrors([
                'semester' => 'This faculty has no class for that semester yet. Add the class first.',
            ]);
        }

        Subject::create([
            'class_id'   => $class->id,
            'name'       => trim($data['name']),
            'code'       => $data['code'] ?? null,
            'teacher_id' => $data['teacher_id'],
            'status'     => $data['status'],
            'created_by' => Auth::user()->name,
        ]);

        return redirect()->route('subject.list')->with('success', 'Subject added successfully.');
    }

    public function edit($id)
    {
        $subject = Subject::with('classroom')->findOrFail($id);

        return view('admin.subject.edit', $this->formData($subject->classroom) + [
            'subject'          => $subject,
            'selectedFaculty'  => $subject->classroom->faculty_id ?? null,
            'selectedSemester' => $subject->classroom->semester ?? null,
        ]);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $class   = $this->chosenClass($request);

        $data = $request->validate($this->rules($class?->id, $subject->id), $this->messages());

        if (! $class) {
            return back()->withInput()->withErrors([
                'semester' => 'This faculty has no class for that semester yet. Add the class first.',
            ]);
        }

        $subject->update([
            'class_id'   => $class->id,
            'name'       => trim($data['name']),
            'code'       => $data['code'] ?? null,
            'teacher_id' => $data['teacher_id'],
            'status'     => $data['status'],
        ]);

        return redirect()->route('subject.list')->with('success', 'Subject updated successfully.');
    }

    public function delete($id)
    {
        Subject::findOrFail($id)->delete();

        return redirect()->route('subject.list')->with('success', 'Subject deleted successfully.');
    }
}