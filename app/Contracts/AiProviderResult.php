<?php

namespace App\Contracts;

final readonly class AiProviderResult
{
    /** @param array<string, mixed> $output */
    public function __construct(
        public array $output,
        public int $inputTokens = 0,
        public int $outputTokens = 0,
        public float $cost = 0.0,
    ) {}
}
