<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your MyFatoorah API key. Get it from your MyFatoorah dashboard.
    |
    */
    'api_key' => env('MYFATOORAH_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | MyFatoorah API base URL. Use test URL for testing, production for live.
    |
    */
    'base_url' => env('MYFATOORAH_BASE_URL', 'https://apitest.myfatoorah.com'),

    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    |
    | Set to true for test mode, false for production.
    |
    */
    'test_mode' => env('MYFATOORAH_TEST_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Secret key for validating webhook signatures.
    |
    */
    'webhook_secret' => env('MYFATOORAH_WEBHOOK_SECRET', ''),

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
    'currency' => env('MYFATOORAH_CURRENCY', 'KWD'),

    /*
    |--------------------------------------------------------------------------
    | Language
    |--------------------------------------------------------------------------
    |
    | Default language code (en, ar).
    |
    */
    'language' => env('MYFATOORAH_LANGUAGE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Country ISO
    |--------------------------------------------------------------------------
    |
    | Default country ISO code (e.g., KWT, SAU, UAE).
    |
    */
    'country_iso' => env('MYFATOORAH_COUNTRY_ISO', 'KWT'),

    /*
    |--------------------------------------------------------------------------
    | Payment Model
    |--------------------------------------------------------------------------
    |
    | The model class that will be used to store payment records.
    |
    */
    'payment_model' => \Greelogix\MyFatoorah\Models\MyFatoorahPayment::class,
];

