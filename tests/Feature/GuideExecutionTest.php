<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\Guide;
use App\Models\User;
use App\Services\GuideExecutionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

function publishedGuide(User $author, string $slug = 'customer-validation'): Guide
{
    $guide = Guide::query()->create([
        'slug' => $slug,
        'title' => 'Customer Validation',
        'description' => 'Validate demand before building.',
        'status' => 'published',
        'version' => 1,
        'author_id' => $author->id,
        'published_at' => now(),
    ]);

    $section = $guide->sections()->create([
        'position' => 1,
        'title' => 'Talk to customers',
        'content' => 'Run structured interviews.',
    ]);
    $section->steps()->create([
        'position' => 1,
        'title' => 'Interview five customers',
        'content' => 'Ask about the problem and current alternatives.',
    ]);

    return $guide->fresh(['sections.steps']);
}

it('only exposes published guides through the entrepreneur catalogue', function (): void {
    $user = User::factory()->create();
    $draft = Guide::query()->create([
        'slug' => 'draft-guide',
        'title' => 'Draft Guide',
        'author_id' => $user->id,
    ]);
    $published = publishedGuide($user);

    expect(app(GuideExecutionService::class)->published()->pluck('id')->all())
        ->toBe([$published->id]);

    expect($draft->fresh()->getAttribute('status'))->toBeNull();
});

it('persists business-scoped progress for the published guide version', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $guide = publishedGuide($user);
    $service = app(GuideExecutionService::class);
    $step = $guide->sections->first()->steps->first();

    $progress = $service->start($guide, $business, $user);
    $updated = $service->completeStep($guide, $business, $user, $step->id);

    expect($progress->guide_version)->toBe(1)
        ->and($updated->completed_steps)->toContain($step->id)
        ->and($updated->completed_at)->not->toBeNull();
});

it('rejects guide execution for a non-member business', function (): void {
    $author = User::factory()->create();
    $member = User::factory()->create();
    $business = Business::factory()->create();
    $guide = publishedGuide($author);

    expect(fn () => app(GuideExecutionService::class)->start($guide, $business, $member))
        ->toThrow(HttpException::class);
});

it('does not allow progress against an unpublished guide', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user, ['role' => 'owner', 'joined_at' => now()]);
    $guide = publishedGuide($user, 'revision-guide');
    $guide->update(['status' => 'draft', 'published_at' => null]);

    expect(fn () => app(GuideExecutionService::class)->start($guide->fresh(), $business, $user))
        ->toThrow(ValidationException::class);
});

test('guide routes require authentication', function (): void {
    $response = $this->get(route('business.guides'));

    $response->assertRedirect(route('login'));
});
