<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\Business;
use App\Models\DomainEvent;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\BusinessContextService;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notifications')]
final class Notifications extends Component
{
    public function markRead(int $notificationId, NotificationService $notifications): void
    {
        $notifications->markRead($this->user(), $notificationId);
        unset($this->notifications, $this->unread);
    }

    public function markUnread(int $notificationId, NotificationService $notifications): void
    {
        $notifications->markUnread($this->user(), $notificationId);
        unset($this->notifications, $this->unread);
    }

    #[Computed]
    public function business(): ?Business
    {
        return app(BusinessContextService::class)->current($this->user());
    }

    /** @return array<int, UserNotification> */
    #[Computed]
    public function notifications(): array
    {
        return app(NotificationService::class)->recent($this->user(), $this->business());
    }

    /** @return Collection<int, DomainEvent> */
    #[Computed]
    public function activity(): Collection
    {
        return app(NotificationService::class)->activity($this->user(), $this->business());
    }

    #[Computed]
    public function unread(): int
    {
        return app(NotificationService::class)->unreadCount($this->user(), $this->business());
    }

    private function user(): User
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.notifications');
    }
}
