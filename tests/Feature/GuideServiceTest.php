<?php

declare(strict_types=1);

use App\Models\Guide;
use App\Models\GuideSection;
use App\Models\GuideStep;
use App\Models\User;
use App\Services\GuideService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('publishes a structured guide and stores an immutable revision snapshot', function (): void {
    $admin = User::factory()->admin()->create();
    $guide = Guide::query()->create([
        'slug' => 'validate-your-business',
        'title' => 'Validate Your Business',
        'description' => 'A practical guide.',
        'author_id' => $admin->id,
    ]);
    $section = $guide->sections()->create([
        'position' => 1,
        'title' => 'Start here',
        'content' => 'Define the problem.',
    ]);
    $section->steps()->create([
        'position' => 1,
        'title' => 'Interview customers',
        'content' => 'Talk to five people.',
        'resources' => ['worksheet.pdf'],
    ]);

    $published = app(GuideService::class)->publish($guide, $admin);

    expect($published->status)->toBe('published')
        ->and($published->published_at)->not->toBeNull()
        ->and($published->version)->toBe(1)
        ->and($published->revisions)->toHaveCount(1)
        ->and($published->revisions->first()->version)->toBe(1)
        ->and($published->revisions->first()->content['sections'][0]['steps'][0]['title'])
        ->toBe('Interview customers');
});

it('does not expose an unpublished guide to unrelated users', function (): void {
    $admin = User::factory()->admin()->create();
    $other = User::factory()->create();
    $guide = Guide::query()->create([
        'slug' => 'draft-guide',
        'title' => 'Draft Guide',
        'author_id' => $admin->id,
    ]);

    expect(Gate::forUser($other)->allows('view', $guide))->toBeFalse();
});

it('allows the author to manage but not an unrelated user', function (): void {
    $author = User::factory()->create();
    $other = User::factory()->create();
    $guide = Guide::query()->create([
        'slug' => 'my-guide',
        'title' => 'My Guide',
        'author_id' => $author->id,
    ]);

    expect(Gate::forUser($author)->allows('update', $guide))->toBeTrue()
        ->and(Gate::forUser($other)->allows('update', $guide))->toBeFalse();
});

it('requires content before publication', function (): void {
    $admin = User::factory()->admin()->create();
    $guide = Guide::query()->create([
        'slug' => 'empty-guide',
        'title' => 'Empty Guide',
        'author_id' => $admin->id,
    ]);

    expect(fn () => app(GuideService::class)->publish($guide, $admin))
        ->toThrow(ValidationException::class);
});

it('creates a new draft version without changing the stable guide identity', function (): void {
    $admin = User::factory()->admin()->create();
    $guide = Guide::query()->create([
        'slug' => 'stable-guide',
        'title' => 'Stable Guide',
        'author_id' => $admin->id,
        'status' => 'published',
        'version' => 2,
        'published_at' => now(),
    ]);

    $revision = app(GuideService::class)->startRevision($guide, $admin);

    expect($revision->id)->toBe($guide->id)
        ->and($revision->slug)->toBe('stable-guide')
        ->and($revision->version)->toBe(3)
        ->and($revision->status)->toBe('draft')
        ->and($revision->published_at)->toBeNull();
});
