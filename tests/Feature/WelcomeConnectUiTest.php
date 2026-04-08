<?php

use App\Models\GitAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows connect github action when account is not connected', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $response->assertSee('Connect GitHub');
    $response->assertSee('Not connected yet');
});

it('shows disconnect github action when account is connected', function () {
    $user = User::factory()->create();

    GitAccount::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'access_token' => 'token-value',
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertOk();
    $response->assertSee('Disconnect GitHub');
    $response->assertSee('Connected to GitHub');
});

it('redirects guests to login instead of rendering app shell', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
