<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? $googleUser->getEmail(),
                    'email_verified_at' => now(),
                    'avatar' => $googleUser->getAvatar(),
                    'google_locale' => $googleUser->getRaw()['locale'] ?? null,
                    'password' => bcrypt('google_oauth_placeholder'),
                ]
            );

            if ($user->is_blocked) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been blocked.']);
            }

            $user->update(['avatar' => $user->avatar ?? $googleUser->getAvatar()]);

            Auth::login($user, true);
            $user->recordActivity();
            $request = request();
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            \Log::error('Google OAuth failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('login')
                ->withErrors(['email' => 'Google auth error: ' . $e->getMessage()]);
        }
    }
}
