<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CommunityPost;
use App\Models\PeerReview;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class PeerReviewService
{
    public function request(User $user, CommunityPost $post, string $visibility): PeerReview
    {
        if ($post->user_id !== $user->id) {
            abort(403);
        }
        if (! in_array($visibility, ['private', 'selected', 'community', 'anonymized'], true)) {
            throw ValidationException::withMessages(['visibility' => 'Invalid visibility mode.']);
        }

        return PeerReview::query()->create(['community_post_id' => $post->id, 'requester_id' => $user->id, 'visibility' => $visibility, 'status' => 'open']);
    }

    public function respond(PeerReview $review, User $reviewer, string $body): int
    {
        if ($review->status !== 'open') {
            throw ValidationException::withMessages(['review' => 'This review is closed.']);
        }
        if ($review->requester_id === $reviewer->id) {
            throw ValidationException::withMessages(['reviewer' => 'The requester cannot review their own request.']);
        }

        return (int) $review->getConnection()->table('peer_review_responses')->insertGetId([
            'peer_review_id' => $review->id,
            'reviewer_id' => $reviewer->id,
            'body' => $body,
            'accepted' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}