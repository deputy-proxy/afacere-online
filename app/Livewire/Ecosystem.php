<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Business;
use App\Models\CommunityPost;
use App\Models\Expert;
use App\Models\ExpertAvailability;
use App\Models\MarketplaceProvider;
use App\Models\MarketplaceService;
use App\Models\PeerReview;
use App\Models\PlatformEvent;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\EventRegistrationService;
use App\Services\ExpertConsultationService;
use App\Services\MarketplaceServiceLayer;
use App\Services\ModerationReportService;
use App\Services\PeerReviewService;
use App\Services\UnifiedSearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Ecosystem')]
final class Ecosystem extends Component
{
    public string $search = '';
    public string $section = 'all';
    public ?int $selectedExpertId = null;
    public ?int $selectedProviderId = null;
    public ?int $selectedServiceId = null;
    public ?int $selectedEventId = null;
    public ?int $selectedPostId = null;
    public ?int $selectedAvailabilityId = null;
    public string $consultationNote = '';
    public string $leadMessage = '';
    public string $postTitle = '';
    public string $postBody = '';
    public string $peerReviewVisibility = 'community';
    public string $reviewBody = '';
    public string $reportReason = '';

    public function mount(BusinessContextService $businessContext): void
    {
        abort_unless($businessContext->current($this->user()) !== null, 404);
    }

    #[Computed]
    public function business(): ?Business
    {
        return app(BusinessContextService::class)->current($this->user());
    }

    /** @return Collection<int, Expert> */
    #[Computed]
    public function experts(): Collection
    {
        return app(ExpertConsultationService::class)->discover(trim($this->search) !== '' ? trim($this->search) : null)->take(12);
    }

    #[Computed]
    public function selectedExpert(): ?Expert
    {
        if ($this->selectedExpertId === null) {
            return null;
        }

        return Expert::query()->where('verification_status', 'verified')->with('user:id,name')->find($this->selectedExpertId);
    }

    /** @return Collection<int, ExpertAvailability> */
    #[Computed]
    public function expertAvailabilities(): Collection
    {
        if ($this->selectedExpert === null) {
            return new Collection;
        }

        return $this->selectedExpert->availabilities()->where('is_bookable', true)->where('starts_at', '>=', now())->orderBy('starts_at')->limit(8)->get();
    }

    /** @return Collection<int, MarketplaceProvider> */
    #[Computed]
    public function providers(): Collection
    {
        return app(MarketplaceServiceLayer::class)->discover($this->businessOrFail(), trim($this->search))->take(12);
    }

    #[Computed]
    public function selectedProvider(): ?MarketplaceProvider
    {
        if ($this->selectedProviderId === null) {
            return null;
        }

        return MarketplaceProvider::query()->where('verification_status', 'verified')->with(['services' => fn (Builder $query): Builder => $query->where('is_published', true)])->find($this->selectedProviderId);
    }

    #[Computed]
    public function selectedService(): ?MarketplaceService
    {
        if ($this->selectedProvider === null || $this->selectedServiceId === null) {
            return null;
        }

        return $this->selectedProvider->services->firstWhere('id', $this->selectedServiceId);
    }

    /** @return Collection<int, CommunityPost> */
    #[Computed]
    public function communityPosts(): Collection
    {
        return CommunityPost::query()->where('status', 'published')->whereIn('visibility', ['community', 'anonymized'])->when(trim($this->search) !== '', function (Builder $query): void {
            $term = trim($this->search);
            $query->where(function (Builder $query) use ($term): void {
                $query->where('title', 'like', "%{$term}%")->orWhere('body', 'like', "%{$term}%");
            });
        })->latest()->limit(12)->get();
    }

    #[Computed]
    public function selectedPost(): ?CommunityPost
    {
        if ($this->selectedPostId === null) {
            return null;
        }

        return CommunityPost::query()->where('status', 'published')->whereIn('visibility', ['community', 'anonymized'])->find($this->selectedPostId);
    }

    /** @return Collection<int, PeerReview> */
    #[Computed]
    public function peerReviews(): Collection
    {
        $publishedPostIds = CommunityPost::query()->where('status', 'published')->whereIn('visibility', ['community', 'anonymized'])->pluck('id');

        return PeerReview::query()->where('status', 'open')->whereIn('community_post_id', $publishedPostIds)->latest()->limit(12)->get();
    }

    /** @return Collection<int, PlatformEvent> */
    #[Computed]
    public function events(): Collection
    {
        return PlatformEvent::query()->where('status', 'published')->where('starts_at', '>=', now())->when(trim($this->search) !== '', function (Builder $query): void {
            $term = trim($this->search);
            $query->where(function (Builder $query) use ($term): void {
                $query->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
            });
        })->orderBy('starts_at')->limit(12)->get();
    }

    #[Computed]
    public function selectedEvent(): ?PlatformEvent
    {
        if ($this->selectedEventId === null) {
            return null;
        }

        return PlatformEvent::query()->where('status', 'published')->find($this->selectedEventId);
    }

