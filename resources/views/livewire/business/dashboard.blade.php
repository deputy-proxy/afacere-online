<x-ui.page>
    @if ($this->business)
        <x-ui.page-header
            :title="$this->business->name"
            :description="__('Your business workspace and next steps')"
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
                    <flux:heading size="lg" class="mt-2">
                        {{ $this->latestEvaluation?->status->value ?? __('Not started') }}
                    </flux:heading>
                    @if ($this->latestEvaluation?->completed_at)
                        <flux:text class="mt-1 text-xs">{{ __('Completed :date', ['date' => $this->latestEvaluation->completed_at->toFormattedDateString()]) }}</flux:text>
                    @endif
                </flux:card>

                <flux:card class="min-w-0">
                    <flux:text>{{ __('Active priorities') }}</flux:text>
                    <flux:heading size="lg" class="mt-2">{{ count($this->priorities) }}</flux:heading>
                    @if ($this->priorities !== [])
                        <flux:text class="mt-1 text-xs">{{ $this->priorities[0]->title }}</flux:text>
                    @endif
                </flux:card>

                <flux:card class="min-w-0">
                    <flux:text>{{ __('Action Plan') }}</flux:text>
                    @if ($this->actionPlan)
                        <flux:heading size="lg" class="mt-2">{{ $this->actionPlan->status }}</flux:heading>
                        <flux:text class="mt-1 text-xs">
                            {{ $this->actionPlan->actions->where('status.value', 'completed')->count() }} / {{ $this->actionPlan->actions->count() }} {{ __('actions completed') }}
                        </flux:text>
                    @else
                        <flux:heading size="lg" class="mt-2">{{ __('Not created') }}</flux:heading>
                    @endif
                </flux:card>
            </div>
        </x-ui.section>

        <x-ui.section>
            <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(18rem,1fr)]">
                <flux:card class="min-w-0">
                    <flux:text>{{ __('What should I do next?') }}</flux:text>
                    <flux:heading level="2" size="xl" class="mt-2">{{ $this->nextAction['label'] }}</flux:heading>
                    <flux:text class="mt-2 max-w-2xl">{{ $this->nextAction['description'] }}</flux:text>
                    <div class="mt-4">
                        <flux:button variant="primary" :href="route($this->nextAction['route'], $this->nextAction['route_parameters'])" wire:navigate>
                            {{ $this->nextAction['label'] }}
                        </flux:button>
                    </div>
                </flux:card>

                <flux:card class="min-w-0">
                    <flux:heading level="2" size="lg">{{ __('Business overview') }}</flux:heading>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-zinc-500">{{ __('Name') }}</dt>
                            <dd>{{ $this->business->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-500">{{ __('Description') }}</dt>
                            <dd>{{ $this->business->description ?: __('No description added yet.') }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-500">{{ __('Goals') }}</dt>
                            <dd>{{ count($this->goals) }}</dd>
                        </div>
                        <div>
                            <dt class="text-zinc-500">{{ __('Metrics') }}</dt>
                            <dd>{{ count($this->metrics) }}</dd>
                        </div>
                    </dl>
                </flux:card>
            </div>
        </x-ui.section>

        <x-ui.section>
            <div class="grid gap-6 lg:grid-cols-2">
                <flux:card class="min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <flux:heading level="2" size="lg">{{ __('Goals') }}</flux:heading>
                            <flux:text class="mt-1">{{ __('Current measurable outcomes for this business.') }}</flux:text>
                        </div>
                        <flux:badge>{{ count($this->goals) }}</flux:badge>
                    </div>

                    @if ($this->goals !== [])
                        <ul class="mt-4 space-y-3">
                            @foreach ($this->goals as $goal)
                                <li class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="font-medium">{{ $goal->title }}</span>
                                        <span class="text-xs text-zinc-500">{{ $goal->status->value }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Target: :target :unit', ['target' => $goal->target, 'unit' => $goal->unit ?? '']) }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-ui.empty-state
                            class="mt-4"
                            :title="__('No active goals yet')"
                            :description="__('Add measurable outcomes when the supported goals workflow is available.')"
                        />
                    @endif
                </flux:card>

                <flux:card class="min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <flux:heading level="2" size="lg">{{ __('Metrics') }}</flux:heading>
                            <flux:text class="mt-1">{{ __('Measures used to understand business progress.') }}</flux:text>
                        </div>
                        <flux:badge>{{ count($this->metrics) }}</flux:badge>
                    </div>

                    @if ($this->metrics !== [])
                        <ul class="mt-4 space-y-3">
                            @foreach ($this->metrics as $metric)
                                <li class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                                    <div class="flex items-start justify-between gap-3">
                                        <span class="font-medium">{{ $metric->name }}</span>
                                        <span class="text-xs text-zinc-500">{{ $metric->aggregation->value }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $metric->unit ?: __('No unit specified') }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-ui.empty-state
                            class="mt-4"
                            :title="__('No metrics yet')"
                            :description="__('Add measures when the supported metrics workflow is available.')"
                        />
                    @endif
                </flux:card>
            </div>
        </x-ui.section>

        <x-ui.section>
            <div class="flex items-end justify-between gap-4">
                <div>
                    <flux:heading level="2" size="lg">{{ __('Recommendations') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Suggested work remains separate from accepted or active actions.') }}</flux:text>
                </div>
                <flux:button variant="ghost" :href="route('business.evaluation.diagnosis')" wire:navigate>{{ __('Review diagnosis') }}</flux:button>
            </div>
            <div class="mt-4">
                <livewire:business.recommendations />
            </div>
        </x-ui.section>
    @else
        <x-ui.empty-state
            :title="__('No business selected')"
            :description="__('Create or select a business to continue.')"
            :action-href="route('business.onboarding')"
            :action-label="__('Set up a business')"
        />
    @endif
</x-ui.page>
