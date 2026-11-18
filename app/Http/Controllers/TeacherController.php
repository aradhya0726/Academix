<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\NewUserWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
   public function list(Request $request)
{
    $search = $request->search;

    $teachers = User::where('role', 'teacher')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        })
        ->orderBy('name', 'asc')
        ->paginate(10)
        ->withQueryString();

    return view('admin.teacher.list', compact('teachers', 'search'));
}


    public function add()
    {
        return view('admin.teacher.add');
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
            'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);


        $user = new User();

        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = Hash::make($request->password);
        $user->phone_number = trim($request->phone);
        $user->role = 'teacher';


        // Upload profile image
        if ($request->hasFile('profile_image')) {

            $file = $request->file('profile_image');

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/profile'),
                $filename
            );

            $user->photo = $filename;
        }


        // Save teacher account
        $user->save();


        // Send welcome email
        try {

           Mail::to($user->email)->send(
    new NewUserWelcomeMail($user, $request->password)
);

        } catch (\Throwable $exception) {

            Log::error('Teacher welcome email failed.', [
                'user_id' => $user->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('teacher.list')
                ->with('success', 'New Teacher Added Successfully')
                ->with(
                    'warning',
                    'Teacher account created, but welcome email could not be sent.'
                );
        }


        return redirect()
            ->route('teacher.list')
            ->with('success', 'New Teacher Added Successfully')
            ->with('email_sent', true);
    }


    public function edit($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        return view('admin.teacher.edit', compact('teacher'));
    }


    public function update(Request $request, $id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => [
                'required',
                'regex:/^(97|98)[0-9]{8}$/',
                'unique:users,phone_number,' . $id,
            ],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);


        $teacher->name = trim($request->name);
        $teacher->email = trim($request->email);
        $teacher->phone_number = trim($request->phone);


        // Update profile image
        if ($request->hasFile('profile_image')) {

            // Delete old profile image
            if (
                $teacher->photo &&
                file_exists(public_path('uploads/profile/' . $teacher->photo))
            ) {
                unlink(
                    public_path('uploads/profile/' . $teacher->photo)
                );
            }


            $file = $request->file('profile_image');

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/profile'),
                $filename
            );

            $teacher->photo = $filename;
        }


        $teacher->save();


        return redirect()
            ->route('teacher.list')
            ->with('success', 'Teacher updated successfully');
    }


    public function delete($id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);


        // Delete profile image
        if (
            $teacher->photo &&
            file_exists(public_path('uploads/profile/' . $teacher->photo))
        ) {
            unlink(
                public_path('uploads/profile/' . $teacher->photo)
            );
        }


        $teacher->delete();


        return redirect()
            ->route('teacher.list')
            ->with('success', 'Teacher deleted successfully.');
    }
}
