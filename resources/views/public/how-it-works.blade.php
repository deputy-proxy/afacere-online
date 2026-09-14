<x-layouts.public title="How afacere.online works" description="Understand the afacere.online progression from evaluation to monitored action.">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <section class="max-w-3xl" aria-labelledby="how-it-works-heading">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">How it works</p>
            <h1 id="how-it-works-heading" class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">Understand the business. Choose the priority. Do the work.</h1>
            <p class="mt-5 text-lg leading-8 text-zinc-600 dark:text-zinc-400">The core loop is deliberately simple. Business context drives the evaluation, the evaluation drives priorities, and execution creates the evidence for the next iteration.</p>
        </section>
        <ol class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3" aria-label="Product progression">
            @foreach ([['Evaluate', 'Answer a structured assessment about the business. Save progress and keep historical evaluations rather than overwriting the past.'], ['Diagnose', 'Understand the areas that deserve attention, with reasoning captured in the application rather than hidden in a black box.'], ['Prioritize', 'Turn diagnosis into a focused set of priorities instead of treating every possible improvement as equally urgent.'], ['Act', 'Accept actions and use practical Guides and Opportunities to move from understanding to execution.'], ['Monitor', 'Record check-ins and meaningful indicators so progress remains visible between evaluations.'], ['Reassess', 'When the business changes, create a new evaluation version without destroying the history.']] as [$title, $description])
                <li class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-950"><div class="flex items-center gap-3"><span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-sm font-semibold text-white dark:bg-white dark:text-zinc-900">{{ $loop->iteration }}</span><h2 class="text-lg font-semibold">{{ $title }}</h2></div><p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $description }}</p></li>
            @endforeach
        </ol>
        <section class="mt-12 rounded-2xl bg-zinc-50 p-8 dark:bg-zinc-900" aria-labelledby="ecosystem-path-heading">
            <h2 id="ecosystem-path-heading" class="text-2xl font-semibold">The next step can come from the ecosystem.</h2>
            <p class="mt-3 max-w-3xl text-zinc-600 dark:text-zinc-400">Guides, Opportunities, Funding, Experts, Marketplace services, Community and Events are connected to the product progression. They are not a separate content portal, and personalized discovery belongs in the authenticated business context.</p>
            <div class="mt-6 flex flex-wrap gap-3"><a class="rounded-lg bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900" href="{{ route('register') }}" data-analytics-event="public.evaluator.cta">Start the evaluator</a><a class="rounded-lg border border-zinc-300 px-4 py-2.5 text-sm font-semibold hover:bg-white dark:border-zinc-700 dark:hover:bg-zinc-800" href="{{ route('public.pricing') }}" wire:navigate>See pricing</a></div>
        </section>
    </div>
</x-layouts.public>
