<div class="pointer-events-none fixed inset-x-0 top-0 z-[100]" aria-live="polite" aria-atomic="true">
    <div wire:loading class="h-1 w-full bg-zinc-900 dark:bg-white"></div>
    <div class="mx-auto mt-3 flex max-w-3xl justify-center px-4">
        <div wire:loading.flex class="pointer-events-auto items-center gap-2 rounded-full border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-lg dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200">
            <flux:icon name="arrow-path" class="size-4 animate-spin" aria-hidden="true" />
            <span>Working…</span>
        </div>
    </div>

    <div id="livewire-request-error" class="pointer-events-auto mx-auto mt-3 hidden max-w-3xl px-4">
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-lg dark:border-red-900/60 dark:bg-red-950/50 dark:text-red-200" role="alert">
            <flux:icon name="exclamation-triangle" class="mt-0.5 size-5 shrink-0" aria-hidden="true" />
            <div class="min-w-0 flex-1">
                <p class="font-semibold" data-livewire-error-heading>Something went wrong</p>
                <p class="mt-1" data-livewire-error-message>We could not complete that action. No changes were applied.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <flux:button size="sm" variant="primary" type="button" onclick="window.location.reload()">Reload</flux:button>
                    <button type="button" class="rounded-lg px-3 py-1.5 text-sm font-medium hover:bg-red-100 dark:hover:bg-red-900/40" onclick="this.closest('#livewire-request-error').classList.add('hidden')">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
</div>
