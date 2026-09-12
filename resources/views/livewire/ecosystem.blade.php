<div class="mx-auto max-w-6xl space-y-8">
    <header class="space-y-2">
        <p class="text-sm text-zinc-500">{{ $this->business?->name }}</p>
        <h1 class="text-2xl font-semibold">Ecosystem</h1>
        <p class="max-w-3xl text-sm text-zinc-600">Find experts, providers, peer conversations and events that can help with the work your business needs now.</p>
    </header>

    <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm" aria-labelledby="search-heading">
        <h2 id="search-heading" class="font-medium">Search</h2>
        <label for="ecosystem-search" class="mt-3 block text-sm font-medium text-zinc-700">Search guides and opportunities</label>
        <input id="ecosystem-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Search by topic, need or title" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm" />
        @if ($this->results->isNotEmpty())
            <ul class="mt-4 divide-y divide-zinc-100" aria-label="Search results">
                @foreach ($this->results as $result)
                    <li class="py-3">
                        @if ($result['type'] === 'guide')
                            <a href="{{ route('business.guides') }}" wire:navigate class="font-medium underline">{{ $result['title'] }}</a>
                            <span class="ml-2 text-xs text-zinc-500">Guide</span>
                        @else
                            <a href="{{ route('business.opportunities.show', $result['id']) }}" wire:navigate class="font-medium underline">{{ $result['title'] }}</a>
                            <span class="ml-2 text-xs text-zinc-500">Opportunity</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @elseif (trim($search) !== '')
            <p class="mt-3 text-sm text-zinc-600">No matching published guides or opportunities were found.</p>
        @endif
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm" aria-labelledby="experts-heading">
            <div class="flex items-baseline justify-between gap-3">
                <h2 id="experts-heading" class="font-medium">Experts</h2>
                <span class="text-xs text-zinc-500">Verified profiles</span>
            </div>
            @if ($this->experts->isEmpty())
                <p class="mt-3 text-sm text-zinc-600">No verified experts are currently available.</p>
            @else
                <ul class="mt-4 space-y-4">
                    @foreach ($this->experts as $expert)
                        <li class="border-t border-zinc-100 pt-4 first:border-0 first:pt-0">
                            <p class="font-medium">{{ $expert->title }}</p>
                            <p class="text-sm text-zinc-500">{{ $expert->user->name }}</p>
                            @if ($expert->bio)
                                <p class="mt-1 text-sm text-zinc-600">{{ $expert->bio }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm" aria-labelledby="providers-heading">
            <div class="flex items-baseline justify-between gap-3">
                <h2 id="providers-heading" class="font-medium">Marketplace providers</h2>
                <span class="text-xs text-zinc-500">Verified and published</span>
            </div>
            @if ($this->providers->isEmpty())
                <p class="mt-3 text-sm text-zinc-600">No verified providers with published services are currently available.</p>
            @else
                <ul class="mt-4 space-y-4">
                    @foreach ($this->providers as $provider)
                        @php($publishedServices = $provider->services->where('is_published', true))
                        <li class="border-t border-zinc-100 pt-4 first:border-0 first:pt-0">
                            <p class="font-medium">{{ $provider->name }}</p>
                            @if ($provider->description)
                                <p class="mt-1 text-sm text-zinc-600">{{ $provider->description }}</p>
                            @endif
                            <p class="mt-2 text-xs text-zinc-500">{{ $publishedServices->count() }} published {{ str('service')->plural($publishedServices->count()) }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm" aria-labelledby="community-heading">
            <div class="flex items-baseline justify-between gap-3">
                <h2 id="community-heading" class="font-medium">Community</h2>
                <span class="text-xs text-zinc-500">Published conversations</span>
            </div>
            @if ($this->communityPosts->isEmpty())
                <p class="mt-3 text-sm text-zinc-600">No public community conversations are currently available.</p>
            @else
                <ul class="mt-4 space-y-4">
                    @foreach ($this->communityPosts as $post)
                        <li class="border-t border-zinc-100 pt-4 first:border-0 first:pt-0">
                            <p class="font-medium">{{ $post->title }}</p>
                            <p class="mt-1 text-sm text-zinc-600">{{ str($post->body)->limit(180) }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm" aria-labelledby="events-heading">
            <div class="flex items-baseline justify-between gap-3">
                <h2 id="events-heading" class="font-medium">Events</h2>
                <span class="text-xs text-zinc-500">Upcoming</span>
            </div>
            @if ($this->events->isEmpty())
                <p class="mt-3 text-sm text-zinc-600">No upcoming events are currently published.</p>
            @else
                <ul class="mt-4 space-y-4">
                    @foreach ($this->events as $event)
                        <li class="border-t border-zinc-100 pt-4 first:border-0 first:pt-0">
                            <p class="font-medium">{{ $event->title }}</p>
                            <p class="mt-1 text-sm text-zinc-500">{{ $event->starts_at->format('d M Y, H:i') }}</p>
                            @if ($event->description)
                                <p class="mt-1 text-sm text-zinc-600">{{ str($event->description)->limit(180) }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</div>
