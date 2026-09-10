<?php

namespace App\Contracts;

interface AiProvider
{
    public function generate(string $model, string $prompt, array $input): AiProviderResult;
}
