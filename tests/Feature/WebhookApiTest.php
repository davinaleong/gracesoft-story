<?php

use App\Models\Account;
use App\Models\Plan;
use App\Models\StripeWebhookEvent;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('rejects stripe webhook with invalid signature', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test');

    $response = $this->postJson('/api/webhooks/stripe', [
        'id' => 'evt_invalid',
        'type' => 'customer.subscription.updated',
    ]);

    $response->assertStatus(401);
});

it('processes stripe subscription update webhook and stores event', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test');

    $user = User::factory()->create();

    $account = Account::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'Main Account',
        'owner_user_id' => $user->id,
        'stripe_customer_id' => 'cus_123',
    ]);

    $plan = Plan::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'Growth',
        'slug' => 'growth',
        'stripe_price_id' => 'price_growth',
        'stripe_product_id' => 'prod_growth',
        'max_users' => 5,
        'max_items' => 500,
        'max_replies' => 2000,
    ]);

    $payload = [
        'id' => 'evt_123',
        'type' => 'customer.subscription.updated',
        'data' => [
            'object' => [
                'id' => 'sub_123',
                'customer' => 'cus_123',
                'status' => 'active',
                'current_period_end' => now()->addMonth()->timestamp,
                'items' => [
                    'data' => [
                        [
                            'price' => ['id' => 'price_growth'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $json = json_encode($payload, JSON_THROW_ON_ERROR);
    $timestamp = time();
    $signature = hash_hmac('sha256', $timestamp.'.'.$json, 'whsec_test');

    $response = $this->call(
        'POST',
        '/api/webhooks/stripe',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 't='.$timestamp.',v1='.$signature,
        ],
        $json,
    );

    $response->assertOk();

    expect(StripeWebhookEvent::query()->where('event_id', 'evt_123')->exists())->toBeTrue();

    $subscription = Subscription::query()->where('stripe_subscription_id', 'sub_123')->first();

    expect($subscription)->not->toBeNull();
    expect($subscription?->account_id)->toBe($account->id);
    expect($subscription?->plan_id)->toBe($plan->id);
});

it('processes stripe subscription update using metadata tier when price id is missing', function () {
    config()->set('services.stripe.webhook_secret', 'whsec_test');

    $user = User::factory()->create();

    $account = Account::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'Story Account',
        'owner_user_id' => $user->id,
        'stripe_customer_id' => 'cus_meta_123',
    ]);

    $plan = Plan::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'Pro',
        'slug' => 'pro',
        'stripe_price_id' => null,
        'stripe_product_id' => null,
        'max_users' => 20,
        'max_items' => 5000,
        'max_replies' => 20000,
    ]);

    $payload = [
        'id' => 'evt_meta_123',
        'type' => 'customer.subscription.updated',
        'data' => [
            'object' => [
                'id' => 'sub_meta_123',
                'customer' => 'cus_meta_123',
                'status' => 'active',
                'current_period_end' => now()->addMonth()->timestamp,
                'metadata' => [
                    'app' => 'story',
                    'tier' => 'pro',
                ],
                'items' => [
                    'data' => [
                        [
                            'price' => [
                                'metadata' => [
                                    'app' => 'story',
                                    'tier' => 'pro',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $json = json_encode($payload, JSON_THROW_ON_ERROR);
    $timestamp = time();
    $signature = hash_hmac('sha256', $timestamp.'.'.$json, 'whsec_test');

    $response = $this->call(
        'POST',
        '/api/webhooks/stripe',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 't='.$timestamp.',v1='.$signature,
        ],
        $json,
    );

    $response->assertOk();

    $subscription = Subscription::query()->where('stripe_subscription_id', 'sub_meta_123')->first();

    expect($subscription)->not->toBeNull();
    expect($subscription?->account_id)->toBe($account->id);
    expect($subscription?->plan_id)->toBe($plan->id);
});

it('accepts postmark webhook with valid token', function () {
    config()->set('services.postmark.webhook_token', 'pm_token');

    $response = $this->withHeaders([
        'X-Postmark-Server-Token' => 'pm_token',
    ])->postJson('/api/webhooks/postmark', [
        'RecordType' => 'Delivery',
        'MessageID' => 'message_123',
    ]);

    $response->assertOk();
});

it('rejects postmark webhook with invalid token', function () {
    config()->set('services.postmark.webhook_token', 'pm_token');

    $response = $this->withHeaders([
        'X-Postmark-Server-Token' => 'wrong_token',
    ])->postJson('/api/webhooks/postmark', [
        'RecordType' => 'Delivery',
        'MessageID' => 'message_123',
    ]);

    $response->assertStatus(401);
});
