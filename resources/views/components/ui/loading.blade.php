@props([
    'label' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-3']) }} role="status" aria-live="polite" aria-busy="true">
    @if ($label)
        <flux:text class="sr-only">{{ $label }}</flux:text>
    @endif
    <flux:skeleton class="h-5 w-1/3" />
    <flux:skeleton class="h-4 w-full" />
    <flux:skeleton class="h-4 w-2/3" />
</div>
