<x-auth-shell title="Confirm password" subtitle="Please confirm your password before continuing.">
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
            Password
            <input type="password" name="password" required autofocus class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800">
        </label>

        <button type="submit" data-loading-text="Confirming..." class="w-full rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Confirm password</button>
    </form>
</x-auth-shell>
