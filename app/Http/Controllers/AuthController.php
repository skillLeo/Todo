<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends Controller
{
    use AuthorizesRequests;

    public function login()
    {
        return view('auth.login');
    }

    // ✅ Logout Method
    public function logout(Request $request)
    {
        Auth::logout(); // Log the user out

        // Invalidate the session and regenerate CSRF token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login page or homepage
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
