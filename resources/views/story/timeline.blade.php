<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $repository->full_name ?: $repository->name }} Story</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <header class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Story Timeline</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $repository->full_name ?: $repository->name }}</h1>
            <p class="mt-1 text-sm text-slate-600">Commits are shown newest first to highlight your latest development chapters.</p>
        </header>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('status') }}
            </div>
        @endif

        <section class="mt-6 space-y-4">
            @forelse ($commits as $commit)
                <article class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-base font-semibold leading-tight text-slate-900">{{ $commit->message }}</p>
                            <p class="mt-2 text-sm text-slate-600">
                                {{ $commit->author_name ?: 'Unknown author' }}
                                @if ($commit->author_email)
                                    <span class="text-slate-400">&middot;</span>
                                    {{ $commit->author_email }}
                                @endif
                            </p>
                        </div>
                        <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-medium tracking-wide text-white">{{ substr($commit->sha, 0, 7) }}</span>
                    </div>
                    <div class="mt-3 text-xs uppercase tracking-wide text-slate-500">
                        {{ optional($commit->committed_at)->format('M d, Y H:i') }}
                        @if ($commit->branch)
                            <span class="text-slate-400">&middot;</span>
                            {{ $commit->branch }}
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <p class="text-sm text-slate-600">No story chapters yet for this repository. Run a sync to start building your timeline.</p>
                </div>
            @endforelse
        </section>

        @if ($commits->hasPages())
            <div class="mt-6 rounded-2xl bg-white px-4 py-3 shadow-sm ring-1 ring-slate-200">
                {{ $commits->links() }}
            </div>
        @endif
    </main>
</body>
</html>
