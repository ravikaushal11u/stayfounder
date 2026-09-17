<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Force HTTPS in production, behind reverse proxy (Render, Koyeb), or for cloud domains
        if (
            $this->app->environment('production') ||
            request()->header('x-forwarded-proto') === 'https' ||
            !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ||
            (isset($_SERVER['HTTP_HOST']) && (str_contains($_SERVER['HTTP_HOST'], 'onrender.com') || str_contains($_SERVER['HTTP_HOST'], 'koyeb.app')))
        ) {
            URL::forceScheme('https');
        }
    }
}
