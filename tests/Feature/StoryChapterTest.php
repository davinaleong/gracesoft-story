<?php

use App\Models\Commit;
use App\Models\Label;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows chapter detail for owned repository commit including labels', function () {
    $user = User::factory()->create();

    $repo = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => 'repo-chapter',
        'name' => 'demo-chapter',
    ]);

    $commit = Commit::query()->create([
        'repository_id' => $repo->id,
        'sha' => 'sha-chapter-1',
        'message' => 'Build chapter detail',
        'author_name' => 'Octo Cat',
        'author_email' => 'octo@example.com',
        'committed_at' => '2026-04-06 15:00:00',
        'branch' => 'main',
    ]);

    $label = Label::query()->create([
        'user_id' => $user->id,
        'name' => 'Refactor',
        'color' => '#f97316',
    ]);

    $commit->labels()->attach($label->id);

    $response = $this->actingAs($user)
        ->get('/story/'.$repo->id.'/chapter/'.$commit->id);

    $response->assertOk();
    $response->assertSee('Chapter Details');
    $response->assertSee('Build chapter detail');
    $response->assertSee('sha-chapter-1');
    $response->assertSee('Refactor');
});

it('returns not found for chapter route when commit is from another repository', function () {
    $user = User::factory()->create();

    $repoA = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => 'repo-a',
        'name' => 'repo-a',
    ]);

    $repoB = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => 'repo-b',
        'name' => 'repo-b',
    ]);

    $commit = Commit::query()->create([
        'repository_id' => $repoB->id,
        'sha' => 'sha-chapter-2',
        'message' => 'Other repo commit',
        'committed_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get('/story/'.$repoA->id.'/chapter/'.$commit->id);

    $response->assertNotFound();
});
