<x-layouts::app :title="'Action Plan · '.$this->business->name">
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <p class="text-sm text-zinc-500">{{ $this->business->name }}</p>
            <h1 class="text-2xl font-semibold">Action Plan</h1>
            <p class="mt-1 text-sm text-zinc-600">Turn confirmed priorities into work you can actually complete.</p>
        </div>

        @if ($this->plan)
            <div class="grid gap-4">
                @foreach ($this->plan->actions as $action)
                    <article class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs text-zinc-500">Action {{ $action->position }}</p>
                                <h2 class="mt-1 font-medium">{{ $action->title }}</h2>
                                @if ($action->description)
                                    <p class="mt-2 text-sm text-zinc-600">{{ $action->description }}</p>
                                @endif
                            </div>
                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs uppercase">{{ $action->status->value }}</span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if ($action->status === \App\Enums\ActionStatus::Recommended)
                                <button wire:click="updateStatus({{ $action->id }}, 'accepted')" class="rounded-lg bg-zinc-900 px-3 py-2 text-xs text-white">Accept</button>
                                <button wire:click="updateStatus({{ $action->id }}, 'skipped')" class="rounded-lg border px-3 py-2 text-xs">Skip</button>
                            @elseif ($action->status === \App\Enums\ActionStatus::Accepted)
                                <button wire:click="updateStatus({{ $action->id }}, 'active')" class="rounded-lg bg-zinc-900 px-3 py-2 text-xs text-white">Start</button>
                                <button wire:click="updateStatus({{ $action->id }}, 'blocked')" class="rounded-lg border px-3 py-2 text-xs">Block</button>
                            @elseif ($action->status === \App\Enums\ActionStatus::Active)
                                <button wire:click="updateStatus({{ $action->id }}, 'blocked')" class="rounded-lg border px-3 py-2 text-xs">Block</button>
                                <div class="basis-full space-y-2 pt-2">
                                    <input wire:model="outcome" placeholder="What happened?" class="w-full rounded-lg border-zinc-300 text-sm">
                                    <input wire:model="evidence" placeholder="Evidence or reference (optional)" class="w-full rounded-lg border-zinc-300 text-sm">
                                    <button wire:click="complete({{ $action->id }})" class="rounded-lg bg-zinc-900 px-3 py-2 text-xs text-white">Complete</button>
                                </div>
                            @elseif ($action->status === \App\Enums\ActionStatus::Completed)
                                @if ($action->outcome)
                                    <p class="text-sm text-zinc-600">Outcome: {{ $action->outcome->summary }}</p>
                                @endif
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <h2 class="font-medium">No Action Plan yet</h2>
                <p class="mt-2 text-sm text-zinc-600">Create one from your active priorities.</p>
                @if ($this->business->priorities()->where('status', 'active')->exists())
                    <button wire:click="createPlan" class="mt-4 rounded-lg bg-zinc-900 px-4 py-2 text-sm text-white">Create Action Plan</button>
                @endif
            </div>
        @endif
    </div>
</x-layouts::app>
