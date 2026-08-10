<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $googleAuthService;

    public function __construct(GoogleAuthService $googleAuthService)
    {
        $this->googleAuthService = $googleAuthService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            return back()->withErrors([
                'email' => 'Account locked. Try again in ' . $user->locked_until->diffForHumans(),
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->is_blocked) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been blocked.']);
            }

            $user->resetFailedLogins();
            $user->update(['last_login' => now()]);

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        if ($user) {
            $attempts = ($user->failed_login_attempts ?? 0) + 1;
            $maxAttempts = (int) config('auth.max_login_attempts', 5);
            $lockoutTime = (int) config('auth.lockout_time', 900);

            $user->recordFailedLogin();

            if ($attempts >= $maxAttempts) {
                $user->lockAccount($lockoutTime);
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function loginGoogle(Request $request)
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        try {
            $payload = $this->googleAuthService->verifyIdToken($request->credential);

            $user = User::firstOrCreate(
                ['email' => $payload['email']],
                [
                    'name' => $payload['name'] ?? $payload['email'],
                    'email_verified_at' => now(),
                    'avatar' => $payload['picture'] ?? null,
                ]
            );

            if ($user->is_blocked) {
                return back()->withErrors(['email' => 'Your account has been blocked.']);
            }

            Auth::login($user);

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Google authentication failed.']);
        }
    }
}
