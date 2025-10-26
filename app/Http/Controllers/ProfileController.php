<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'timezone' => 'nullable|string|max:100',
            'notification_enabled' => 'boolean',
        ]);

        try {
            $user->update($validated);

            return redirect()
                ->route('profile.edit')
                ->with('success', '✅ Profile updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to update profile. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            Auth::user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()
                ->route('profile.edit')
                ->with('success', '🔐 Password updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update password. Please try again.']);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();

        try {
            Auth::logout();
            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Your account has been deleted.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete account. Please try again.']);
        }
    }
}