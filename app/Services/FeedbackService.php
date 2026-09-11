<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiFeedback;
use App\Models\AiRun;
use App\Models\User;

final class FeedbackService
{
    public function submit(User $user, AiRun $run, ?int $rating, ?string $feedback): AiFeedback
    {
        abort_unless($run->user_id === $user->id, 403);
        abort_unless($rating === null || ($rating >= 1 && $rating <= 5), 422);

        return AiFeedback::create([
            'ai_run_id' => $run->id,
            'user_id' => $user->id,
            'rating' => $rating,
            'feedback' => $feedback,
        ]);
    }
}
