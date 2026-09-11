<?php

declare(strict_types=1);

use App\Enums\BusinessStage;
use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('business context and preferences are persisted as structured data', function (): void {
    $business = Business::factory()->create([
        'profile' => ['industry' => 'education'],
        'context' => ['market' => 'Romania', 'team_size' => 4],
        'preferences' => ['currency' => 'EUR', 'recommendation_style' => 'practical'],
    ]);

    $fresh = $business->fresh();

    expect($fresh)->not->toBeNull()
        ->and($fresh?->profile)->toBe(['industry' => 'education'])
        ->and($fresh?->context)->toBe(['market' => 'Romania', 'team_size' => 4])
        ->and($fresh?->preferences)->toBe(['currency' => 'EUR', 'recommendation_style' => 'practical']);
});

test('business factory supports every lifecycle stage', function (): void {
    foreach (BusinessStage::cases() as $stage) {
        expect(Business::factory()->atStage($stage)->create()->stage)->toBe($stage);
    }
});

test('business documents belong to a business and are private by default', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();

    $document = BusinessDocument::query()->create([
        'business_id' => $business->id,
        'uploaded_by' => $user->id,
        'name' => 'Business plan.pdf',
        'disk' => 'local',
        'path' => 'businesses/'.$business->id.'/business-plan.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1024,
        'visibility' => 'private',
        'uploaded_at' => Carbon::now(),
    ]);

    expect($business->fresh()?->documents()->first()?->is($document))->toBeTrue()
        ->and($document->visibility)->toBe('private')
        ->and($document->business->is($business))->toBeTrue()
        ->and($document->uploader->is($user))->toBeTrue();
});
