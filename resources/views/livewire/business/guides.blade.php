<x-layouts::app :title="'Guides · '.$this->business->name">
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <p class="text-sm text-zinc-500">{{ $this->business->name }}</p>
            <h1 class="text-2xl font-semibold">Guides</h1>
            <p class="mt-1 text-sm text-zinc-600">Practical tools to help you move your business forward.</p>
        </div>

        @if ($this->guides->isEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <h2 class="font-medium">No published guides yet</h2>
                <p class="mt-2 text-sm text-zinc-600">Guides will appear here when useful execution content is published.</p>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($this->guides as $guide)
                    <a href="{{ route('business.guides.show', $guide->slug) }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-zinc-400">
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="font-medium">{{ $guide->title }}</h2>
                            <span class="text-xs text-zinc-500">v{{ $guide->version }}</span>
                        </div>
                        @if ($guide->description)
                            <p class="mt-2 text-sm text-zinc-600">{{ $guide->description }}</p>
                        @endif
                        <p class="mt-4 text-sm font-medium">Open guide →</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::app>
