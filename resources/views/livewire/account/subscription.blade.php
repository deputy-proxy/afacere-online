<div class="space-y-8 p-6">
    <div>
        <h1 class="text-2xl font-semibold">Account & subscription</h1>
        <p class="mt-1 text-sm text-zinc-600">Your plan, business limit and available features.</p>
    </div>
    @if (session('subscription_status')) <div class="rounded-lg border p-3 text-sm">{{ session('subscription_status') }}</div> @endif
    @if ($this->current)
        <div class="rounded-xl border p-5"><div class="font-medium">Current plan: {{ $this->current->plan?->name }}</div><div class="mt-1 text-sm text-zinc-600">{{ $this->businessCount }} business{{ $this->businessCount === 1 ? '' : 'es' }} of {{ $this->businessLimit ?? 'unlimited' }} allowed.</div></div>
    @else
        <div class="rounded-xl border p-5"><div class="font-medium">Free plan</div><div class="mt-1 text-sm text-zinc-600">1 business included. Upgrade when you need more capacity and Monitor features.</div></div>
    @endif
    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($plans as $plan)
            <div class="rounded-xl border p-5">
                <h2 class="font-semibold">{{ $plan->name }}</h2>
                <div class="my-3 text-2xl font-bold">€{{ number_format($plan->price_minor / 100, 0) }}<span class="text-sm font-normal">/month</span></div>
                <ul class="space-y-1 text-sm text-zinc-600">
                    @foreach ((array) $plan->entitlements as $key => $value)<li>{{ str_replace('_', ' ', ucfirst($key)) }}: {{ $value === 'unlimited' ? 'Unlimited' : ($value ? 'Included' : 'Not included') }}</li>@endforeach
                </ul>
                <button wire:click="selectPlan({{ $plan->id }})" class="mt-5 rounded-lg border px-4 py-2 text-sm">{{ $this->current?->product_plan_id === $plan->id ? 'Current plan' : 'Select plan' }}</button>
            </div>
        @endforeach
    </div>
    <p class="text-xs text-zinc-500">Plan selection is isolated behind the subscription service so a payment provider can be integrated without leaking provider-specific logic into the application.</p>
</div>
