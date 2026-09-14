<x-layouts::app :title="'Diagnosis · '.$this->business->name">
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <p class="text-sm text-zinc-500">{{ $this->business->name }}</p>
            <h1 class="text-2xl font-semibold">Your business diagnosis</h1>
            <p class="mt-1 text-sm text-zinc-600">Based on evaluation {{ $this->evaluation->version->version }}. Your answers and this diagnosis remain tied to that version.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($this->evaluation->findings as $finding)
                <article wire:key="finding-{{ $finding->id }}" class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="font-medium">{{ $finding->title }}</h2>
                        <span class="rounded-full border border-zinc-300 px-2 py-1 text-xs uppercase" aria-label="Severity {{ $finding->severity }}">{{ $finding->severity }}</span>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">{{ $finding->description }}</p>
                    <p class="mt-4 text-xs text-zinc-500">Dimension: {{ $finding->dimension }}</p>
                </article>
            @endforeach
        </div>
        <div class="rounded-xl bg-zinc-900 p-6 text-white">
            <h2 class="text-lg font-medium">What happens next?</h2>
            <p class="mt-2 text-sm text-zinc-300">Use these findings to choose the priorities that matter most, then turn them into concrete actions.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex rounded-lg bg-white px-4 py-2 text-sm font-medium text-zinc-900">Continue to dashboard</a>
                <a href="{{ route('business.action-plan') }}" class="inline-flex rounded-lg border border-zinc-600 px-4 py-2 text-sm font-medium text-white">Open Action Plan</a>
            </div>
        </div>
    </div>
</x-layouts::app>
