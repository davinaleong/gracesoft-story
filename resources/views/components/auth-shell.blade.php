@props([
    'title' => 'Welcome',
    'subtitle' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - GraceSoft Story</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="gs-page">
    <main class="mx-auto flex min-h-screen w-full max-w-6xl items-center justify-center p-4 sm:p-6">
        <section class="grid w-full max-w-4xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm md:grid-cols-[1.1fr_minmax(0,1fr)]">
            <aside class="hidden bg-gray-900 p-8 text-white md:block">
                <img src="{{ asset('logo-w.svg') }}" alt="GraceSoft Story" class="h-10 w-10 rounded-lg bg-white/10 p-1">
                <h1 class="mt-6 text-3xl font-semibold leading-tight">Write your product story from every chapter.</h1>
                <p class="mt-3 text-sm text-gray-200">Connect your repository, label your work, and keep a clean narrative of progress.</p>
            </aside>

            <div class="p-6 sm:p-8">
                <div class="mb-6">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">GraceSoft Story</p>
                    <h2 class="mt-2 text-2xl font-semibold text-gray-900">{{ $title }}</h2>
                    @if ($subtitle)
                        <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
                    @endif
                </div>

                @if (session('status'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </section>
    </main>
</body>
</html>
