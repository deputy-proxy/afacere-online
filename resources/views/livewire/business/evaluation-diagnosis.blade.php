<x-layouts::app :title="'Diagnosis · '.$this->business->name">
    <div class="mx-auto max-w-5xl space-y-6">
        <header>
            <p class="text-sm text-zinc-500">{{ $this->business->name }}</p>
            <h1 class="text-2xl font-semibold">Your business diagnosis</h1>
            <p class="mt-1 text-sm text-zinc-600">Evaluation version {{ $this->evaluation->version->version }}. Findings remain tied to this evaluation version.</p>
        </header>
        @if ($this->findings->isEmpty())
            <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-medium">No findings are available</h2>
                <p class="mt-2 text-sm text-zinc-600">This completed evaluation did not produce any diagnosis findings.</p>
            </section>
        @else
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach ($this->findings as $finding)
                    <article wire:key="finding-{{ $finding->id }}" class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">{{ $finding->dimension }}</p>
                        <div class="mt-1 flex items-start justify-between gap-4">
                            <h2 class="text-lg font-medium">{{ $finding->title }}</h2>
                            <span class="shrink-0 rounded-full border border-zinc-300 px-2 py-1 text-xs font-medium uppercase" aria-label="Severity {{ $finding->severity }}">{{ $finding->severity }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-zinc-600">{{ $finding->description }}</p>
                        @if (is_array($finding->context) && isset($finding->context['answered'], $finding->context['total']))
                            <p class="mt-3 text-xs text-zinc-500">{{ $finding->context['answered'] }} of {{ $finding->context['total'] }} questions assessed in this dimension.</p>
                        @endif
                        @php($findingRecommendations = $this->recommendationsFor($finding))
                        @if ($findingRecommendations->isNotEmpty())
                            <div class="mt-5 border-t border-zinc-100 pt-4">
                                <h3 class="font-medium">Recommended next actions</h3>
                                @foreach ($findingRecommendations as $recommendation)
                                    @php($context = $this->recommendationContext($recommendation))
                                    <div wire:key="recommendation-{{ $recommendation->id }}" class="mt-3 rounded-lg border border-zinc-200 p-4">
                                        <h4 class="font-medium">{{ $recommendation->title }}</h4>
                                        <p class="mt-1 text-sm text-zinc-600">{{ $context['reason'] }}</p>
                                        <p class="mt-2 text-xs text-zinc-500">Expected outcome: {{ $context['expected_outcome'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-5 border-t border-zinc-100 pt-4 text-sm text-zinc-500">No recommendation is currently linked to this finding.</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
        <section class="rounded-xl bg-zinc-900 p-6 text-white">
            <h2 class="text-lg font-medium">Turn findings into action</h2>
            <p class="mt-2 text-sm text-zinc-300">Review recommendations and priorities from the business dashboard, then continue into your Action Plan.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex rounded-lg bg-white px-4 py-2 text-sm font-medium text-zinc-900">Continue to dashboard</a>
                <a href="{{ route('business.action-plan') }}" class="inline-flex rounded-lg border border-zinc-600 px-4 py-2 text-sm font-medium text-white">Open Action Plan</a>
            </div>
        </section>
    </div>
</x-layouts::app>
