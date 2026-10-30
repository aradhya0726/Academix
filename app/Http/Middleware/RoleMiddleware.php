<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

abstract class RoleMiddleware
{
    /** The single role allowed through this middleware. */
    abstract protected function role(): string;

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Guests: send to login. No logout() call, so nobody's remember token is touched.
        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== $this->role()) {
            $home = $user->dashboardRoute();

            // Unknown role: end the session safely.
            if (! $home) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Your account role is not recognized.');
            }

            // Wrong role: never serve the page. Stay signed in.
            if ($request->isMethodSafe()) {
                return redirect()->route($home);
            }

            abort(403);
        }

        $response = $next($request);

        // Stop the back button from showing protected pages after logout.
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');

        return $response;
    }
}