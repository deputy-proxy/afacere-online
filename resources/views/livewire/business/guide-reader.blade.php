<x-layouts::app :title="$this->guide->title.' · '.$this->business->name">
    <div class="mx-auto max-w-4xl space-y-8">
        <div>
            <a href="{{ route('business.guides') }}" wire:navigate class="text-sm text-zinc-500">← All guides</a>
            <div class="mt-3 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold">{{ $this->guide->title }}</h1>
                    @if ($this->guide->description)
                        <p class="mt-2 text-zinc-600">{{ $this->guide->description }}</p>
                    @endif
                </div>
                <span class="text-xs text-zinc-500">Version {{ $this->guide->version }}</span>
            </div>
        </div>

        @if ($this->progress)
            @php
                $totalSteps = $this->guide->sections->sum(fn ($section) => $section->steps->count());
                $completedCount = count($this->completedStepIds);
                $percentage = $totalSteps > 0 ? (int) round(($completedCount / $totalSteps) * 100) : 0;
            @endphp
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between text-sm">
                    <span>Progress</span>
                    <span>{{ $completedCount }}/{{ $totalSteps }} steps · {{ $percentage }}%</span>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-zinc-100">
                    <div class="h-full rounded-full bg-zinc-900" style="width: {{ $percentage }}%"></div>
                </div>
                @if ($this->progress->completed_at)
                    <p class="mt-3 text-sm font-medium">Guide completed.</p>
                @endif
            </div>
        @else
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-zinc-600">Start this guide to save your progress for this business.</p>
                <button wire:click="start" class="mt-4 rounded-lg bg-zinc-900 px-4 py-2 text-sm text-white">Start guide</button>
            </div>
        @endif

        <div class="space-y-6">
            @foreach ($this->guide->sections as $section)
                <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-zinc-500">Section {{ $section->position }}</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ $section->title }}</h2>
                    @if ($section->content)
                        <div class="mt-3 whitespace-pre-line text-sm leading-6 text-zinc-700">{{ $section->content }}</div>
                    @endif

                    <div class="mt-5 space-y-4">
                        @foreach ($section->steps as $step)
                            @php $completed = in_array($step->id, $this->completedStepIds, true); @endphp
                            <article class="rounded-lg border p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs text-zinc-500">Step {{ $step->position }}</p>
                                        <h3 class="mt-1 font-medium">{{ $step->title }}</h3>
                                        @if ($step->content)
                                            <div class="mt-2 whitespace-pre-line text-sm leading-6 text-zinc-600">{{ $step->content }}</div>
                                        @endif
                                    </div>
                                    @if ($completed)
                                        <span class="text-xs font-medium">Completed</span>
                                    @endif
                                </div>

                                @if ($step->resources)
                                    <ul class="mt-3 space-y-1 text-sm">
                                        @foreach ($step->resources as $resource)
                                            <li class="text-zinc-600">{{ is_string($resource) ? $resource : json_encode($resource) }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if ($this->progress && ! $completed && ! $this->progress->completed_at)
                                    <button wire:click="completeStep({{ $step->id }})" class="mt-4 rounded-lg border px-3 py-2 text-xs">Mark complete</button>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        @if ($this->relatedGuides->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold">Related to your business</h2>
                <div class="mt-3 grid gap-3 md:grid-cols-3">
                    @foreach ($this->relatedGuides as $relatedGuide)
                        <a href="{{ route('business.guides.show', $relatedGuide->slug) }}" wire:navigate class="rounded-lg border border-zinc-200 bg-white p-4 text-sm hover:border-zinc-400">
                            {{ $relatedGuide->title }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
