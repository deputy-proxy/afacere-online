<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiFeedback;
use App\Models\AiPrompt;
use App\Models\AiPromptVersion;
use App\Models\AiRun;
use App\Models\AiUsageRecord;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AiGovernanceService
{
    /** @param array<string, mixed>|null $outputSchema */
    public function publishPromptVersion(AiPrompt $prompt, string $template, ?array $outputSchema = null): AiPromptVersion
    {
        return DB::transaction(function () use ($prompt, $template, $outputSchema): AiPromptVersion {
            $version = ((int) $prompt->versions()->max('version')) + 1;
            $prompt->versions()->update(['active' => false]);
            $revision = $prompt->versions()->create(['version' => $version, 'template' => $template, 'output_schema' => $outputSchema, 'active' => true]);
            $prompt->update(['template' => $template, 'version' => $version]);
            return $revision;
        });
    }

    public function recordFeedback(AiRun $run, User $user, ?int $rating, ?string $feedback): AiFeedback
    {
        abort_unless($run->user_id === null || $run->user_id === $user->id || $user->isAdmin(), 403);
        if ($rating !== null && ($rating < 1 || $rating > 5)) {
            throw ValidationException::withMessages(['rating' => 'Rating must be between 1 and 5.']);
        }
        return AiFeedback::create(['ai_run_id' => $run->id, 'user_id' => $user->id, 'rating' => $rating, 'feedback' => $feedback]);
    }

    public function recordUsage(AiRun $run): AiUsageRecord
    {
        return AiUsageRecord::query()->updateOrCreate(
            ['ai_run_id' => $run->id],
            ['user_id' => $run->user_id, 'business_id' => $run->business_id, 'input_tokens' => $run->input_tokens ?? 0, 'output_tokens' => $run->output_tokens ?? 0, 'cost' => $run->cost ?? 0],
        );
    }

    public function confirmRecommendation(AiRun $run, Business $business, array $payload): void
    {
        abort_unless($run->business_id === $business->id, 403);
        $recommendation = $run->aiRecommendation()->first();
        if ($recommendation !== null) {
            $recommendation->update(['status' => 'confirmed', 'confirmed_at' => now(), 'payload' => $payload]);
        }
    }
}
