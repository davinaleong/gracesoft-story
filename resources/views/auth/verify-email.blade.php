<x-auth-shell title="Verify email" subtitle="Please verify your email before continuing.">
    <p class="text-sm text-gray-600">A verification link has been sent to your email address.</p>

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
        @csrf
        <button type="submit" data-loading-text="Sending..." class="gs-btn-primary w-full">Resend verification email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Sign out</button>
    </form>
</x-auth-shell>
