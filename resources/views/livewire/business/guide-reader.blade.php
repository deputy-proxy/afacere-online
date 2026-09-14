<x-ui.page>
    <div class="mx-auto max-w-4xl space-y-8">
        <a href="{{ route('business.guides') }}" wire:navigate class="text-sm font-medium text-zinc-600 underline-offset-4 hover:underline dark:text-zinc-300">
            ← All guides
        </a>

        <x-ui.page-header
            :title="$this->guide->title"
            :description="$this->guide->description"
        >
            <x-slot name="actions">
                <span class="text-sm text-zinc-500">Version {{ $this->guide->version }}</span>
            </x-slot>
        </x-ui.page-header>

        @if ($errors->has('guide'))
            <x-ui.alert variant="danger">{{ $errors->first('guide') }}</x-ui.alert>
        @endif

        @if ($this->progress)
            @php
                $totalSteps = $this->guide->sections->sum(fn ($section) => $section->steps->count());
                $completedCount = count($this->completedStepIds);
                $percentage = $totalSteps > 0 ? (int) round(($completedCount / $totalSteps) * 100) : 0;
            @endphp

            <section aria-labelledby="guide-progress-heading" class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 id="guide-progress-heading" class="font-semibold">Your progress</h2>
                    <span class="text-sm text-zinc-600 dark:text-zinc-300">{{ $completedCount }}/{{ $totalSteps }} steps · {{ $percentage }}%</span>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percentage }}" aria-label="Guide progress">
                    <div class="h-full rounded-full bg-zinc-900 dark:bg-white" style="width: {{ $percentage }}%"></div>
                </div>
                @if ($this->progress->completed_at)
                    <x-ui.alert variant="success" class="mt-4">Guide completed.</x-ui.alert>
                @else
                    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">Continue from any unfinished step. Your progress is saved for this business.</p>
                @endif
            </section>
        @else
            <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">Start this guide</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Your progress will be saved for this business and guide version.</p>
                <flux:button wire:click="start" wire:loading.attr="disabled" wire:target="start" class="mt-4">
                    <span wire:loading.remove wire:target="start">Start guide</span>
                    <span wire:loading wire:target="start">Starting…</span>
                </flux:button>
            </section>
        @endif

        <div class="space-y-6">
            @forelse ($this->guide->sections as $section)
                <section wire:key="guide-section-{{ $section->id }}" aria-labelledby="section-{{ $section->id }}" class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm sm:p-6 dark:border-zinc-700 dark:bg-zinc-900">
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">Section {{ $section->position }}</p>
                    <h2 id="section-{{ $section->id }}" class="mt-1 text-xl font-semibold text-zinc-900 dark:text-white">{{ $section->title }}</h2>
                    @if ($section->content)
                        <div class="mt-3 whitespace-pre-line text-sm leading-7 text-zinc-700 dark:text-zinc-300">{{ $section->content }}</div>
                    @endif

                    <div class="mt-6 space-y-4">
                        @forelse ($section->steps as $step)
                            @php $completed = in_array($step->id, $this->completedStepIds, true); @endphp
                            <article wire:key="guide-step-{{ $step->id }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="text-xs text-zinc-500">Step {{ $step->position }}</p>
                                        <h3 class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $step->title }}</h3>
                                        @if ($step->content)
                                            <div class="mt-2 whitespace-pre-line text-sm leading-7 text-zinc-600 dark:text-zinc-300">{{ $step->content }}</div>
                                        @endif
                                    </div>
                                    @if ($completed)
                                        <span class="shrink-0 text-xs font-medium text-zinc-700 dark:text-zinc-200" role="status">Completed</span>
                                    @endif
                                </div>

                                @if (is_array($step->resources) && $step->resources !== [])
                                    <div class="mt-4 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                                        <h4 class="text-sm font-medium">Resources</h4>
                                        <ul class="mt-2 space-y-2 text-sm">
                                            @foreach ($step->resources as $resource)
                                                @php
                                                    $resourceUrl = is_array($resource) ? ($resource['url'] ?? null) : null;
                                                    $resourceTitle = is_array($resource) ? ($resource['title'] ?? $resource['label'] ?? null) : null;
                                                @endphp
                                                <li>
                                                    @if (is_string($resourceUrl) && filter_var($resourceUrl, FILTER_VALIDATE_URL))
                                                        <a href="{{ $resourceUrl }}" target="_blank" rel="noreferrer" class="font-medium underline underline-offset-4">
                                                            {{ is_string($resourceTitle) && $resourceTitle !== '' ? $resourceTitle : $resourceUrl }}
                                                        </a>
                                                    @elseif (is_string($resource))
                                                        <span class="text-zinc-600 dark:text-zinc-300">{{ $resource }}</span>
                                                    @else
                                                        <span class="text-zinc-600 dark:text-zinc-300">{{ json_encode($resource) }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($this->progress && ! $completed && ! $this->progress->completed_at)
                                    <flux:button wire:click="completeStep({{ $step->id }})" wire:loading.attr="disabled" wire:target="completeStep({{ $step->id }})" variant="outline" size="sm" class="mt-4">
                                        <span wire:loading.remove wire:target="completeStep({{ $step->id }})">Mark complete</span>
                                        <span wire:loading wire:target="completeStep({{ $step->id }})">Saving…</span>
                                    </flux:button>
                                @endif
                            </article>
                        @empty
                            <x-ui.empty-state title="No steps in this section" description="This published section currently has no executable steps." />
                        @endforelse
                    </div>
                </section>
            @empty
                <x-ui.empty-state title="This guide has no sections" description="The guide is published, but no readable sections are currently available." />
            @endforelse
        </div>

        @if ($this->relatedGuides->isNotEmpty())
            <section aria-labelledby="related-guides-heading">
                <h2 id="related-guides-heading" class="text-lg font-semibold">Related to your business</h2>
                <div class="mt-3 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
                    @foreach ($this->relatedGuides as $relatedGuide)
                        <a wire:key="related-guide-{{ $relatedGuide->id }}" href="{{ route('business.guides.show', $relatedGuide->slug) }}" wire:navigate class="rounded-lg border border-zinc-200 bg-white p-4 text-sm font-medium hover:border-zinc-400 dark:border-zinc-700 dark:bg-zinc-900">
                            {{ $relatedGuide->title }}
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-ui.page>
