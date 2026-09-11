<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\Opportunity;
use App\Models\OpportunityEvent;
use App\Models\User;
use App\Services\OpportunityMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

function opportunity(array $criteria = [], ?Carbon $validUntil = null): Opportunity
{
    return Opportunity::query()->create([
        'title' => 'Growth Grant',
        'description' => 'Support for qualifying businesses.',
        'criteria' => $criteria,
        'is_published' => true,
        'valid_from' => now()->subDay(),
        'valid_until' => $validUntil,
    ]);
}

it('excludes expired opportunities from current discovery', function (): void {
    $expired = opportunity([], now()->subMinute());
    $current = opportunity([], now()->addDay());

    expect(app(OpportunityMatchingService::class)->current()->pluck('id')->all())
        ->toBe([$current->id])
        ->not->toContain($expired->id);
});

it('evaluates eligibility deterministically and explains each criterion', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create(['profile' => ['employees' => 8, 'sector' => 'software']]);
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $match = opportunity([
        'employees' => ['operator' => 'min', 'value' => 5],
        'sector' => ['operator' => 'equals', 'value' => 'software'],
    ]);

    $result = app(OpportunityMatchingService::class)->matches($business, $user)->first();

    expect($result)->not->toBeNull()
        ->and((float) $result->score)->toBe(1.0)
        ->and($result->criteria_results['employees']['passed'])->toBeTrue()
        ->and($result->criteria_results['sector']['passed'])->toBeTrue();
});

it('does not allow an ineligible business to apply', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create(['profile' => ['employees' => 2]]);
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $match = opportunity(['employees' => ['operator' => 'min', 'value' => 5]]);

    expect(fn () => app(OpportunityMatchingService::class)->apply($match, $business, $user))
        ->toThrow(ValidationException::class);
});

it('keeps opportunity interactions business-scoped and records engagement', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $match = opportunity();
    $service = app(OpportunityMatchingService::class);

    $service->viewed($match, $business, $user);
    $service->matches($business, $user);
    $service->apply($match, $business, $user);

    expect(OpportunityEvent::query()->where('business_id', $business->id)->pluck('event_type')->all())
        ->toContain('viewed', 'matched', 'applied');
});

it('rejects interactions from users outside the business', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $business = Business::factory()->create();
    $match = opportunity();

    $business->members()->attach($owner, ['role' => 'owner', 'joined_at' => now()]);

    expect(fn () => app(OpportunityMatchingService::class)->matches($business, $other))
        ->toThrow(HttpException::class);
});

test('opportunity routes require authentication', function (): void {
    $response = $this->get(route('business.opportunities'));

    $response->assertRedirect(route('login'));
});
