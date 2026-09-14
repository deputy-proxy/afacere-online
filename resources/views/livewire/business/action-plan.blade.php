<x-layouts::app :title="'Action Plan · '.$this->business->name">
    <div class="mx-auto max-w-5xl space-y-6">
        <header class="space-y-1">
            <p class="text-sm text-zinc-500">{{ $this->business->name }}</p>
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <flux:heading size="xl">Action Plan</flux:heading>
                    <flux:text class="mt-1">Turn confirmed priorities into work you can actually complete.</flux:text>
                </div>
                @if ($this->plan)
                    <flux:badge variant="outline">Version {{ $this->plan->version }} · {{ $this->plan->status }}</flux:badge>
                @endif
            </div>
        </header>

        @if ($this->plan)
            <div class="grid gap-6 lg:grid-cols-2">
                @foreach ([
                    'recommended' => ['title' => 'Recommended', 'description' => 'Priorities ready to be accepted.'],
                    'accepted' => ['title' => 'Accepted', 'description' => 'Actions ready to start.'],
                    'active' => ['title' => 'Active', 'description' => 'Work currently in progress.'],
                    'completed' => ['title' => 'Completed', 'description' => 'Finished actions and recorded outcomes.'],
                    'skipped' => ['title' => 'Skipped', 'description' => 'Actions intentionally not pursued.'],
                    'blocked' => ['title' => 'Blocked', 'description' => 'Actions stopped because a blocker was recorded.'],
                ] as $status => $group)
                    @if ($this->actionsByStatus->has($status))
                        <section class="space-y-3" aria-labelledby="action-group-{{ $status }}">
                            <div>
                                <flux:heading id="action-group-{{ $status }}" size="lg">{{ $group['title'] }}</flux:heading>
                                <flux:text>{{ $group['description'] }}</flux:text>
                            </div>
                            @foreach ($this->actionsByStatus->get($status, collect()) as $action)
                                <article wire:key="action-{{ $action->id }}" class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <flux:text size="sm">Action {{ $action->position }}</flux:text>
                                            <flux:heading size="lg" class="mt-1">{{ $action->title }}</flux:heading>
                                            @if ($action->description)
                                                <flux:text class="mt-2">{{ $action->description }}</flux:text>
                                            @endif
                                        </div>
                                        <flux:badge variant="outline" aria-label="Status {{ $action->status->value }}">{{ $action->status->value }}</flux:badge>
                                    </div>

                                    <div class="mt-4 grid gap-2 text-xs text-zinc-500 sm:grid-cols-3">
                                        @if ($action->accepted_at)<span>Accepted {{ $action->accepted_at->format('d M Y H:i') }}</span>@endif
                                        @if ($action->started_at)<span>Started {{ $action->started_at->format('d M Y H:i') }}</span>@endif
                                        @if ($action->completed_at)<span>Completed {{ $action->completed_at->format('d M Y H:i') }}</span>@endif
                                    </div>

                                    @if ($action->guides->isNotEmpty())
                                        <div class="mt-4 space-y-2">
                                            <flux:text size="sm" class="font-medium">Execution guides</flux:text>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($action->guides as $guide)
                                                    @if ($guide->status === 'published')
                                                        <flux:button size="sm" variant="outline" href="{{ route('business.guides.show', $guide->slug) }}" wire:navigate>{{ $guide->title }}</flux:button>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($action->status === \App\Enums\ActionStatus::Recommended)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <flux:button size="sm" variant="primary" wire:click="updateStatus({{ $action->id }}, 'accepted')">Accept</flux:button>
                                            <flux:button size="sm" variant="ghost" wire:click="beginResolution({{ $action->id }}, 'skipped')">Skip</flux:button>
                                        </div>
                                    @elseif ($action->status === \App\Enums\ActionStatus::Accepted)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <flux:button size="sm" variant="primary" wire:click="updateStatus({{ $action->id }}, 'active')">Start</flux:button>
                                            <flux:button size="sm" variant="ghost" wire:click="beginResolution({{ $action->id }}, 'blocked')">Block</flux:button>
                                            <flux:button size="sm" variant="ghost" wire:click="beginResolution({{ $action->id }}, 'skipped')">Skip</flux:button>
                                        </div>
                                    @elseif ($action->status === \App\Enums\ActionStatus::Active)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <flux:button size="sm" variant="primary" wire:click="beginCompletion({{ $action->id }})">Complete</flux:button>
                                            <flux:button size="sm" variant="ghost" wire:click="beginResolution({{ $action->id }}, 'blocked')">Block</flux:button>
                                            <flux:button size="sm" variant="ghost" wire:click="beginResolution({{ $action->id }}, 'skipped')">Skip</flux:button>
                                        </div>
                                        @if ($completionActionId === $action->id)
                                            <form wire:submit="complete({{ $action->id }})" class="mt-4 space-y-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                                <flux:field>
                                                    <flux:label>Outcome</flux:label>
                                                    <flux:textarea wire:model="outcome" rows="4" placeholder="What happened?" required />
                                                    <flux:error name="outcome" />
                                                </flux:field>
                                                <flux:field>
                                                    <flux:label>Evidence or reference</flux:label>
                                                    <flux:textarea wire:model="evidence" rows="3" placeholder="Optional evidence or reference" />
                                                    <flux:error name="evidence" />
                                                </flux:field>
                                                <div class="flex flex-wrap gap-2">
                                                    <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="complete">Record completion</flux:button>
                                                    <flux:button type="button" variant="ghost" wire:click="cancelCompletion">Cancel</flux:button>
                                                </div>
                                            </form>
                                        @endif
                                    @elseif ($action->status === \App\Enums\ActionStatus::Completed)
                                        @if ($action->outcome)
                                            <div class="mt-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                                                <flux:text size="sm"><strong>Outcome:</strong> {{ $action->outcome->summary }}</flux:text>
                                                @if ($action->outcome->recorded_at)<flux:text size="sm">Recorded {{ $action->outcome->recorded_at->format('d M Y H:i') }}</flux:text>@endif
                                            </div>
                                        @endif
                                    @elseif ($action->status === \App\Enums\ActionStatus::Skipped || $action->status === \App\Enums\ActionStatus::Blocked)
                                        <div class="mt-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800">
                                            <flux:text size="sm"><strong>{{ $action->status->value === 'blocked' ? 'Blocker' : 'Reason' }}:</strong> {{ $action->resolution_reason ?: 'No reason was recorded.' }}</flux:text>
                                            <flux:text size="sm" class="mt-1">This state is terminal under the current action transition rules. Its history remains available below.</flux:text>
                                        </div>
                                    @endif

                                    @if ($action->evidence->isNotEmpty())
                                        <div class="mt-4 space-y-2">
                                            <flux:text size="sm" class="font-medium">Evidence</flux:text>
                                            <ul class="space-y-1 text-sm text-zinc-600 dark:text-zinc-300">
                                                @foreach ($action->evidence as $item)
                                                    <li>{{ $item->description ?: $item->reference }} <span class="text-xs text-zinc-500">· {{ $item->recorded_at->format('d M Y H:i') }}</span></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @php($history = $this->historyFor($action))
                                    @if ($history->isNotEmpty())
                                        <details class="mt-4 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                                            <summary class="cursor-pointer text-sm font-medium">Progress history ({{ $history->count() }})</summary>
                                            <ol class="mt-3 space-y-2 text-xs text-zinc-500">
                                                @foreach ($history as $event)
                                                    <li>
                                                        {{ $event->occurred_at?->format('d M Y H:i') }} · {{ data_get($event->context, 'from') }} → {{ data_get($event->context, 'to') }}
                                                        @if (data_get($event->context, 'reason')) · {{ data_get($event->context, 'reason') }} @endif
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </details>
                                    @endif
                                </article>
                            @endforeach
                        </section>
                    @endif
                @endforeach
            </div>

            @if ($this->revisions->isNotEmpty())
                <section class="space-y-3">
                    <div>
                        <flux:heading size="lg">Plan history</flux:heading>
                        <flux:text>Immutable snapshots of the Action Plan as execution progressed.</flux:text>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($this->revisions as $revision)
                            <flux:card wire:key="revision-{{ $revision->id }}">
                                <flux:heading size="sm">Revision {{ $revision->version }}</flux:heading>
                                <flux:text size="sm">{{ $revision->created_at?->format('d M Y H:i') }}</flux:text>
                                @if ($revision->creator)<flux:text size="sm">By {{ $revision->creator->name }}</flux:text>@endif
                            </flux:card>
                        @endforeach
                    </div>
                </section>
            @endif
        @else
            <flux:card>
                <flux:heading size="lg">No Action Plan yet</flux:heading>
                <flux:text class="mt-2">Create one from your active priorities.</flux:text>
                @if ($this->business->priorities()->where('status', 'active')->exists())
                    <flux:button class="mt-4" variant="primary" wire:click="createPlan" wire:loading.attr="disabled" wire:target="createPlan">Create Action Plan</flux:button>
                @else
                    <flux:text class="mt-4">Activate at least one priority before creating an Action Plan.</flux:text>
                @endif
                <flux:error name="priorities" class="mt-2" />
            </flux:card>
        @endif
    </div>

    <flux:modal wire:model="showResolutionModal" name="action-resolution">
        <div class="space-y-4">
            <flux:heading size="lg">{{ $resolutionStatus === 'blocked' ? 'Block action' : 'Skip action' }}</flux:heading>
            <flux:text>Record why this action is being {{ $resolutionStatus === 'blocked' ? 'blocked' : 'skipped' }}. This transition cannot be reversed under the current domain rules.</flux:text>
            <flux:field>
                <flux:label>Reason</flux:label>
                <flux:textarea wire:model="resolutionReason" rows="4" required placeholder="Explain the reason" />
                <flux:error name="resolutionReason" />
            </flux:field>
            <div class="flex flex-wrap justify-end gap-2">
                <flux:button variant="ghost" wire:click="resetResolution">Cancel</flux:button>
                <flux:button variant="primary" wire:click="resolveAction" wire:loading.attr="disabled" wire:target="resolveAction">Confirm</flux:button>
            </div>
        </div>
    </flux:modal>
</x-layouts::app>
