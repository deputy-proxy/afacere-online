<header {{ $attributes->merge(['class' => 'flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0 break-words">
        <flux:heading level="1" size="xl" class="break-words">{{ $title }}</flux:heading>
        @isset($description)
            <flux:subheading class="mt-1">{{ $description }}</flux:subheading>
        @endisset
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</header>