<div class="mx-auto max-w-6xl space-y-8">
    <header class="space-y-3">
        <p class="text-sm text-zinc-500">{{ $this->business?->name }}</p>
        <h1 class="text-3xl font-semibold tracking-tight">Ecosystem</h1>
        <p class="max-w-3xl text-sm text-zinc-600">Find verified experts, useful providers, peer feedback and relevant events without leaving your business workspace.</p>
    </header>

    @if (session('ecosystem_success'))
        <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('ecosystem_success') }}
        </div>
    @endif

    <nav class="flex flex-wrap gap-2" aria-label="Ecosystem sections">
        @foreach ([
            'all' => 'Overview',
            'experts' => 'Experts',
            'marketplace' => 'Marketplace',
            'community' => 'Community',
            'events' => 'Events',
        ] as $key => $label)
            <button type="button" wire:click="showSection('{{ $key }}')" class="rounded-lg border px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-zinc-500 {{ $section === $key ? 'border-zinc-900 bg-zinc-900 text-white' : 'border-zinc-200 bg-white text-zinc-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </nav>

    <section class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm" aria-labelledby="ecosystem-search-heading">
        <div class="space-y-1">
            <h2 id="ecosystem-search-heading" class="font-medium">Search the ecosystem</h2>
            <p class="text-sm text-zinc-500">Searches verified experts, published marketplace providers, community posts and upcoming events.</p>
        </div>
        <label for="ecosystem-search" class="mt-4 block text-sm font-medium text-zinc-700">Search</label>
        <input id="ecosystem-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Topic, expertise, provider or event" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm focus:border-zinc-500 focus:ring-zinc-500" />
        @if ($this->results->isNotEmpty())
            <ul class="mt-4 divide-y divide-zinc-100" aria-label="Existing product search results">
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
        @endif
    </section>

    @if ($section === 'all' || $section === 'experts')
        <section class="space-y-4" aria-labelledby="experts-heading">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 id="experts-heading" class="text-xl font-semibold">Experts</h2>
                    <p class="text-sm text-zinc-500">Verified profiles with bookable availability where supported.</p>
                </div>
            </div>
            @if ($this->experts->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-600">No verified experts match this search.</div>
            @else
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->experts as $expert)
                        <article class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-medium">{{ $expert->title }}</h3>
                                    <p class="text-sm text-zinc-500">{{ $expert->user->name }}</p>
                                </div>
                                <span class="rounded-full border border-emerald-200 px-2 py-1 text-xs text-emerald-700">Verified</span>
                            </div>
                            @if ($expert->bio)
                                <p class="mt-3 text-sm text-zinc-600">{{ $expert->bio }}</p>
                            @endif
                            @if ($selectedExpertId === $expert->id)
                                <div class="mt-4 border-t border-zinc-100 pt-4">
                                    <label for="availability" class="block text-sm font-medium">Available sessions</label>
                                    <select id="availability" wire:model="selectedAvailabilityId" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm">
                                        <option value="">Select a time</option>
                                        @foreach ($this->expertAvailabilities as $availability)
                                            <option value="{{ $availability->id }}">{{ $availability->starts_at->format('d M Y, H:i') }} - {{ $availability->ends_at->format('H:i') }}</option>
                                        @endforeach
                                    </select>
                                    @if ($this->expertAvailabilities->isEmpty())
                                        <p class="mt-2 text-sm text-zinc-500">No bookable sessions are currently available.</p>
                                    @endif
                                    <label for="consultation-note" class="mt-3 block text-sm font-medium">Request note</label>
                                    <textarea id="consultation-note" wire:model="consultationNote" rows="3" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm" placeholder="What would you like help with?"></textarea>
                                    @error('selectedAvailabilityId') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                    @error('consultationNote') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                    <button type="button" wire:click="requestConsultation" class="mt-3 w-full rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-zinc-500">Request consultation</button>
                                </div>
                            @else
                                <button type="button" wire:click="selectExpert({{ $expert->id }})" class="mt-4 rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-zinc-500">View availability</button>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @if ($section === 'all' || $section === 'marketplace')
        <section class="space-y-4" aria-labelledby="marketplace-heading">
            <div>
                <h2 id="marketplace-heading" class="text-xl font-semibold">Marketplace</h2>
                <p class="text-sm text-zinc-500">Verified providers and their published services. Relevance is not replaced by paid visibility.</p>
            </div>
            @if ($this->providers->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-600">No verified providers with published services match this search.</div>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($this->providers as $provider)
                        <article class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-medium">{{ $provider->name }}</h3>
                                    @if ($provider->description)
                                        <p class="mt-1 text-sm text-zinc-600">{{ $provider->description }}</p>
                                    @endif
                                </div>
                                <span class="rounded-full border border-emerald-200 px-2 py-1 text-xs text-emerald-700">Verified</span>
                            </div>
                            <div class="mt-4 space-y-2">
                                @foreach ($provider->services->where('is_published', true) as $service)
                                    <div class="rounded-lg border border-zinc-100 p-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="font-medium text-sm">{{ $service->name }}</p>
                                            @if ($service->price)
                                                <span class="text-xs text-zinc-500">{{ number_format((float) $service->price, 2) }}</span>
                                            @endif
                                        </div>
                                        @if ($selectedProviderId === $provider->id && $selectedServiceId === $service->id)
                                            <label for="lead-message-{{ $service->id }}" class="mt-3 block text-sm font-medium">Message</label>
                                            <textarea id="lead-message-{{ $service->id }}" wire:model="leadMessage" rows="3" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm" placeholder="Tell the provider what you need."></textarea>
                                            @error('leadMessage') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                                            <button type="button" wire:click="createMarketplaceLead" class="mt-2 rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-zinc-500">Contact provider</button>
                                        @else
                                            <button type="button" wire:click="selectProvider({{ $provider->id }}); $wire.set('selectedServiceId', {{ $service->id }})" class="mt-2 text-sm font-medium underline">Request this service</button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @if ($section === 'all' || $section === 'community')
        <section class="space-y-4" aria-labelledby="community-heading">
            <div>
                <h2 id="community-heading" class="text-xl font-semibold">Community & peer review</h2>
                <p class="text-sm text-zinc-500">Published conversations remain subject to visibility and moderation rules.</p>
            </div>
            <form wire:submit="createPost" class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                <h3 class="font-medium">Start a conversation</h3>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label for="post-title" class="block text-sm font-medium">Title</label>
                        <input id="post-title" wire:model="postTitle" type="text" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm" />
                        @error('postTitle') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="post-body" class="block text-sm font-medium">Question or insight</label>
                        <textarea id="post-body" wire:model="postBody" rows="4" class="mt-1 block w-full rounded-lg border-zinc-300 text-sm"></textarea>
                        @error('postBody') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                </div>
                <button type="submit" class="mt-4 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-zinc-500">Publish post</button>
            </form>
            @if ($this->communityPosts->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-600">No public community conversations match this search.</div>
            @else
                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach ($this->communityPosts as $post)
                        <article class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-medium">{{ $post->title }}</h3>
                                    <p class="mt-1 text-sm text-zinc-600">{{ str($post->body)->limit(300) }}</p>
                                </div>
                                <span class="text-xs text-zinc-500">{{ ucfirst($post->visibility) }}</span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-3">
                                <button type="button" wire:click="$set('selectedPostId', {{ $post->id }})" class="text-sm font-medium underline">Request peer review</button>
                                @if ($post->user_id !== auth()->id())
                                    <button type="button" wire:click="$set('selectedPostId', {{ $post->id }})" class="text-sm font-medium underline">Report</button>
                                @endif
                            </div>
                            @if ($selectedPostId === $post->id)
                                <div class="mt-4 space-y-3 border-t border-zinc-100 pt-4">
                                    @if ($post->user_id === auth()->id())
                                        <label for="review-visibility" class="block text-sm font-medium">Peer review visibility</label>
                                        <select id="review-visibility" wire:model="peerReviewVisibility" class="block w-full rounded-lg border-zinc-300 text-sm">
                                            <option value="community">Community</option>
                                            <option value="selected">Selected reviewers</option>
                                            <option value="anonymized">Anonymized</option>
                                            <option value="private">Private</option>
                                        </select>
                                        <button type="button" wire:click="requestPeerReview" class="rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white">Request peer review</button>
                                    @else
                                        <label for="report-reason" class="block text-sm font-medium">Report reason</label>
                                        <textarea id="report-reason" wire:model="reportReason" rows="3" class="block w-full rounded-lg border-zinc-300 text-sm" placeholder="Why should this post be reviewed?"></textarea>
                                        <button type="button" wire:click="reportPost({{ $post->id }})" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium">Submit report</button>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif

            <div class="space-y-3">
                <h3 class="text-lg font-semibold">Open peer reviews</h3>
                @forelse ($this->peerReviews as $review)
                    <article class="rounded-xl border border-zinc-200 bg-white p-4">
                        <p class="font-medium">Peer review request #{{ $review->id }}</p>
                        <p class="mt-1 text-sm text-zinc-500">Visibility: {{ ucfirst($review->visibility) }}</p>
                        @if ($review->requester_id !== auth()->id())
                            <textarea wire:model="reviewBody" rows="3" class="mt-3 block w-full rounded-lg border-zinc-300 text-sm" placeholder="Share constructive feedback."></textarea>
                            <button type="button" wire:click="respondToPeerReview({{ $review->id }})" class="mt-2 rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white">Respond</button>
                        @else
                            <p class="mt-3 text-sm text-zinc-600">You requested this review. Another community member can respond.</p>
                        @endif
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-600">There are no open peer-review requests.</div>
                @endforelse
            </div>
        </section>
    @endif

    @if ($section === 'all' || $section === 'events')
        <section class="space-y-4" aria-labelledby="events-heading">
            <div>
                <h2 id="events-heading" class="text-xl font-semibold">Events</h2>
                <p class="text-sm text-zinc-500">Upcoming published events with server-authoritative registration state.</p>
            </div>
            @if ($this->events->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-300 p-6 text-sm text-zinc-600">No upcoming events match this search.</div>
            @else
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->events as $event)
                        <article class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-medium">{{ $event->title }}</h3>
                                @if ($event->is_online)
                                    <span class="text-xs text-zinc-500">Online</span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-zinc-500">{{ $event->starts_at->format('d M Y, H:i') }}</p>
                            @if ($event->description)
                                <p class="mt-3 text-sm text-zinc-600">{{ str($event->description)->limit(240) }}</p>
                            @endif
                            @if ($event->capacity !== null)
                                <p class="mt-3 text-xs text-zinc-500">Capacity: {{ $event->capacity }}</p>
                            @endif
                            @if ($eventRegistrationIds->has($event->id))
                                <button type="button" wire:click="cancelEventRegistration({{ $eventRegistrationIds[$event->id] }})" class="mt-4 rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-zinc-500">Cancel registration</button>
                            @else
                                <button type="button" wire:click="registerForEvent({{ $event->id }})" class="mt-4 rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-zinc-500">Register</button>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</div>
