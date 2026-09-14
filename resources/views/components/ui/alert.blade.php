@props([
    'variant' => 'info',
])

@php
    $variants = [
        'info' => 'info',
        'success' => 'success',
        'warning' => 'warning',
        'danger' => 'danger',
    ];
@endphp

<flux:callout :variant="$variants[$variant] ?? 'info'" {{ $attributes }} role="status">
    {{ $slot }}
</flux:callout>
