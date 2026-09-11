<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\BusinessInvitation;
use App\Models\User;
use App\Services\BusinessMembershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

test('an owner can invite a user and the user can accept the invitation', function (): void {
    $owner = User::factory()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);
    $business = Business::factory()->create();
    $business->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => Carbon::now()]);

    $result = app(BusinessMembershipService::class)->invite($business, $owner, $invitee->email, 'member');

    expect($result['invitation'])->toBeInstanceOf(BusinessInvitation::class)
        ->and($result['invitation']->isPending())->toBeTrue();

    app(BusinessMembershipService::class)->accept($result['token'], $invitee);

    expect($business->fresh()->members()->whereKey($invitee->id)->wherePivot('role', 'member')->exists())->toBeTrue()
        ->and($result['invitation']->fresh()->accepted_at)->not->toBeNull();
});

test('a non-manager cannot invite business members', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($owner->id, ['role' => 'owner']);
    $business->members()->attach($member->id, ['role' => 'member']);

    expect(fn () => app(BusinessMembershipService::class)->invite($business, $member, 'new@example.com'))
        ->toThrow(HttpException::class);
});

test('an invitation cannot be accepted by another user', function (): void {
    $owner = User::factory()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);
    $other = User::factory()->create(['email' => 'other@example.com']);
    $business = Business::factory()->create();
    $business->members()->attach($owner->id, ['role' => 'owner']);

    $result = app(BusinessMembershipService::class)->invite($business, $owner, $invitee->email);

    expect(fn () => app(BusinessMembershipService::class)->accept($result['token'], $other))
        ->toThrow(ValidationException::class);
});

test('expired invitations cannot be accepted', function (): void {
    $owner = User::factory()->create();
    $invitee = User::factory()->create(['email' => 'invitee@example.com']);
    $business = Business::factory()->create();
    $business->members()->attach($owner->id, ['role' => 'owner']);

    $result = app(BusinessMembershipService::class)->invite($business, $owner, $invitee->email);
    $result['invitation']->update(['expires_at' => Carbon::now()->subMinute()]);

    expect(fn () => app(BusinessMembershipService::class)->accept($result['token'], $invitee))
        ->toThrow(ValidationException::class);
});

test('membership invitations are audited', function (): void {
    $owner = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($owner->id, ['role' => 'owner']);

    $result = app(BusinessMembershipService::class)->invite($business, $owner, 'audit@example.com');

    expect(DB::table('audit_logs')
        ->where('subject_type', BusinessInvitation::class)
        ->where('subject_id', $result['invitation']->id)
        ->where('action', 'business_membership_invited')
        ->exists())->toBeTrue();
});
