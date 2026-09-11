<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\RecommendationPresentationService;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Recommendations extends Component
{
    public ?int $businessId = null;

    public function mount(BusinessContextService $businessContext): void
    {
        $business = $businessContext->current($this->user());
        $this->businessId = $business?->id;
    }

    #[Computed]
    public function business(): ?Business
    {
        return $this->currentBusiness();
    }

    /** @return array<int, Recommendation> */
    #[Computed]
    public function recommendations(): array
    {
        $business = $this->currentBusiness();
        if ($business === null) {
            return [];
        }

        return app(RecommendationPresentationService::class)
            ->forBusiness($business, $this->user())
            ->all();
    }

    public function accept(int $recommendationId, RecommendationService $service): void
    {
        $recommendation = $this->recommendation($recommendationId);
        $service->accept($recommendation, $this->user());
        unset($this->recommendations);
    }

    public function reject(int $recommendationId, RecommendationService $service): void
    {
        $recommendation = $this->recommendation($recommendationId);
        $service->reject($recommendation, $this->user(), 'Rejected by entrepreneur.');
        unset($this->recommendations);
    }

    private function recommendation(int $id): Recommendation
    {
        $business = $this->currentBusiness();
        abort_unless($business !== null, 404);

        return Recommendation::query()
            ->whereKey($id)
            ->where('business_id', $business->id)
            ->where('status', 'suggested')
            ->firstOrFail();
    }

    private function currentBusiness(): ?Business
    {
        return $this->businessId === null
            ? null
            : $this->user()->businesses()->whereKey($this->businessId)->first();
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.recommendations', [
            'presentation' => app(RecommendationPresentationService::class),
        ]);
    }
}
