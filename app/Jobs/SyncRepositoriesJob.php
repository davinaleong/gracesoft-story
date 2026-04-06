<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Git\GitProviderInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SyncRepositoriesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly int $userId) {}

    public function handle(GitProviderInterface $provider): void
    {
        $user = User::query()->findOrFail($this->userId);
        $repositories = $provider->getRepositories($user);

        $now = Carbon::now();

        $rows = array_map(function (array $repository) use ($user, $now): array {
            return [
                'user_id' => $user->id,
                'provider' => (string) ($repository['provider'] ?? 'github'),
                'external_id' => (string) $repository['external_id'],
                'name' => (string) $repository['name'],
                'full_name' => $repository['full_name'] ?? null,
                'url' => $repository['url'] ?? null,
                'last_synced_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $repositories);

        if ($rows === []) {
            return;
        }

        $user->repositories()->upsert(
            $rows,
            ['provider', 'external_id'],
            ['name', 'full_name', 'url', 'last_synced_at', 'updated_at'],
        );
    }
}
