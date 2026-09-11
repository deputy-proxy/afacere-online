<x-layouts::app :title="'Opportunities · '.$this->business->name">
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <p class="text-sm text-zinc-500">{{ $this->business->name }}</p>
            <h1 class="text-2xl font-semibold">Opportunities</h1>
            <p class="mt-1 text-sm text-zinc-600">Current opportunities matched against your business profile using explicit eligibility rules.</p>
        </div>

        @if ($this->matches->isEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <h2 class="font-medium">No current matches</h2>
                <p class="mt-2 text-sm text-zinc-600">There are no published, current opportunities that match your business profile right now.</p>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($this->matches as $match)
                    @php $opportunity = $match->opportunity; @endphp
                    <a href="{{ route('business.opportunities.show', $opportunity->id) }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-zinc-400">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-zinc-500">{{ $opportunity->type?->name ?? 'Opportunity' }}</p>
                                <h2 class="mt-1 font-medium">{{ $opportunity->title }}</h2>
                            </div>
                            <span class="text-xs font-medium">{{ (int) round((float) $match->score * 100) }}% eligible</span>
                        </div>
                        @if ($opportunity->description)
                            <p class="mt-2 text-sm text-zinc-600">{{ $opportunity->description }}</p>
                        @endif
                        @if ($opportunity->valid_until)
                            <p class="mt-4 text-xs text-zinc-500">Current until {{ $opportunity->valid_until->format('d M Y') }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
