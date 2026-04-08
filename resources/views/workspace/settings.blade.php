<x-story-shell
    title="Settings"
    :repositories="$repositories"
    active-nav="settings"
>
    <header class="gs-panel p-5 sm:p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Settings</p>
        <h1 class="mt-2 text-2xl font-semibold text-gray-900">Workspace preferences</h1>
        <p class="mt-1 text-sm text-gray-600">Manage connected accounts, repository sync, and subscription details.</p>
    </header>

    @if ($isLoading)
        <section class="mt-4 grid gap-3">
            <div class="gs-skeleton h-28 rounded-xl"></div>
            <div class="gs-skeleton h-28 rounded-xl"></div>
            <div class="gs-skeleton h-24 rounded-xl"></div>
        </section>
    @endif

    <section class="mt-4 grid gap-4 lg:grid-cols-2 {{ $isLoading ? 'hidden' : '' }}">
        <article class="gs-panel p-5">
            <h2 class="text-sm font-semibold text-gray-900">Connected accounts</h2>
            <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-3">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">GitHub</p>
                        <p class="text-xs text-gray-500">{{ $githubAccount ? 'Connected' : 'Not connected' }}</p>
                    </div>
                    @if ($githubAccount)
                        <form method="POST" action="{{ route('auth.github.disconnect') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-loading-text="Disconnecting..." class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Disconnect</button>
                        </form>
                    @else
                        <a href="{{ route('auth.github.redirect') }}" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100">Connect</a>
                    @endif
                </div>
            </div>
        </article>

        <article class="gs-panel p-5">
            <h2 class="text-sm font-semibold text-gray-900">Repository sync</h2>
            <p class="mt-2 text-sm text-gray-600">Trigger a manual sync to fetch newest chapters from your providers.</p>
            <form method="POST" action="{{ route('sync.github.refresh') }}" class="mt-3">
                @csrf
                <button type="submit" data-loading-text="Queueing sync..." class="gs-btn-primary">Sync now</button>
            </form>
            <div class="mt-4 space-y-2 text-xs text-gray-500">
                @forelse ($repositories->take(5) as $repo)
                    <p>{{ $repo->full_name ?: $repo->name }}: {{ optional($repo->last_synced_at)->diffForHumans() ?? 'Never synced' }}</p>
                @empty
                    <p>No repositories connected yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="mt-4 {{ $isLoading ? 'hidden' : '' }}">
        <article class="gs-panel p-5">
            <h2 class="text-sm font-semibold text-gray-900">Subscription and billing</h2>
            <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Plan</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activePlan?->name ?? 'Free' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Timelines</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activePlan?->max_timelines ?? 'N/A' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Storage</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activePlan?->storage_mb ? $activePlan->storage_mb.' MB' : 'N/A' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-3">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Insights</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $activePlan?->can_use_insights ? 'Included' : 'Locked' }}</p>
                </div>
            </div>
        </article>
    </section>

    <x-slot:inspector>
        <div class="gs-panel p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Quick notes</p>
            <h2 class="mt-2 text-lg font-semibold text-gray-900">Account health</h2>
            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                <li>Connected providers: {{ $githubAccount ? 1 : 0 }}</li>
                <li>Repositories tracked: {{ $repositories->count() }}</li>
                <li>Plan: {{ $activePlan?->name ?? 'Free' }}</li>
            </ul>
        </div>
    </x-slot:inspector>
</x-story-shell>
