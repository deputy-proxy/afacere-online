<div class="mx-auto max-w-6xl space-y-6 p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Monitor</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">A recurring snapshot of the signals that matter in your business.</p>
        </div>
        @if ($this->configuration?->next_check_in_at)
            <div class="rounded-lg border p-3 text-sm" aria-label="Next check-in">
                Next check-in: <strong>{{ $this->configuration->next_check_in_at->format('d M Y, H:i') }}</strong>
            </div>
        @endif
    </div>

    @if (session('monitor_status'))
        <div role="status" class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('monitor_status') }}</div>
    @endif

    <section class="rounded-xl border p-5" aria-labelledby="monitor-settings-heading">
        <h2 id="monitor-settings-heading" class="font-semibold">Monitor settings</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <label class="flex items-center gap-2 text-sm sm:col-span-1">
                <input wire:model="enabled" type="checkbox" class="rounded">
                Enable recurring monitor
            </label>
            <label class="text-sm sm:col-span-2">
                <span class="block font-medium">Cadence</span>
                <select wire:model="cadence" class="mt-1 w-full rounded-lg border px-3 py-2">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </label>
        </div>
        <button type="button" wire:click="saveConfiguration" wire:loading.attr="disabled" class="mt-4 rounded-lg bg-black px-4 py-2 text-sm font-medium text-white disabled:opacity-50">
            <span wire:loading.remove wire:target="saveConfiguration">Save settings</span>
            <span wire:loading wire:target="saveConfiguration">Saving…</span>
        </button>
        @error('cadence') <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p> @enderror
    </section>

    @if ($this->configuration?->enabled)
        <section class="rounded-xl border p-5" aria-labelledby="check-in-heading">
            <h2 id="check-in-heading" class="font-semibold">Check-in</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Keep each answer as a point in time. Monitor never replaces your previous check-ins.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label class="text-sm">Revenue<input wire:model="revenue" type="number" step="0.01" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="This period"></label>
                <label class="text-sm">Customers<input wire:model="customers" type="number" step="1" min="0" class="mt-1 w-full rounded-lg border px-3 py-2"></label>
                <label class="text-sm">Cash confidence (1-5)<input wire:model="cash" type="number" min="1" max="5" step="1" class="mt-1 w-full rounded-lg border px-3 py-2"></label>
                <label class="text-sm">Business confidence (1-5)<input wire:model="confidence" type="number" min="1" max="5" step="1" class="mt-1 w-full rounded-lg border px-3 py-2"></label>
            </div>
            @foreach (['revenue', 'customers', 'cash', 'confidence'] as $field)
                @error($field) <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p> @enderror
            @endforeach
            <button type="button" wire:click="checkIn" wire:loading.attr="disabled" class="mt-4 rounded-lg bg-black px-4 py-2 text-sm font-medium text-white disabled:opacity-50">
                <span wire:loading.remove wire:target="checkIn">Save check-in</span>
                <span wire:loading wire:target="checkIn">Saving…</span>
            </button>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-xl border p-5" aria-labelledby="health-heading">
                <h2 id="health-heading" class="font-semibold">Current health</h2>
                <div class="mt-4 space-y-3" aria-live="polite">
                    @forelse ($this->healthIndicators as $indicator)
                        <div wire:key="health-{{ $indicator->key }}" class="flex items-center justify-between gap-4 rounded-lg bg-gray-50 p-3 text-sm dark:bg-zinc-800">
                            <span>{{ str($indicator->key)->headline() }}</span>
                            <span class="font-medium">{{ str($indicator->status)->headline() }} · {{ $indicator->value }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600 dark:text-gray-400">Complete your first check-in to establish a baseline.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border p-5" aria-labelledby="alerts-heading">
                <h2 id="alerts-heading" class="font-semibold">Open alerts</h2>
                <div class="mt-4 space-y-3" aria-live="polite">
                    @forelse ($this->alerts as $alert)
                        <div wire:key="alert-{{ $alert->id }}" class="rounded-lg border p-3 text-sm" role="alert">
                            <div class="font-medium">{{ str($alert->severity)->headline() }}</div>
                            <div class="mt-1 text-gray-600 dark:text-gray-400">{{ $alert->message }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600 dark:text-gray-400">No open threshold alerts.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-xl border p-5" aria-labelledby="trend-heading">
            <h2 id="trend-heading" class="font-semibold">Trend</h2>
            @if ($this->trends)
                <dl class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($this->trends as $key => $change)
                        <div wire:key="trend-{{ $key }}" class="rounded-lg bg-gray-50 p-3 dark:bg-zinc-800">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">{{ str($key)->headline() }}</dt>
                            <dd class="mt-1 text-lg font-semibold">{{ $change > 0 ? '+' : '' }}{{ $change }}</dd>
                        </div>
                    @endforeach
                </dl>
            @else
                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Record at least two check-ins to see changes over time.</p>
            @endif
        </section>

        <section class="rounded-xl border p-5" aria-labelledby="history-heading">
            <h2 id="history-heading" class="font-semibold">Progress history</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <caption class="sr-only">Monitor check-in history</caption>
                    <thead><tr class="border-b"><th scope="col" class="px-3 py-2">Date</th><th scope="col" class="px-3 py-2">Revenue</th><th scope="col" class="px-3 py-2">Customers</th><th scope="col" class="px-3 py-2">Cash</th><th scope="col" class="px-3 py-2">Confidence</th></tr></thead>
                    <tbody>
                        @forelse ($this->checkIns as $checkIn)
                            <tr wire:key="check-in-{{ $checkIn->id }}" class="border-b last:border-0"><td class="px-3 py-2">{{ $checkIn->recorded_at->format('d M Y') }}</td><td class="px-3 py-2">{{ $checkIn->responses['revenue'] ?? '—' }}</td><td class="px-3 py-2">{{ $checkIn->responses['customers'] ?? '—' }}</td><td class="px-3 py-2">{{ $checkIn->responses['cash'] ?? '—' }}/5</td><td class="px-3 py-2">{{ $checkIn->responses['confidence'] ?? '—' }}/5</td></tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-4 text-gray-600 dark:text-gray-400">No check-ins recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <section class="rounded-xl border border-dashed p-6" aria-labelledby="monitor-empty-heading">
            <h2 id="monitor-empty-heading" class="font-semibold">Monitor is not configured</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Enable recurring monitoring above to start recording business check-ins and health signals.</p>
        </section>
    @endif
</div>
