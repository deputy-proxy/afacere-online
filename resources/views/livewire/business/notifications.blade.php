<div class="flex w-full flex-col gap-6 py-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm text-zinc-500">{{ __('Activity that needs your attention') }}</p>
            <h1 class="text-3xl font-semibold tracking-tight">{{ __('Notifications') }}</h1>
        </div>
        <span class="rounded-full bg-zinc-100 px-3 py-1 text-sm dark:bg-zinc-800">{{ $this->unread }} {{ __('unread') }}</span>
    </div>

    @forelse ($this->notifications as $notification)
        <article class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900 {{ $notification->read_at === null ? 'ring-1 ring-zinc-300 dark:ring-zinc-600' : '' }}">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-semibold">{{ $notification->title }}</h2>
                    <p class="mt-1 text-zinc-600 dark:text-zinc-400">{{ $notification->body }}</p>
                    <p class="mt-2 text-xs text-zinc-500">{{ $notification->created_at?->diffForHumans() }}</p>
                </div>
                @if ($notification->read_at === null)
                    <button type="button" wire:click="markRead({{ $notification->id }})" class="text-sm underline">{{ __('Mark read') }}</button>
                @else
                    <button type="button" wire:click="markUnread({{ $notification->id }})" class="text-sm underline">{{ __('Mark unread') }}</button>
                @endif
            </div>
            @if (is_string($notification->data['url'] ?? null))
                <a href="{{ $notification->data['url'] }}" wire:navigate class="mt-4 inline-block text-sm font-medium underline">{{ __('Open') }} →</a>
            @endif
        </article>
    @empty
        <div class="rounded-xl border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
            <p class="font-medium">{{ __('You are all caught up.') }}</p>
            <p class="mt-1 text-sm text-zinc-500">{{ __('Important business updates will appear here.') }}</p>
        </div>
    @endforelse
</div>
