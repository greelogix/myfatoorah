<?php

use Illuminate\Support\Facades\Route;
use Greelogix\MyFatoorah\Http\Controllers\WebhookController;

// Webhook route (CSRF exempt)
Route::post('myfatoorah/webhook', [WebhookController::class, 'handle'])
    ->name('myfatoorah.webhook')
    ->withoutMiddleware(['web']);

