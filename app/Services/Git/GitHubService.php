<?php

namespace App\Services\Git;

use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GitHubService implements GitProviderInterface
{
    public function getRepositories(User $user): array
    {
        $response = $this->client($this->resolveTokenForUser($user))
            ->get('/user/repos', [
                'per_page' => 100,
                'sort' => 'updated',
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Failed to fetch repositories from GitHub.');
        }

        return collect($response->json())
            ->map(function (array $repo): array {
                return [
                    'provider' => 'github',
                    'external_id' => (string) Arr::get($repo, 'id'),
                    'name' => (string) Arr::get($repo, 'name'),
                    'full_name' => (string) Arr::get($repo, 'full_name'),
                    'url' => (string) Arr::get($repo, 'html_url'),
                    'private' => (bool) Arr::get($repo, 'private', false),
                ];
            })
            ->all();
    }

    public function getCommits(string $repoId): array
    {
        $response = $this->client($this->resolveTokenFromConfig())
            ->get('/repos/'.$repoId.'/commits', [
                'per_page' => 100,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Failed to fetch commits from GitHub.');
        }

        return collect($response->json())
            ->map(function (array $commit): array {
                return [
                    'sha' => (string) Arr::get($commit, 'sha'),
                    'message' => (string) Arr::get($commit, 'commit.message'),
                    'author_name' => Arr::get($commit, 'commit.author.name'),
                    'author_email' => Arr::get($commit, 'commit.author.email'),
                    'committed_at' => Arr::get($commit, 'commit.author.date'),
                    'html_url' => (string) Arr::get($commit, 'html_url'),
                ];
            })
            ->all();
    }

    private function client(string $token): PendingRequest
    {
        return Http::baseUrl((string) config('services.github.api_url', 'https://api.github.com'))
            ->acceptJson()
            ->withToken($token)
            ->withHeaders([
                'X-GitHub-Api-Version' => '2022-11-28',
            ]);
    }

    private function resolveTokenForUser(User $user): string
    {
        $userToken = $user->getAttribute('github_token');

        if (is_string($userToken) && $userToken !== '') {
            return $userToken;
        }

        return $this->resolveTokenFromConfig();
    }

    private function resolveTokenFromConfig(): string
    {
        $token = (string) config('services.github.token', '');

        if ($token === '') {
            throw new RuntimeException('Missing GitHub token configuration.');
        }

        return $token;
    }
}
