<?php

use App\Models\Account;
use App\Models\Plan;
use App\Models\Repository;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('shows labels management page for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/labels/manage');

    $response->assertOk();
    $response->assertSee('Label management');
    $response->assertSee('Create new label');
});

it('shows insights paywall for users without insights access', function () {
    $user = User::factory()->create();

    Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => 'repo-insights-1',
        'name' => 'repo-insights-1',
    ]);

    $response = $this->actingAs($user)->get('/insights');

    $response->assertOk();
    $response->assertSee('Insights are available on paid plans');
});

it('shows settings page with subscription details', function () {
    $user = User::factory()->create();

    $plan = Plan::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'Growth',
        'slug' => 'growth',
        'max_users' => 2,
        'max_timelines' => 25,
        'storage_mb' => 1024,
        'can_use_insights' => true,
    ]);

    $account = Account::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'Workspace',
        'owner_user_id' => $user->id,
    ]);

    Subscription::query()->create([
        'id' => (string) Str::uuid(),
        'account_id' => $account->id,
        'plan_id' => $plan->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->get('/settings');

    $response->assertOk();
    $response->assertSee('Workspace preferences');
    $response->assertSee('Growth');
    $response->assertSee('Subscription and billing');
});