    /** @return BaseCollection<int|string, int> */
    #[Computed]
    public function eventRegistrationIds(): BaseCollection
    {
        return DB::table('event_registrations')->where('user_id', $this->user()->id)->where('status', 'registered')->pluck('id', 'event_id');
    }

    /** @return BaseCollection<int, array{type: string, id: int, title: string}> */
    #[Computed]
    public function results(): BaseCollection
    {
        if (trim($this->search) === '') {
            return collect();
        }

        return app(UnifiedSearchService::class)->search($this->user(), $this->search);
    }

    public function selectExpert(int $expertId): void
    {
        $this->selectedExpertId = $expertId;
        $this->selectedAvailabilityId = null;
    }

    public function requestConsultation(ExpertConsultationService $consultations): void
    {
        $this->validate(['selectedAvailabilityId' => ['required', 'integer'], 'consultationNote' => ['nullable', 'string', 'max:2000']]);
        $expert = $this->selectedExpert;
        $availability = ExpertAvailability::query()->findOrFail($this->selectedAvailabilityId);
        abort_unless($expert !== null, 404);
        $consultations->request($this->businessOrFail(), $this->user(), $expert, $availability, trim($this->consultationNote) !== '' ? trim($this->consultationNote) : null);
        $this->consultationNote = '';
        $this->selectedAvailabilityId = null;
        session()->flash('ecosystem_success', 'Consultation request sent.');
    }

    public function selectService(int $providerId, int $serviceId): void
    {
        $this->selectedProviderId = $providerId;
        $this->selectedServiceId = $serviceId;
    }

    public function createMarketplaceLead(MarketplaceServiceLayer $marketplace): void
    {
        $this->validate(['selectedServiceId' => ['required', 'integer'], 'leadMessage' => ['required', 'string', 'max:2000']]);
        $provider = $this->selectedProvider;
        $service = $this->selectedService;
        abort_unless($provider !== null && $service !== null, 404);
        $marketplace->createLead($this->user(), $this->businessOrFail(), $provider, $service, trim($this->leadMessage));
        $this->leadMessage = '';
        session()->flash('ecosystem_success', 'Provider request sent.');
    }

    public function createPost(): void
    {
        $this->validate(['postTitle' => ['required', 'string', 'max:255'], 'postBody' => ['required', 'string', 'max:10000']]);
        CommunityPost::query()->create(['user_id' => $this->user()->id, 'title' => trim($this->postTitle), 'body' => trim($this->postBody), 'visibility' => 'community', 'status' => 'published']);
        $this->postTitle = '';
        $this->postBody = '';
        session()->flash('ecosystem_success', 'Community post published.');
    }

    public function requestPeerReview(PeerReviewService $peerReviews): void
    {
        $this->validate(['selectedPostId' => ['required', 'integer'], 'peerReviewVisibility' => ['required', 'in:private,selected,community,anonymized']]);
        $post = $this->selectedPost;
        abort_unless($post !== null, 404);
        $peerReviews->request($this->user(), $post, $this->peerReviewVisibility);
        session()->flash('ecosystem_success', 'Peer review requested.');
    }

    public function respondToPeerReview(PeerReviewService $peerReviews, int $reviewId): void
    {
        $this->validate(['reviewBody' => ['required', 'string', 'max:5000']]);
        $review = PeerReview::query()->where('status', 'open')->findOrFail($reviewId);
        $peerReviews->respond($review, $this->user(), trim($this->reviewBody));
        $this->reviewBody = '';
        session()->flash('ecosystem_success', 'Peer review response submitted.');
    }

    public function reportPost(ModerationReportService $reports, int $postId): void
    {
        $this->validate(['reportReason' => ['required', 'string', 'max:1000']]);
        $post = CommunityPost::query()->findOrFail($postId);
        $reports->reportCommunityPost($this->user(), $post, trim($this->reportReason));
        $this->reportReason = '';
        session()->flash('ecosystem_success', 'Report submitted for moderation.');
    }

    public function registerForEvent(EventRegistrationService $registrations, int $eventId): void
    {
        $event = PlatformEvent::query()->findOrFail($eventId);
        $registrations->register($event, $this->user());
        session()->flash('ecosystem_success', 'Event registration confirmed.');
    }

    public function cancelEventRegistration(EventRegistrationService $registrations, int $registrationId): void
    {
        $registrations->cancel($registrationId, $this->user());
        session()->flash('ecosystem_success', 'Event registration cancelled.');
    }

    public function showSection(string $section): void
    {
        abort_unless(in_array($section, ['all', 'experts', 'marketplace', 'community', 'events'], true), 404);
        $this->section = $section;
    }

    private function businessOrFail(): Business
    {
        $business = $this->business();
        abort_unless($business !== null, 404);
        return $business;
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.ecosystem');
    }
}
