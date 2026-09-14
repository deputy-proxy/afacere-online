<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ModerationReportService
{
    public function reportCommunityPost(User $user, CommunityPost $post, string $reason): int
    {
        abort_unless($post->status === 'published', 404);
        abort_unless($post->user_id !== $user->id, 403);

        return (int) DB::table('moderation_reports')->insertGetId([
            'reporter_id' => $user->id,
            'reportable_type' => CommunityPost::class,
            'reportable_id' => $post->id,
            'reason' => $reason,
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
