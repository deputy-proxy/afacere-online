<x-ui.page>
    @if ($this->business)
        <x-ui.page-header
            :title="$this->business->name"
            :description="__('What should I do next?')"
        >
            <x-slot:actions>
                @if (count($this->businesses) > 1)
                    <flux:field>
                        <flux:label for="business-switcher" class="sr-only">{{ __('Business') }}</flux:label>
                        <flux:select id="business-switcher" wire:model.live="businessId" wire:change="switchBusiness">
                            @foreach ($this->businesses as $business)
                                <option value="{{ $business->id }}">{{ $business->name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                @endif
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.section>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <flux:card class="min-w-0">
                    <flux:text>{{ __('Current stage') }}</flux:text>
                    <flux:heading size="lg" class="mt-2">{{ $this->business->stage->value }}</flux:heading>
                </flux:card>

                <flux:card class="min-w-0">
                    <flux:text>{{ __('Evaluation') }}</flux:text>
                    <flux:heading size="lg" class="mt-2">{{ $this->business->evaluations()->latest()->exists() ? __('Started') : __('Not started') }}</flux:heading>
                </flux:card>

                <flux:card class="min-w-0">
                    <flux:text>{{ __('Next action') }}</flux:text>
                    <flux:heading size="lg" class="mt-2">{{ __('Complete your evaluation') }}</flux:heading>
                </flux:card>

                <a href="{{ route('business.opportunities') }}" wire:navigate class="block rounded-xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-zinc-950 dark:focus-visible:outline-white">
                    <flux:card class="h-full min-w-0 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800">
                        <flux:text>{{ __('Opportunities') }}</flux:text>
                        <flux:heading size="lg" class="mt-2">{{ __('See current matches') }} <span aria-hidden="true">→</span></flux:heading>
                    </flux:card>
                </a>
            </div>
        </x-ui.section>

        <x-ui.section>
            <flux:heading level="2" size="lg">{{ __('Recommendations') }}</flux:heading>
            <livewire:business.recommendations />
        </x-ui.section>
    @else
        <x-ui.empty-state
            :title="__('No business selected')"
            :description="__('Create or select a business to continue.')"
        />
    @endif
</x-ui.page>
