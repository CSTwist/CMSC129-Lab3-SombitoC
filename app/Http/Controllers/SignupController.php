<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SignupController extends Controller
{
    /**
     * Display the signup view.
     */
    public function create(): View
    {
        return view('layouts/sign-up');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Added unique validation to email to prevent duplicates
        $credentials = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'min:5'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $credentials['username'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        // Automatically log the user in after signing up
        Auth::login($user);

        return redirect()->intended('dashboard');
    }
}
