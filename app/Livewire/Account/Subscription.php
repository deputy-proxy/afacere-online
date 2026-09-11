<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Models\ProductPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Account & subscription')]
final class Subscription extends Component
{
    public function plans(SubscriptionService $service): mixed { return $service->plans(); }

    #[Computed]
    public function current(): mixed { return app(SubscriptionService::class)->current($this->user()); }

    #[Computed]
    public function businessLimit(): ?int { return app(SubscriptionService::class)->businessLimit($this->user()); }

    #[Computed]
    public function businessCount(): int { return $this->user()->businesses()->count(); }

    public function selectPlan(int $planId, SubscriptionService $service): void
    {
        $plan = ProductPlan::query()->where('active', true)->findOrFail($planId);
        $service->selectPlan($this->user(), $plan);
        unset($this->current, $this->businessLimit);
        session()->flash('subscription_status', 'Subscription updated.');
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);
        return $user;
    }

    public function render(): mixed { return view('livewire.account.subscription', ['plans' => $this->plans(app(SubscriptionService::class))]); }
}
