<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    protected TotpService $totp;

    public function __construct(TotpService $totp)
    {
        $this->totp = $totp;
    }

    protected function getChallengeUser(): ?User
    {
        $userId = session('2fa_user_id');
        if (!$userId) {
            return null;
        }
        return User::find($userId);
    }

    public function showChallenge()
    {
        $user = $this->getChallengeUser();
        if (!$user) {
            return redirect()->route('login');
        }
        return view('auth.two-factor.challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $this->getChallengeUser();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$this->totp->verifyCode($user->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => __('Invalid code. Please try again.')]);
        }

        Auth::login($user);
        $user->resetFailedLogins();
        $user->update(['last_login' => now()]);
        $user->recordActivity();

        session()->forget('2fa_user_id');
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showSetup(Request $request)
    {
        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.show');
        }

        $secret = $this->totp->generateSecret();
        session(['2fa_secret' => $secret]);

        $qrUri = $this->totp->getUri($secret, $user->email);

        return view('auth.two-factor.setup', [
            'secret' => $secret,
            'qrUri' => $qrUri,
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $secret = session('2fa_secret');
        if (!$secret) {
            return redirect()->route('two-factor.setup');
        }

        if (!$this->totp->verifyCode($secret, $request->code)) {
            return back()->withErrors(['code' => __('Invalid code. Please try again.')]);
        }

        $user = $request->user();
        $user->setTwoFactorSecret($secret);

        $recoveryCodes = $this->totp->generateRecoveryCodes();
        $user->setRecoveryCodes($this->totp->hashRecoveryCodes($recoveryCodes));
        $user->confirmTwoFactor();

        session()->forget('2fa_secret');

        return view('auth.two-factor.recovery-codes', [
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    public function show(Request $request)
    {
        $user = $request->user();

        return view('auth.two-factor.index', [
            'enabled' => $user->hasTwoFactorEnabled(),
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => __('Incorrect password.')]);
        }

        $user->disableTwoFactor();

        return back()->with('success', __('Two-factor authentication has been disabled.'));
    }

    public function showRecoveryChallenge()
    {
        return view('auth.two-factor.challenge-recovery');
    }

    public function verifyRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $user = $this->getChallengeUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $remaining = $this->totp->verifyRecoveryCode(
            $user->two_factor_recovery_codes,
            str_replace('-', '', strtoupper($request->recovery_code))
        );

        if ($remaining === null) {
            return back()->withErrors(['recovery_code' => __('Invalid recovery code.')]);
        }

        $user->setRecoveryCodes($remaining);

        Auth::login($user);
        $user->resetFailedLogins();
        $user->update(['last_login' => now()]);
        $user->recordActivity();

        session()->forget('2fa_user_id');
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
