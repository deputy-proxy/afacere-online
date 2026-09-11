<?php

declare(strict_types=1);

use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\User;
use App\Services\DocumentAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('keeps business documents private until explicitly shared', function (): void {
    $owner = User::factory()->create();
    $recipient = User::factory()->create();
    $business = Business::factory()->create();
    $business->members()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
    $document = BusinessDocument::query()->create(['business_id' => $business->id, 'uploaded_by' => $owner->id, 'name' => 'evidence.pdf', 'disk' => 'local', 'path' => 'documents/evidence.pdf', 'visibility' => 'private', 'uploaded_at' => now()]);

    expect(app(DocumentAccessService::class)->canAccess($document, $recipient))->toBeFalse();
    app(DocumentAccessService::class)->share($document, $business, $owner, 'user', $recipient->id);
    expect(app(DocumentAccessService::class)->canAccess($document, $recipient))->toBeTrue();
    expect(DB::table('document_shares')->where('business_document_id', $document->id)->exists())->toBeTrue();
});
