<?php

use App\Models\Commit;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows repository story timeline for owner', function () {
    $user = User::factory()->create();

    $repository = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => 'r-1',
        'name' => 'demo',
        'full_name' => 'octocat/demo',
        'url' => 'https://github.com/octocat/demo',
    ]);

    Commit::query()->create([
        'repository_id' => $repository->id,
        'sha' => 'abc123def',
        'message' => 'Ship timeline page',
        'author_name' => 'Octo Cat',
        'author_email' => 'octo@example.com',
        'committed_at' => '2026-04-06 10:00:00',
        'branch' => 'main',
    ]);

    $response = $this->actingAs($user)->get('/story/'.$repository->id);

    $response->assertOk();
    $response->assertSee('Story Timeline');
    $response->assertSee('octocat/demo');
    $response->assertSee('Ship timeline page');
    $response->assertSee('abc123d');
});

it('returns not found for repository not owned by user', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $repository = Repository::query()->create([
        'user_id' => $owner->id,
        'provider' => 'github',
        'external_id' => 'r-2',
        'name' => 'private-repo',
    ]);

    $response = $this->actingAs($otherUser)->get('/story/'.$repository->id);

    $response->assertNotFound();
});

it('requires authentication for story timeline', function () {
    $user = User::factory()->create();

    $repository = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => 'r-3',
        'name' => 'public-repo',
    ]);

    $response = $this->get('/story/'.$repository->id);

    $response->assertStatus(401);
});
