<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        Event::listen(\Illuminate\Auth\Events\Login::class, [\App\Listeners\LogSuccessfulLogin::class, 'handle']);

        \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
            return "<?php echo \App\Models\Setting::format($expression); ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('currencySymbol', function () {
            return "<?php echo \App\Models\Setting::currencySymbol(); ?>";
        });
    }
}
