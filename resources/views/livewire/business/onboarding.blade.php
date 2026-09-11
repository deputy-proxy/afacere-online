<div class="mx-auto flex w-full max-w-2xl flex-col gap-8 py-8">
    <div class="space-y-2">
        <p class="text-sm font-medium text-zinc-500">{{ __('Welcome to afacere.online') }}</p>
        <h1 class="text-3xl font-semibold tracking-tight">{{ __('Let’s start with your business') }}</h1>
        <p class="text-zinc-600 dark:text-zinc-400">
            {{ __('Create your business workspace. We will use it to keep your evaluation, priorities and progress in one place.') }}
        </p>
    </div>

    <form wire:submit="createBusiness" class="flex flex-col gap-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex flex-col gap-2">
            <label for="business-name" class="text-sm font-medium">{{ __('Business name') }}</label>
            <input id="business-name" wire:model="name" type="text" required autofocus autocomplete="organization" class="w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-600 dark:bg-zinc-800" />
            @error('name')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="business-description" class="text-sm font-medium">{{ __('What does the business do?') }}</label>
            <textarea id="business-description" wire:model="description" rows="4" class="w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-600 dark:bg-zinc-800"></textarea>
            @error('description')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white disabled:opacity-50 dark:bg-white dark:text-zinc-900" wire:loading.attr="disabled">
                {{ __('Create business') }}
            </button>
        </div>
    </form>
</div>
