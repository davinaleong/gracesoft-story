<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $repository->full_name ?: $repository->name }} Story</title>
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
                <a href="/" class="gs-interactive flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:text-gray-900">
                    <span class="text-sky-600">+</span>Connect Repository
                </a>
                <a href="{{ route('story.timeline', $repository) }}" class="gs-interactive flex items-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-sm font-medium text-sky-700">
                    <span>●</span>Story Timeline
                </a>
                <span class="flex cursor-not-allowed items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400">🔒 Insights (Paid)</span>
                <span class="flex cursor-not-allowed items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400">⚙ Settings</span>
            </nav>

            <section class="mt-6">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Repositories</p>
                <div class="max-h-80 space-y-1 overflow-y-auto rounded-lg border border-gray-200 bg-white p-2">
                    @forelse ($repositories as $repoItem)
                        <a
                            href="{{ route('story.timeline', $repoItem) }}"
                            class="gs-interactive block rounded-md px-2 py-2 text-sm {{ $repoItem->is($repository) ? 'bg-gray-900 text-white hover:bg-gray-900' : 'text-gray-700' }}"
                        >
                            {{ $repoItem->full_name ?: $repoItem->name }}
                        </a>
                    @empty
                        <p class="px-2 py-4 text-sm text-gray-500">No repositories yet.</p>
                    @endforelse
                </div>
            </section>
        </aside>

        <main class="gs-main">
            <header class="gs-panel gs-fade-up p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Story Timeline</p>
                <h1 class="mt-2 text-2xl font-semibold text-gray-900">{{ $repository->full_name ?: $repository->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Each chapter captures what was built, when it happened, and who contributed.</p>
            </header>

            @if (session('status'))
                <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            <section class="mt-5 space-y-2">
                @forelse ($commits as $commit)
                    <article class="gs-interactive gs-fade-up rounded-lg border border-transparent border-b-gray-200 px-3 py-3 hover:border-gray-200" style="animation-delay: {{ min($loop->index * 35, 260) }}ms;">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <a href="{{ route('story.chapter', ['repo' => $repository, 'commit' => $commit]) }}" class="text-base font-semibold leading-tight text-gray-900 hover:text-sky-700">
                                    {{ $commit->message }}
                                </a>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $commit->author_name ?: 'Unknown author' }}
                                    <span class="text-gray-300">•</span>
                                    {{ optional($commit->committed_at)->format('M d, Y H:i') ?: 'Unknown time' }}
                                    @if ($commit->branch)
                                        <span class="text-gray-300">•</span>
                                        path: {{ $commit->branch }}
                                    @endif
                                </p>
                                @if ($commit->labels->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($commit->labels as $label)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white" style="background-color: {{ $label->color }}">
                                                {{ $label->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <span class="rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700">{{ substr($commit->sha, 0, 7) }}</span>
                        </div>
                    </article>
                @empty
                    <div class="gs-panel p-8 text-center">
                        <p class="text-sm text-gray-600">No chapters yet. Sync this repository to start your story.</p>
                    </div>
                @endforelse
            </section>

            @if ($commits->hasPages())
                <div class="mt-5 rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    {{ $commits->links() }}
                </div>
            @endif
        </main>

        <aside class="gs-inspector">
            <div class="gs-panel p-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Chapter Details</p>
                <h2 class="mt-2 text-lg font-semibold text-gray-900">Select a Chapter</h2>
                <p class="mt-2 text-sm text-gray-600">Choose any chapter from the timeline to inspect full author details, labels, and chapter metadata.</p>
                <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-500">
                    Tip: labels help turn raw history into themes like Feature, Fix, or Refactor.
                </div>
            </div>
        </aside>
    </div>
</body>
</html>
