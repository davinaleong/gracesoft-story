<x-auth-shell title="Create account" subtitle="Start turning commits into chapters.">
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Name
            <input type="text" name="name" value="{{ old('name') }}" required autofocus class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Email
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Password
            <input type="password" name="password" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Confirm password
            <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <button type="submit" data-loading-text="Creating account..." class="gs-btn-primary w-full">Create account</button>
    </form>

    <p class="mt-4 text-sm text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="text-sky-700 hover:text-sky-800">Sign in</a>
    </p>
</x-auth-shell>
