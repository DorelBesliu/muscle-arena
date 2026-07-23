<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::anonymousComponentPath(resource_path('views'), 'app.components');

        $this->publishes([
            resource_path('lang-stubs/auth/en') => lang_path('en'),
            resource_path('lang-stubs/auth/ro') => lang_path('ro'),
            resource_path('lang-stubs/auth/ru') => lang_path('ru'),
        ], 'auth-translations');
    }
}
