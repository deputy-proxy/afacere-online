<x-layouts::app.sidebar :title="$title ?? null">
    <x-ui.livewire-feedback />
    <x-ui.livewire-resilience-script />
    {{ $slot }}
</x-layouts::app.sidebar>