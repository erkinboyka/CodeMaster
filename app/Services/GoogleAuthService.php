<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAuthService
{
    protected string $clientId;

    public function __construct()
    {
        $this->clientId = config('services.google.client_id', '');
    }

    public function verifyIdToken(string $token): array
    {
        try {
            $response = Http::get("https://oauth2.googleapis.com/tokeninfo", [
                'id_token' => $token,
            ]);

            if ($response->successful()) {
                $payload = $response->json();

                if (isset($payload['aud']) && $payload['aud'] !== $this->clientId) {
                    throw new \Exception('Token audience mismatch.');
                }

                if (isset($payload['exp']) && $payload['exp'] < time()) {
                    throw new \Exception('Token has expired.');
                }

                if (isset($payload['iss']) && !in_array($payload['iss'], ['accounts.google.com', 'https://accounts.google.com'])) {
                    throw new \Exception('Token issuer mismatch.');
                }

                return $payload;
            }

            throw new \Exception('Invalid Google token.');
        } catch (\Exception $e) {
            Log::error('Google token verification failed', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
