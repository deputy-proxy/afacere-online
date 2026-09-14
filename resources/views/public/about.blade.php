<x-layouts.public title="About | afacere.online">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <section class="max-w-3xl" aria-labelledby="about-heading">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">About</p>
            <h1 id="about-heading" class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">A calmer way to work on the business.</h1>
            <p class="mt-5 text-lg leading-8 text-zinc-600 dark:text-zinc-400">afacere.online is built around a simple idea: entrepreneurs need clarity about what matters now, not another system that creates work merely to prove it is working.</p>
        </section>
        <section class="mt-12" aria-labelledby="principles-heading">
            <h2 id="principles-heading" class="text-2xl font-semibold">The principles behind the product</h2>
            <div class="mt-6 grid gap-5 md:grid-cols-3">
                @foreach ([['Action over information', 'Useful insight should lead to a decision or a practical next step.'], ['Business context first', 'Recommendations should make sense for the stage, goals and history of the business.'], ['Human judgment where it matters', 'Automation and AI can assist. The entrepreneur remains responsible for the decision.']] as [$title, $description])
                    <article class="rounded-2xl border border-zinc-200 p-6 dark:border-zinc-800"><h3 class="font-semibold">{{ $title }}</h3><p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $description }}</p></article>
                @endforeach
            </div>
        </section>
        <section class="mt-12 max-w-3xl rounded-2xl border border-zinc-200 p-8 dark:border-zinc-800" aria-labelledby="history-heading"><h2 id="history-heading" class="text-2xl font-semibold">Progress, not performance theatre</h2><p class="mt-3 text-zinc-600 dark:text-zinc-400">The product is designed around a progression from current state to diagnosis, priority, action, outcome and a new state. Historical evaluations and important business decisions remain traceable where the domain requires it.</p></section>
    </div>
</x-layouts.public>
