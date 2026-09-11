<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\Expert;
use App\Models\ExpertAvailability;
use App\Models\User;
use App\Services\ExpertConsultationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('discovers only verified experts', function (): void {
    $verified = Expert::query()->create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Growth Mentor',
        'verification_status' => 'verified',
    ]);
    Expert::query()->create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Pending Mentor',
        'verification_status' => 'pending',
    ]);

    $experts = app(ExpertConsultationService::class)->discover();

    expect($experts)->toHaveCount(1);
    expect($experts->first()?->id)->toBe($verified->id);
});

it('requires an authorized business member and valid availability to request a consultation', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    $expert = Expert::query()->create(['user_id' => User::factory()->create()->id, 'title' => 'Mentor', 'verification_status' => 'verified']);
    $availability = ExpertAvailability::query()->create([
        'expert_id' => $expert->id,
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHour(),
        'is_bookable' => true,
    ]);

    $consultation = app(ExpertConsultationService::class)->request($business, $user, $expert, $availability, 'Need help with sales.');

    expect($consultation->status)->toBe('requested')->and($consultation->scheduled_at)->not->toBeNull();
});

it('rejects unavailable expert slots', function (): void {
    $user = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    $expert = Expert::query()->create(['user_id' => User::factory()->create()->id, 'title' => 'Mentor', 'verification_status' => 'verified']);
    $availability = ExpertAvailability::query()->create([
        'expert_id' => $expert->id,
        'starts_at' => now()->subHour(),
        'ends_at' => now(),
        'is_bookable' => true,
    ]);

    expect(fn (): mixed => app(ExpertConsultationService::class)->request($business, $user, $expert, $availability))->toThrow(ValidationException::class);
});