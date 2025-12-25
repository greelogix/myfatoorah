<?php

namespace Greelogix\MyFatoorah;

use Illuminate\Support\ServiceProvider;
use Greelogix\MyFatoorah\Services\MyFatoorahService;

class MyFatoorahServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/myfatoorah.php',
            'myfatoorah'
        );

        $this->app->singleton('myfatoorah', function ($app) {
            $apiKey = config('myfatoorah.api_key', '');
            $baseUrl = config('myfatoorah.base_url', 'https://apitest.myfatoorah.com');
            $testMode = config('myfatoorah.test_mode', true);
            
            return new MyFatoorahService($apiKey, $baseUrl, $testMode);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->publishes([
            __DIR__ . '/../config/myfatoorah.php' => config_path('myfatoorah.php'),
        ], 'myfatoorah-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'myfatoorah-migrations');
    }
}

