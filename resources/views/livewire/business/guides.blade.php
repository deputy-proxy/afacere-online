<x-ui.page>
    <div class="space-y-8">
        <x-ui.page-header
            title="Guides"
            description="Practical tools to help you understand your priorities and turn them into action."
        >
            <x-slot name="actions">
                <flux:button href="{{ route('dashboard') }}" variant="ghost" wire:navigate>
                    Back to dashboard
                </flux:button>
            </x-slot>
        </x-ui.page-header>

        <div wire:loading class="space-y-3" aria-live="polite" aria-busy="true">
            <x-ui.loading label="Loading guides" />
        </div>

        <div wire:loading.remove>
            @if ($this->guides->isEmpty())
                <x-ui.empty-state
                    title="No guides available"
                    description="Published guides will appear here when they are relevant to your business or generally available."
                />
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($this->guides as $guide)
                        <article wire:key="guide-{{ $guide->id }}" class="flex h-full flex-col rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Guide</p>
                                    <h2 class="mt-1 text-lg font-semibold text-zinc-900 dark:text-white">{{ $guide->title }}</h2>
                                </div>
                                <span class="shrink-0 text-xs text-zinc-500">v{{ $guide->version }}</span>
                            </div>

                            @if ($guide->description)
                                <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $guide->description }}</p>
                            @endif

                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                                <span class="text-xs text-zinc-500">{{ $guide->sections->count() }} {{ $guide->sections->count() === 1 ? 'section' : 'sections' }}</span>
                                <flux:button href="{{ route('business.guides.show', $guide->slug) }}" variant="primary" size="sm" wire:navigate>
                                    Read guide
                                </flux:button>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-ui.page>
