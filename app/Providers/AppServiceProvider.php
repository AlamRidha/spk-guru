<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\URL::forceScheme('http');
        \Illuminate\Support\Facades\View::share('currentYear', date('Y'));
        view()->composer('*', function ($view) {
            $view->with('toast', session('toast'));
        });
    }
}
