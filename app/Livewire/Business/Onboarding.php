<?php

declare(strict_types=1);

namespace App\Livewire\Business;

use App\Models\User;
use App\Services\BusinessContextService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Set up your business')]
final class Onboarding extends Component
{
    public string $name = '';

    public string $description = '';

    public function mount(BusinessContextService $businessContext): void
    {
        if ($businessContext->current($this->user()) !== null) {
            $this->redirectRoute('dashboard');
        }
    }

    public function createBusiness(BusinessContextService $businessContext): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $businessContext->create(
            $this->user(),
            $validated['name'],
            $validated['description'] !== '' ? $validated['description'] : null,
        );

        $this->redirectRoute('dashboard');
    }

    private function user(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 401);

        return $user;
    }

    public function render(): mixed
    {
        return view('livewire.business.onboarding');
    }
}
