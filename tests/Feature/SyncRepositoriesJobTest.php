<?php

use App\Jobs\SyncRepositoriesJob;
use App\Models\Repository;
use App\Models\User;
use App\Services\Git\GitProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores repositories returned by git provider', function () {
    $user = User::factory()->create();

    app()->bind(GitProviderInterface::class, fn (): GitProviderInterface => new class implements GitProviderInterface
    {
        public function getRepositories(User $user): array
        {
            return [
                [
                    'provider' => 'github',
                    'external_id' => '1001',
                    'name' => 'demo',
                    'full_name' => 'octocat/demo',
                    'url' => 'https://github.com/octocat/demo',
                ],
            ];
        }

        public function getCommits(string $repoId): array
        {
            return [];
        }
    });

    (new SyncRepositoriesJob($user->id))->handle(app(GitProviderInterface::class));

    expect(Repository::query()->count())->toBe(1);

    $repository = Repository::query()->firstOrFail();

    expect($repository->user_id)->toBe($user->id);
    expect($repository->provider)->toBe('github');
    expect($repository->external_id)->toBe('1001');
    expect($repository->full_name)->toBe('octocat/demo');
});

it('updates existing repository on resync', function () {
    $user = User::factory()->create();

    Repository::query()->create([
        'user_id' => $user->id,
        'provider' => 'github',
        'external_id' => '1001',
        'name' => 'demo-old',
        'full_name' => 'octocat/demo-old',
        'url' => 'https://github.com/octocat/demo-old',
    ]);

    app()->bind(GitProviderInterface::class, fn (): GitProviderInterface => new class implements GitProviderInterface
    {
        public function getRepositories(User $user): array
        {
            return [
                [
                    'provider' => 'github',
                    'external_id' => '1001',
                    'name' => 'demo-new',
                    'full_name' => 'octocat/demo-new',
                    'url' => 'https://github.com/octocat/demo-new',
                ],
            ];
        }

        public function getCommits(string $repoId): array
        {
            return [];
        }
    });

    (new SyncRepositoriesJob($user->id))->handle(app(GitProviderInterface::class));

    expect(Repository::query()->count())->toBe(1);

    $repository = Repository::query()->firstOrFail();

    expect($repository->name)->toBe('demo-new');
    expect($repository->full_name)->toBe('octocat/demo-new');
});
