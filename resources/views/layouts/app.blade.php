<x-layouts::app.sidebar :title="$title ?? null">
    <x-ui.livewire-feedback />
    <x-ui.livewire-resilience-script />
    <flux:main class="min-w-0">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
