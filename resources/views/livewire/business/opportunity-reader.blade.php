<x-layouts::app :title="$this->opportunity->title.' · '.$this->business->name">
    <div class="mx-auto max-w-4xl space-y-6">
        <a href="{{ route('business.opportunities') }}" wire:navigate class="text-sm text-zinc-500">← All opportunities</a>

        <article class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-zinc-500">{{ $this->opportunity->type?->name ?? 'Opportunity' }}</p>
            <h1 class="mt-1 text-2xl font-semibold">{{ $this->opportunity->title }}</h1>
            @if ($this->opportunity->description)
                <div class="mt-4 whitespace-pre-line text-sm leading-6 text-zinc-700">{{ $this->opportunity->description }}</div>
            @endif

            <div class="mt-6 rounded-lg bg-zinc-50 p-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-medium">Eligibility</h2>
                    <span class="text-sm font-medium">{{ (int) round((float) $this->match->score * 100) }}%</span>
                </div>
                <div class="mt-3 space-y-2">
                    @foreach ($this->match->criteria_results ?? [] as $key => $result)
                        <div class="flex items-start justify-between gap-4 text-sm">
                            <span>{{ $key }}</span>
                            <span class="font-medium">{{ ($result['passed'] ?? false) ? 'Meets criteria' : 'Does not meet criteria' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 grid gap-2 text-sm text-zinc-600">
                @if ($this->opportunity->valid_from)
                    <p>Available from {{ $this->opportunity->valid_from->format('d M Y') }}.</p>
                @endif
                @if ($this->opportunity->valid_until)
                    <p>Current until {{ $this->opportunity->valid_until->format('d M Y') }}.</p>
                @endif
            </div>

            @if ((float) $this->match->score === 1.0)
                <button wire:click="apply" class="mt-6 rounded-lg bg-zinc-900 px-4 py-2 text-sm text-white">Track application</button>
            @else
                <p class="mt-6 rounded-lg border border-zinc-200 p-4 text-sm text-zinc-600">This opportunity cannot be applied for because one or more explicit eligibility criteria are not met.</p>
            @endif
        </article>
    </div>
</x-layouts::app>
