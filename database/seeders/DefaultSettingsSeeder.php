<?php

namespace Greelogix\MyFatoorah\Database\Seeders;

use Illuminate\Database\Seeder;
use Greelogix\MyFatoorah\Models\SiteSetting;

/**
 * Default settings seeder for MyFatoorah package
 */
class DefaultSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            // MyFatoorah Configuration
            [
                'key' => 'myfatoorah_api_key',
                'value' => 'SK_KWT_vVZlnnAqu8jRByOWaRPNId4ShzEDNt256dvnjebuyzo52dXjAfRx2ixW5umjWSUx',
                'type' => 'password',
                'description' => 'MyFatoorah API key for payment processing',
            ],
            [
                'key' => 'myfatoorah_test_mode',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable test mode for MyFatoorah payments',
            ],
            [
                'key' => 'myfatoorah_country_iso',
                'value' => 'KWT',
                'type' => 'text',
                'description' => 'MyFatoorah country ISO code',
            ],
            [
                'key' => 'myfatoorah_save_card',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow saving payment cards',
            ],
            [
                'key' => 'myfatoorah_webhook_secret_key',
                'value' => '',
                'type' => 'password',
                'description' => 'MyFatoorah webhook secret key for security',
            ],
            [
                'key' => 'myfatoorah_register_apple_pay',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Register Apple Pay with MyFatoorah',
            ],
            [
                'key' => 'myfatoorah_currency',
                'value' => config('myfatoorah.currency', 'KWD'),
                'type' => 'text',
                'group' => 'payment',
                'description' => 'Default Currency (ISO 4217 code, e.g., KWD, USD, SAR)',
            ],
            [
                'key' => 'myfatoorah_language',
                'value' => config('myfatoorah.language', 'en'),
                'type' => 'text',
                'group' => 'payment',
                'description' => 'Default Language (en or ar)',
            ],
        ];

        foreach ($defaultSettings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Default settings seeded successfully.');
    }
}

