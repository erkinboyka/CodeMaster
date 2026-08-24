<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.recaptcha.secret_key', '');
    }

    public function verify(?string $token, ?string $remoteIp = null): bool
    {
        if (empty($this->secretKey)) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $token,
                'remoteip' => $remoteIp,
            ]);

            $result = $response->json();

            return $result['success'] ?? false;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification failed', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
