<x-auth-shell title="Forgot password" subtitle="We will send a reset link to your email.">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <button type="submit" data-loading-text="Sending link..." class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Email reset link</button>
    </form>

    <p class="mt-4 text-sm text-gray-600">
        Remembered your password?
        <a href="{{ route('login') }}" class="text-sky-700 hover:text-sky-800">Back to sign in</a>
    </p>
</x-auth-shell>
