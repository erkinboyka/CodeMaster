<?php

namespace App\Services;

class TotpService
{
    private const ISSUER = 'CodeMaster';
    private const PERIOD = 30;
    private const DIGITS = 6;
    private const ALGORITHM = 'sha1';

    public function generateSecret(int $length = 20): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    public function getUri(string $secret, string $email): string
    {
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => self::ISSUER,
            'algorithm' => 'SHA1',
            'digits' => self::DIGITS,
            'period' => self::PERIOD,
        ]);
        return 'otpauth://totp/' . rawurlencode(self::ISSUER) . ':' . rawurlencode($email) . '?' . $params;
    }

    public function verifyCode(string $secret, string $code, int $tolerance = 1): bool
    {
        $currentTime = floor(time() / self::PERIOD);

        for ($i = -$tolerance; $i <= $tolerance; $i++) {
            $calculatedCode = $this->generateCode($secret, $currentTime + $i);
            if (hash_equals($calculatedCode, str_pad($code, self::DIGITS, '0', STR_PAD_LEFT))) {
                return true;
            }
        }

        return false;
    }

    private function generateCode(string $secret, int $timestamp): string
    {
        $time = pack('N*', 0) . pack('N*', $timestamp);
        $hash = hash_hmac(self::ALGORITHM, $time, $this->base32Decode($secret), true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
        $code = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1] ?? 0) & 0xFF) << 16) |
            ((ord($hash[$offset + 2] ?? 0) & 0xFF) << 8) |
            (ord($hash[$offset + 3] ?? 0) & 0xFF)
        ) % pow(10, self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $input): string
    {
        $map = [
            'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5,
            'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9, 'K' => 10, 'L' => 11,
            'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17,
            'S' => 18, 'T' => 19, 'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
            'Y' => 24, 'Z' => 25, '2' => 26, '3' => 27, '4' => 28, '5' => 29,
            '6' => 30, '7' => 31,
        ];

        $input = strtoupper(rtrim($input, '='));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0, $len = strlen($input); $i < $len; $i++) {
            $val = $map[$input[$i]] ?? null;
            if ($val === null) continue;

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(bin2hex(random_bytes(4)));
            $codes[] = substr($code, 0, 4) . '-' . substr($code, 4, 4);
        }
        return $codes;
    }

    public function hashRecoveryCodes(array $codes): string
    {
        $hashed = array_map(fn($code) => hash('sha256', $code), $codes);
        return json_encode($hashed);
    }

    public function verifyRecoveryCode(string $hashedCodes, string $code): ?string
    {
        $codes = json_decode($hashedCodes, true);
        if (!is_array($codes)) return null;

        $hashed = hash('sha256', $code);
        foreach ($codes as $i => $storedHash) {
            if (hash_equals($storedHash, $hashed)) {
                unset($codes[$i]);
                return json_encode(array_values($codes));
            }
        }

        return null;
    }
}
