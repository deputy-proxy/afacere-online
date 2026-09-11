<?php

declare(strict_types=1);

use App\Livewire\Business\ActionPlan;
use App\Livewire\Business\Dashboard;
use App\Livewire\Business\EvaluationDiagnosis;
use App\Livewire\Business\EvaluationWizard;
use App\Livewire\Business\GuideReader;
use App\Livewire\Business\Guides;
use App\Livewire\Business\Monitor;
use App\Livewire\Business\Onboarding;
use App\Livewire\Business\Opportunities;
use App\Livewire\Business\OpportunityReader;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('business/onboarding', Onboarding::class)->name('business.onboarding');
    Route::livewire('business/evaluation', EvaluationWizard::class)->name('business.evaluation');
    Route::livewire('business/evaluation/diagnosis', EvaluationDiagnosis::class)->name('business.evaluation.diagnosis');
    Route::livewire('business/action-plan', ActionPlan::class)->name('business.action-plan');
    Route::livewire('business/guides', Guides::class)->name('business.guides');
    Route::livewire('business/guides/{slug}', GuideReader::class)->name('business.guides.show');
    Route::livewire('business/opportunities', Opportunities::class)->name('business.opportunities');
    Route::livewire('business/opportunities/{opportunityId}', OpportunityReader::class)->name('business.opportunities.show');
    Route::livewire('business/monitor', Monitor::class)->name('business.monitor');
});

require __DIR__.'/settings.php';
