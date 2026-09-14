<x-layouts.public title="Pricing | afacere.online" description="Explore the active afacere.online plans and their configured business entitlements.">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <section class="max-w-3xl" aria-labelledby="pricing-heading">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-zinc-500">Pricing</p>
            <h1 id="pricing-heading" class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">Plans that follow the product configuration.</h1>
            <p class="mt-5 text-lg text-zinc-600 dark:text-zinc-400">Available plans are read from the active commercial configuration. The public site does not invent prices, limits or entitlements that are not configured in the application.</p>
        </section>
        @if ($plans->isEmpty())
            <div class="mt-10"><x-ui.empty-state title="No public plans are currently available." description="The commercial configuration does not currently expose an active plan. You can still create an account and explore the product." /></div>
        @else
            <div class="mt-10 grid gap-5 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    @php
                        $entitlements = is_array($plan->entitlements) ? $plan->entitlements : [];
                        $businessLimit = $entitlements['business_limit'] ?? null;
                    @endphp
                    <article class="flex flex-col rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                        <div><p class="text-sm font-semibold text-zinc-500">afacere.online</p><h2 class="mt-2 text-2xl font-semibold">{{ $plan->name }}</h2></div>
                        <p class="mt-5 text-3xl font-semibold tracking-tight">{{ number_format($plan->price_minor / 100, 2, ',', '.') }} {{ strtoupper($plan->currency) }}<span class="text-sm font-normal text-zinc-500"> / month</span></p>
                        <dl class="mt-6 space-y-3 border-t border-zinc-200 pt-6 text-sm dark:border-zinc-800">
                            <div class="flex justify-between gap-4"><dt class="text-zinc-500">Business access</dt><dd class="font-medium text-right">{{ $businessLimit === 'unlimited' ? 'Unlimited' : ($businessLimit ?? 'Configured') }}</dd></div>
                            @foreach ($entitlements as $key => $value)
                                @continue($key === 'business_limit')
                                <div class="flex justify-between gap-4"><dt class="text-zinc-500">{{ str($key)->replace('_', ' ')->title() }}</dt><dd class="font-medium text-right">{{ is_bool($value) ? ($value ? 'Included' : 'Not included') : $value }}</dd></div>
                            @endforeach
                        </dl>
                        <div class="mt-auto pt-8"><a class="inline-flex w-full justify-center rounded-lg bg-zinc-900 px-4 py-3 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" href="{{ route('register') }}" data-analytics-event="public.pricing.cta">Start with this product</a></div>
                    </article>
                @endforeach
            </div>
        @endif
        <p class="mt-8 text-sm text-zinc-500 dark:text-zinc-400">Plan availability and entitlements are enforced by the application. Payment processing remains behind the existing provider-neutral billing boundary.</p>
    </div>
</x-layouts.public>
