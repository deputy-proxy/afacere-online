<div class="flex w-full flex-col gap-8 py-6">
    @if ($this->business)
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm text-zinc-500">{{ __('Your business') }}</p>
                <h1 class="text-3xl font-semibold tracking-tight">{{ $this->business->name }}</h1>
                <p class="mt-1 text-zinc-600 dark:text-zinc-400">{{ __('What should I do next?') }}</p>
            </div>

            @if (count($this->businesses) > 1)
                <div class="flex items-center gap-2">
                    <label for="business-switcher" class="sr-only">{{ __('Business') }}</label>
                    <select id="business-switcher" wire:model.live="businessId" wire:change="switchBusiness" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800">
                        @foreach ($this->businesses as $business)
                            <option value="{{ $business->id }}">{{ $business->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500">{{ __('Current stage') }}</p>
                <p class="mt-2 text-lg font-medium">{{ $this->business->stage->value }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500">{{ __('Evaluation') }}</p>
                <p class="mt-2 text-lg font-medium">{{ $this->business->evaluations()->latest()->exists() ? __('Started') : __('Not started') }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500">{{ __('Next action') }}</p>
                <p class="mt-2 text-lg font-medium">{{ __('Complete your evaluation') }}</p>
            </div>
            <a href="{{ route('business.opportunities') }}" wire:navigate class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500">{{ __('Opportunities') }}</p>
                <p class="mt-2 text-lg font-medium">{{ __('See current matches') }} →</p>
            </a>
        </div>

        <livewire:business.recommendations />
    @endif
</div>
