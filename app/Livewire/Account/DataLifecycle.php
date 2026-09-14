<?php

declare(strict_types=1);

namespace App\Livewire\Account;

use App\Models\DataRequest;
use App\Models\User;
use App\Services\DataLifecycleService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Data & privacy')]
final class DataLifecycle extends Component
{
    public bool $confirmDeletion = false;

    #[Computed]
    public function deletionRequest(): ?DataRequest
    {
        return DataRequest::query()
            ->where('user_id', $this->user()->id)
            ->where('type', DataRequest::TYPE_DELETION)
            ->latest('requested_at')
            ->first();
    }

    /** @return array<string, mixed> */
    #[Computed]
    public function exportSummary(): array
    {
        $data = app(DataLifecycleService::class)->export($this->user());

        return [
            'businesses' => is_array($data['businesses'] ?? null) ? count($data['businesses']) : 0,
            'data_requests' => is_array($data['data_requests'] ?? null) ? count($data['data_requests']) : 0,
        ];
    }

    public function requestDeletion(DataLifecycleService $service): void
    {
        abort_unless($this->confirmDeletion, 422);
        $service->requestDeletion($this->user());
        $this->confirmDeletion = false;
        unset($this->deletionRequest);
        session()->flash('data_lifecycle_status', 'Deletion request submitted for review.');
    }

    public function cancelConfirmation(): void
    {
        $this->confirmDeletion = false;
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.account.data-lifecycle');
    }
}
