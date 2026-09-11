<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\Guide;
use App\Models\GuideProgress;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\GuideExecutionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Guide')]
final class GuideReader extends Component
{
    public string $slug;

    public function mount(string $slug, BusinessContextService $businessContext, GuideExecutionService $service): void
    {
        $this->slug = $slug;
        $business = $businessContext->current($this->user());
        abort_unless($business !== null, 404);
        abort_unless($service->published()->where('slug', $slug)->exists(), 404);
    }

    #[Computed]
    public function guide(): Guide
    {
        $guide = app(GuideExecutionService::class)->published()->where('slug', $this->slug)->first();
        abort_unless($guide instanceof Guide, 404);

        return $guide;
    }

    #[Computed]
    public function progress(): ?GuideProgress
    {
        return app(GuideExecutionService::class)->progress($this->guide(), $this->business(), $this->user());
    }

    /** @return array<int, int> */
    #[Computed]
    public function completedStepIds(): array
    {
        return collect($this->progress()?->completed_steps ?? [])
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    /** @return Collection<int, Guide> */
    #[Computed]
    public function relatedGuides(): Collection
    {
        return app(GuideExecutionService::class)
            ->relevantForBusiness($this->business(), $this->user())
            ->where('id', '!=', $this->guide()->id)
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function business(): Business
    {
        $business = app(BusinessContextService::class)->current($this->user());
        abort_unless($business !== null, 404);

        return $business;
    }

    public function start(GuideExecutionService $service): void
    {
        $service->start($this->guide(), $this->business(), $this->user());
        unset($this->progress, $this->completedStepIds);
    }

    public function completeStep(int $stepId, GuideExecutionService $service): void
    {
        $service->completeStep($this->guide(), $this->business(), $this->user(), $stepId);
        unset($this->progress, $this->completedStepIds);
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.guide-reader');
    }
}
