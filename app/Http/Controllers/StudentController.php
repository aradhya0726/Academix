<?php

namespace App\Http\Controllers;
use App\Models\Faculty;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Mail\NewUserWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    /**
     * Get available classes with faculty information.
     */
    private function classes()
    {
        return ClassModel::with('faculty')
            ->where('status', 0)
            ->get()
            ->sortBy(fn ($c) => ($c->faculty->name ?? '') .
                str_pad((string) $c->semester, 3, '0', STR_PAD_LEFT))
            ->values();
    }

    private function studentFormOptions($includeInactive = false, $currentFacultyId = null)
{
    $facultyQuery = Faculty::query();

    if ($includeInactive) {
        $facultyQuery->where(function ($query) use ($currentFacultyId) {
            $query->where('status', 0);

            if ($currentFacultyId) {
                $query->orWhere('id', $currentFacultyId);
            }
        });
    } else {
        $facultyQuery->where('status', 0);
    }

    $faculties = $facultyQuery->orderBy('name')->get();

    $classQuery = ClassModel::query();

    if (!$includeInactive) {
        $classQuery->where('status', 0);
    }

    $classes = $classQuery->get(['id', 'faculty_id', 'semester']);

    $semesterMap = $classes
        ->groupBy('faculty_id')
        ->map(function ($facultyClasses) {
            return $facultyClasses
                ->pluck('semester')
                ->filter()
                ->unique()
                ->sort()
                ->values();
        })
        ->toArray();

    return compact('faculties', 'semesterMap');
}

    /**
     * Display all students.
     */
  
public function list(Request $request)
{
    $filters = $request->validate([
        'search' => ['nullable', 'string', 'max:100'],
        'faculty_id' => ['nullable', 'integer', 'exists:faculty,id'],
        'semester' => ['nullable', 'integer', 'min:1'],
    ]);

    $search = $filters['search'] ?? '';
    $facultyId = $filters['faculty_id'] ?? null;
    $semester = $filters['semester'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | Academic filter options
    |--------------------------------------------------------------------------
    | Build the faculty and semester options from existing class records.
    | No database IDs are displayed in the interface.
    */
    $academicClasses = ClassModel::with('faculty')
        ->get();

    $faculties = $academicClasses
        ->pluck('faculty')
        ->filter()
        ->unique('id')
        ->sortBy('name')
        ->values();

    $semesterMap = $academicClasses
        ->groupBy('faculty_id')
        ->map(function ($classes) {
            return $classes
                ->pluck('semester')
                ->filter(fn ($value) => $value !== null && $value !== '')
                ->unique()
                ->sort()
                ->values();
        })
        ->toArray();

    $semesterOptions = $facultyId
        ? collect($semesterMap[$facultyId] ?? [])
        : $academicClasses
            ->pluck('semester')
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->unique()
            ->sort()
            ->values();

    /*
    |--------------------------------------------------------------------------
    | Student query
    |--------------------------------------------------------------------------
    */
    $students = User::with('classroom.faculty')
        ->where('role', 'student')

        // Search by student name, email, phone or enrolled year.
        ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('enrolled_year', 'like', '%' . $search . '%');
            });
        })

        // Filter by faculty and/or semester.
        ->when($facultyId || $semester, function ($query) use ($facultyId, $semester) {
            $query->whereHas('classroom', function ($classQuery) use ($facultyId, $semester) {
                if ($facultyId) {
                    $classQuery->where('faculty_id', $facultyId);
                }

                if ($semester) {
                    $classQuery->where('semester', $semester);
                }
            });
        })

        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();

    return view('admin.student.list', compact(
        'students',
        'search',
        'faculties',
        'semesterOptions',
        'semesterMap',
        'facultyId',
        'semester'
    ));
}



    /**
     * Show the add student form.
     */
    public function add()
{
    $options = $this->studentFormOptions();

    return view('admin.student.add', $options);
}


    /**
     * Insert a new student and send welcome email.
     */
    public function insert(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',

        'email' => 'required|email|unique:users,email',

        'password' => 'required|min:8|confirmed',

        'phone' => [
            'required',
            'regex:/^(97|98)[0-9]{8}$/',
            'unique:users,phone_number',
        ],

        'enrolled_year' => 'required|integer|between:2000,' . date('Y'),

        'faculty_id' => [
            'required',
            Rule::exists('faculty', 'id')->where('status', 0),
        ],

        'semester' => [
            'required',
            'integer',
            Rule::exists((new ClassModel)->getTable(), 'semester')
                ->where(function ($query) use ($request) {
                    $query->where('faculty_id', $request->faculty_id)
                          ->where('status', 0);
                }),
        ],

        'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',

    ], [
        'faculty_id.required' => 'Please select a faculty.',
        'semester.required' => 'Please select a semester.',
        'semester.exists' => 'The selected semester is not available for this faculty.',
    ]);

    $class = ClassModel::where('faculty_id', $request->faculty_id)
        ->where('semester', $request->semester)
        ->where('status', 0)
        ->firstOrFail();

    $user = new User();

    $user->name = trim($request->name);
    $user->email = trim($request->email);
    $user->password = Hash::make($request->password);
    $user->phone_number = trim($request->phone);
    $user->enrolled_year = $request->enrolled_year;
    $user->class_id = $class->id;
    $user->role = 'student';

    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');

        $filename = time() . '_' . uniqid() . '.' .
            $file->getClientOriginalExtension();

        $file->move(public_path('uploads/profile'), $filename);

        $user->photo = $filename;
    }

    $user->save();

    try {
        Mail::to($user->email)->send(
            new NewUserWelcomeMail($user, $request->password)
        );
    } catch (\Throwable $exception) {
        Log::error('Student welcome email failed.', [
            'user_id' => $user->id,
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
        ]);

        return redirect()
            ->route('student.list')
            ->with('success', 'New Student Added Successfully')
            ->with('warning', 'Student account created, but welcome email could not be sent.');
    }

    return redirect()
        ->route('student.list')
        ->with('success', 'New Student Added Successfully')
        ->with('email_sent', true);
}


    /**
     * Show the edit student form.
     */
    public function edit($id)
{
    $student = User::where('role', 'student')
        ->findOrFail($id);

    $currentClass = $student->class_id
        ? ClassModel::find($student->class_id)
        : null;

    $options = $this->studentFormOptions(
        true,
        $currentClass?->faculty_id
    );

    return view('admin.student.edit', array_merge(
        $options,
        compact('student')
    ));
}

    /**
     * Update an existing student.
     */
   

