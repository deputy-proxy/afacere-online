<?php

declare(strict_types=1);

use App\Enums\ActionStatus;
use App\Livewire\Business\ActionPlan as ActionPlanComponent;
use App\Livewire\Business\EvaluationDiagnosis;
use App\Livewire\Business\EvaluationWizard;
use App\Livewire\Business\Monitor;
use App\Livewire\Business\Onboarding;
use App\Models\Action;
use App\Models\ActionOutcome;
use App\Models\ActionPlan;
use App\Models\Business;
use App\Models\Entitlement;
use App\Models\EvaluationVersion;
use App\Models\Guide;
use App\Models\Priority;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function canonicalEvaluationVersion(): EvaluationVersion
{
    $version = EvaluationVersion::query()->create([
        'key' => 'canonical-journey',
        'name' => 'Canonical journey',
        'version' => '1.0',
        'is_active' => true,
        'definition' => [],
    ]);

    $section = $version->sections()->create([
        'position' => 1,
        'key' => 'customer',
        'title' => 'Customer & Market',
    ]);

    $section->questions()->createMany([
        [
            'position' => 1,
            'key' => 'customer',
            'type' => 'text',
            'prompt' => 'Who is your customer?',
            'required' => true,
            'validation_rules' => ['string', 'max:255'],
        ],
        [
            'position' => 2,
            'key' => 'channel',
            'type' => 'text',
            'prompt' => 'How do customers find you?',
            'required' => true,
            'validation_rules' => ['string', 'max:255'],
        ],
    ]);

    return $version;
}

it('completes the canonical entrepreneur lifecycle from public discovery through monitoring', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('register'));

    $this->post(route('register.store'), [
        'name' => 'Journey Founder',
        'email' => 'journey@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasNoErrors()->assertRedirect(route('dashboard', absolute: false));

    $user = User::query()->where('email', 'journey@example.com')->firstOrFail();
    $user->forceFill(['email_verified_at' => now()])->save();

    $this->actingAs($user)
        ->get(route('business.onboarding'))
        ->assertOk()
        ->assertSee('Create business');

    Livewire::actingAs($user)
        ->test(Onboarding::class)
        ->set('name', 'Journey Business')
        ->set('description', 'A business used to prove the complete entrepreneur journey.')
        ->call('createBusiness')
        ->assertRedirect(route('dashboard'));

    $business = Business::query()->where('name', 'Journey Business')->firstOrFail();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee($business->name)
        ->assertSee('Start your evaluation');

    foreach ([
        'business.guides',
        'business.opportunities',
        'business.ecosystem',
        'business.monitor',
    ] as $routeName) {
        $this->actingAs($user)
            ->get(route($routeName))
            ->assertOk()
            ->assertSee($business->name);
    }

    canonicalEvaluationVersion();

    Livewire::actingAs($user)
        ->test(EvaluationWizard::class)
        ->set('answer', 'Independent founders')
        ->call('saveAndNext')
        ->set('answer', 'Direct referrals')
        ->call('saveAndNext')
        ->assertRedirect(route('business.evaluation.diagnosis'));

    $evaluation = $business->evaluations()->latest('id')->firstOrFail();
    expect($evaluation->getRawOriginal('status'))->toBe('completed');

    $finding = $evaluation->findings()->firstOrFail();
    $recommendation = Recommendation::factory()->create([
        'business_id' => $business->id,
        'evaluation_id' => $evaluation->id,
        'evaluation_finding_id' => $finding->id,
        'title' => 'Improve customer acquisition',
        'reason' => 'A clearer acquisition path will make the next step measurable.',
        'recommended_action' => 'Document and test one acquisition channel.',
        'status' => 'suggested',
    ]);

    Livewire::actingAs($user)
        ->test(EvaluationDiagnosis::class)
        ->assertSee($finding->title)
        ->assertSee($recommendation->title)
        ->call('prioritizeRecommendation', $recommendation->id)
        ->assertSee($recommendation->title);

    expect(Priority::query()->where('business_id', $business->id)->where('recommendation_id', $recommendation->id)->exists())->toBeTrue();

    Livewire::actingAs($user)
        ->test(ActionPlanComponent::class)
        ->call('createPlan')
        ->assertSee('Version 1')
        ->assertSee($recommendation->title);

    $action = Action::query()
        ->whereHas('plan', fn ($query) => $query->where('business_id', $business->id))
        ->firstOrFail();

    Livewire::actingAs($user)
        ->test(ActionPlanComponent::class)
        ->set('planId', $action->action_plan_id)
        ->call('updateStatus', $action->id, ActionStatus::Accepted->value)
        ->call('updateStatus', $action->id, ActionStatus::Active->value)
        ->call('beginCompletion', $action->id)
        ->set('outcome', 'Tested the acquisition channel with five prospects.')
        ->set('evidence', 'Five prospect conversations recorded.')
        ->call('complete', $action->id)
        ->assertSee('Completed')
        ->assertSee('Tested the acquisition channel');

    expect(Action::query()->findOrFail($action->id)->status)->toBe(ActionStatus::Completed)
        ->and(ActionOutcome::query()->where('action_id', $action->id)->exists())->toBeTrue();

    Entitlement::query()->create([
        'user_id' => $user->id,
        'key' => 'monitor',
        'value' => 'true',
    ]);

    Livewire::actingAs($user)
        ->test(Monitor::class)
        ->set('enabled', true)
        ->set('cadence', 'weekly')
        ->call('saveConfiguration')
        ->set('revenue', '1000')
        ->set('cash', '3')
        ->set('customers', '5')
        ->set('confidence', '3')
        ->call('checkIn')
        ->set('revenue', '1400')
        ->set('cash', '4')
        ->set('customers', '8')
        ->set('confidence', '4')
        ->call('checkIn')
        ->assertSee('Check-in saved');

    $monitor = Livewire::actingAs($user)->test(Monitor::class);

    expect($business->monitorCheckIns()->count())->toBe(2)
        ->and(array_keys($monitor->get('trends')))->toBe(['revenue', 'customers', 'cash', 'confidence']);

    $this->actingAs($user)
        ->get(route('business.monitor'))
        ->assertOk()
        ->assertSee($business->name)
        ->assertSee('Monitor');
});

