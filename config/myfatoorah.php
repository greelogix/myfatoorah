<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your MyFatoorah API key. Get it from your MyFatoorah dashboard.
    |
    | NOTE: This is a fallback value. The actual API key should be configured
    | in the admin panel at /admin/myfatoorah/settings and stored in the
    | myfatoorah_site_settings table. The SiteSetting value takes priority
    | over this value and over the .env value.
    |
    */
    'api_key' => \Greelogix\MyFatoorah\Models\SiteSetting::getValue(
        'myfatoorah_api_key',
        env('MYFATOORAH_API_KEY', '')
    ),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | MyFatoorah API base URL. Use test URL for testing, production for live.
    |
    | NOTE: This is a fallback value. Configure in admin panel for production use.
    |
    */
    'base_url' => \Greelogix\MyFatoorah\Models\SiteSetting::getValue(
        'myfatoorah_base_url',
        env('MYFATOORAH_BASE_URL', 'https://apitest.myfatoorah.com')
    ),

    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    |
    | Set to true for test mode, false for production.
    |
    | NOTE: This is a fallback value. Configure in admin panel for production use.
    |
    */
    'test_mode' => filter_var(
        \Greelogix\MyFatoorah\Models\SiteSetting::getValue(
            'myfatoorah_test_mode',
            env('MYFATOORAH_TEST_MODE', true)
        ),
        FILTER_VALIDATE_BOOLEAN
    ),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Secret key for validating webhook signatures.
    |
    */
    'webhook_secret' => \Greelogix\MyFatoorah\Models\SiteSetting::getValue(
        'myfatoorah_webhook_secret',
        env('MYFATOORAH_WEBHOOK_SECRET', '')
    ),

    /*
    |--------------------------------------------------------------------------
    | Recurring Payments
    |--------------------------------------------------------------------------
    |
    | Configuration for recurring payments.
    |
    */
    'recurring' => [
        'enabled' => env('MYFATOORAH_RECURRING_ENABLED', true),
        'default_cycle' => env('MYFATOORAH_RECURRING_CYCLE', 'Monthly'),
        'default_cycle_count' => env('MYFATOORAH_RECURRING_CYCLE_COUNT', 12),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Default currency code (ISO 4217).
    |
    */
    'currency' => \Greelogix\MyFatoorah\Models\SiteSetting::getValue(
        'myfatoorah_currency',
        env('MYFATOORAH_CURRENCY', 'KWD')
    ),

    /*
    |--------------------------------------------------------------------------
    | Language
    |--------------------------------------------------------------------------
    |
    | Default language code (en, ar).
    |
    */
    'language' => \Greelogix\MyFatoorah\Models\SiteSetting::getValue(
        'myfatoorah_language',
        env('MYFATOORAH_LANGUAGE', 'en')
    ),

    /*
    |--------------------------------------------------------------------------
    | Payment Model
    |--------------------------------------------------------------------------
    |
    | The model class that will be used to store payment records.
    |
    */
    'payment_model' => \Greelogix\MyFatoorah\Models\MyFatoorahPayment::class,

    /*
    |--------------------------------------------------------------------------
    | Payment Method Model
    |--------------------------------------------------------------------------
    |
    | The model class that will be used to store payment methods.
    |
    */
    'payment_method_model' => \Greelogix\MyFatoorah\Models\PaymentMethod::class,
];

