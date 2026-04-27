<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Show the profile page
    public function edit(Request $request)
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    // Update profile information (Email)
    public function update(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ]);

        $user = $request->user();
        $user->email = $request->email;
        $user->save();

        // Redirect to dashboard upon success
        return redirect()->route('dashboard')->with('status', 'profile-updated');
    }

    // Update user password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // Redirect to dashboard upon success
        return redirect()->route('dashboard')->with('status', 'password-updated');
    }
}
