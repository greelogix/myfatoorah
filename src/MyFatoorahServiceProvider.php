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
            // Get API key from site settings, fallback to config/env
            $apiKey = \Greelogix\MyFatoorah\Models\SiteSetting::getValue('myfatoorah_api_key', config('myfatoorah.api_key', ''));
            $baseUrl = \Greelogix\MyFatoorah\Models\SiteSetting::getValue('myfatoorah_base_url', config('myfatoorah.base_url', 'https://apitest.myfatoorah.com'));
            $testMode = \Greelogix\MyFatoorah\Models\SiteSetting::getValue('myfatoorah_test_mode', config('myfatoorah.test_mode', true));
            
            // Convert test mode to boolean
            $testMode = filter_var($testMode, FILTER_VALIDATE_BOOLEAN);
            
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

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'myfatoorah');
    }
}

