<?php

namespace App\Services;

use App\Contracts\AiProvider;
use App\Models\AiPrompt;
use App\Models\AiRun;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

final class AiService
{
    public function __construct(
        private readonly AiProvider $provider,
    ) {}

    /** @param array<string, mixed> $input */
    /** @param array<string, string> $outputRules */
    public function run(
        AiPrompt $prompt,
        string $model,
        array $input,
        ?User $user = null,
        ?Business $business = null,
        array $outputRules = [],
    ): AiRun {
        $run = AiRun::create([
            'user_id' => $user?->id,
            'business_id' => $business?->id,
            'ai_prompt_id' => $prompt->id,
            'provider' => $this->provider::class,
            'model' => $model,
            'status' => 'running',
            'input' => $input,
        ]);

        try {
            $result = $this->provider->generate($model, $prompt->template, $input);

            if ($outputRules !== []) {
                Validator::make($result->output, $outputRules)->validate();
            }

            $run->update([
                'status' => 'completed',
                'output' => $result->output,
                'input_tokens' => $result->inputTokens,
                'output_tokens' => $result->outputTokens,
                'cost' => $result->cost,
            ]);
        } catch (RuntimeException $exception) {
            $run->update([
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);
            throw $exception;
        }

        return $run->fresh() ?? $run;
    }
}
