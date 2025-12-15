<?php

namespace Greelogix\MyFatoorah\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Greelogix\MyFatoorah\Models\SiteSetting;

class SiteSettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Ensure default settings exist
        $this->ensureDefaultSettings();
        
        $settings = SiteSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('myfatoorah::admin.settings.index', compact('settings'));
    }

    /**
     * Ensure default settings exist
     */
    protected function ensureDefaultSettings(): void
    {
        $defaultSettings = [
            [
                'key' => 'myfatoorah_api_key',
                'value' => config('myfatoorah.api_key', ''),
                'type' => 'password',
                'group' => 'api',
                'description' => 'MyFatoorah API Key (Get from MyFatoorah Dashboard)',
            ],
            [
                'key' => 'myfatoorah_base_url',
                'value' => config('myfatoorah.base_url', 'https://apitest.myfatoorah.com'),
                'type' => 'text',
                'group' => 'api',
                'description' => 'API Base URL (Test: https://apitest.myfatoorah.com, Production: https://api.myfatoorah.com)',
            ],
            [
                'key' => 'myfatoorah_test_mode',
                'value' => config('myfatoorah.test_mode', true) ? '1' : '0',
                'type' => 'boolean',
                'group' => 'api',
                'description' => 'Test Mode (Yes for testing, No for production)',
            ],
            [
                'key' => 'myfatoorah_webhook_secret',
                'value' => config('myfatoorah.webhook_secret', ''),
                'type' => 'password',
                'group' => 'webhook',
                'description' => 'Webhook Secret (From MyFatoorah Dashboard > Settings > Webhooks)',
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
            SiteSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Store or update settings
     */
    public function store(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'required|string',
            'settings.*.group' => 'required|string',
            'settings.*.description' => 'nullable|string',
        ]);

        foreach ($request->settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'] ?? '',
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                    'description' => $setting['description'] ?? '',
                ]
            );
        }

        return redirect()->route('myfatoorah.admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Update a single setting
     */
    public function update(Request $request, string $key)
    {
        $request->validate([
            'value' => 'nullable',
            'type' => 'required|string',
            'group' => 'required|string',
            'description' => 'nullable|string',
        ]);

        SiteSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $request->value ?? '',
                'type' => $request->type,
                'group' => $request->group,
                'description' => $request->description ?? '',
            ]
        );

        return redirect()->route('myfatoorah.admin.settings.index')
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Delete a setting
     */
    public function destroy(string $key)
    {
        SiteSetting::where('key', $key)->delete();

        return redirect()->route('myfatoorah.admin.settings.index')
            ->with('success', 'Setting deleted successfully.');
    }
}

