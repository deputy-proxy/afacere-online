<x-ui.page>
    <div class="space-y-8">
        <x-ui.page-header
            title="Opportunities"
            description="Current opportunities matched against your business using explicit eligibility rules."
        >
            <x-slot name="actions">
                <flux:button href="{{ route('dashboard') }}" variant="ghost" wire:navigate>
                    Back to dashboard
                </flux:button>
            </x-slot>
        </x-ui.page-header>

        <div wire:loading class="space-y-3" aria-live="polite" aria-busy="true">
            <x-ui:loading label="Loading opportunities" />
        </div>

        <div wire:loading.remove>
            @if ($this->matches->isEmpty())
                <x-ui.empty-state
                    title="No current matches"
                    description="There are no published, current opportunities with a positive match against your business profile right now."
                />
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($this->matches as $match)
                        @php
                            $opportunity = $match->opportunity;
                            $criteria = is_array($opportunity->criteria) ? $opportunity->criteria : [];
                            $results = is_array($match->criteria_results) ? $match->criteria_results : [];
                            $passed = collect($results)->where('passed', true)->count();
                            $total = count($criteria);
                        @endphp
                        <article wire:key="opportunity-match-{{ $opportunity->id }}" class="flex h-full flex-col rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ $opportunity->type?->name ?? 'Opportunity' }}</p>
                                    <h2 class="mt-1 text-lg font-semibold text-zinc-900 dark:text-white">{{ $opportunity->title }}</h2>
                                </div>
                                <span class="shrink-0 text-xs font-medium">{{ (int) round((float) $match->score * 100) }}% match</span>
                            </div>

                            @if ($opportunity->description)
                                <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $opportunity->description }}</p>
                            @endif

                            <div class="mt-4 text-sm text-zinc-600 dark:text-zinc-300">
                                @if ($total > 0)
                                    <p>{{ $passed }} of {{ $total }} eligibility {{ $total === 1 ? 'criterion' : 'criteria' }} met.</p>
                                @else
                                    <p>No additional eligibility criteria are configured.</p>
                                @endif
                                @if ($opportunity->valid_until)
                                    <p class="mt-1 text-xs text-zinc-500">Current until {{ $opportunity->valid_until->format('d M Y') }}</p>
                                @endif
                            </div>

                            <div class="mt-auto pt-5">
                                <flux:button href="{{ route('business.opportunities.show', $opportunity->id) }}" variant="primary" size="sm" wire:navigate>
                                    View opportunity
                                </flux:button>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-ui.page>
