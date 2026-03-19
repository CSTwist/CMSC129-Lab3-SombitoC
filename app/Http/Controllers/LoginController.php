<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        // This returns your resources/views/login.blade.php file
        return view('layouts/login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the form data sent from your auth-form component
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if the "Remember me" checkbox was checked
        $remember = $request->boolean('remember');

        // 2. Attempt to log the user in using Laravel's Auth facade
        if (Auth::attempt($credentials, $remember)) {

            // 3. Security measure: Regenerate session ID to prevent session fixation attacks
            $request->session()->regenerate();

            // 4. Redirect the user to their intended destination (or a default like '/dashboard')
            return redirect()->intended('dashboard');
        }

        // 5. If authentication fails, send them back to the login page with an error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email'); // This keeps the email field populated so they don't have to re-type it
    }

    /**
     * Destroy an authenticated session (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log the user out
        Auth::logout();

        // Invalidate the session data
        $request->session()->invalidate();

        // Regenerate the CSRF token for security
        $request->session()->regenerateToken();

        // Redirect back to the login page or home page
        return redirect('/login');
    }
}
