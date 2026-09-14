<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Data & privacy')" :subheading="__('Export your data or manage your deletion request')">
        @if (session('data_lifecycle_status'))
            <div role="status" class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">{{ session('data_lifecycle_status') }}</div>
        @endif

        <div class="space-y-6">
            <section class="rounded-xl border p-5" aria-labelledby="export-heading">
                <h2 id="export-heading" class="font-semibold">{{ __('Export your data') }}</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('The export contains your profile, business data and data-request history.') }}</p>
                <dl class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-zinc-500">{{ __('Businesses') }}</dt><dd class="font-medium">{{ $this->exportSummary['businesses'] }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Data requests') }}</dt><dd class="font-medium">{{ $this->exportSummary['data_requests'] }}</dd></div>
                </dl>
                <a href="{{ route('account.data.export') }}" class="mt-5 inline-flex rounded-lg border px-4 py-2 text-sm font-medium" download>
                    {{ __('Download export') }}
                </a>
            </section>

            <section class="rounded-xl border border-red-200 p-5 dark:border-red-900" aria-labelledby="deletion-heading">
                <h2 id="deletion-heading" class="font-semibold">{{ __('Delete your account data') }}</h2>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ __('Deletion is a controlled lifecycle request and may require review. Existing retention and authorization rules still apply.') }}</p>

                @if ($this->deletionRequest)
                    <div class="mt-4 rounded-lg bg-zinc-50 p-4 text-sm dark:bg-zinc-800" role="status">
                        <span class="font-medium">{{ __('Current status:') }}</span>
                        {{ str($this->deletionRequest->status)->headline() }}
                        @if ($this->deletionRequest->requested_at)
                            <span class="text-zinc-500"> · {{ $this->deletionRequest->requested_at->diffForHumans() }}</span>
                        @endif
                    </div>
                @else
                    <button type="button" wire:click="$set('confirmDeletion', true)" class="mt-5 rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700">
                        {{ __('Request deletion') }}
                    </button>
                @endif

                @if ($confirmDeletion)
                    <div class="mt-4 rounded-lg border border-red-300 bg-red-50 p-4 dark:bg-red-950/30" role="alert">
                        <p class="text-sm font-medium">{{ __('Confirm deletion request') }}</p>
                        <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300">{{ __('This starts the server-controlled deletion workflow. It does not immediately erase data.') }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button type="button" wire:click="requestDeletion" wire:loading.attr="disabled" class="rounded-lg bg-red-700 px-4 py-2 text-sm font-medium text-white disabled:opacity-50">{{ __('Confirm request') }}</button>
                            <button type="button" wire:click="cancelConfirmation" class="rounded-lg border px-4 py-2 text-sm">{{ __('Cancel') }}</button>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </x-settings.layout>
</section>
