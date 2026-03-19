<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SignupController extends Controller
{
        /**
     * Display the signup view.
     */
    public function create(): View
    {
        // This returns your resources/views/login.blade.php file
        return view('layouts/sign-up');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate the form data sent from your auth-form component
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'username' => ['required', 'min:5'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $credentials['username'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        return redirect()->intended('login');
    }
}
