<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\CommunityPost;
use App\Models\Expert;
use App\Models\MarketplaceProvider;
use App\Models\PlatformEvent;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\UnifiedSearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Ecosystem')]
final class Ecosystem extends Component
{
    public string $search = '';

    public function mount(BusinessContextService $businessContext): void
    {
        abort_unless($businessContext->current($this->user()) !== null, 404);
    }

    /** @return Collection<int, Expert> */
    #[Computed]
    public function experts(): Collection
    {
        return Expert::query()
            ->where('verification_status', 'verified')
            ->with('user:id,name')
            ->orderBy('title')
            ->limit(6)
            ->get();
    }

    /** @return Collection<int, MarketplaceProvider> */
    #[Computed]
    public function providers(): Collection
    {
        return MarketplaceProvider::query()
            ->where('verification_status', 'verified')
            ->whereHas('services', fn (Builder $query): Builder => $query->where('is_published', true))
            ->with(['services' => fn (Builder $query): Builder => $query->where('is_published', true)->orderBy('name')])
            ->orderBy('name')
            ->limit(6)
            ->get();
    }

    /** @return Collection<int, CommunityPost> */
    #[Computed]
    public function communityPosts(): Collection
    {
        return CommunityPost::query()
            ->where('status', 'published')
            ->whereIn('visibility', ['community', 'anonymized'])
            ->latest()
            ->limit(6)
            ->get();
    }

    /** @return Collection<int, PlatformEvent> */
    #[Computed]
    public function events(): Collection
    {
        return PlatformEvent::query()
            ->where('status', 'published')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(6)
            ->get();
    }

    /** @return Collection<int, array{type: string, id: int, title: string}> */
    #[Computed]
    public function results(): Collection
    {
        if (trim($this->search) === '') {
            return collect();
        }

        return app(UnifiedSearchService::class)->search($this->user(), $this->search);
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
