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
                <button type="submit" class="gs-btn-primary">Apply filters</button>
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

    <section id="timeline-list" class="gs-notes-list mt-4 sm:mt-5 {{ $isLoading ? 'hidden' : '' }}">
        @include('story.partials.commit-rows', [
            'commits' => $commits,
            'availableLabels' => $availableLabels,
            'repository' => $repository,
            'activeFilters' => $activeFilters,
        ])
    </section>

    @if ($commits->hasPages())
        <div
            id="timeline-lazy-loader"
            class="mt-5"
            data-next-url="{{ $commits->nextPageUrl() }}"
            data-fragment-param="fragment"
            data-target="#timeline-list"
        >
            <button type="button" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" data-load-more-button>
                Load more chapters
            </button>
            <div class="mt-3 hidden text-xs text-gray-500" data-load-more-status></div>
            <div class="h-2" data-load-more-sentinel></div>
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
