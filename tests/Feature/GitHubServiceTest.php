<?php

use App\Models\User;
use App\Services\Git\GitHubService;
use App\Services\Git\GitProviderInterface;
use Illuminate\Support\Facades\Http;

it('binds git provider interface to github service', function () {
    $service = app(GitProviderInterface::class);

    expect($service)->toBeInstanceOf(GitHubService::class);
});

it('normalizes repositories from github api', function () {
    config()->set('services.github.token', 'test-token');

    Http::fake([
        'https://api.github.com/user/repos*' => Http::response([
            [
                'id' => 1001,
                'name' => 'demo',
                'full_name' => 'octocat/demo',
                'html_url' => 'https://github.com/octocat/demo',
                'private' => false,
            ],
        ], 200),
    ]);

    $service = app(GitHubService::class);
    $repositories = $service->getRepositories(User::factory()->make());

    expect($repositories)->toHaveCount(1);
    expect($repositories[0]['provider'])->toBe('github');
    expect($repositories[0]['external_id'])->toBe('1001');
    expect($repositories[0]['full_name'])->toBe('octocat/demo');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/user/repos')
            && $request->hasHeader('Authorization', 'Bearer test-token');
    });
});

it('normalizes commits from github api', function () {
    config()->set('services.github.token', 'test-token');

    Http::fake([
        'https://api.github.com/repos/octocat/demo/commits*' => Http::response([
            [
                'sha' => 'abc123',
                'html_url' => 'https://github.com/octocat/demo/commit/abc123',
                'commit' => [
                    'message' => 'Initial commit',
                    'author' => [
                        'name' => 'Octo Cat',
                        'email' => 'octo@example.com',
                        'date' => '2026-04-01T10:00:00Z',
                    ],
                ],
            ],
        ], 200),
    ]);

    $service = app(GitHubService::class);
    $commits = $service->getCommits('octocat/demo');

    expect($commits)->toHaveCount(1);
    expect($commits[0]['sha'])->toBe('abc123');
    expect($commits[0]['message'])->toBe('Initial commit');
    expect($commits[0]['author_email'])->toBe('octo@example.com');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/repos/octocat/demo/commits')
            && $request->hasHeader('Authorization', 'Bearer test-token');
    });
});
