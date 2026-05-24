<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Jika aplikasi berjalan di Fly.dev (atau environment production), paksa gunakan HTTPS
        if (config('app.env') === 'production' || env('FLY_APP_NAME')) {
        URL::forceScheme('https');
        }
    }
}
