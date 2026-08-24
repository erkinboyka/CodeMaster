<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['ru', 'en', 'tg'];

        $locale = $request->query('lang');

        if ($locale && in_array($locale, $supportedLocales, true)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        } else {
            $locale = Session::get('locale');
            if (!$locale) {
                $cookieLocale = $request->cookie('locale');
                if ($cookieLocale && in_array($cookieLocale, $supportedLocales, true)) {
                    $locale = $cookieLocale;
                } else {
                    $locale = config('app.locale', 'ru');
                }
            }
            App::setLocale($locale);
        }

        // The Tajik catalogue is being expanded. Until a Tajik UI string is
        // available, show its complete Russian equivalent instead of exposing
        // untranslated English keys to visitors.
        app('translator')->setFallback($locale === 'tg' ? 'ru' : 'en');

        return $next($request);
    }
}
