<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\User;
use App\Services\BusinessContextService;
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

    #[Computed]
    public function businesses(): array
    {
        return app(BusinessContextService::class)->forUser($this->user());
    }

    public function switchBusiness(BusinessContextService $businessContext): void
    {
        if ($this->businessId === null) {
            return;
        }

        $business = $this->user()->businesses()->whereKey($this->businessId)->firstOrFail();
        $businessContext->select($business, $this->user());
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
