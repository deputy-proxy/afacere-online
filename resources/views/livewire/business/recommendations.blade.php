<div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
    <div class="flex items-start justify-between gap-4">
        <div><p class="text-sm text-zinc-500">{{ __('Recommended next steps') }}</p><h2 class="mt-1 text-lg font-semibold">{{ __('Recommendations') }}</h2></div>
        <span class="text-xs text-zinc-500">{{ __('You decide') }}</span>
    </div>
    @forelse ($this->recommendations as $recommendation)
        @php($context = $presentation->context($recommendation))
        <article class="mt-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
            <h3 class="font-medium">{{ $recommendation->title }}</h3>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $context['reason'] }}</p>
            <p class="mt-2 text-sm">{{ __('Expected outcome') }}: {{ $context['expected_outcome'] }}</p>
            <p class="mt-1 text-xs text-zinc-500">{{ __('Confidence') }}: {{ number_format($context['confidence'] * 100, 0) }}% · {{ __('Source') }}: {{ $context['source'] }}</p>
            <div class="mt-3 flex gap-2">
                <button type="button" wire:click="accept({{ $recommendation->id }})" class="rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white">{{ __('Accept') }}</button>
                <button type="button" wire:click="reject({{ $recommendation->id }})" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium dark:border-zinc-600">{{ __('Reject') }}</button>
            </div>
        </article>
    @empty
        <p class="mt-4 text-sm text-zinc-500">{{ __('No new recommendations right now. Continue your action plan or monitor your business for the next useful signal.') }}</p>
    @endforelse
</div>
