<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load('classroom.faculty');

        return $this->noCache(view('profile.show', compact('user')));
    }

    public function settings()
    {
        return $this->noCache(view('profile.settings', ['user' => Auth::user()]));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name'          => 'required|string|max:255',
            'phone'         => [
                'required',
                'regex:/^(97|98)[0-9]{8}$/',
                Rule::unique('users', 'phone_number')->ignore($user->id),
            ],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        // Only admins may change their own email.
        if ($user->role === 'admin') {
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ];
        }

        $request->validateWithBag('profile', $rules, [
            'phone.regex' => 'Enter a valid phone number (98XXXXXXXX or 97XXXXXXXX).',
        ]);

        $user->name         = trim($request->name);
        $user->phone_number = trim($request->phone);

        if ($user->role === 'admin') {
            $user->email = trim($request->email);
        }

        if ($request->hasFile('profile_image')) {
            $old = $user->photo ? public_path('uploads/profile/' . basename($user->photo)) : null;

            if ($old && file_exists($old)) {
                @unlink($old);
            }

            $file     = $request->file('profile_image');
            $filename = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('uploads/profile'), $filename);

            $user->photo = $filename;
        }

        $user->save();

        return redirect()->route('account.settings')
            ->with('profile_success', 'Your profile has been updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'The current password you entered is incorrect.',
            'password.different'                => 'The new password must be different from your current password.',
            'password.confirmed'                => 'The new passwords do not match.',
        ]);

        // The User model casts "password" as hashed, so assign the plain value.
        $user->password = $request->password;
        // Sign out "remember me" sessions on other devices.
        $user->setRememberToken(Str::random(60));
        $user->save();

        $request->session()->regenerate();

        return redirect()->route('account.settings')
            ->with('password_success', 'Your password has been changed.');
    }

    private function noCache($view)
    {
        return response($view)->withHeaders([
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            'Pragma'        => 'no-cache',
        ]);
    }
}