<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('shows auth entry pages to guests', function () {
    $this->get('/login')->assertOk()->assertSee('Sign in');
    $this->get('/register')->assertOk()->assertSee('Create account');
    $this->get('/forgot-password')->assertOk()->assertSee('Forgot password');
});

it('registers a user and sends verification email', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'name' => 'Auth User',
        'email' => 'auth@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertRedirect('/verify-email');

    $user = User::query()->where('email', 'auth@example.com')->first();

    expect($user)->not->toBeNull();
    $this->assertAuthenticatedAs($user);

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('logs in with valid credentials', function () {
    $user = User::factory()->create([
        'password' => 'Password123!',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'Password123!',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

it('sends password reset link from forgot password form', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'forgot@example.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'forgot@example.com',
    ]);

    $response->assertSessionHas('status');

    expect($user->email)->toBe('forgot@example.com');
});

it('shows verify email page to authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/verify-email')
        ->assertOk()
        ->assertSee('Verify email');
});
