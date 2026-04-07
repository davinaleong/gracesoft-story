<?php

use App\Http\Controllers\Api\PostmarkWebhookController;
use App\Http\Controllers\Api\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle'])
    ->name('api.webhooks.stripe');

Route::post('/webhooks/postmark', [PostmarkWebhookController::class, 'handle'])
    ->name('api.webhooks.postmark');
