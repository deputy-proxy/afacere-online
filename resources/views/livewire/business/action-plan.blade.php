<x-ui.page>
    <x-ui.page-header
        :title="'Action Plan · '.$this->business->name"
        :description="__('Turn confirmed priorities into work you can complete and record.')"
    >
        <x-slot:actions>
            <flux:button variant="ghost" :href="route('business.dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($this->plan)
        <x-ui.section>
            <flux:card class="min-w-0">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <flux:text>{{ __('Current version') }}</flux:text>
                        <flux:heading size="lg" class="mt-1">{{ $this->plan->version }}</flux:heading>
                        <flux:text class="mt-1">{{ $this->plan->status }}</flux:text>
                    </div>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div>
                            <flux:text>{{ __('Total') }}</flux:text>
                            <flux:heading size="lg" class="mt-1">{{ $this->plan->actions->count() }}</flux:heading>
                        </div>
                        <div>
                            <flux:text>{{ __('Completed') }}</flux:text>
                            <flux:heading size="lg" class="mt-1">{{ $this->plan->actions->where('status', \App\Enums\ActionStatus::Completed)->count() }}</flux:heading>
                        </div>
                        <div>
                            <flux:text>{{ __('History') }}</flux:text>
                            <flux:heading size="lg" class="mt-1">{{ $this->plan->revisions->count() }}</flux:heading>
                        </div>
                    </div>
                </div>
            </flux:card>
        </x-ui.section>

        @php
            $groups = [
                'active' => ['label' => __('Active'), 'statuses' => [\App\Enums\ActionStatus::Active]],
                'accepted' => ['label' => __('Accepted'), 'statuses' => [\App\Enums\ActionStatus::Accepted]],
                'recommended' => ['label' => __('Recommended'), 'statuses' => [\App\Enums\ActionStatus::Recommended]],
                'blocked' => ['label' => __('Blocked'), 'statuses' => [\App\Enums\ActionStatus::Blocked]],
                'skipped' => ['label' => __('Skipped'), 'statuses' => [\App\Enums\ActionStatus::Skipped]],
                'completed' => ['label' => __('Completed'), 'statuses' => [\App\Enums\ActionStatus::Completed]],
            ];
        @endphp

        @foreach ($groups as $group)
            @php
                $actions = $this->plan->actions->filter(fn ($action) => in_array($action->status, $group['statuses'], true));
            @endphp

            @if ($actions->isNotEmpty())
                <x-ui.section>
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <flux:heading level="2" size="lg">{{ $group['label'] }}</flux:heading>
                        <flux:badge>{{ $actions->count() }}</flux:badge>
                    </div>

                    <div class="grid gap-4">
                        @foreach ($actions as $action)
                            <flux:card wire:key="action-{{ $action->id }}" class="min-w-0">
                                <div class="flex flex-col gap-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <flux:text class="text-xs">{{ __('Action :position', ['position' => $action->position]) }}</flux:text>
                                            <flux:heading level="3" size="lg" class="mt-1">{{ $action->title }}</flux:heading>
                                            @if ($action->description)
                                                <flux:text class="mt-2">{{ $action->description }}</flux:text>
                                            @endif
                                            @if ($action->priority)
                                                <flux:text class="mt-2 text-xs">{{ __('Priority: :title', ['title' => $action->priority->title]) }}</flux:text>
                                            @endif
                                        </div>
                                        <flux:badge>{{ $action->status->value }}</flux:badge>
                                    </div>

                                    @if ($action->guides->isNotEmpty())
                                        <div class="border-t pt-4">
                                            <flux:text class="text-xs font-medium uppercase tracking-wide">{{ __('Execution guides') }}</flux:text>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @foreach ($action->guides as $guide)
                                                    @if ($guide->status === 'published')
                                                        <flux:button size="sm" variant="ghost" :href="route('business.guides.show', $guide->slug)" wire:navigate>
                                                            {{ $guide->title }}
                                                        </flux:button>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($action->accepted_at || $action->started_at || $action->completed_at)
                                        <dl class="grid gap-2 text-xs sm:grid-cols-3">
                                            @if ($action->accepted_at)
                                                <div>
                                                    <dt class="text-zinc-500">{{ __('Accepted') }}</dt>
                                                    <dd>{{ $action->accepted_at->toFormattedDateString() }}</dd>
                                                </div>
                                            @endif
                                            @if ($action->started_at)
                                                <div>
                                                    <dt class="text-zinc-500">{{ __('Started') }}</dt>
                                                    <dd>{{ $action->started_at->toFormattedDateString() }}</dd>
                                                </div>
                                            @endif
                                            @if ($action->completed_at)
                                                <div>
                                                    <dt class="text-zinc-500">{{ __('Completed') }}</dt>
                                                    <dd>{{ $action->completed_at->toFormattedDateString() }}</dd>
                                                </div>
                                            @endif
                                        </dl>
                                    @endif

                                    <div class="flex flex-wrap gap-2">
                                        @if ($action->status === \App\Enums\ActionStatus::Recommended)
                                            <flux:button wire:click="updateStatus({{ $action->id }}, 'accepted')" wire:loading.attr="disabled" wire:target="updateStatus({{ $action->id }}, 'accepted')" variant="primary">
                                                {{ __('Accept') }}
                                            </flux:button>
                                            <flux:button wire:click="updateStatus({{ $action->id }}, 'skipped')" wire:confirm="{{ __('Skip this action? It cannot be reopened with the current workflow.') }}" wire:loading.attr="disabled" variant="ghost">
                                                {{ __('Skip') }}
                                            </flux:button>
                                        @elseif ($action->status === \App\Enums\ActionStatus::Accepted)
                                            <flux:button wire:click="updateStatus({{ $action->id }}, 'active')" wire:loading.attr="disabled" variant="primary">
                                                {{ __('Start') }}
                                            </flux:button>
                                            <flux:button wire:click="updateStatus({{ $action->id }}, 'skipped')" wire:confirm="{{ __('Skip this action? It cannot be reopened with the current workflow.') }}" wire:loading.attr="disabled" variant="ghost">
                                                {{ __('Skip') }}
                                            </flux:button>
                                            <flux:button wire:click="updateStatus({{ $action->id }}, 'blocked')" wire:confirm="{{ __('Block this action? It cannot be reopened with the current workflow.') }}" wire:loading.attr="disabled" variant="ghost">
                                                {{ __('Block') }}
                                            </flux:button>
                                        @elseif ($action->status === \App\Enums\ActionStatus::Active)
                                            @if ($completionActionId === $action->id)
                                                <div class="basis-full space-y-4 rounded-lg border p-4">
                                                    <flux:field>
                                                        <flux:label>{{ __('Outcome') }}</flux:label>
                                                        <flux:textarea wire:model="outcome" required aria-describedby="completion-help-{{ $action->id }}" />
                                                        <flux:error name="outcome" />
                                                    </flux:field>
                                                    <flux:field>
                                                        <flux:label>{{ __('Evidence or reference') }}</flux:label>
                                                        <flux:textarea wire:model="evidence" aria-describedby="completion-help-{{ $action->id }}" />
                                                        <flux:error name="evidence" />
                                                    </flux:field>
                                                    <p id="completion-help-{{ $action->id }}" class="text-xs text-zinc-500">{{ __('Record what happened and, when available, the evidence supporting completion.') }}</p>
                                                    <div class="flex flex-wrap gap-2">
                                                        <flux:button wire:click="complete" wire:loading.attr="disabled" wire:target="complete" variant="primary">
                                                            {{ __('Complete action') }}
                                                        </flux:button>
                                                        <flux:button wire:click="$set('completionActionId', null)" variant="ghost">
                                                            {{ __('Cancel') }}
                                                        </flux:button>
                                                    </div>
                                                </div>
                                            @else
                                                <flux:button wire:click="openCompletionForm({{ $action->id }})" variant="primary">
                                                    {{ __('Complete') }}
                                                </flux:button>
                                            @endif
                                            <flux:button wire:click="updateStatus({{ $action->id }}, 'skipped')" wire:confirm="{{ __('Skip this action? It cannot be reopened with the current workflow.') }}" wire:loading.attr="disabled" variant="ghost">
                                                {{ __('Skip') }}
                                            </flux:button>
                                            <flux:button wire:click="updateStatus({{ $action->id }}, 'blocked')" wire:confirm="{{ __('Block this action? It cannot be reopened with the current workflow.') }}" wire:loading.attr="disabled" variant="ghost">
                                                {{ __('Block') }}
                                            </flux:button>
                                        @endif
                                    </div>

                                    @if ($action->outcome)
                                        <div class="border-t pt-4">
                                            <flux:heading level="4" size="sm">{{ __('Outcome') }}</flux:heading>
                                            <flux:text class="mt-1">{{ $action->outcome->summary }}</flux:text>
                                        </div>
                                    @endif

                                    @if ($action->evidence->isNotEmpty())
                                        <div class="border-t pt-4">
                                            <flux:heading level="4" size="sm">{{ __('Evidence') }}</flux:heading>
                                            <ul class="mt-2 space-y-2 text-sm">
                                                @foreach ($action->evidence as $item)
                                                    <li>
                                                        <span>{{ $item->description }}</span>
                                                        <span class="text-xs text-zinc-500">{{ $item->recorded_at->toFormattedDateString() }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if ($action->auditLogs->isNotEmpty())
                                        <details class="border-t pt-4">
                                            <summary class="cursor-pointer text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-500">{{ __('Progress history') }}</summary>
                                            <ol class="mt-3 space-y-2 text-sm">
                                                @foreach ($action->auditLogs as $log)
                                                    <li>
                                                        <span>{{ data_get($log->context, 'from') }} → {{ data_get($log->context, 'to') }}</span>
                                                        <span class="text-xs text-zinc-500">{{ $log->occurred_at->toFormattedDateString() }}</span>
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </details>
                                    @endif
                                </div>
                            </flux:card>
                        @endforeach
                    </div>
                </x-ui.section>
            @endif
        @endforeach
    @else
        <x-ui.section>
            <x-ui.empty-state
                :title="__('No Action Plan yet')"
                :description="__('Create one from your active priorities.')"
                :action-label="$this->business->priorities()->where('status', 'active')->exists() ? __('Create Action Plan') : null"
                :action-href="null"
            >
                @if ($this->business->priorities()->where('status', 'active')->exists())
                    <x-slot:action>
                        <flux:button wire:click="createPlan" wire:loading.attr="disabled" variant="primary">
                            {{ __('Create Action Plan') }}
                        </flux:button>
                    </x-slot:action>
                @endif
            </x-ui.empty-state>
        </x-ui.section>
    @endif
</x-ui.page>
