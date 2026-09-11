<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('allows a verified user to export account data without internal credentials', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    $business = Business::factory()->create(['name' => 'Exportable Business']);
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);

    $this->actingAs($user)
        ->getJson('/account/data/export')
        ->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('businesses.0.name', 'Exportable Business')
        ->assertJsonMissingPath('user.is_admin')
        ->assertJsonMissingPath('user.password');
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
