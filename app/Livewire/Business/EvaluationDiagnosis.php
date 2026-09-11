<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\Evaluation;
use App\Models\User;
use App\Services\BusinessContextService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

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
        return Evaluation::query()->whereKey($this->evaluationId)->where('business_id', $this->business()->id)->with('version', 'findings')->firstOrFail();
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
