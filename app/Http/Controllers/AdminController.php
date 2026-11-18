<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\NewUserWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
   public function list(Request $request)
{
    $search = $request->search;

    $admins = User::where('role', 'admin')
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

    return view('admin.admin.list', compact('admins', 'search'));
}


    public function add()
    {
        return view('admin.admin.add');
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

            'profile_image' =>
                'required|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);


        $user = new User();

        $user->name = trim($request->name);

        $user->email = trim($request->email);

        $user->password = Hash::make($request->password);

        $user->phone_number = trim($request->phone);

        $user->role = 'admin';


        // Upload profile image
        if ($request->hasFile('profile_image')) {

            $file = $request->file('profile_image');

            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->move(
                public_path('uploads/profile'),
                $filename
            );

            $user->photo = $filename;
        }


        // Save admin account
        $user->save();


        // Send welcome email
        try {

            Mail::to($user->email)->send(
    new NewUserWelcomeMail($user, $request->password)
);

        } catch (\Throwable $exception) {

            Log::error('Admin welcome email failed.', [
                'user_id' => $user->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('admin.list')
                ->with('success', 'New Admin Added Successfully')
                ->with(
                    'warning',
                    'Admin account created, but welcome email could not be sent.'
                );
        }


        return redirect()
            ->route('admin.list')
            ->with('success', 'New Admin Added Successfully')
            ->with('email_sent', true);
    }


    public function edit($id)
    {
        $admin = User::where('role', 'admin')
            ->findOrFail($id);

        return view('admin.admin.edit', compact('admin'));
    }


    public function update(Request $request, $id)
    {
        $admin = User::where('role', 'admin')
            ->findOrFail($id);


        $request->validate([

            'name' => 'required|string|max:255',

            'email' =>
                'required|email|unique:users,email,' . $id,

            'phone' => [
                'required',
                'regex:/^(97|98)[0-9]{8}$/',
                'unique:users,phone_number,' . $id,
            ],

            'profile_image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);


        $admin->name = trim($request->name);

        $admin->email = trim($request->email);

        $admin->phone_number = trim($request->phone);


        // Update profile image
        if ($request->hasFile('profile_image')) {

            // Delete old profile image
            if (
                $admin->photo &&
                file_exists(public_path('uploads/profile/' . $admin->photo))
            ) {

                unlink(
                    public_path('uploads/profile/' . $admin->photo)
                );
            }


            $file = $request->file('profile_image');

            $filename = time() . '.' . $file->getClientOriginalExtension();


            $file->move(
                public_path('uploads/profile'),
                $filename
            );


            $admin->photo = $filename;
        }


        $admin->save();


        return redirect()
            ->route('admin.list')
            ->with('success', 'Admin updated successfully');
    }


    public function delete($id)
    {
        $admin = User::where('role', 'admin')
            ->findOrFail($id);


        // Delete profile image
        if (
            $admin->photo &&
            file_exists(public_path('uploads/profile/' . $admin->photo))
        ) {

            unlink(
                public_path('uploads/profile/' . $admin->photo)
            );
        }


        $admin->delete();


        return redirect()
            ->route('admin.list')
            ->with('success', 'Admin deleted successfully.');
    }
}
