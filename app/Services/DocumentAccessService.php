<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DocumentAccessService
{
    public function canAccess(BusinessDocument $document, User $user): bool
    {
        if ($document->business->members()->whereKey($user->id)->exists()) {
            return true;
        }

        return DB::table('document_shares')->where('business_document_id', $document->id)->where('recipient_type', 'user')->where('recipient_id', $user->id)->whereNull('revoked_at')->exists();
    }

    public function share(BusinessDocument $document, Business $business, User $user, string $recipientType, int $recipientId): void
    {
        abort_unless($document->business_id === $business->id && $business->members()->whereKey($user->id)->exists(), 403);
        DB::table('document_shares')->insert([
            'business_document_id' => $document->id,
            'shared_by' => $user->id,
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientId,
            'shared_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}