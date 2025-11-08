<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Get the currently logged-in user from session
     */
    private function getAuthUser(Request $request)
    {
        $userId = $request->session()->get('user_id');

        if (!$userId) {
            return null;
        }

        return User::find($userId);
    }

    /**
     * Show the user's profile.
     */
    public function show(Request $request)
    {
        $user = $this->getAuthUser($request);

        if (!$user) {
            return redirect()->route('auth-login-basic')->with('error', 'Please login to access your profile.');
        }

        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit(Request $request)
    {
        $user = $this->getAuthUser($request);

        if (!$user) {
            return redirect()->route('auth-login-basic')->with('error', 'Please login to access your profile.');
        }

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = $this->getAuthUser($request);

        if (!$user) {
            return redirect()->route('auth-login-basic')->with('error', 'Please login to access your profile.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the change password form.
     */
    public function showChangePasswordForm(Request $request)
    {
        $user = $this->getAuthUser($request);

        if (!$user) {
            return redirect()->route('auth-login-basic')->with('error', 'Please login to access your profile.');
        }

        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function changePassword(Request $request)
    {
        $user = $this->getAuthUser($request);

        if (!$user) {
            return redirect()->route('auth-login-basic')->with('error', 'Please login to access your profile.');
        }

        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully.');
    }
}
