<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\OpportunityMatch;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\OpportunityMatchingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Opportunities')]
final class Opportunities extends Component
{
    public function mount(BusinessContextService $businessContext): void
    {
        abort_unless($businessContext->current($this->user()) !== null, 404);
    }

    /** @return Collection<int, OpportunityMatch> */
    #[Computed]
    public function matches(): Collection
    {
        return app(OpportunityMatchingService::class)->matches($this->business(), $this->user());
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
        return view('livewire.business.opportunities');
    }
}
