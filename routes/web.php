<?php

declare(strict_types=1);

use App\Http\Controllers\AdminSupportController;
use App\Http\Controllers\DataLifecycleController;
use App\Http\Controllers\ReadinessController;
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
use App\Livewire\Ecosystem;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/how-it-works', 'public.how-it-works')->name('public.how-it-works');
Route::view('/pricing', 'public.pricing')->name('public.pricing');
Route::view('/about', 'public.about')->name('public.about');
Route::view('/faq', 'public.faq')->name('public.faq');
Route::view('/contact', 'public.contact')->name('public.contact');
Route::view('/legal', 'public.legal')->name('public.legal');
Route::get('/health/ready', ReadinessController::class)->name('health.ready');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('account/data/export', [DataLifecycleController::class, 'export'])->name('account.data.export');
    Route::post('account/data/deletion', [DataLifecycleController::class, 'requestDeletion'])->name('account.data.deletion');
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
    Route::livewire('ecosystem', Ecosystem::class)->name('business.ecosystem');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('internal/support')->name('internal.support.')->group(function (): void {
    Route::get('users/{user}', [AdminSupportController::class, 'summary'])->name('users.summary');
    Route::post('users/{user}/password-recovery', [AdminSupportController::class, 'passwordRecovery'])->name('users.password-recovery');
});

require __DIR__.'/settings.php';
