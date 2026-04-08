<x-story-shell
    title="Connect your repository"
    :repositories="$repositories"
    active-nav="connect"
>
    <section class="gs-panel gs-fade-up p-5 sm:p-6 lg:p-8">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Step 1</p>
        <h1 class="mt-2 text-2xl font-semibold text-gray-900 sm:text-3xl">Connect your repository</h1>
        <p class="mt-2 max-w-2xl text-sm text-gray-600">Start your story by connecting a repository. Once connected, each commit becomes a chapter you can explore and tag.</p>

        @guest
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Sign in</a>
                <a href="{{ route('register') }}" class="gs-btn-primary">Create account</a>
            </div>
        @endguest

        <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @auth
                <a href="{{ route('auth.github.redirect') }}" data-loading-text="Connecting..." class="gs-interactive rounded-xl border border-sky-200 bg-sky-50 px-4 py-4 shadow-sm hover:border-sky-300 hover:bg-sky-100">
                    <x-lucide-icon name="github" class="h-5 w-5 text-sky-700" />
                    <p class="text-sm font-semibold text-sky-800">GitHub</p>
                    <p class="mt-1 text-xs text-sky-700">Connect now</p>
                </a>
            @else
                <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-4 text-sm text-gray-500">
                    <x-lucide-icon name="github" class="h-5 w-5 text-gray-500" />
                    GitHub
                    <p class="mt-1 text-xs">Sign in first to connect.</p>
                </div>
            @endauth

            <button type="button" disabled class="cursor-not-allowed rounded-xl border border-gray-200 bg-white px-4 py-4 text-left text-sm text-gray-400">
                <x-lucide-icon name="git-branch" class="h-5 w-5 text-gray-400" />
                GitLab
                <p class="mt-1 text-xs">Coming soon</p>
            </button>

            <button type="button" disabled class="cursor-not-allowed rounded-xl border border-gray-200 bg-white px-4 py-4 text-left text-sm text-gray-400">
                <x-lucide-icon name="git-branch" class="h-5 w-5 text-gray-400" />
                Bitbucket
                <p class="mt-1 text-xs">Coming soon</p>
            </button>
        </div>

        <section class="mt-6 rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">GitHub Account</p>
            @auth
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $hasGitHubAccount ? 'Connected to GitHub' : 'Not connected yet' }}</p>
                        <p class="text-xs text-gray-500">{{ $hasGitHubAccount ? 'Your repositories can sync into chapters.' : 'Connect to start syncing repositories and chapters.' }}</p>
                    </div>

                    @if ($hasGitHubAccount)
                        <form method="POST" action="{{ route('auth.github.disconnect') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-loading-text="Disconnecting..." class="inline-flex items-center rounded-lg border border-rose-200 px-4 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50">Disconnect GitHub</button>
                        </form>
                    @else
                        <a href="{{ route('auth.github.redirect') }}" data-loading-text="Connecting..." class="gs-btn-primary">Connect GitHub</a>
                    @endif
                </div>
            @else
                <p class="mt-3 text-sm text-gray-600">Sign in to connect your GitHub account.</p>
            @endauth
        </section>

        @if ($hasGitHubAccount && $repositories->isNotEmpty())
            <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-900">GitHub connected</p>
                <p class="mt-1 text-sm text-gray-600">You can jump to your first repository timeline now.</p>
                <a href="{{ route('story.timeline', $repositories->first()) }}" class="gs-btn-primary mt-3">Open timeline</a>
            </div>
        @endif
    </section>

    <x-slot:inspector>
        <div class="gs-panel p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Connection Status</p>
            <h2 class="mt-2 text-lg font-semibold text-gray-900">Provider Setup</h2>
            <ul class="mt-4 space-y-3 text-sm text-gray-600">
                <li class="flex items-center gap-2 rounded-md bg-gray-50 px-3 py-2">
                    <x-lucide-icon name="github" class="h-4 w-4 text-gray-500" />
                    GitHub: {{ $hasGitHubAccount ? 'Connected' : 'Not connected' }}
                </li>
                <li class="flex items-center gap-2 rounded-md bg-gray-50 px-3 py-2">
                    <x-lucide-icon name="git-branch" class="h-4 w-4 text-gray-400" />
                    GitLab: Coming soon
                </li>
                <li class="flex items-center gap-2 rounded-md bg-gray-50 px-3 py-2">
                    <x-lucide-icon name="git-branch" class="h-4 w-4 text-gray-400" />
                    Bitbucket: Coming soon
                </li>
            </ul>
        </div>
    </x-slot:inspector>
</x-story-shell>
