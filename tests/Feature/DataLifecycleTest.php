<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\DataRequest;
use App\Models\User;
use App\Services\DataDeletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('allows a verified user to export account data without internal credentials', function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    $business = Business::factory()->create(['name' => 'Exportable Business']);
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    $this->actingAs($user)->getJson('/account/data/export')->assertOk()
        ->assertJsonPath('schema_version', 1)->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('businesses.0.name', 'Exportable Business')
        ->assertJsonMissingPath('user.is_admin')->assertJsonMissingPath('user.password');
});

it('reuses one pending deletion request', function (): void {
    $user = User::factory()->create();
    $first = $this->actingAs($user)->postJson('/account/data/deletion')->assertStatus(202);
    $second = $this->actingAs($user)->postJson('/account/data/deletion')->assertStatus(202);
    expect($first->json('data_request_id'))->toBe($second->json('data_request_id'));
    expect(DB::table('data_requests')->where('user_id', $user->id)->count())->toBe(1);
    expect(DB::table('audit_logs')->where('actor_id', $user->id)->where('action', 'data_deletion_requested')->count())->toBe(1);
});

it('exposes deletion status to the authenticated user', function (): void {
    $user = User::factory()->create();
    $request = DataRequest::query()->create(['user_id' => $user->id, 'type' => DataRequest::TYPE_DELETION, 'status' => DataRequest::STATUS_PENDING, 'requested_at' => now()]);
    $this->actingAs($user)->getJson('/account/data/deletion')->assertOk()
        ->assertJsonPath('data_request_id', $request->id)->assertJsonPath('status', DataRequest::STATUS_PENDING);
});

it('rejects deletion review by a non administrator', function (): void {
    $user = User::factory()->create();
    $request = DataRequest::query()->create(['user_id' => $user->id, 'type' => DataRequest::TYPE_DELETION, 'status' => DataRequest::STATUS_PENDING, 'requested_at' => now()]);
    $this->actingAs($user)->postJson('/internal/support/data-requests/'.$request->id.'/approve')->assertForbidden();
});

it('allows an administrator to approve or reject pending deletion requests', function (): void {
    $user = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $approved = DataRequest::query()->create(['user_id' => $user->id, 'type' => DataRequest::TYPE_DELETION, 'status' => DataRequest::STATUS_PENDING, 'requested_at' => now()]);
    $this->actingAs($admin)->postJson('/internal/support/data-requests/'.$approved->id.'/approve')->assertOk()->assertJsonPath('status', DataRequest::STATUS_APPROVED);
    $rejected = DataRequest::query()->create(['user_id' => $user->id, 'type' => DataRequest::TYPE_DELETION, 'status' => DataRequest::STATUS_PENDING, 'requested_at' => now()->addSecond()]);
    $this->actingAs($admin)->postJson('/internal/support/data-requests/'.$rejected->id.'/reject')->assertOk()->assertJsonPath('status', DataRequest::STATUS_REJECTED);
});

it('deletes an approved account and its sole-owner business while retaining financial records without the user reference', function (): void {
    Storage::fake('local');
    $user = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $business = Business::factory()->create();
    $business->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);
    Storage::disk('local')->put('documents/evidence.pdf', 'evidence');
    $document = BusinessDocument::query()->create(['business_id' => $business->id, 'uploaded_by' => $user->id, 'name' => 'evidence.pdf', 'disk' => 'local', 'path' => 'documents/evidence.pdf', 'visibility' => 'private', 'uploaded_at' => now()]);
    $transactionId = DB::table('commerce_transactions')->insertGetId(['user_id' => $user->id, 'purchasable_type' => 'product', 'purchasable_id' => 1, 'provider' => 'test', 'provider_reference' => 'ref-'.$user->id, 'status' => 'confirmed', 'amount' => 100, 'currency' => 'RON', 'idempotency_key' => 'key-'.$user->id, 'confirmed_at' => now(), 'refunded_at' => null, 'created_at' => now(), 'updated_at' => now()]);
    $request = DataRequest::query()->create(['user_id' => $user->id, 'type' => DataRequest::TYPE_DELETION, 'status' => DataRequest::STATUS_APPROVED, 'requested_at' => now(), 'reviewed_by' => $admin->id, 'reviewed_at' => now()]);
    $result = app(DataDeletionService::class)->execute($request, $admin);
    expect($result['status'])->toBe(DataRequest::STATUS_COMPLETED)->and(User::query()->find($user->id))->toBeNull();
    expect(Business::withTrashed()->find($business->id))->toBeNull()->and(BusinessDocument::query()->find($document->id))->toBeNull();
    expect(Storage::disk('local')->exists('documents/evidence.pdf'))->toBeFalse();
    expect(DB::table('commerce_transactions')->whereKey($transactionId)->value('user_id'))->toBeNull();
    expect(DB::table('data_requests')->whereKey($request->id)->value('status'))->toBe(DataRequest::STATUS_COMPLETED);
    expect(DB::table('audit_logs')->where('subject_type', DataRequest::class)->where('subject_id', $request->id)->where('action', 'data_deletion_completed')->exists())->toBeTrue();
});

it('removes only the deleted member from a shared business', function (): void {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $business = Business::factory()->create();
    $business->members()->attach([$user->id => ['role' => 'owner', 'joined_at' => now()], $member->id => ['role' => 'member', 'joined_at' => now()]]);
    $request = DataRequest::query()->create(['user_id' => $user->id, 'type' => DataRequest::TYPE_DELETION, 'status' => DataRequest::STATUS_APPROVED, 'requested_at' => now(), 'reviewed_by' => $admin->id, 'reviewed_at' => now()]);
    app(DataDeletionService::class)->execute($request, $admin);
    expect(User::query()->find($user->id))->toBeNull();
    expect(Business::query()->find($business->id))->not->toBeNull();
    expect($business->members()->whereKey($member->id)->exists())->toBeTrue()->and($business->members()->whereKey($user->id)->exists())->toBeFalse();
});
