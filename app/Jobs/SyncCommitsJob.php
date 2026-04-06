<?php

namespace App\Jobs;

use App\Models\Repository;
use App\Services\Git\GitProviderInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SyncCommitsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly int $repositoryId) {}

    public function handle(GitProviderInterface $provider): void
    {
        $repository = Repository::query()->findOrFail($this->repositoryId);
        $repoIdentifier = $repository->full_name ?: $repository->name;

        $commits = $provider->getCommits($repoIdentifier);
        $now = Carbon::now();

        $rows = array_map(function (array $commit) use ($repository, $now): array {
            $committedAt = $commit['committed_at'] ?? null;

            return [
                'repository_id' => $repository->id,
                'sha' => (string) ($commit['sha'] ?? ''),
                'message' => (string) ($commit['message'] ?? ''),
                'author_name' => $commit['author_name'] ?? null,
                'author_email' => $commit['author_email'] ?? null,
                'committed_at' => $committedAt ? Carbon::parse((string) $committedAt) : $now,
                'branch' => $commit['branch'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $commits);

        $rows = array_values(array_filter($rows, fn (array $row): bool => $row['sha'] !== ''));

        if ($rows === []) {
            return;
        }

        $repository->commits()->upsert(
            $rows,
            ['sha'],
            ['message', 'author_name', 'author_email', 'committed_at', 'branch', 'updated_at'],
        );
    }
}
