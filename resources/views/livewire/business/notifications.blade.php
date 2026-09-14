<div class="flex w-full flex-col gap-8 py-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm text-zinc-500">{{ __('Activity that needs your attention') }}</p>
            <h1 class="text-3xl font-semibold tracking-tight">{{ __('Notifications & activity') }}</h1>
        </div>
        <span class="rounded-full bg-zinc-100 px-3 py-1 text-sm dark:bg-zinc-800" aria-label="{{ $this->unread }} unread notifications">{{ $this->unread }} {{ __('unread') }}</span>
    </div>

    <section class="space-y-3" aria-labelledby="notifications-heading">
        <h2 id="notifications-heading" class="text-xl font-semibold">{{ __('Notifications') }}</h2>
        @forelse ($this->notifications as $notification)
            <article wire:key="notification-{{ $notification->id }}" class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900 {{ $notification->read_at === null ? 'ring-1 ring-zinc-300 dark:ring-zinc-600' : '' }}" aria-label="{{ $notification->read_at === null ? __('Unread notification') : __('Read notification') }}">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="font-semibold">{{ $notification->title }}</h3>
                        <p class="mt-1 text-zinc-600 dark:text-zinc-400">{{ $notification->body }}</p>
                        <p class="mt-2 text-xs text-zinc-500">{{ $notification->created_at?->diffForHumans() }}</p>
                    </div>
                    @if ($notification->read_at === null)
                        <button type="button" wire:click="markRead({{ $notification->id }})" wire:loading.attr="disabled" class="shrink-0 text-left text-sm underline disabled:opacity-50" aria-label="{{ __('Mark notification as read') }}">{{ __('Mark read') }}</button>
                    @else
                        <button type="button" wire:click="markUnread({{ $notification->id }})" wire:loading.attr="disabled" class="shrink-0 text-left text-sm underline disabled:opacity-50" aria-label="{{ __('Mark notification as unread') }}">{{ __('Mark unread') }}</button>
                    @endif
                </div>
                @if (is_string($notification->data['url'] ?? null))
                    <a href="{{ $notification->data['url'] }}" wire:navigate class="mt-4 inline-block text-sm font-medium underline">{{ __('Open notification') }} <span aria-hidden="true">→</span></a>
                @endif
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-center dark:border-zinc-700" role="status">{{ __('You are all caught up.') }}</div>
        @endforelse
    </section>

    <section class="space-y-3" aria-labelledby="activity-heading">
        <h2 id="activity-heading" class="text-xl font-semibold">{{ __('Recent activity') }}</h2>
        @forelse ($this->activity as $event)
            <article wire:key="activity-{{ $event->id }}" class="rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="font-medium">{{ str_replace('.', ' · ', $event->type) }}</p>
                <p class="mt-1 text-sm text-zinc-500">{{ $event->occurred_at?->diffForHumans() }}</p>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-center dark:border-zinc-700">{{ __('Your recent progress will appear here.') }}</div>
        @endforelse
    </section>
</div>
