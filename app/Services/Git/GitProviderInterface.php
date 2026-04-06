<?php

namespace App\Services\Git;

use App\Models\User;

interface GitProviderInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRepositories(User $user): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCommits(string $repoId): array;
}
