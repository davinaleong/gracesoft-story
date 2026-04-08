<x-auth-shell title="Reset password" subtitle="Choose a new password for your account.">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Email
            <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            New password
            <input type="password" name="password" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Confirm password
            <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <button type="submit" data-loading-text="Resetting..." class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Reset password</button>
    </form>
</x-auth-shell>
