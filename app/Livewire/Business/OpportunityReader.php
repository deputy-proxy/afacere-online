<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\Opportunity;
use App\Models\OpportunityMatch;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\OpportunityMatchingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Opportunity')]
final class OpportunityReader extends Component
{
    public int $opportunityId;

    public function mount(int $opportunityId, BusinessContextService $businessContext, OpportunityMatchingService $service): void
    {
        $this->opportunityId = $opportunityId;
        abort_unless($businessContext->current($this->user()) !== null, 404);
        abort_unless($service->current()->whereKey($opportunityId)->exists(), 404);
        $service->viewed($this->opportunity(), $this->business(), $this->user());
    }

    #[Computed]
    public function opportunity(): Opportunity
    {
        $opportunity = app(OpportunityMatchingService::class)->current()->whereKey($this->opportunityId)->first();
        abort_unless($opportunity instanceof Opportunity, 404);

        return $opportunity;
    }

    #[Computed]
    public function match(): OpportunityMatch
    {
        $matches = app(OpportunityMatchingService::class)->matches($this->business(), $this->user());
        $match = $matches->first(fn (OpportunityMatch $candidate): bool => $candidate->opportunity_id === $this->opportunityId);
        abort_unless($match instanceof OpportunityMatch, 404);

        return $match;
    }

    #[Computed]
    public function business(): Business
    {
        $business = app(BusinessContextService::class)->current($this->user());
        abort_unless($business !== null, 404);

        return $business;
    }

    public function apply(OpportunityMatchingService $service): void
    {
        $service->apply($this->opportunity(), $this->business(), $this->user());
        unset($this->match);
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.opportunity-reader');
    }
}
