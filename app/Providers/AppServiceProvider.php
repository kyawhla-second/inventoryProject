<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrap();

        // Blade directive for Kyat currency
        Blade::directive('money', function ($expression) {
            return "<?php echo 'Ks ' . number_format($expression, 2); ?>";
        });

        // Set application locale from session if available
        $locale = session('locale', config('app.locale'));
        app()->setLocale($locale);
    }
}
