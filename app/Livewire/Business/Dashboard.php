<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Enums\EvaluationStatus;
use App\Models\ActionPlan;
use App\Models\Business;
use App\Models\BusinessGoal;
use App\Models\BusinessMetric;
use App\Models\Evaluation;
use App\Models\Priority;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\RecommendationPresentationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
final class Dashboard extends Component
{
    public ?int $businessId = null;

    public function mount(BusinessContextService $businessContext): void
    {
        $business = $businessContext->current($this->user());

        if ($business === null) {
            $this->redirectRoute('business.onboarding');

            return;
        }

        $this->businessId = $business->id;
    }

    #[Computed]
    public function business(): ?Business
    {
        if ($this->businessId === null) {
            return null;
        }

        return $this->user()->businesses()->whereKey($this->businessId)->first();
    }

    /** @return array<int, Business> */
    #[Computed]
    public function businesses(): array
    {
        return app(BusinessContextService::class)->forUser($this->user());
    }

    #[Computed]
    public function latestEvaluation(): ?Evaluation
    {
        return $this->business()?->evaluations()->latest('id')->first();
    }

    /** @return array<int, Priority> */
    #[Computed]
    public function priorities(): array
    {
        $business = $this->business();
        if ($business === null) {
            return [];
        }

        return Priority::query()
            ->where('business_id', $business->id)
            ->where('status', 'active')
            ->orderBy('position')
            ->get()
            ->all();
    }

    /** @return array<int, Recommendation> */
    #[Computed]
    public function recommendations(): array
    {
        $business = $this->business();
        if ($business === null) {
            return [];
        }

        return app(RecommendationPresentationService::class)
            ->forBusiness($business, $this->user())
            ->all();
    }

    #[Computed]
    public function actionPlan(): ?ActionPlan
    {
        $business = $this->business();
        if ($business === null) {
            return null;
        }

        return ActionPlan::query()
            ->where('business_id', $business->id)
            ->latest('version')
            ->with('actions')
            ->first();
    }

    /** @return array<int, BusinessGoal> */
    #[Computed]
    public function goals(): array
    {
        return $this->business()?->goals()->where('status', '!=', 'completed')->latest('id')->get()->all() ?? [];
    }

    /** @return array<int, BusinessMetric> */
    #[Computed]
    public function metrics(): array
    {
        return $this->business()?->metrics()->latest('id')->get()->all() ?? [];
    }

    /** @return array{label: string, description: string, route: string, route_parameters: array<string, mixed>} */
    #[Computed]
    public function nextAction(): array
    {
        $business = $this->business();
        if ($business === null) {
            return [
                'label' => __('Set up your business'),
                'description' => __('Create a business workspace before continuing.'),
                'route' => 'business.onboarding',
                'route_parameters' => [],
            ];
        }

        $evaluation = $this->latestEvaluation();
        if ($evaluation === null || $evaluation->status !== EvaluationStatus::Completed) {
            return [
                'label' => $evaluation === null ? __('Start your evaluation') : __('Continue your evaluation'),
                'description' => __('Use your evaluation to establish the priorities that drive your next steps.'),
                'route' => 'business.evaluation',
                'route_parameters' => [],
            ];
        }

        if ($this->priorities() === []) {
            return [
                'label' => __('Review your diagnosis'),
                'description' => __('Turn your evaluation findings into clear business priorities.'),
                'route' => 'business.evaluation.diagnosis',
                'route_parameters' => [],
            ];
        }

        $plan = $this->actionPlan();
        if ($plan === null) {
            return [
                'label' => __('Build your Action Plan'),
                'description' => __('Turn your active priorities into a sequence of recommended actions.'),
                'route' => 'business.action-plan',
                'route_parameters' => [],
            ];
        }

        $openAction = $plan->actions->first(fn ($action): bool => in_array($action->status->value, ['recommended', 'accepted', 'active'], true));
        if ($openAction !== null) {
            return [
                'label' => __('Continue your Action Plan'),
                'description' => $openAction->title,
                'route' => 'business.action-plan',
                'route_parameters' => [],
            ];
        }

        if ($this->recommendations() !== []) {
            return [
                'label' => __('Review new recommendations'),
                'description' => __('You have new suggested work to consider.'),
                'route' => 'dashboard',
                'route_parameters' => [],
            ];
        }

        return [
            'label' => __('Explore opportunities'),
            'description' => __('Look for external opportunities that match your current business context.'),
            'route' => 'business.opportunities',
            'route_parameters' => [],
        ];
    }

    public function switchBusiness(BusinessContextService $businessContext): void
    {
        if ($this->businessId === null) {
            return;
        }

        $business = $this->user()->businesses()->whereKey($this->businessId)->firstOrFail();
        $businessContext->select($business, $this->user());
        unset($this->business, $this->latestEvaluation, $this->priorities, $this->recommendations, $this->actionPlan, $this->goals, $this->metrics, $this->nextAction);
    }

    private function user(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.dashboard');
    }
}
