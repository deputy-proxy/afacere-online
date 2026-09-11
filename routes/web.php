<?php

declare(strict_types=1);

use App\Livewire\Account\Subscription;
use App\Livewire\Business\ActionPlan;
use App\Livewire\Business\Dashboard;
use App\Livewire\Business\EvaluationDiagnosis;
use App\Livewire\Business\EvaluationWizard;
use App\Livewire\Business\GuideReader;
use App\Livewire\Business\Guides;
use App\Livewire\Business\Monitor;
use App\Livewire\Business\Notifications;
use App\Livewire\Business\Onboarding;
use App\Livewire\Business\Opportunities;
use App\Livewire\Business\OpportunityReader;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/how-it-works', 'public.how-it-works')->name('public.how-it-works');
Route::view('/pricing', 'public.pricing')->name('public.pricing');
Route::view('/about', 'public.about')->name('public.about');
Route::view('/faq', 'public.faq')->name('public.faq');
Route::view('/contact', 'public.contact')->name('public.contact');
Route::view('/legal', 'public.legal')->name('public.legal');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('notifications', Notifications::class)->name('business.notifications');
    Route::livewire('account/subscription', Subscription::class)->name('account.subscription');
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
