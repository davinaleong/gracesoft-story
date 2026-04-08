<?php

use App\Jobs\SyncRepositoriesJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

it('queues github sync for authenticated user', function () {
    Bus::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/')
        ->post('/sync/github/refresh');

    $response->assertRedirect('/');

    Bus::assertDispatched(SyncRepositoriesJob::class, function (SyncRepositoriesJob $job) use ($user): bool {
        return $job->userId === $user->id
            && $job->trigger === 'manual';
    });
});

it('rejects unauthenticated sync requests', function () {
    Bus::fake();

    $response = $this->post('/sync/github/refresh');

    $response->assertRedirect('/login');

    Bus::assertNotDispatched(SyncRepositoriesJob::class);
});
