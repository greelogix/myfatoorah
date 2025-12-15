<?php

use Illuminate\Support\Facades\Route;
use Greelogix\MyFatoorah\Http\Controllers\WebhookController;
use Greelogix\MyFatoorah\Http\Controllers\Admin\SiteSettingController;
use Greelogix\MyFatoorah\Http\Controllers\Admin\PaymentMethodController;

// Webhook route (CSRF exempt)
Route::post('myfatoorah/webhook', [WebhookController::class, 'handle'])
    ->name('myfatoorah.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

// Admin routes
Route::prefix('admin/myfatoorah')->name('myfatoorah.admin.')->middleware(['web', 'auth'])->group(function () {
    // Settings routes
    Route::get('settings', [SiteSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SiteSettingController::class, 'store'])->name('settings.store');
    Route::put('settings/{key}', [SiteSettingController::class, 'update'])->name('settings.update');
    Route::delete('settings/{key}', [SiteSettingController::class, 'destroy'])->name('settings.destroy');

    // Payment methods routes
    Route::get('payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::post('payment-methods/sync', [PaymentMethodController::class, 'sync'])->name('payment-methods.sync');
    Route::post('payment-methods/{paymentMethod}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])->name('payment-methods.toggle-status');
});

