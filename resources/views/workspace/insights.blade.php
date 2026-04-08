<x-story-shell
    title="Insights"
    :repositories="$repositories"
    active-nav="insights"
>
    <header class="gs-panel p-5 sm:p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Insights dashboard</p>
        <h1 class="mt-2 text-2xl font-semibold text-gray-900">Understand your story at a glance</h1>
        <p class="mt-1 text-sm text-gray-600">Summaries and activity trends across your chapters and labels.</p>
    </header>

    @if ($isLoading)
        <section class="mt-4 grid gap-3">
            <div class="gs-skeleton h-20 rounded-xl"></div>
            <div class="gs-skeleton h-40 rounded-xl"></div>
            <div class="gs-skeleton h-40 rounded-xl"></div>
        </section>
    @endif

    @if (! $insightsEnabled)
        <section class="gs-panel mt-4 p-5 sm:p-6">
            <div class="gs-paywall-overlay rounded-xl border border-dashed border-sky-200 bg-sky-50/60 p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-sky-700">Locked feature preview</p>
                <h2 class="mt-2 text-xl font-semibold text-gray-900">Insights are available on paid plans</h2>
                <p class="mt-2 text-sm text-gray-700">You can still preview the dashboard layout below. Upgrade to unlock live metrics and richer insights.</p>
                <a href="{{ route('settings.index') }}" class="mt-4 inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Unlock insights</a>
            </div>
        </section>
    @endif

    <section class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3 {{ $insightsEnabled ? '' : 'gs-dimmed' }} {{ $isLoading ? 'hidden' : '' }}">
        <article class="gs-panel p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total chapters</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalChapters }}</p>
        </article>
        <article class="gs-panel p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Top label</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $topLabel?->name ?? 'N/A' }}</p>
        </article>
        <article class="gs-panel p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Weekly activity</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $weeklyChapters }}</p>
        </article>
    </section>

    <section class="mt-4 grid gap-4 lg:grid-cols-2 {{ $insightsEnabled ? '' : 'gs-dimmed' }} {{ $isLoading ? 'hidden' : '' }}">
        <article class="gs-panel p-5">
            <h2 class="text-sm font-semibold text-gray-900">Label distribution</h2>
            <div class="mt-4 space-y-2">
                @forelse ($labelChart as $row)
                    @php
                        $percent = $totalChapters > 0 ? (int) max(5, round(($row->repo_commits_count / $totalChapters) * 100)) : 5;
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs text-gray-600">
                            <span>{{ $row->name }}</span>
                            <span>{{ $row->repo_commits_count }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100">
                            <div class="h-2 rounded-full" style="width: {{ $percent }}%; background-color: {{ $row->color }}"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No label data yet.</p>
                @endforelse
            </div>
        </article>

        <article class="gs-panel p-5">
            <h2 class="text-sm font-semibold text-gray-900">Activity over time</h2>
            <div class="mt-4 flex items-end gap-3">
                @foreach ($activityChart as $bar)
                    @php
                        $height = max(12, $bar['value'] * 12);
                    @endphp
                    <div class="flex flex-1 flex-col items-center gap-1">
                        <div class="w-full rounded-t-md bg-sky-400/70" style="height: {{ $height }}px"></div>
                        <span class="text-xs text-gray-500">{{ $bar['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </article>
    </section>

    <article class="gs-panel mt-4 p-5 {{ $insightsEnabled ? '' : 'gs-dimmed' }}">
        <h2 class="text-sm font-semibold text-gray-900">Story insights</h2>
        <p class="mt-2 text-sm text-gray-700">{{ $topLabel ? 'You spent much of your recent work on '.$topLabel->name.'.' : 'No data yet. Connect your repo and add labels to unlock story insights.' }}</p>
        <p class="mt-1 text-sm text-gray-700">{{ $weeklyChapters > 0 ? 'Chapter activity increased this week with '.$weeklyChapters.' chapters committed.' : 'No chapter activity this week yet.' }}</p>
    </article>

    <x-slot:inspector>
        <div class="gs-panel p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Plan</p>
            <h2 class="mt-2 text-lg font-semibold text-gray-900">{{ $activePlan?->name ?? 'Free' }}</h2>
            <p class="mt-2 text-sm text-gray-600">Insights: {{ $insightsEnabled ? 'Unlocked' : 'Locked' }}</p>
        </div>
    </x-slot:inspector>
</x-story-shell>
