<?php

use App\Jobs\SyncRepositoriesJob;
use App\Models\GitAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('redirects authenticated user to github authorize url with state', function () {
    config()->set('services.github.client_id', 'client-id');
    config()->set('services.github.redirect', 'http://localhost/auth/github/callback');
    config()->set('services.github.authorize_url', 'https://github.com/login/oauth/authorize');

    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/auth/github/redirect');

    $response->assertRedirect();

    $location = $response->headers->get('Location');

    expect($location)->toContain('https://github.com/login/oauth/authorize');
    expect($location)->toContain('client_id=client-id');
    expect($location)->toContain('redirect_uri=http%3A%2F%2Flocalhost%2Fauth%2Fgithub%2Fcallback');
    expect(session('github_oauth_state'))->not->toBeEmpty();
});

it('stores github token and dispatches sync job on callback', function () {
    config()->set('services.github.client_id', 'client-id');
    config()->set('services.github.client_secret', 'client-secret');
    config()->set('services.github.redirect', 'http://localhost/auth/github/callback');
    config()->set('services.github.token_url', 'https://github.com/login/oauth/access_token');
    config()->set('services.github.user_url', 'https://api.github.com/user');

    Bus::fake();

    Http::fake([
        'https://github.com/login/oauth/access_token' => Http::response([
            'access_token' => 'github-access-token',
            'token_type' => 'bearer',
            'scope' => 'repo read:user',
        ], 200),
        'https://api.github.com/user' => Http::response([
            'id' => 12345,
            'login' => 'octocat',
        ], 200),
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession(['github_oauth_state' => 'valid-state'])
        ->get('/auth/github/callback?code=oauth-code&state=valid-state');

    $response->assertRedirect('/');

    $account = GitAccount::query()
        ->where('user_id', $user->id)
        ->where('provider', 'github')
        ->first();

    expect($account)->not->toBeNull();
    expect($account?->access_token)->toBe('github-access-token');

    Bus::assertDispatched(SyncRepositoriesJob::class, function (SyncRepositoriesJob $job) use ($user): bool {
        return $job->userId === $user->id;
    });
});

it('rejects callback when state does not match', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession(['github_oauth_state' => 'expected-state'])
        ->get('/auth/github/callback?code=oauth-code&state=bad-state');

    $response->assertStatus(422);

    expect(GitAccount::query()->count())->toBe(0);
});
