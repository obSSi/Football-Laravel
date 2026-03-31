<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Authenticate a user and start a session.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = $this->throttleKey($request);
        $maxAttempts = max((int) config('security.login.max_attempts', 5), 1);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors([
                    'username' => "Trop de tentatives de connexion. Reessayez dans {$seconds} secondes.",
                ])
                ->onlyInput('username');
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        $lockoutSeconds = max((int) config('security.login.lockout_seconds', 300), 1);
        RateLimiter::hit($throttleKey, $lockoutSeconds);

        return back()
            ->withErrors(['username' => 'Identifiants invalides.'])
            ->onlyInput('username');
    }

    /**
     * Build a unique throttle key per username and IP.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower((string) $request->input('username')).'|'.$request->ip();
    }

    /**
     * Logout user and invalidate current session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
