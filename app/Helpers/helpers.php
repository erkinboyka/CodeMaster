<?php

use App\Services\I18nService;

if (!function_exists('t')) {
    function t(string $key, string $default = ''): string
    {
        return app(I18nService::class)->translate($key, $default);
    }
}

if (!function_exists('currentLang')) {
    function currentLang(): string
    {
        return app(I18nService::class)->currentLang();
    }
}

if (!function_exists('langUrl')) {
    function langUrl(string $lang): string
    {
        return app(I18nService::class)->langUrl($lang);
    }
}

if (!function_exists('normalizeMojibakeText')) {
    function normalizeMojibakeText(string $text): string
    {
        $mojibakeMap = [
            'Ð' => 'Д', 'Ñ' => 'Н',
        ];

        $result = $text;
        foreach ($mojibakeMap as $moji => $real) {
            $result = str_replace($moji, $real, $result);
        }

        if (preg_match('/[\x80-\xFF]{2,}/', $result)) {
            $decoded = @mb_convert_encoding($result, 'UTF-8', 'CP1251');
            if ($decoded && mb_check_encoding($decoded, 'UTF-8')) {
                return $decoded;
            }
        }

        return $result;
    }
}

if (!function_exists('getAvatarUrl')) {
    function getAvatarUrl(string $name): string
    {
        $initials = '';
        $parts = explode(' ', trim($name));
        foreach ($parts as $part) {
            if ($part !== '') {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            }
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }

        if ($initials === '') {
            $initials = 'U';
        }

        $hash = md5($name);
        $hue = hexdec(substr($hash, 0, 3)) % 360;

        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&background=hsl({$hue},60%,50%)&color=fff&bold=true&size=128";
    }
}
