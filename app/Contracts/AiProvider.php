<?php

namespace App\Contracts;

interface AiProvider
{
    /** @param array<string, mixed> $input */
    public function generate(string $model, string $prompt, array $input): AiProviderResult;
}
