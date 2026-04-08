<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connect your repository</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gs-page">
    <div class="gs-shell">
        <aside class="gs-sidebar">
            <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-3 py-2 shadow-sm">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-sky-600 text-sm font-semibold text-white">GS</div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">GraceSoft Story</p>
                    <p class="text-xs text-gray-500">Your development chapters</p>
                </div>
            </div>

            <nav class="mt-5 space-y-1">
                <a href="/" class="gs-interactive flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-sm font-medium text-sky-700">
                    <span>●</span>Connect Repository
                </a>
                @if ($repositories->isNotEmpty())
                    <a href="{{ route('story.timeline', $repositories->first()) }}" class="gs-interactive flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:text-gray-900">
                        <span class="text-sky-600">→</span>Story Timeline
                    </a>
                @else
                    <span class="flex cursor-not-allowed items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400">Story Timeline</span>
                @endif
                <span class="flex cursor-not-allowed items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400">🔒 Insights (Paid)</span>
                <span class="flex cursor-not-allowed items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400">⚙ Settings</span>
            </nav>

            <section class="mt-6">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Repositories</p>
                <div class="max-h-80 space-y-1 overflow-y-auto rounded-lg border border-gray-200 bg-white p-2">
                    @forelse ($repositories as $repoItem)
                        <a href="{{ route('story.timeline', $repoItem) }}" class="gs-interactive block rounded-md px-2 py-2 text-sm text-gray-700">
                            {{ $repoItem->full_name ?: $repoItem->name }}
                        </a>
                    @empty
                        <p class="px-2 py-4 text-sm text-gray-500">No repositories yet.</p>
                    @endforelse
                </div>
            </section>
        </aside>

        <main class="gs-main">
            <section class="gs-panel gs-fade-up p-8">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Step 1</p>
                <h1 class="mt-2 text-3xl font-semibold text-gray-900">Connect your repository</h1>
                <p class="mt-2 max-w-2xl text-sm text-gray-600">Start your story by connecting a repository. Once connected, each commit becomes a chapter you can explore and tag.</p>

                @if (session('status'))
                    <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @auth
                        <a href="{{ route('auth.github.redirect') }}" class="gs-interactive rounded-lg border border-sky-200 bg-sky-50 px-4 py-4 shadow-sm hover:border-sky-300 hover:bg-sky-100">
                            <p class="text-sm font-semibold text-sky-800">GitHub</p>
                            <p class="mt-1 text-xs text-sky-700">Connect now</p>
                        </a>
                    @else
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-4 text-sm text-gray-500">
                            GitHub
                            <p class="mt-1 text-xs">Sign in first to connect.</p>
                        </div>
                    @endauth

                    <button type="button" disabled class="cursor-not-allowed rounded-lg border border-gray-200 bg-white px-4 py-4 text-left text-sm text-gray-400">
                        GitLab
                        <p class="mt-1 text-xs">Coming soon</p>
                    </button>

                    <button type="button" disabled class="cursor-not-allowed rounded-lg border border-gray-200 bg-white px-4 py-4 text-left text-sm text-gray-400">
                        Bitbucket
                        <p class="mt-1 text-xs">Coming soon</p>
                    </button>
                </div>

                @if ($hasGitHubAccount && $repositories->isNotEmpty())
                    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-medium text-gray-900">GitHub connected</p>
                        <p class="mt-1 text-sm text-gray-600">You can jump to your first repository timeline now.</p>
                        <a href="{{ route('story.timeline', $repositories->first()) }}" class="mt-3 inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Open timeline</a>
                    </div>
                @endif
            </section>
        </main>

        <aside class="gs-inspector">
            <div class="gs-panel p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Connection Status</p>
                <h2 class="mt-2 text-lg font-semibold text-gray-900">Provider Setup</h2>
                <ul class="mt-4 space-y-3 text-sm text-gray-600">
                    <li class="rounded-md bg-gray-50 px-3 py-2">GitHub: {{ $hasGitHubAccount ? 'Connected' : 'Not connected' }}</li>
                    <li class="rounded-md bg-gray-50 px-3 py-2">GitLab: Coming soon</li>
                    <li class="rounded-md bg-gray-50 px-3 py-2">Bitbucket: Coming soon</li>
                </ul>
            </div>
        </aside>
    </div>
</body>
</html>