it('covers critical validation, authorization, stale-resource and recovery paths', function (): void {
    $owner = User::factory()->create();
    $outsider = User::factory()->create();
    $business = Business::factory()->create(['name' => 'Protected Journey']);
    $business->members()->attach($owner, ['role' => 'owner', 'joined_at' => now()]);

    $guide = Guide::query()->create([
        'slug' => 'protected-journey-guide',
        'title' => 'Protected Journey Guide',
        'description' => 'A guide used for authorization and stale-resource coverage.',
        'status' => 'published',
        'version' => 1,
        'author_id' => $owner->id,
        'published_at' => now(),
    ]);

    $this->actingAs($outsider)
        ->get(route('business.guides.show', $guide->slug))
        ->assertNotFound();

    $guide->delete();

    $this->actingAs($owner)
        ->get(route('business.guides.show', 'protected-journey-guide'))
        ->assertNotFound();

    Livewire::actingAs($owner)
        ->test(Onboarding::class)
        ->assertRedirect(route('dashboard'));

    $newcomer = User::factory()->create();

    Livewire::actingAs($newcomer)
        ->test(Onboarding::class)
        ->set('name', '')
        ->call('createBusiness')
        ->assertHasErrors(['name']);

    $plan = ActionPlan::query()->create(['business_id' => $business->id,
        'version' => 1,
        'status' => 'active',
        'activated_at' => now(),
    ]);
    $action = $plan->actions()->create([
        'position' => 1,
        'title' => 'Recoverable action',
        'status' => ActionStatus::Recommended,
    ]);

    Livewire::actingAs($owner)
        ->test(ActionPlanComponent::class)
        ->set('planId', $plan->id)
        ->call('beginCompletion', $action->id)
        ->call('complete', $action->id)
        ->assertHasErrors(['outcome']);

    expect(Action::query()->findOrFail($action->id)->status)->toBe(ActionStatus::Recommended);

    Livewire::actingAs($owner)
        ->test(ActionPlanComponent::class)
        ->set('planId', $plan->id)
        ->call('beginCompletion', $action->id)
        ->set('outcome', 'Recovered by supplying the required outcome.')
        ->call('updateStatus', $action->id, ActionStatus::Accepted->value)
        ->call('updateStatus', $action->id, ActionStatus::Active->value)
        ->call('complete', $action->id)
        ->assertSee('Completed');

    expect(Action::query()->findOrFail($action->id)->status)->toBe(ActionStatus::Completed);
});
