<?php

declare(strict_types=1);

use App\Livewire\Business\GuideReader;
use App\Livewire\Business\Guides;
use App\Livewire\Business\Opportunities;
use App\Livewire\Business\OpportunityReader;
use App\Models\Business;
use App\Models\Guide;
use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use App\Models\OpportunityType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function workflowGuide(User $author, string $slug = 'workflow-guide'): Guide
{
    $guide = Guide::query()->create([
        'slug' => $slug,
        'title' => 'Workflow Guide',
        'description' => 'A guide for the workflow test.',
        'status' => 'published',
        'version' => 1,
        'author_id' => $author->id,
        'published_at' => now(),
    ]);

    $section = $guide->sections()->create([
        'position' => 1,
        'title' => 'First section',
        'content' => 'Section content.',
    ]);
    $section->steps()->create([
        'position' => 1,
        'title' => 'First step',
        'content' => 'Step content.',
        'resources' => [
            ['title' => 'Useful resource', 'url' => 'https://example.com/resource'],
        ],
    ]);

    return $guide->fresh(['sections.steps']);
}

function workflowOpportunity(array $criteria = []): Opportunity
{
    $type = OpportunityType::query()->firstOrCreate(
        ['key' => 'workflow-funding'],
        ['name' => 'Workflow funding'],
    );

    return Opportunity::query()->create([
        'opportunity_type_id' => $type->id,
        'title' => 'Workflow Grant',
        'description' => 'A grant for the workflow test.',
        'criteria' => $criteria,
        'is_published' => true,
        'valid_from' => now()->subDay(),
        'valid_until' => now()->addDay(),
    ]);
}

it('renders published guides with their sections and resources', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $guide = workflowGuide($user);

    Livewire::actingAs($user)
        ->test(Guides::class)
        ->assertSee($guide->title)
        ->assertSee('Read guide');

    Livewire::actingAs($user)
        ->test(GuideReader::class, ['slug' => $guide->slug])
        ->assertSee($guide->title)
        ->assertSee('First section')
        ->assertSee('First step')
        ->assertSee('Useful resource');
});

it('preserves guide progress through the reader', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $guide = workflowGuide($user, 'progress-guide');
    $step = $guide->sections->first()->steps->first();

    Livewire::actingAs($user)
        ->test(GuideReader::class, ['slug' => $guide->slug])
        ->call('start')
        ->assertSee('0/1 steps')
        ->call('completeStep', $step->id)
        ->assertSee('1/1 steps')
        ->assertSee('Guide completed.');
});

it('does not expose a guide reader to a user outside the business', function (): void {
    $author = User::factory()->create();
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($owner, ['role' => 'owner', 'joined_at' => now()]);
    $guide = workflowGuide($author, 'private-guide');

    $this->actingAs($other)
        ->get(route('business.guides.show', $guide->slug))
        ->assertNotFound();
});

it('renders matched opportunities with deterministic match context', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create(['profile' => ['employees' => 8]]);
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $opportunity = workflowOpportunity([
        'employees' => ['operator' => 'min', 'value' => 5],
    ]);

    Livewire::actingAs($user)
        ->test(Opportunities::class)
        ->assertSee($opportunity->title)
        ->assertSee('100% match')
        ->assertSee('1 of 1 eligibility criterion met.');

    Livewire::actingAs($user)
        ->test(OpportunityReader::class, ['opportunityId' => $opportunity->id])
        ->assertSee('Why this matches your business')
        ->assertSee('Employees')
        ->assertSee('Criterion met');
});

it('shows the application state after a successful opportunity handoff', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $opportunity = workflowOpportunity();

    Livewire::actingAs($user)
        ->test(OpportunityReader::class, ['opportunityId' => $opportunity->id])
        ->call('apply')
        ->assertSee('Application tracking started');

    expect(OpportunityApplication::query()
        ->where('business_id', $business->id)
        ->where('opportunity_id', $opportunity->id)
        ->where('user_id', $user->id)
        ->exists())->toBeTrue();
});

it('does not expose opportunities to a user outside the business', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($owner, ['role' => 'owner', 'joined_at' => now()]);
    $opportunity = workflowOpportunity();

    $this->actingAs($other)
        ->get(route('business.opportunities.show', $opportunity->id))
        ->assertNotFound();
});

test('guide and opportunity routes require authentication', function (): void {
    $this->get(route('business.guides'))->assertRedirect(route('login'));
    $this->get(route('business.opportunities'))->assertRedirect(route('login'));
});
