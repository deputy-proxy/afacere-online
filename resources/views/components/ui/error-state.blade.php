@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-red-200 p-6 dark:border-red-900/50']) }} role="alert">
    <flux:heading size="lg">{{ $title }}</flux:heading>
    @if ($description)
        <flux:text class="mt-2">{{ $description }}</flux:text>
    @endif

    @if (isset($actions))
        <div class="mt-4 flex flex-wrap gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
