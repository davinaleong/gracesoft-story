<x-story-shell
    title="Label management"
    :repositories="$repositories"
    active-nav="labels"
>
    <header class="gs-panel p-5 sm:p-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Labels</p>
        <h1 class="mt-2 text-2xl font-semibold text-gray-900">Label management</h1>
        <p class="mt-1 text-sm text-gray-600">Create, edit, and organize labels that turn chapters into themes.</p>
    </header>

    <section class="mt-4 grid gap-4 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <div class="gs-notes-list">
            @if ($isLoading)
                <div class="space-y-3 p-4">
                    <div class="gs-skeleton h-12 rounded-lg"></div>
                    <div class="gs-skeleton h-12 rounded-lg"></div>
                    <div class="gs-skeleton h-12 rounded-lg"></div>
                </div>
            @else
                @forelse ($labels as $label)
                    <article class="gs-note-row">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold text-white" style="background-color: {{ $label->color }}">
                                    {{ $label->name }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $label->commits_count }} chapters</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('labels.update', $label) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="name" value="{{ $label->name }}" class="w-28 rounded-lg border border-gray-200 px-2 py-1 text-xs text-gray-800">
                                    <input type="color" name="color" value="{{ $label->color }}" class="h-7 w-10 cursor-pointer rounded border border-gray-200 bg-white p-1">
                                    <button type="submit" data-loading-text="Saving..." class="rounded-lg border border-gray-200 px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50">Save</button>
                                </form>
                                <form method="POST" action="{{ route('labels.destroy', $label) }}" onsubmit="return confirm('Delete this label?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" data-loading-text="Deleting..." class="rounded-lg border border-rose-200 px-2 py-1 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="p-8 text-center text-sm text-gray-600">Add labels to understand your work.</div>
                @endforelse
            @endif
        </div>

        <aside class="gs-panel p-5">
            <h2 class="text-sm font-semibold text-gray-900">Create new label</h2>
            <form method="POST" action="{{ route('labels.store') }}" class="mt-3 space-y-3">
                @csrf
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Name
                    <input type="text" name="name" required maxlength="50" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-800" placeholder="Feature">
                </label>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Color
                    <input type="color" name="color" value="#0ea5e9" class="mt-1 h-10 w-full cursor-pointer rounded-lg border border-gray-200 bg-white px-2 py-1">
                </label>
                <button type="submit" data-loading-text="Creating..." class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Create label</button>
            </form>
        </aside>
    </section>

    <x-slot:inspector>
        <div class="gs-panel p-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Inline Tagging</p>
            <h2 class="mt-2 text-lg font-semibold text-gray-900">Fast and optional</h2>
            <p class="mt-2 text-sm text-gray-600">Labels are optional, instant, and designed for minimal clicks while browsing chapters.</p>
        </div>
    </x-slot:inspector>
</x-story-shell>
