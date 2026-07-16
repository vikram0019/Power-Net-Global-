<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        $user = User::where($field, $credentials['login'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['login' => 'These credentials do not match our records.'])->onlyInput('login');
        }

        if ($user->status === 'pending') {
            session(['pending_user_id' => $user->id]);

            return redirect()->route('signup.otp')->withErrors(['login' => 'Please verify your account with the OTP sent to your email.']);
        }

        if ($user->status === 'suspended') {
            return back()->withErrors(['login' => 'Your account has been suspended. Please contact support.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $user->is_admin
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
