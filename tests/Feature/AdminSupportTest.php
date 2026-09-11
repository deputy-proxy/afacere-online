<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('denies support access to non-admin users', function (): void {
    $actor = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor)
        ->getJson("/internal/support/users/{$target->id}")
        ->assertForbidden();
});

it('allows admins to request controlled password recovery', function (): void {
    Notification::fake();
    $actor = User::factory()->admin()->create();
    $target = User::factory()->create();

    $this->actingAs($actor)
        ->postJson("/internal/support/users/{$target->id}/password-recovery")
        ->assertOk();

    expect(DB::table('audit_logs')
        ->where('actor_id', $actor->id)
        ->where('subject_id', $target->id)
        ->where('action', 'admin_password_recovery_requested')
        ->exists())->toBeTrue();
});
