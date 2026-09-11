<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AiProvider;
use App\Models\AiPrompt;
use App\Models\AiRecommendation;
use App\Models\AiRun;
use App\Models\AiUsageRecord;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

final class AiService
{
    private readonly AiProvider $provider;

    public function __construct(AiProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param  array<string, mixed>  $input
     * @param  array<string, string>  $outputRules
     */
    public function run(AiPrompt $prompt, string $model, array $input, ?User $user = null, ?Business $business = null, array $outputRules = []): AiRun
    {
        /** @var array<string, mixed> $typedInput */
        $typedInput = $input;
        /** @var array<string, string> $typedOutputRules */
        $typedOutputRules = $outputRules;
        $run = AiRun::create([
            'user_id' => $user?->id,
            'business_id' => $business?->id,
            'ai_prompt_id' => $prompt->id,
            'provider' => $this->provider::class,
            'model' => $model,
            'status' => 'running',
            'input' => $typedInput,
        ]);

        try {
            $result = $this->provider->generate($model, $prompt->template, $typedInput);
            if ($typedOutputRules !== []) {
                Validator::make($result->output, $typedOutputRules)->validate();
            }

            $run->update([
                'status' => 'completed',
                'output' => $result->output,
                'input_tokens' => $result->inputTokens,
                'output_tokens' => $result->outputTokens,
                'cost' => $result->cost,
            ]);
            AiUsageRecord::updateOrCreate(
                ['ai_run_id' => $run->id],
                [
                    'user_id' => $user?->id,
                    'business_id' => $business?->id,
                    'input_tokens' => $result->inputTokens,
                    'output_tokens' => $result->outputTokens,
                    'cost' => $result->cost,
                ],
            );
            if ($business !== null) {
                AiRecommendation::create([
                    'ai_run_id' => $run->id,
                    'business_id' => $business->id,
                    'status' => 'suggested',
                    'payload' => $result->output,
                ]);
            }
        } catch (RuntimeException $exception) {
            $run->update(['status' => 'failed', 'error' => $exception->getMessage()]);
            throw $exception;
        }

        return $run->fresh() ?? $run;
    }
}
