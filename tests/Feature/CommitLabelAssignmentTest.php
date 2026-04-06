<?php

use App\Models\Commit;
use App\Models\Label;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('adds and removes a label on a commit', function () {
    $user = User::factory()->create();

    $repo = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => 'repo-1',
        'name' => 'demo',
    ]);

    $commit = Commit::query()->create([
        'repository_id' => $repo->id,
        'sha' => 'sha-attach-1',
        'message' => 'Attach label',
        'committed_at' => now(),
    ]);

    $label = Label::query()->create([
        'user_id' => $user->id,
        'name' => 'Feature',
        'color' => '#16a34a',
    ]);

    $attach = $this->actingAs($user)
        ->from('/')
        ->post('/story/'.$repo->id.'/commits/'.$commit->id.'/labels', [
            'label_id' => $label->id,
        ]);

    $attach->assertRedirect('/');

    expect($commit->labels()->count())->toBe(1);

    $detach = $this->actingAs($user)
        ->from('/')
        ->delete('/story/'.$repo->id.'/commits/'.$commit->id.'/labels/'.$label->id);

    $detach->assertRedirect('/');

    expect($commit->labels()->count())->toBe(0);
});

it('bulk applies a label to multiple commits', function () {
    $user = User::factory()->create();

    $repo = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => 'repo-2',
        'name' => 'demo-bulk',
    ]);

    $commitA = Commit::query()->create([
        'repository_id' => $repo->id,
        'sha' => 'sha-bulk-a',
        'message' => 'A',
        'committed_at' => now(),
    ]);

    $commitB = Commit::query()->create([
        'repository_id' => $repo->id,
        'sha' => 'sha-bulk-b',
        'message' => 'B',
        'committed_at' => now(),
    ]);

    $label = Label::query()->create([
        'user_id' => $user->id,
        'name' => 'Client A',
        'color' => '#2563eb',
    ]);

    $response = $this->actingAs($user)
        ->from('/')
        ->post('/story/'.$repo->id.'/labels/bulk-apply', [
            'label_id' => $label->id,
            'commit_ids' => [$commitA->id, $commitB->id],
        ]);

    $response->assertRedirect('/');

    expect($commitA->labels()->count())->toBe(1);
    expect($commitB->labels()->count())->toBe(1);
});
