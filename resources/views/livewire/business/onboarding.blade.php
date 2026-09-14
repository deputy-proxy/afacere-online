<div class="mx-auto flex w-full max-w-2xl flex-col gap-8">
    <header class="space-y-3">
        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Welcome to afacere.online') }}</p>
        <h1 class="text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('Let’s start with your business') }}</h1>
        <p class="max-w-xl text-base leading-7 text-zinc-600 dark:text-zinc-300">
            {{ __('Create your business workspace. We will use it to keep your evaluation, priorities and progress in one place.') }}
        </p>
    </header>

    <section aria-labelledby="business-setup-heading" class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mb-6 space-y-1">
            <h2 id="business-setup-heading" class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('Business details') }}</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('You can refine these details later.') }}</p>
        </div>

        <form wire:submit="createBusiness" class="flex flex-col gap-6" aria-describedby="business-setup-help">
            <p id="business-setup-help" class="sr-only">{{ __('Fields marked as required must be completed before the business can be created.') }}</p>

            <flux:input
                name="name"
                wire:model="name"
                :label="__('Business name')"
                type="text"
                required
                autofocus
                autocomplete="organization"
                :placeholder="__('e.g. Acme SRL')"
            />

            <flux:textarea
                name="description"
                wire:model="description"
                :label="__('What does the business do?')"
                rows="5"
                :placeholder="__('A short description of your business')"
            />

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Your workspace is created securely on the server.') }}</p>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="createBusiness" data-test="create-business-button">
                    <span wire:loading.remove wire:target="createBusiness">{{ __('Create business') }}</span>
                    <span wire:loading wire:target="createBusiness">{{ __('Creating…') }}</span>
                </flux:button>
            </div>
        </form>
    </section>
</div>
