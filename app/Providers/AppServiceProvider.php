<?php

namespace App\Providers;

use App\Services\I18nService;
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
        Blade::directive('t', function (string $expression) {
            return "<?php echo e(t({$expression})); ?>";
        });

        $locale = Session::get('locale', config('app.locale', 'ru'));
        app()->setLocale($locale);
    }
}
