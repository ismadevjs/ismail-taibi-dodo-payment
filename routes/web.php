<?php

use DodoPayments\Laravel\Http\Controllers\PaymentController;
use DodoPayments\Laravel\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

$prefix = config('dodopayments.routes.prefix', 'payment');
$middleware = config('dodopayments.routes.middleware', ['web']);

// Payment Routes
Route::prefix($prefix)
    ->middleware($middleware)
    ->group(function () {
        Route::get('/checkout', [PaymentController::class, 'showCheckout'])->name('dodopayments.checkout');
        Route::post('/checkout', [PaymentController::class, 'createCheckout'])->name('dodopayments.create-checkout');
        Route::get('/success', [PaymentController::class, 'success'])->name('dodopayments.success');
        Route::get('/cancel', [PaymentController::class, 'cancel'])->name('dodopayments.cancel');
        Route::post('/create-link', [PaymentController::class, 'createPaymentLink'])->name('dodopayments.create-link');
    });

// Webhook Route (without CSRF middleware)
Route::post('/webhook/dodopayments', [WebhookController::class, 'handleWebhook'])
    ->middleware(config('dodopayments.routes.webhook_middleware', ['api']))
    ->name('dodopayments.webhook');
