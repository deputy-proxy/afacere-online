<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Actions\TransitionAction;
use App\Enums\ActionStatus;
use App\Models\Action;
use App\Models\ActionPlan as ActionPlanModel;
use App\Models\Business;
use App\Models\User;
use App\Services\ActionPlanService;
use App\Services\BusinessContextService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Action Plan')]
final class ActionPlan extends Component
{
    public ?int $planId = null;

    public ?int $completionActionId = null;

    public string $outcome = '';

    public string $evidence = '';

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
            ->with([
                'actions.priority',
                'actions.recommendation',
                'actions.outcome',
                'actions.evidence.user',
                'actions.guides',
                'actions.auditLogs.actor',
                'revisions',
            ])
            ->first();
    }

    public function createPlan(ActionPlanService $service): void
    {
        $this->planId = $service->createFromPriorities($this->business(), $this->user())->id;
    }

    public function updateStatus(int $actionId, string $status, TransitionAction $transition): void
    {
        $action = $this->actionForCurrentPlan($actionId);
        $next = ActionStatus::tryFrom($status);
        abort_unless($next !== null, 422);

        $transition->execute($action, $next, $this->user());
    }

    public function openCompletionForm(int $actionId): void
    {
        $action = $this->actionForCurrentPlan($actionId);
        abort_unless($action->status === ActionStatus::Active, 422);

        $this->completionActionId = $action->id;
        $this->outcome = '';
        $this->evidence = '';
    }

    public function complete(TransitionAction $transition): void
    {
        $this->validate([
            'outcome' => ['required', 'string', 'max:5000'],
            'evidence' => ['nullable', 'string', 'max:5000'],
        ]);
        abort_unless($this->completionActionId !== null, 422);

        $action = $this->actionForCurrentPlan($this->completionActionId);
        abort_unless($action->status === ActionStatus::Active, 422);
        $transition->execute($action, ActionStatus::Completed, $this->user());
        $action->outcome()->updateOrCreate([], [
            'summary' => $this->outcome,
            'evidence' => $this->evidence !== '' ? [$this->evidence] : null,
            'recorded_at' => now(),
        ]);

        if ($this->evidence !== '') {
            $action->evidence()->create([
                'user_id' => $this->user()->id,
                'type' => 'note',
                'description' => $this->evidence,
                'recorded_at' => now(),
            ]);
        }

        $this->completionActionId = null;
        $this->outcome = '';
        $this->evidence = '';
    }

    private function actionForCurrentPlan(int $actionId): Action
    {
        return Action::query()
            ->whereKey($actionId)
            ->whereHas('plan', fn ($query) => $query->whereKey($this->planId)->where('business_id', $this->business()->id))
            ->firstOrFail();
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
