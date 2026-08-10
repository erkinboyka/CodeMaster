<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class I18nService
{
    protected array $translations = [];
    protected string $currentLang;

    public function __construct()
    {
        $this->currentLang = $this->currentLang();
        $this->loadTranslations();
    }

    public function translate(string $key, string $default = ''): string
    {
        $keys = explode('.', $key);
        $value = $this->translations[$this->currentLang] ?? [];

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default ?: $key;
            }
            $value = $value[$k];
        }

        return is_string($value) ? $value : ($default ?: $key);
    }

    public function currentLang(): string
    {
        return session('locale', config('app.locale', 'ru'));
    }

    public function langUrl(string $lang): string
    {
        $url = request()->url();
        $currentLang = $this->currentLang();

        if (str_contains($url, "/{$currentLang}/")) {
            return str_replace("/{$currentLang}/", "/{$lang}/", $url);
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . "lang={$lang}";
    }

    protected function loadTranslations(): void
    {
        $locales = ['ru', 'en', 'tg'];
        $langPath = base_path('lang');

        foreach ($locales as $locale) {
            $this->translations[$locale] = [];
            $localePath = "{$langPath}/{$locale}.php";

            if (File::exists($localePath)) {
                $this->translations[$locale] = require $localePath;
            }
        }
    }
}
