<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\Evaluation;
use App\Models\EvaluationFinding;
use App\Models\Priority;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\RecommendationPresentationService;
use App\Services\RecommendationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Evaluation diagnosis')]
final class EvaluationDiagnosis extends Component
{
    public ?int $evaluationId = null;

    public function mount(BusinessContextService $businessContext): void
    {
        $business = $businessContext->current($this->user());
        abort_unless($business !== null, 404);
        $evaluation = $business->evaluations()->where('status', 'completed')->latest('id')->first();
        abort_unless($evaluation !== null, 404);
        $this->evaluationId = $evaluation->id;
    }

    #[Computed]
    public function evaluation(): Evaluation
    {
        return Evaluation::query()
            ->whereKey($this->evaluationId)
            ->where('business_id', $this->business()->id)
            ->with('version', 'findings')
            ->firstOrFail();
    }

    /** @return Collection<int, EvaluationFinding> */
    #[Computed]
    public function findings(): Collection
    {
        return $this->evaluation()->findings;
    }

    /** @return Collection<int, Recommendation> */
    #[Computed]
    public function recommendations(): Collection
    {
        return app(RecommendationPresentationService::class)->forBusiness($this->business(), $this->user());
    }

    /** @return Collection<int, Recommendation> */
    public function recommendationsFor(EvaluationFinding $finding): Collection
    {
        return $this->recommendations()->filter(fn (Recommendation $recommendation): bool => $recommendation->evaluation_finding_id === $finding->id);
    }

    /** @return array<string, mixed> */
    public function recommendationContext(Recommendation $recommendation): array
    {
        return app(RecommendationPresentationService::class)->context($recommendation);
    }

    public function acceptRecommendation(int $recommendationId, RecommendationService $service): void
    {
        $recommendation = $this->recommendation($recommendationId);
        $service->accept($recommendation, $this->user());
        unset($this->recommendations);
    }

    public function rejectRecommendation(int $recommendationId, RecommendationService $service): void
    {
        $recommendation = $this->recommendation($recommendationId);
        $service->reject($recommendation, $this->user(), 'Rejected from diagnosis.');
        unset($this->recommendations);
    }

    public function prioritizeRecommendation(int $recommendationId, RecommendationService $service): void
    {
        $recommendation = $this->recommendation($recommendationId);
        $position = (int) (Priority::query()->where('business_id', $this->business()->id)->max('position') ?? 0) + 1;
        $service->prioritize($recommendation, $this->user(), $position);
        unset($this->recommendations);
    }

    private function recommendation(int $id): Recommendation
    {
        $recommendation = $this->recommendations()->firstWhere('id', $id);
        abort_unless($recommendation !== null, 404);

        return $recommendation;
    }

    #[Computed]
    public function business(): Business
    {
        $business = app(BusinessContextService::class)->current($this->user());
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
        return view('livewire.business.evaluation-diagnosis');
    }
}
