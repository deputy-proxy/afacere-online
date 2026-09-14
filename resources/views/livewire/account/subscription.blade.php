<div class="space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">Account & subscription</h1>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Your server-authoritative plan, entitlement limits and available features.</p>
    </div>

    @if (session('subscription_status'))
        <div role="status" class="rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('subscription_status') }}</div>
    @endif

    <section class="rounded-xl border p-5" aria-labelledby="current-plan-heading">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 id="current-plan-heading" class="font-semibold">Current subscription</h2>
                @if ($this->current)
                    <p class="mt-1 text-lg font-medium">{{ $this->current->plan?->name }}</p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Status: {{ str($this->current->status)->headline() }}</p>
                @else
                    <p class="mt-1 text-lg font-medium">No active subscription</p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Choose an available plan below. Current entitlements are resolved on the server.</p>
                @endif
            </div>
            <div class="text-sm sm:text-right">
                <div class="font-medium">{{ $this->businessCount }} business{{ $this->businessCount === 1 ? '' : 'es' }}</div>
                <div class="text-zinc-500">{{ $this->businessLimit === null ? 'Unlimited' : $this->businessLimit }} allowed</div>
            </div>
        </div>
    </section>

    <section aria-labelledby="plans-heading">
        <h2 id="plans-heading" class="text-xl font-semibold">Available plans</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            @forelse ($plans as $plan)
                <article wire:key="plan-{{ $plan->id }}" class="rounded-xl border p-5">
                    <h3 class="font-semibold">{{ $plan->name }}</h3>
                    <div class="my-3 text-2xl font-bold">{{ number_format($plan->price_minor / 100, 0) }} {{ $plan->currency }}<span class="text-sm font-normal">/month</span></div>
                    <ul class="space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                        @foreach ((array) $plan->entitlements as $key => $value)
                            <li wire:key="plan-{{ $plan->id }}-entitlement-{{ $key }}">{{ str_replace('_', ' ', ucfirst($key)) }}: {{ $value === 'unlimited' ? 'Unlimited' : ($value ? 'Included' : 'Not included') }}</li>
                        @endforeach
                    </ul>
                    <button type="button" wire:click="selectPlan({{ $plan->id }})" wire:loading.attr="disabled" class="mt-5 rounded-lg border px-4 py-2 text-sm disabled:opacity-50" @disabled($this->current?->product_plan_id === $plan->id)>
                        {{ $this->current?->product_plan_id === $plan->id ? 'Current plan' : 'Select plan' }}
                    </button>
                </article>
            @empty
                <p class="rounded-xl border border-dashed p-5 text-sm text-zinc-600 dark:text-zinc-400">No active plans are currently available.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-xl border p-5" aria-labelledby="billing-heading">
        <h2 id="billing-heading" class="font-semibold">Billing</h2>
        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Payment-provider checkout, payment status and invoices are only shown when the corresponding provider workflow is available. Plan selection above uses the application's subscription service and does not invent provider state.</p>
    </section>

    <div class="flex flex-wrap gap-3 text-sm">
        <a href="{{ route('account.data') }}" wire:navigate class="rounded-lg border px-4 py-2 font-medium">Data & privacy</a>
        <a href="{{ route('profile.edit') }}" wire:navigate class="rounded-lg border px-4 py-2 font-medium">Profile settings</a>
        <a href="{{ route('security.edit') }}" wire:navigate class="rounded-lg border px-4 py-2 font-medium">Security</a>
    </div>
</div>
