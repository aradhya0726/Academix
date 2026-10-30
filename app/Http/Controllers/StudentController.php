<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    private function classes()
    {
        return ClassModel::with('faculty')
            ->where('status', 0)
            ->get()
            ->sortBy(fn ($c) => ($c->faculty->name ?? '') . str_pad((string) $c->semester, 3, '0', STR_PAD_LEFT))
            ->values();
    }

    public function list(Request $request)
    {
        $search = $request->search;

        $students = User::with('classroom.faculty')
            ->where('role', 'student')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('phone_number', 'like', '%' . $search . '%')
                      ->orWhere('enrolled_year', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.student.list', compact('students', 'search'));
    }

    public function add()
    {
        $classes = $this->classes();

        return view('admin.student.add', compact('classes'));
    }

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
            'class_id' => ['required', Rule::exists((new ClassModel)->getTable(), 'id')],
            'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'class_id.required' => 'Please choose a faculty and semester.',
        ]);

        $user = new User();
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = Hash::make($request->password);
        $user->phone_number = trim($request->phone);
        $user->enrolled_year = $request->enrolled_year;
        $user->class_id = $request->class_id;
        $user->role = 'student';

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $user->photo = $filename;
        }

        $user->save();

        return redirect()
            ->route('student.list')
            ->with('success', 'New Student Added Successfully');
    }

    public function edit($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);
        $classes = $this->classes();

        return view('admin.student.edit', compact('student', 'classes'));
    }

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
            'class_id' => ['nullable', Rule::exists((new ClassModel)->getTable(), 'id')],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $student->name = trim($request->name);
        $student->email = trim($request->email);
        $student->phone_number = trim($request->phone);
        $student->enrolled_year = $request->enrolled_year;
        $student->class_id = $request->class_id ?: null;

        if ($request->hasFile('profile_image')) {

            if ($student->photo && file_exists(public_path('uploads/profile/' . $student->photo))) {
                unlink(public_path('uploads/profile/' . $student->photo));
            }

            $file = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $student->photo = $filename;
        }

        $student->save();

        return redirect()
            ->route('student.list')
            ->with('success', 'Student updated successfully');
    }

    public function delete($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        if ($student->photo && file_exists(public_path('uploads/profile/' . $student->photo))) {
            unlink(public_path('uploads/profile/' . $student->photo));
        }

        $student->delete();

        return redirect()
            ->route('student.list')
            ->with('success', 'Student deleted successfully.');
    }
}