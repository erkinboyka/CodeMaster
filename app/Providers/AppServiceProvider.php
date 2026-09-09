<?php

namespace App\Providers;

use App\Services\I18nService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(I18nService::class, function ($app) {
            return new I18nService();
        });
    }

    public function boot(): void
    {
        // The project stores its flat UI dictionaries in lang/{locale}.php,
        // while Laravel's __() helper normally checks JSON files for flat keys.
        // Register both sources as translation lines so __() works consistently
        // throughout all existing views.
        $translator = app('translator');
        $translator->addJsonPath(base_path('lang'));

        Blade::directive('t', function (string $expression) {
            return "<?php echo e(t({$expression})); ?>";
        });

        $locale = Session::get('locale', config('app.locale', 'ru'));
        app()->setLocale($locale);

        RateLimiter::for('auth', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5);
        });

        RateLimiter::for('api', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60);
        });

        RateLimiter::for('ai', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(20);
        });

        RateLimiter::for('peer', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(300);
        });

        RateLimiter::for('community', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10);
        });

        RateLimiter::for('contest', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30);
        });

        RateLimiter::for('submit', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(20);
        });
    }
}
