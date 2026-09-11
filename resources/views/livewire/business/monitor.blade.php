<div class="mx-auto max-w-6xl space-y-6 p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">Monitor</h1>
            <p class="mt-1 text-sm text-gray-600">A recurring snapshot of the signals that matter in your business.</p>
        </div>
        @if ($this->configuration?->next_check_in_at)
            <div class="rounded-lg border p-3 text-sm">
                Next check-in: <strong>{{ $this->configuration->next_check_in_at->format('d M Y, H:i') }}</strong>
            </div>
        @endif
    </div>

    @if (session('monitor_status'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('monitor_status') }}</div>
    @endif

    <section class="rounded-xl border p-5">
        <h2 class="font-semibold">Monitor settings</h2>
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
        <button wire:click="saveConfiguration" class="mt-4 rounded-lg bg-black px-4 py-2 text-sm font-medium text-white">Save settings</button>
    </section>

    @if ($this->configuration?->enabled)
        <section class="rounded-xl border p-5">
            <h2 class="font-semibold">Weekly check-in</h2>
            <p class="mt-1 text-sm text-gray-600">Keep each answer as a point in time. Monitor never replaces your previous check-ins.</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label class="text-sm">Revenue<input wire:model="revenue" type="number" step="0.01" class="mt-1 w-full rounded-lg border px-3 py-2" placeholder="This period"></label>
                <label class="text-sm">Customers<input wire:model="customers" type="number" step="1" min="0" class="mt-1 w-full rounded-lg border px-3 py-2"></label>
                <label class="text-sm">Cash confidence (1-5)<input wire:model="cash" type="number" min="1" max="5" step="1" class="mt-1 w-full rounded-lg border px-3 py-2"></label>
                <label class="text-sm">Business confidence (1-5)<input wire:model="confidence" type="number" min="1" max="5" step="1" class="mt-1 w-full rounded-lg border px-3 py-2"></label>
            </div>
            <button wire:click="checkIn" class="mt-4 rounded-lg bg-black px-4 py-2 text-sm font-medium text-white">Save check-in</button>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-xl border p-5">
                <h2 class="font-semibold">Current health</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($this->healthIndicators as $indicator)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3 text-sm">
                            <span>{{ str($indicator->key)->headline() }}</span>
                            <span class="font-medium">{{ str($indicator->status)->headline() }} · {{ $indicator->value }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">Complete your first check-in to establish a baseline.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border p-5">
                <h2 class="font-semibold">Open alerts</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($this->alerts as $alert)
                        <div class="rounded-lg border p-3 text-sm">
                            <div class="font-medium">{{ str($alert->severity)->headline() }}</div>
                            <div class="mt-1 text-gray-600">{{ $alert->message }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">No open threshold alerts.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-xl border p-5">
            <h2 class="font-semibold">Progress history</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead><tr class="border-b"><th class="px-3 py-2">Date</th><th class="px-3 py-2">Revenue</th><th class="px-3 py-2">Customers</th><th class="px-3 py-2">Cash</th><th class="px-3 py-2">Confidence</th></tr></thead>
                    <tbody>
                        @forelse ($this->checkIns as $checkIn)
                            <tr class="border-b last:border-0"><td class="px-3 py-2">{{ $checkIn->recorded_at->format('d M Y') }}</td><td class="px-3 py-2">{{ $checkIn->responses['revenue'] ?? '—' }}</td><td class="px-3 py-2">{{ $checkIn->responses['customers'] ?? '—' }}</td><td class="px-3 py-2">{{ $checkIn->responses['cash'] ?? '—' }}/5</td><td class="px-3 py-2">{{ $checkIn->responses['confidence'] ?? '—' }}/5</td></tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-4 text-gray-600">No check-ins recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>
