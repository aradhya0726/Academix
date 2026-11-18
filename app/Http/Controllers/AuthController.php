<?php

namespace App\Http\Controllers;



use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const RESET_TABLE = 'password_reset_tokens';

    // ==========================================
    // PUBLIC ENTRY POINTS
    // ==========================================

    public function welcome()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('welcome');
    }

    public function home()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        return $this->redirectToDashboard();
    }

    // ==========================================
    // LOGIN / LOGOUT
    // ==========================================

    public function login()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }

        return view('auth.login');
    }

    public function AuthLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Please enter your email address.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return $this->redirectToDashboard();
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'The email or password you entered is incorrect. Please try again.');
    }

    public function logout(Request $request)
    {
        $this->endSession($request);

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }

    // ==========================================
    // PASSWORD RESET
    // ==========================================

    public function forgotpassword()
    {
        return view('auth.forgot');
    }

    public function PostForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::getEmailSingle($request->email);

        /*
         * SECURITY FIX:
         * Always show the same response regardless of whether
         * the email address exists in the database.
         */

        if ($user) {
            $token = Str::random(64);

            // Store only the SHA-256 hash of the reset token.
            DB::table(self::RESET_TABLE)->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => hash('sha256', $token),
                    'created_at' => now(),
                ]
            );

            try {
                Mail::to($user->email)->send(
                    new ForgotPasswordMail($user, $token)
                );
            } catch (\Throwable $exception) {

                // Log the failure internally.
                // Never expose account existence or mail errors
                // to the person requesting a password reset.

              Log::error('Password reset email could not be sent.', [
    'exception' => $exception::class,
    'message' => $exception->getMessage(),
]);
            }
        }

        // Identical response for existing and non-existing emails.
        return redirect()->back()->with(
            'status',
            'If an account exists with that email address, a password reset link will be sent.'
        );
    }

    public function reset($token)
    {
        abort_unless($this->resetRecord($token), 404);

        return view('auth.reset', [
            'token' => $token,
        ]);
    }

    public function PostReset($token, Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Please enter a new password.',
            'password.min'       => 'Your password must be at least 8 characters long.',
            'password.confirmed' => 'The passwords you entered do not match. Please try again.',
        ]);

        $record = $this->resetRecord($token);

        $user = $record
            ? User::getEmailSingle($record->email)
            : null;

        if (! $user) {
            return redirect()->route('password.request')
                ->with(
                    'error',
                    'This password reset link is invalid or has expired. Please request a new one.'
                );
        }

        $user->password = Hash::make($request->password);

        // Invalidate remembered sessions on other devices.
        $user->setRememberToken(Str::random(60));

        $user->save();

        // Delete the used reset token.
        DB::table(self::RESET_TABLE)
            ->where('email', $user->email)
            ->delete();

        return redirect()->route('login')
            ->with(
                'success',
                'Your password has been reset successfully. You can now sign in with your new password.'
            );
    }

    // ==========================================
    // HELPERS
    // ==========================================

    private function redirectToDashboard(): RedirectResponse
    {
        $route = Auth::user()->dashboardRoute();

        if ($route) {
            return redirect()->route($route);
        }

        $this->endSession(request());

        return redirect()->route('login')
            ->with('error', 'Your account role is not recognized.');
    }

    private function endSession(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Returns a valid, unexpired password reset record.
     */
    private function resetRecord(string $token): ?object
    {
        $record = DB::table(self::RESET_TABLE)
            ->where('token', hash('sha256', $token))
            ->first();

        if (! $record) {
            return null;
        }

        $minutes = (int) config('auth.passwords.users.expire', 60);

        if (
            ! $record->created_at ||
            Carbon::parse($record->created_at)->lt(
                now()->subMinutes($minutes)
            )
        ) {
            DB::table(self::RESET_TABLE)
                ->where('email', $record->email)
                ->delete();

            return null;
        }

        return $record;
    }
}
