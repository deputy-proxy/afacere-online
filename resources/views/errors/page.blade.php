<x-layouts.public :title="$title">
    <div class="mx-auto flex min-h-[60vh] max-w-2xl flex-col items-center justify-center px-6 py-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-wider text-zinc-500">{{ $status }}</p>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ $heading }}</h1>
        <p class="mt-3 max-w-xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900">Go home</a>
            <button type="button" onclick="history.back();" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-900">Go back</button>
        </div>
    </div>
</x-layouts.public>
