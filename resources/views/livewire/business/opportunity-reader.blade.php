<x-ui.page>
    <div class="mx-auto max-w-4xl space-y-6">
        <a href="{{ route('business.opportunities') }}" wire:navigate class="text-sm font-medium text-zinc-600 underline-offset-4 hover:underline dark:text-zinc-300">
            ← All opportunities
        </a>

        <x-ui.page-header
            :title="$this->opportunity->title"
            :description="$this->opportunity->description"
        >
            <x-slot name="actions">
                <span class="text-sm text-zinc-500">{{ $this->opportunity->type?->name ?? 'Opportunity' }}</span>
            </x-slot>
        </x-ui.page-header>

        @if ($errors->has('opportunity'))
            <x-ui.alert variant="danger">{{ $errors->first('opportunity') }}</x-ui.alert>
        @endif

        <article class="space-y-6 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6 dark:border-zinc-700 dark:bg-zinc-900">
            <section aria-labelledby="eligibility-heading">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 id="eligibility-heading" class="text-lg font-semibold">Why this matches your business</h2>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">This is a deterministic match against the opportunity's configured criteria, not a guarantee of eligibility or funding.</p>
                    </div>
                    <span class="text-sm font-medium">{{ (int) round((float) $this->match->score * 100) }}% match</span>
                </div>

                @php
                    $criteriaResults = is_array($this->match->criteria_results) ? $this->match->criteria_results : [];
                @endphp
                @if ($criteriaResults === [])
                    <x-ui.alert variant="info" class="mt-4">No additional eligibility criteria are configured for this opportunity.</x-ui.alert>
                @else
                    <div class="mt-4 divide-y divide-zinc-100 rounded-lg border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-700">
                        @foreach ($criteriaResults as $key => $result)
                            @php $passed = is_array($result) && ($result['passed'] ?? false); @endphp
                            <div wire:key="criterion-{{ $key }}" class="flex flex-col gap-1 p-4 sm:flex-row sm:items-start sm:justify-between sm:gap-6">
                                <div class="min-w-0">
                                    <h3 class="font-medium">{{ str($key)->headline() }}</h3>
                                    @if (is_array($result) && array_key_exists('expected', $result))
                                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Configured rule: {{ json_encode($result['expected']) }}</p>
                                    @endif
                                </div>
                                <span class="shrink-0 text-sm font-medium">{{ $passed ? 'Criterion met' : 'Criterion not met' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section aria-labelledby="availability-heading" class="border-t border-zinc-100 pt-6 dark:border-zinc-800">
                <h2 id="availability-heading" class="text-lg font-semibold">Availability and next step</h2>
                <div class="mt-3 grid gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                    @if ($this->opportunity->valid_from)
                        <p>Available from {{ $this->opportunity->valid_from->format('d M Y') }}.</p>
                    @endif
                    @if ($this->opportunity->valid_until)
                        <p>Current until {{ $this->opportunity->valid_until->format('d M Y') }}.</p>
                    @endif
                </div>

                @if ($this->application)
                    <x-ui.alert variant="success" class="mt-4">
                        Application tracking started{{ $this->application->submitted_at ? ' on '.$this->application->submitted_at->format('d M Y H:i') : '' }}.
                    </x-ui.alert>
                @elseif ((float) $this->match->score === 1.0)
                    <flux:button wire:click="apply" wire:loading.attr="disabled" wire:target="apply" class="mt-5">
                        <span wire:loading.remove wire:target="apply">Track application</span>
                        <span wire:loading wire:target="apply">Saving…</span>
                    </flux:button>
                @else
                    <x-ui.alert variant="warning" class="mt-4">One or more configured eligibility criteria are not met, so application tracking is unavailable.</x-ui.alert>
                @endif
            </section>
        </article>
    </div>
</x-ui.page>
