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