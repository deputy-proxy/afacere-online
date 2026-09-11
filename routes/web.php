<?php

declare(strict_types=1);

use App\Livewire\Business\Dashboard;
use App\Livewire\Business\EvaluationDiagnosis;
use App\Livewire\Business\EvaluationWizard;
use App\Livewire\Business\Onboarding;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('business/onboarding', Onboarding::class)->name('business.onboarding');
    Route::livewire('business/evaluation', EvaluationWizard::class)->name('business.evaluation');
    Route::livewire('business/evaluation/diagnosis', EvaluationDiagnosis::class)->name('business.evaluation.diagnosis');
});

require __DIR__.'/settings.php';
