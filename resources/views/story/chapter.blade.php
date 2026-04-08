<x-story-shell
    :title="'Chapter ' . substr($commit->sha, 0, 7)"
    :repositories="$repositories"
    :current-repository="$repository"
    active-nav="chapter"
>
    <header class="gs-panel p-5 sm:p-6">
        <a href="{{ route('story.timeline', ['repo' => $repository] + array_filter($activeFilters)) }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">← Back to timeline</a>
        <h1 class="mt-3 text-2xl font-semibold text-gray-900">{{ $repository->full_name ?: $repository->name }}</h1>
        <p class="mt-1 text-sm text-gray-600">Open chapters stay in context while the inspector shows full details.</p>
    </header>

    <section class="gs-notes-list mt-4 sm:mt-5">
        @foreach ($commits as $timelineCommit)
            <article class="gs-note-row gs-interactive {{ $timelineCommit->id === $commit->id ? 'bg-sky-50' : '' }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <a href="{{ route('story.chapter', ['repo' => $repository, 'commit' => $timelineCommit] + array_filter($activeFilters)) }}" class="text-base font-semibold leading-tight {{ $timelineCommit->id === $commit->id ? 'text-sky-800' : 'text-gray-900 hover:text-sky-700' }}">
                            {{ $timelineCommit->message }}
                        </a>
                        <p class="gs-note-meta mt-1">
                            {{ $timelineCommit->author_name ?: 'Unknown author' }}
                            <span class="text-gray-300">•</span>
                            {{ optional($timelineCommit->committed_at)->format('M d, Y H:i') ?: 'Unknown time' }}
                        </p>
                    </div>
                    <span class="hidden rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700 sm:inline-flex">{{ substr($timelineCommit->sha, 0, 7) }}</span>
                </div>
            </article>
        @endforeach
    </section>

    @if ($commits->hasPages())
        <div class="mt-5 rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
            {{ $commits->links() }}
        </div>
    @endif

    <x-slot:inspector>
        <article class="gs-panel p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Chapter Details</p>
            <h2 class="mt-2 text-xl font-semibold text-gray-900">{{ $commit->message }}</h2>

            <dl class="mt-5 space-y-3 text-sm text-gray-700">
                <div>
                    <dt class="font-semibold text-gray-900">Author</dt>
                    <dd>
                        {{ $commit->author_name ?: 'Unknown author' }}
                        @if ($commit->author_email)
                            <span class="text-gray-300">•</span> {{ $commit->author_email }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-900">Date</dt>
                    <dd>{{ optional($commit->committed_at)->format('M d, Y H:i') ?: 'Unknown time' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-900">Path</dt>
                    <dd>{{ $commit->branch ?: 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-900">Chapter ID</dt>
                    <dd class="break-all">{{ $commit->sha }}</dd>
                </div>
            </dl>

            <section class="mt-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Labels</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse ($commit->labels as $label)
                        <form method="POST" action="{{ route('story.commits.labels.detach', ['repo' => $repository, 'commit' => $commit, 'label' => $label] + array_filter($activeFilters)) }}" class="inline-flex items-center">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-loading-text="Removing..." class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold text-white" style="background-color: {{ $label->color }}">
                                {{ $label->name }}
                                <span aria-hidden="true">×</span>
                            </button>
                        </form>
                    @empty
                        <p class="text-sm text-gray-600">No labels yet.</p>
                    @endforelse
                </div>

                @if ($availableLabels->isNotEmpty())
                    <form method="POST" action="{{ route('story.commits.labels.attach', ['repo' => $repository, 'commit' => $commit] + array_filter($activeFilters)) }}" class="mt-3 flex items-center gap-2">
                        @csrf
                        <select name="label_id" class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-xs text-gray-700">
                            <option value="">+ Add label</option>
                            @foreach ($availableLabels as $label)
                                <option value="{{ $label->id }}">{{ $label->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" data-loading-text="Adding..." class="rounded-lg border border-gray-200 px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">Apply</button>
                    </form>
                @endif
            </section>

            @php
                $changeLines = collect(preg_split('/\r\n|\r|\n/', (string) $commit->message))
                    ->map(static fn (?string $line): string => trim((string) $line))
                    ->filter(static fn (string $line): bool => $line !== '')
                    ->values();
            @endphp

            <section class="mt-6">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Changes</h3>

                @if ($changeLines->isNotEmpty())
                    <ul class="mt-2 space-y-2 text-sm text-gray-700">
                        @foreach ($changeLines as $line)
                            <li class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                                {{ $line }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-2 text-sm text-gray-600">No explicit change summary was provided for this chapter.</p>
                @endif

                <p class="mt-2 text-xs text-gray-500">
                    Path context: {{ $commit->branch ?: 'N/A' }}
                </p>
            </section>

            <a href="{{ route('story.timeline', ['repo' => $repository] + array_filter($activeFilters)) }}" class="mt-6 inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">Close panel</a>
        </article>
    </x-slot:inspector>
</x-story-shell>
