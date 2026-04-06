<?php

use App\Enums\NotificationType;
use App\Jobs\SyncCommitsJob;
use App\Jobs\SyncRepositoriesJob;
use App\Models\Repository;
use App\Models\User;
use App\Notifications\SystemEventNotification;
use App\Services\Git\GitProviderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('stores repositories returned by git provider', function () {
    Bus::fake();
    Notification::fake();

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

    Bus::assertDispatched(SyncCommitsJob::class, 1);
    Notification::assertSentTo(
        $user,
        SystemEventNotification::class,
        fn (SystemEventNotification $notification): bool => $notification->type === NotificationType::AutoSyncSuccess,
    );
});

it('updates existing repository on resync', function () {
    Bus::fake();
    Notification::fake();

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

    Bus::assertDispatched(SyncCommitsJob::class, 1);
    Notification::assertSentTo(
        $user,
        SystemEventNotification::class,
        fn (SystemEventNotification $notification): bool => $notification->type === NotificationType::AutoSyncSuccess,
    );
});

it('does not dispatch commit sync when provider returns no repositories', function () {
    Bus::fake();
    Notification::fake();

    $user = User::factory()->create();

    app()->bind(GitProviderInterface::class, fn (): GitProviderInterface => new class implements GitProviderInterface
    {
        public function getRepositories(User $user): array
        {
            return [];
        }

        public function getCommits(string $repoId): array
        {
            return [];
        }
    });

    (new SyncRepositoriesJob($user->id))->handle(app(GitProviderInterface::class));

    expect(Repository::query()->count())->toBe(0);
    Bus::assertNotDispatched(SyncCommitsJob::class);
    Notification::assertNothingSent();
});

it('sends manual sync completion notification for manual trigger', function () {
    Bus::fake();
    Notification::fake();

    $user = User::factory()->create();

    app()->bind(GitProviderInterface::class, fn (): GitProviderInterface => new class implements GitProviderInterface
    {
        public function getRepositories(User $user): array
        {
            return [
                [
                    'provider' => 'github',
                    'external_id' => 'manual-1001',
                    'name' => 'manual-demo',
                    'full_name' => 'octocat/manual-demo',
                    'url' => 'https://github.com/octocat/manual-demo',
                ],
            ];
        }

        public function getCommits(string $repoId): array
        {
            return [];
        }
    });

    (new SyncRepositoriesJob($user->id, 'manual'))->handle(app(GitProviderInterface::class), app(\App\Services\Notifications\NotificationService::class));

    Notification::assertSentTo(
        $user,
        SystemEventNotification::class,
        fn (SystemEventNotification $notification): bool => $notification->type === NotificationType::ManualSyncCompleted,
    );
});
