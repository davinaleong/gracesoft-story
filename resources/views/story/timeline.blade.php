<x-story-shell
    :title="($repository->full_name ?: $repository->name) . ' Story'"
    :repositories="$repositories"
    :current-repository="$repository"
    active-nav="timeline"
>
    <header class="gs-panel gs-fade-up p-5 sm:p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Story Timeline</p>
        <h1 class="mt-2 text-2xl font-semibold text-gray-900">{{ $repository->full_name ?: $repository->name }}</h1>
        <p class="mt-1 text-sm text-gray-600">Each chapter captures what was built, when it happened, and who contributed.</p>
    </header>

    <section class="gs-panel mt-4 p-4 sm:p-5">
        <form method="GET" action="{{ route('story.timeline', $repository) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <label class="flex flex-col gap-1 text-xs font-semibold uppercase tracking-wide text-gray-500">
                Author
                <input
                    type="text"
                    name="author"
                    value="{{ $activeFilters['author'] }}"
                    placeholder="Name or email"
                    list="author-suggestions"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal text-gray-800 outline-none ring-sky-200 transition focus:border-sky-300 focus:ring"
                >
                <datalist id="author-suggestions">
                    @foreach ($availableAuthors as $authorName)
                        <option value="{{ $authorName }}"></option>
                    @endforeach
                </datalist>
            </label>

            <label class="flex flex-col gap-1 text-xs font-semibold uppercase tracking-wide text-gray-500">
                Label
                <select name="label_id" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal text-gray-800 outline-none ring-sky-200 transition focus:border-sky-300 focus:ring">
                    <option value="">All labels</option>
                    @foreach ($availableLabels as $label)
                        <option value="{{ $label->id }}" @selected((string) $label->id === $activeFilters['label_id'])>
                            {{ $label->name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="flex flex-col gap-1 text-xs font-semibold uppercase tracking-wide text-gray-500">
                From
                <input
                    type="date"
                    name="from"
                    value="{{ $activeFilters['from'] }}"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal text-gray-800 outline-none ring-sky-200 transition focus:border-sky-300 focus:ring"
                >
            </label>

            <label class="flex flex-col gap-1 text-xs font-semibold uppercase tracking-wide text-gray-500">
                To
                <input
                    type="date"
                    name="to"
                    value="{{ $activeFilters['to'] }}"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal text-gray-800 outline-none ring-sky-200 transition focus:border-sky-300 focus:ring"
                >
            </label>

            <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-4">
                <button type="submit" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Apply filters</button>
                <a href="{{ route('story.timeline', $repository) }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Clear</a>
            </div>
        </form>
    </section>

    @if ($isLoading)
        <section class="mt-4 grid gap-3">
            <div class="gs-skeleton h-20 rounded-xl"></div>
            <div class="gs-skeleton h-20 rounded-xl"></div>
            <div class="gs-skeleton h-20 rounded-xl"></div>
        </section>
    @endif

    @if ($commits->count() > 0 && $availableLabels->isNotEmpty() && ! $isLoading)
        <form id="bulk-label-form" method="POST" action="{{ route('story.labels.bulk-apply', $repository) }}" class="mt-4 flex flex-wrap items-center gap-2">
            @csrf
            <select name="label_id" required class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
                <option value="">Label selected chapters</option>
                @foreach ($availableLabels as $label)
                    <option value="{{ $label->id }}">{{ $label->name }}</option>
                @endforeach
            </select>
            <button type="submit" data-loading-text="Applying..." class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Apply to selected</button>
        </form>
    @endif

    <section class="gs-notes-list mt-4 sm:mt-5 {{ $isLoading ? 'hidden' : '' }}">
        @forelse ($commits as $commit)
            <article class="gs-note-row gs-interactive gs-fade-up" style="animation-delay: {{ min($loop->index * 35, 260) }}ms;">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        @if ($availableLabels->isNotEmpty())
                            <label class="mb-2 inline-flex items-center gap-2 text-xs text-gray-500">
                                <input form="bulk-label-form" type="checkbox" name="commit_ids[]" value="{{ $commit->id }}" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                                Select chapter
                            </label>
                        @endif
                        <a href="{{ route('story.chapter', ['repo' => $repository, 'commit' => $commit] + array_filter($activeFilters)) }}" class="text-base font-semibold leading-tight text-gray-900 hover:text-sky-700">
                            {{ $commit->message }}
                        </a>
                        <p class="gs-note-meta mt-1">
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
                                    <form method="POST" action="{{ route('story.commits.labels.detach', ['repo' => $repository, 'commit' => $commit, 'label' => $label] + array_filter($activeFilters)) }}" class="inline-flex items-center">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" data-loading-text="Removing..." class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold text-white" style="background-color: {{ $label->color }}">
                                            {{ $label->name }}
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @endif

                        @if ($availableLabels->isNotEmpty())
                            <form method="POST" action="{{ route('story.commits.labels.attach', ['repo' => $repository, 'commit' => $commit] + array_filter($activeFilters)) }}" class="mt-2 flex items-center gap-2">
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
                    </div>
                    <span class="hidden rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700 sm:inline-flex">{{ substr($commit->sha, 0, 7) }}</span>
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

    <x-slot:inspector>
        <div class="gs-panel p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Chapter Details</p>
            <h2 class="mt-2 text-lg font-semibold text-gray-900">Select a Chapter</h2>
            <p class="mt-2 text-sm text-gray-600">Choose any chapter from the timeline to inspect full author details, labels, and chapter metadata.</p>
            <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-500">
                Tip: labels help turn raw history into themes like Feature, Fix, or Refactor.
            </div>
        </div>
    </x-slot:inspector>
</x-story-shell>
