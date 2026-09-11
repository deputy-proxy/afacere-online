<?php

declare(strict_types=1);

use App\Livewire\Business\Dashboard;
use App\Livewire\Business\Onboarding;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('business/onboarding', Onboarding::class)->name('business.onboarding');
});

require __DIR__.'/settings.php';
