<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chapter {{ substr($commit->sha, 0, 7) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('story.timeline', $repository) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">&larr; Back to timeline</a>

        <article class="mt-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Chapter Details</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $commit->message }}</h1>

            <dl class="mt-5 grid gap-3 text-sm text-slate-700">
                <div>
                    <dt class="font-semibold text-slate-900">SHA</dt>
                    <dd>{{ $commit->sha }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Author</dt>
                    <dd>{{ $commit->author_name ?: 'Unknown author' }} @if($commit->author_email)<span class="text-slate-400">&middot;</span> {{ $commit->author_email }}@endif</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Committed At</dt>
                    <dd>{{ optional($commit->committed_at)->format('M d, Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-900">Branch</dt>
                    <dd>{{ $commit->branch ?: 'N/A' }}</dd>
                </div>
            </dl>

            <section class="mt-6">
                <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500">Labels</h2>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse ($commit->labels as $label)
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white" style="background-color: {{ $label->color }}">
                            {{ $label->name }}
                        </span>
                    @empty
                        <p class="text-sm text-slate-600">No labels yet.</p>
                    @endforelse
                </div>
            </section>
        </article>
    </main>
</body>
</html>
