<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\MonitorCheckIn;
use App\Models\User;
use App\Services\BusinessContextService;
use App\Services\MonitorService;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Monitor')]
final class Monitor extends Component
{
    public bool $enabled = false;

    public string $cadence = 'weekly';

    public string $revenue = '';

    public string $cash = '';

    public string $customers = '';

    public string $confidence = '';

    public function mount(BusinessContextService $businessContext): void
    {
        $business = $businessContext->current($this->user());
        abort_unless($business !== null, 404);
        $configuration = $business->monitorConfiguration()->first();
        if ($configuration !== null) {
            $this->enabled = $configuration->enabled;
            $this->cadence = $configuration->cadence;
        }
    }

    #[Computed]
    public function business(): Business
    {
        $business = app(BusinessContextService::class)->current($this->user());
        abort_unless($business !== null, 404);

        return $business;
    }

    #[Computed]
    public function configuration(): mixed
    {
        return $this->business()->monitorConfiguration()->first();
    }

    /** @return Collection<int, MonitorCheckIn> */
    #[Computed]
    public function checkIns(): Collection
    {
        return $this->business()->monitorCheckIns()->latest('recorded_at')->limit(8)->get();
    }

    #[Computed]
    public function alerts(): mixed
    {
        return $this->business()->monitorAlerts()->whereNull('resolved_at')->latest('triggered_at')->limit(8)->get();
    }

    #[Computed]
    public function healthIndicators(): mixed
    {
        return $this->business()->healthIndicators()->latest('measured_at')->limit(8)->get()->unique('key')->values();
    }

    /** @return array<string, float> */
    #[Computed]
    public function trends(): array
    {
        $checkIns = $this->checkIns()->sortBy('recorded_at')->values();
        if ($checkIns->count() < 2) {
            return [];
        }

        $first = $checkIns->first()->responses;
        $last = $checkIns->last()->responses;
        $trends = [];

        foreach (['revenue', 'customers', 'cash', 'confidence'] as $key) {
            if (is_numeric($first[$key] ?? null) && is_numeric($last[$key] ?? null)) {
                $trends[$key] = (float) $last[$key] - (float) $first[$key];
            }
        }

        return $trends;
    }

    public function saveConfiguration(MonitorService $service): void
    {
        $this->validate(['cadence' => ['required', 'in:daily,weekly,monthly']]);
        $service->configure($this->business(), $this->user(), $this->enabled, $this->cadence);
        unset($this->configuration);
        session()->flash('monitor_status', 'Monitor settings saved.');
    }

    public function checkIn(MonitorService $service, NotificationService $notifications): void
    {
        $this->validate([
            'revenue' => ['required', 'numeric'],
            'cash' => ['required', 'numeric', 'min:1', 'max:5'],
            'customers' => ['required', 'numeric', 'min:0'],
            'confidence' => ['required', 'numeric', 'min:1', 'max:5'],
        ]);
        $checkIn = $service->checkIn($this->business(), $this->user(), [
            'revenue' => (float) $this->revenue,
            'cash' => (float) $this->cash,
            'customers' => (float) $this->customers,
            'confidence' => (float) $this->confidence,
        ]);
        $notifications->recordEvent('monitor.check_in_completed', $this->user(), $this->business(), $checkIn);
        $notifications->notify($this->user(), 'monitor.check_in_completed', 'Monitor updated', 'Your latest business check-in has been recorded.', $this->business(), [
            'event_key' => sprintf('monitor:check-in:%d', $checkIn->id),
            'url' => route('business.monitor'),
        ]);
        $this->reset('revenue', 'cash', 'customers', 'confidence');
        unset($this->configuration, $this->checkIns, $this->alerts, $this->healthIndicators, $this->trends);
        session()->flash('monitor_status', 'Check-in saved. Your progress history has been updated.');
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.monitor');
    }
}
