<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('allows a verified user to export account data', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/account/data/export')
        ->assertOk()
        ->assertJsonPath('user.id', $user->id);
});

it('creates one auditable deletion request for a user', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/account/data/deletion')
        ->assertStatus(202);

    $this->actingAs($user)
        ->postJson('/account/data/deletion')
        ->assertStatus(202);

    expect(DB::table('data_requests')->where('user_id', $user->id)->count())->toBe(1);
    expect(DB::table('audit_logs')->where('actor_id', $user->id)->where('action', 'data_deletion_requested')->count())->toBe(1);
});
