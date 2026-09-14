@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700']) }} role="status">
    <flux:heading size="lg">{{ $title }}</flux:heading>
    @if ($description)
        <flux:text class="mx-auto mt-2 max-w-xl">{{ $description }}</flux:text>
    @endif

    @if (isset($actions))
        <div class="mt-4 flex justify-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
