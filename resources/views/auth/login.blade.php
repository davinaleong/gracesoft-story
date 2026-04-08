<x-auth-shell title="Sign in" subtitle="Welcome back. Continue your story.">
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Password
            <input type="password" name="password" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-sky-600">
            Remember me
        </label>

        <button type="submit" data-loading-text="Signing in..." class="gs-btn-primary w-full">Sign in</button>
    </form>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-2 text-sm">
        <a href="{{ route('password.request') }}" class="text-sky-700 hover:text-sky-800">Forgot password?</a>
        <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900">Create account</a>
    </div>
</x-auth-shell>
