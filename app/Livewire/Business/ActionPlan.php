<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Actions\TransitionAction;
use App\Enums\ActionStatus;
use App\Models\Action;
use App\Models\ActionEvidence;
use App\Models\ActionPlan as ActionPlanModel;
use App\Models\ActionPlanRevision;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\User;
use App\Services\ActionPlanRevisionService;
use App\Services\ActionPlanService;
use App\Services\BusinessContextService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Action Plan')]
final class ActionPlan extends Component
{
    public ?int $planId = null;

    public string $outcome = '';

    public string $evidence = '';

    public string $resolutionReason = '';

    public ?int $completionActionId = null;

    public ?int $resolutionActionId = null;

    public ?string $resolutionStatus = null;

    public bool $showResolutionModal = false;

    public function mount(BusinessContextService $businessContext): void
    {
        $business = $businessContext->current($this->user());
        abort_unless($business !== null, 404);
        $plan = ActionPlanModel::query()->where('business_id', $business->id)->latest('version')->first();
        $this->planId = $plan?->id;
    }

    #[Computed]
    public function business(): Business
    {
        $business = app(BusinessContextService::class)->current($this->user());
        abort_unless($business !== null, 404);

        return $business;
    }

    #[Computed]
    public function plan(): ?ActionPlanModel
    {
        if ($this->planId === null) {
            return null;
        }

        return ActionPlanModel::query()
            ->whereKey($this->planId)
            ->where('business_id', $this->business()->id)
            ->with('actions.priority', 'actions.recommendation', 'actions.outcome', 'actions.guides', 'actions.evidence', 'revisions.creator')
            ->first();
    }

    /** @return Collection<string, Collection<int, Action>> */
    #[Computed]
    public function actionsByStatus(): Collection
    {
        return $this->plan()?->actions->groupBy(fn (Action $action): string => $action->status->value) ?? collect();
    }

    /** @return Collection<int, AuditLog> */
    public function historyFor(Action $action): Collection
    {
        return AuditLog::query()
            ->where('subject_type', Action::class)
            ->where('subject_id', $action->id)
            ->with('actor')
            ->orderByDesc('occurred_at')
            ->get();
    }

    /** @return Collection<int, ActionPlanRevision> */
    #[Computed]
    public function revisions(): Collection
    {
        $plan = $this->plan();
        if ($plan === null) {
            return collect();
        }

        return $plan->revisions;
    }

    public function createPlan(ActionPlanService $service, ActionPlanRevisionService $revisions): void
    {
        $plan = $service->createFromPriorities($this->business(), $this->user());
        $this->planId = $plan->id;
        $revisions->snapshot($plan, $this->user());
    }

    public function updateStatus(int $actionId, string $status, TransitionAction $transition, ActionPlanRevisionService $revisions): void
    {
        $action = $this->action($actionId);
        $next = ActionStatus::tryFrom($status);
        abort_unless($next !== null, 422);
        $transition->execute($action, $next, $this->user());
        $revisions->snapshot($this->planOrFail(), $this->user());
    }

    public function beginResolution(int $actionId, string $status): void
    {
        abort_unless(in_array($status, [ActionStatus::Skipped->value, ActionStatus::Blocked->value], true), 422);
        $this->action($actionId);
        $this->resolutionActionId = $actionId;
        $this->resolutionStatus = $status;
        $this->resolutionReason = '';
        $this->showResolutionModal = true;
    }

    public function resolveAction(TransitionAction $transition, ActionPlanRevisionService $revisions): void
    {
        $this->validate(['resolutionReason' => ['required', 'string', 'max:5000']]);
        abort_unless($this->resolutionActionId !== null && $this->resolutionStatus !== null, 422);
        $action = $this->action($this->resolutionActionId);
        $next = ActionStatus::tryFrom($this->resolutionStatus);
        abort_unless($next !== null, 422);
        $transition->execute($action, $next, $this->user(), $this->resolutionReason);
        $revisions->snapshot($this->planOrFail(), $this->user());
        $this->resetResolution();
    }

    public function beginCompletion(int $actionId): void
    {
        $this->action($actionId);
        $this->completionActionId = $actionId;
        $this->outcome = '';
        $this->evidence = '';
    }

    public function cancelCompletion(): void
    {
        $this->completionActionId = null;
        $this->outcome = '';
        $this->evidence = '';
        $this->resetValidation('outcome');
        $this->resetValidation('evidence');
    }

    public function complete(int $actionId, TransitionAction $transition, ActionPlanRevisionService $revisions): void
    {
        $this->validate(['outcome' => ['required', 'string', 'max:5000'], 'evidence' => ['nullable', 'string', 'max:5000']]);
        $action = $this->action($actionId);
        $transition->execute($action, ActionStatus::Completed, $this->user());
        $action->outcome()->updateOrCreate([], [
            'summary' => $this->outcome,
            'evidence' => $this->evidence !== '' ? [$this->evidence] : null,
            'recorded_at' => now(),
        ]);

        if ($this->evidence !== '') {
            ActionEvidence::create([
                'action_id' => $action->id,
                'user_id' => $this->user()->id,
                'type' => 'note',
                'description' => $this->evidence,
                'recorded_at' => now(),
            ]);
        }

        $revisions->snapshot($this->planOrFail(), $this->user());
        $this->cancelCompletion();
    }

    public function resetResolution(): void
    {
        $this->showResolutionModal = false;
        $this->resolutionActionId = null;
        $this->resolutionStatus = null;
        $this->resolutionReason = '';
        $this->resetValidation('resolutionReason');
    }

    private function action(int $actionId): Action
    {
        return Action::query()
            ->whereKey($actionId)
            ->whereHas('plan', fn ($query) => $query->whereKey($this->planId)->where('business_id', $this->business()->id))
            ->firstOrFail();
    }

    private function planOrFail(): ActionPlanModel
    {
        $plan = $this->plan();
        abort_unless($plan !== null, 404);

        return $plan;
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.action-plan');
    }
}
