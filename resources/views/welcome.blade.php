<x-layouts.public title="afacere.online | Business clarity and action" description="A practical business progression from diagnosis to priorities, action and monitoring.">
    <div data-analytics-event="public.home.view">
        <section class="border-b border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1.2fr_.8fr] lg:items-center lg:px-8 lg:py-24">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">A practical operating companion for entrepreneurs</p>
                    <h1 class="mt-5 text-4xl font-semibold tracking-tight text-zinc-950 sm:text-6xl dark:text-white">Turn business uncertainty into your next best action.</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-zinc-600 sm:text-xl dark:text-zinc-400">Understand where your business stands, identify what matters most, build an action plan and keep moving with practical guidance.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a class="rounded-lg bg-zinc-900 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" data-analytics-event="public.evaluator.cta" href="{{ route('register') }}">Start the free evaluator</a>
                        <a class="rounded-lg border border-zinc-300 px-5 py-3 text-sm font-semibold hover:bg-zinc-100 dark:border-zinc-700 dark:hover:bg-zinc-900" href="{{ route('public.how-it-works') }}" wire:navigate>See how it works</a>
                    </div>
                    <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">Start with an account. Your business data stays in the authenticated workspace.</p>
                </div>
                <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-800 dark:bg-zinc-900">
                    <p class="text-sm font-semibold">The progression</p>
                    <ol class="mt-5 space-y-4">
                        @foreach ([['Evaluate', 'Capture the current business context.'], ['Diagnose', 'Understand what deserves attention.'], ['Prioritize', 'Choose the work that matters now.'], ['Act', 'Execute with Guides and Opportunities.'], ['Monitor', 'Record progress and reassess.']] as [$title, $description])
                            <li class="flex gap-4"><span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-xs font-semibold text-white dark:bg-white dark:text-zinc-900">{{ $loop->iteration }}</span><div><h2 class="font-semibold">{{ $title }}</h2><p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p></div></li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20" aria-labelledby="for-entrepreneurs-heading">
            <div class="max-w-2xl"><p class="text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">For entrepreneurs</p><h2 id="for-entrepreneurs-heading" class="mt-3 text-3xl font-semibold tracking-tight">Less dashboard theatre. More useful decisions.</h2><p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">The product keeps the business at the center. It connects diagnosis, priorities and execution instead of asking you to maintain a collection of disconnected tools.</p></div>
            <div class="mt-10 grid gap-4 md:grid-cols-3">
                <article class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-800"><h3 class="font-semibold">Know what matters</h3><p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">A structured evaluation turns a broad business problem into an understandable diagnosis.</p></article>
                <article class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-800"><h3 class="font-semibold">Know what to do next</h3><p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Priorities and action plans connect findings to concrete work without pretending every task is equally urgent.</p></article>
                <article class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-800"><h3 class="font-semibold">Keep the history</h3><p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Evaluations, actions and progress remain traceable so a new decision does not erase the previous one.</p></article>
            </div>
        </section>
        <section id="ecosystem" class="bg-zinc-50 dark:bg-zinc-900" aria-labelledby="ecosystem-heading">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <div class="max-w-2xl"><p class="text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">Ecosystem</p><h2 id="ecosystem-heading" class="mt-3 text-3xl font-semibold tracking-tight">When the next step needs more than a checklist.</h2><p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Relevant resources live around the business workflow. Public pages explain the categories; personalized discovery happens inside the authenticated workspace.</p></div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([['Guides', 'Practical step-by-step execution support.'], ['Opportunities', 'Relevant options matched using deterministic criteria.'], ['Funding', 'Funding opportunities surfaced through the Opportunities workflow.'], ['Experts', 'Human expertise when specialist judgment matters.'], ['Marketplace', 'Relevant services connected to a real business need.'], ['Community', 'Structured peer conversations and review.'], ['Events', 'Events and sessions relevant to entrepreneurs.'], ['Evaluator', 'Start with the business assessment and find the next priority.']] as [$title, $description])
                        <article class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-950"><h3 class="font-semibold">{{ $title }}</h3><p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p><a class="mt-4 inline-flex text-sm font-semibold underline decoration-zinc-300 underline-offset-4 hover:decoration-current" href="{{ route('register') }}">Explore after signing up</a></article>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20" aria-labelledby="control-heading">
            <div class="rounded-2xl border border-zinc-200 p-8 sm:p-10 dark:border-zinc-800"><h2 id="control-heading" class="text-2xl font-semibold tracking-tight">You stay in control.</h2><p class="mt-3 max-w-3xl text-zinc-600 dark:text-zinc-400">Automation and AI can surface useful context, explanations and suggestions. They do not silently decide what your business should do, and deterministic authorization and eligibility remain server-side.</p><div class="mt-6"><a class="text-sm font-semibold underline decoration-zinc-300 underline-offset-4 hover:decoration-current" href="{{ route('public.how-it-works') }}" wire:navigate>Understand the full progression</a></div></div>
        </section>
        <section class="border-t border-zinc-200 dark:border-zinc-800" aria-labelledby="home-cta-heading"><div class="mx-auto max-w-7xl px-4 py-16 text-center sm:px-6 lg:px-8 lg:py-20"><h2 id="home-cta-heading" class="text-3xl font-semibold tracking-tight">Start with a clearer picture of the business.</h2><p class="mx-auto mt-4 max-w-2xl text-zinc-600 dark:text-zinc-400">The evaluator is the entry point. The rest of the product turns what you learn into a progression you can actually follow.</p><a class="mt-7 inline-flex rounded-lg bg-zinc-900 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" href="{{ route('register') }}" data-analytics-event="public.evaluator.cta">Start the free evaluator</a></div></section>
    </div>
</x-layouts.public>
