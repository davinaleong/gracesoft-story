@props([
    'title' => 'GraceSoft Story',
    'repositories' => collect(),
    'currentRepository' => null,
    'activeNav' => 'connect',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gs-page">
    <div class="gs-shell">
        @include('partials.story-sidebar', [
            'repositories' => $repositories,
            'currentRepository' => $currentRepository,
            'activeNav' => $activeNav,
        ])

        <main class="gs-main">
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                    <p>{{ session('error') }}</p>
                    <a href="{{ url()->current() }}" class="mt-2 inline-flex items-center rounded-md border border-rose-300 bg-white px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50">Retry</a>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                    <p class="font-semibold">Please fix the highlighted input and retry.</p>
                    <ul class="mt-1 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <aside class="gs-inspector">
            @isset($inspector)
                {{ $inspector }}
            @endisset
        </aside>
    </div>
</body>
</html>