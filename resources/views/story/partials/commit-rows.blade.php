@forelse ($commits as $commit)
    <article class="gs-note-row gs-interactive gs-fade-up" style="animation-delay: {{ min($loop->index * 35, 260) }}ms;" data-commit-row>
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
                <div class="mt-2 flex flex-wrap gap-2" data-label-chips>
                    @foreach ($commit->labels as $label)
                        <form method="POST" action="{{ route('story.commits.labels.detach', ['repo' => $repository, 'commit' => $commit, 'label' => $label] + array_filter($activeFilters)) }}" class="inline-flex items-center" data-optimistic-detach>
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-loading-text="Removing..." class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold text-white" style="background-color: {{ $label->color }}">
                                {{ $label->name }}
                                <span aria-hidden="true">×</span>
                            </button>
                        </form>
                    @endforeach
                </div>

                @if ($availableLabels->isNotEmpty())
                    <form method="POST" action="{{ route('story.commits.labels.attach', ['repo' => $repository, 'commit' => $commit] + array_filter($activeFilters)) }}" class="mt-2 flex items-center gap-2" data-optimistic-attach>
                        @csrf
                        <select name="label_id" class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-xs text-gray-700">
                            <option value="">+ Add label</option>
                            @foreach ($availableLabels as $label)
                                <option value="{{ $label->id }}" data-label-color="{{ $label->color }}">{{ $label->name }}</option>
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
