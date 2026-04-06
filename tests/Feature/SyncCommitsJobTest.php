<?php

use App\Jobs\SyncCommitsJob;
use App\Models\Commit;
use App\Models\Repository;
use App\Models\User;
use App\Services\Git\GitProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores commits returned by git provider', function () {
    $user = User::factory()->create();

    $repository = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => '1001',
        'name' => 'demo',
        'full_name' => 'octocat/demo',
        'url' => 'https://github.com/octocat/demo',
    ]);

    app()->bind(GitProviderInterface::class, fn (): GitProviderInterface => new class implements GitProviderInterface
    {
        public function getRepositories(User $user): array
        {
            return [];
        }

        public function getCommits(string $repoId): array
        {
            return [
                [
                    'sha' => 'abc123',
                    'message' => 'Initial commit',
                    'author_name' => 'Octo Cat',
                    'author_email' => 'octo@example.com',
                    'committed_at' => '2026-04-01T10:00:00Z',
                    'branch' => 'main',
                ],
            ];
        }
    });

    (new SyncCommitsJob($repository->id))->handle(app(GitProviderInterface::class));

    expect(Commit::query()->count())->toBe(1);

    $commit = Commit::query()->firstOrFail();

    expect($commit->repository_id)->toBe($repository->id);
    expect($commit->sha)->toBe('abc123');
    expect($commit->message)->toBe('Initial commit');
    expect($commit->branch)->toBe('main');
});

it('updates existing commit on resync', function () {
    $user = User::factory()->create();

    $repository = Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => '1001',
        'name' => 'demo',
        'full_name' => 'octocat/demo',
        'url' => 'https://github.com/octocat/demo',
    ]);

    Commit::query()->create([
        'repository_id' => $repository->id,
        'sha' => 'abc123',
        'message' => 'Old message',
        'author_name' => 'Old Name',
        'author_email' => 'old@example.com',
        'committed_at' => '2026-03-01 10:00:00',
        'branch' => 'main',
    ]);

    app()->bind(GitProviderInterface::class, fn (): GitProviderInterface => new class implements GitProviderInterface
    {
        public function getRepositories(User $user): array
        {
            return [];
        }

        public function getCommits(string $repoId): array
        {
            return [
                [
                    'sha' => 'abc123',
                    'message' => 'Updated message',
                    'author_name' => 'New Name',
                    'author_email' => 'new@example.com',
                    'committed_at' => '2026-04-01T10:00:00Z',
                    'branch' => 'develop',
                ],
            ];
        }
    });

    (new SyncCommitsJob($repository->id))->handle(app(GitProviderInterface::class));

    expect(Commit::query()->count())->toBe(1);

    $commit = Commit::query()->firstOrFail();

    expect($commit->message)->toBe('Updated message');
    expect($commit->author_email)->toBe('new@example.com');
    expect($commit->branch)->toBe('develop');
});
