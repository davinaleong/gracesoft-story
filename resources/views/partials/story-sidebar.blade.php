@php
    $repoItems = $repositories ?? collect();
    $isTimelineDisabled = $repoItems->isEmpty();
@endphp

<aside class="gs-sidebar">
    <div class="gs-panel flex items-center gap-3 px-3 py-2">
        <img src="{{ asset('logo.svg') }}" alt="GraceSoft Story" class="h-8 w-8 rounded-md border border-sky-100 bg-sky-50 p-1">
        <div>
            <p class="text-sm font-semibold text-gray-900">GraceSoft Story</p>
            <p class="text-xs text-gray-500">Your development chapters</p>
        </div>
    </div>

    <nav class="mt-4 grid grid-cols-2 gap-2 lg:grid-cols-1 lg:gap-1">
        <a href="/" class="gs-interactive flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm {{ $activeNav === 'connect' ? 'bg-sky-50 font-medium text-sky-700' : 'text-gray-700 hover:text-gray-900' }}">
            <span class="text-sky-600">+</span>Connect
        </a>

        @if ($isTimelineDisabled)
            <span class="flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400">Timeline</span>
        @else
            <a href="{{ route('story.timeline', $currentRepository ?? $repoItems->first()) }}" class="gs-interactive flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm {{ in_array($activeNav, ['timeline', 'chapter'], true) ? 'bg-sky-50 font-medium text-sky-700' : 'text-gray-700 hover:text-gray-900' }}">
                <span>●</span>Timeline
            </a>
        @endif

        <span class="col-span-2 flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 lg:col-span-1">Insights (Paid)</span>
        <span class="col-span-2 flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-400 lg:col-span-1">Settings</span>
    </nav>

    <section class="mt-5">
        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Repositories</p>
        <div class="gs-repo-list max-h-64 space-y-1 overflow-auto rounded-lg border border-gray-200 bg-white p-2 lg:max-h-[65vh]">
            @forelse ($repoItems as $repoItem)
                <a
                    href="{{ route('story.timeline', $repoItem) }}"
                    class="gs-interactive block rounded-md px-2 py-2 text-sm {{ isset($currentRepository) && $currentRepository && $repoItem->is($currentRepository) ? 'bg-gray-900 text-white hover:bg-gray-900' : 'text-gray-700' }}"
                >
                    {{ $repoItem->full_name ?: $repoItem->name }}
                </a>
            @empty
                <p class="px-2 py-4 text-sm text-gray-500">No repositories yet.</p>
            @endforelse
        </div>
    </section>
</aside>