public function update(Request $request, $id)
{
    $student = User::where('role', 'student')->findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',

        'email' => 'required|email|unique:users,email,' . $id,

        'phone' => [
            'required',
            'regex:/^(97|98)[0-9]{8}$/',
            'unique:users,phone_number,' . $id,
        ],

        'enrolled_year' => 'required|integer|between:2000,' . date('Y'),

        'faculty_id' => [
            'nullable',
            'required_with:semester',
            Rule::exists('faculty', 'id'),
        ],

        'semester' => [
            'nullable',
            'required_with:faculty_id',
            'integer',
            Rule::exists((new ClassModel)->getTable(), 'semester')
                ->where(function ($query) use ($request) {
                    $query->where('faculty_id', $request->faculty_id);
                }),
        ],

        'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ], [
        'faculty_id.required_with' => 'Please select a faculty.',
        'semester.required_with' => 'Please select a semester.',
        'semester.exists' => 'The selected semester is not available for this faculty.',
    ]);

    // Resolve the selected faculty and semester to the existing class record.
    $newClassId = null;

    if ($request->filled('faculty_id') && $request->filled('semester')) {
        $class = ClassModel::where('faculty_id', $request->faculty_id)
            ->where('semester', $request->semester)
            ->first();

        if (!$class) {
            return back()
                ->withErrors([
                    'semester' => 'The selected semester does not belong to this faculty.',
                ])
                ->withInput();
        }

        $newClassId = $class->id;
    }

    $previousClassId = $student->class_id;
    $isPromoted = (string) $previousClassId !== (string) $newClassId;

    $newPhoto = null;
    $oldPhoto = $student->photo;

    // Upload the new image first, if supplied.
    if ($request->hasFile('profile_image')) {
        $file = $request->file('profile_image');

        $newPhoto = time() . '_' . uniqid() . '.' .
            $file->getClientOriginalExtension();

        $file->move(
            public_path('uploads/profile'),
            $newPhoto
        );
    }

    try {
        \Illuminate\Support\Facades\DB::transaction(function () use (
            $student,
            $request,
            $newClassId,
            $newPhoto
        ) {
            $student->name = trim($request->name);
            $student->email = trim($request->email);
            $student->phone_number = trim($request->phone);
            $student->enrolled_year = $request->enrolled_year;
            $student->class_id = $newClassId;

            if ($newPhoto) {
                $student->photo = $newPhoto;
            }

            $student->save();
        });
    } catch (\Throwable $exception) {
        if (
            $newPhoto &&
            file_exists(public_path('uploads/profile/' . $newPhoto))
        ) {
            unlink(public_path('uploads/profile/' . $newPhoto));
        }

        throw $exception;
    }

    // Delete the old photo only after a successful update.
    if (
        $newPhoto &&
        $oldPhoto &&
        file_exists(public_path('uploads/profile/' . $oldPhoto))
    ) {
        unlink(public_path('uploads/profile/' . $oldPhoto));
    }

    if ($isPromoted) {
        return redirect()
            ->route('student.list')
            ->with(
                'success',
                'Student academic class updated successfully. Previous attendance and assignment records have been preserved. The student will now see the new class records.'
            );
    }

    return redirect()
        ->route('student.list')
        ->with('success', 'Student updated successfully.');
}


    /**
     * Delete a student.
     */
    public function delete($id)
    {
        $student = User::where('role', 'student')
            ->findOrFail($id);


        // Delete profile image
        if (
            $student->photo &&
            file_exists(public_path('uploads/profile/' . $student->photo))
        ) {
            unlink(
                public_path('uploads/profile/' . $student->photo)
            );
        }


        $student->delete();


        return redirect()
            ->route('student.list')
            ->with('success', 'Student deleted successfully.');
    }
}
