<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\Guide;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\GuideExecutionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Guides')]
final class Guides extends Component
{
    public function mount(BusinessContextService $businessContext): void
    {
        abort_unless($businessContext->current($this->user()) !== null, 404);
    }

    /** @return Collection<int, Guide> */
    #[Computed]
    public function guides(): Collection
    {
        $service = app(GuideExecutionService::class);
        $relevant = $service->relevantForBusiness($this->business(), $this->user())->get();

        return $relevant->merge($service->published()->get())->unique('id')->values();
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
        return view('livewire.business.guides');
    }
}
